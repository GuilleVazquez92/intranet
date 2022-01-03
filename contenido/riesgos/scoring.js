	window.onload = consultar_puntos();


	function asignar_linea(){
		var cuenta 		  	= document.getElementById("cuenta").innerHTML;
		var puntos 			= document.getElementById('total_puntos').innerHTML;
		var linea_credito 	= document.getElementById('limite_prestable_valor').innerHTML
		linea_credito  		= linea_credito.replace(/\./g, '');

	//	puntos = 40;
	//	linea_credito = 2500000;


		if(puntos>=32){
			$.ajax({
				type:'POST',
				url:"actualizacion_datos.php",
				data:{
					zona:'linea_credito',
					cuenta:cuenta,
					linea_credito : linea_credito
				},
				success:function(resp){
					console.log(resp);
				}
			});
		}
	}


	function check_verificacion(){
		var cuenta 		  	 = document.getElementById("cuenta").innerHTML;
		var x 				 = document.getElementsByClassName("verificado");
		var dato = "";

		for (var i = 0; i < x.length; i++) {
			dato += (x[i].checked == true) ? '1' : '0';
		}

		document.getElementById("btn_verificado").disabled = ('1111' == dato) ? false : true;
		document.getElementById("cuenta").setAttribute("data-verificacion",dato); 

		$.ajax({
			type:'POST',
			url:"actualizacion_datos.php",
			data:{
				cuenta:cuenta,
				zona:'check_veri',
				dato : dato
			},
			success:function(resp){
				console.log(resp);
			}
		});
	}

	function check_analisis(){
		var cuenta 		  	= document.getElementById("cuenta").innerHTML;
		var x 				= document.getElementsByClassName("analizado");
		var dato = "";

		for (var i = 0; i < x.length; i++) {
			dato += (x[i].checked == true) ? '1' : '0';
		}

		var btns_analisis = document.getElementsByClassName("btns_analisis");
		for(var i = 0; i < btns_analisis.length; i++) {
			btns_analisis[i].disabled =  ('111' == dato) ? false : true;
		}

		document.getElementById("cuenta").setAttribute("data-analisis",dato);

		$.ajax({
			type:'POST',
			url:"actualizacion_datos.php",
			data:{
				cuenta:cuenta,
				zona:'check_anal',
				dato : dato
			},
			success:function(resp){
				console.log(resp);
			}
		});
	}


	function firmar_scoring(usuario,cargo){

		var cuenta 		  = document.getElementById("cuenta").innerHTML;

		$.ajax({
			type:'POST',
			url:"actualizacion_datos.php",
			data:{
				cuenta:cuenta,
				usuario:usuario,
				cargo:cargo
			},

			success:function(resp){

				if(cargo == 'analista'){
					var datos_select  = document.getElementsByClassName("datos_select");
					var datos_input   = document.getElementsByClassName("datos_input");
					var datos_resumen = document.getElementsByClassName("datos_resumen");

					var data = new Object();

					for (var i = 0; datos_input.length>i; i++) {
						data[datos_input[i].id] = datos_input[i].value;
					}

					for (var i = 0; datos_select.length>i; i++) {

						if(datos_select[i].options[datos_select[i].selectedIndex]){
							var valor = datos_select[i].options[datos_select[i].selectedIndex].text;
							if (valor.length==0) {
								valor = "SIN DATOS";
							}
							data[datos_select[i].id] = valor;
						}
					}

					for (var i = 0; datos_resumen.length>i; i++) {
						data[datos_resumen[i].id] = datos_resumen[i].innerHTML;
					}  	

					var f = new Date();
					data['analista'] = usuario;
					data['ultimo_analisis']	= f.getFullYear()+'-'+(f.getMonth() +1)+'-'+f.getDate()+' '+f.getHours()+':'+f.getMinutes()+':'+f.getSeconds();
					
					asignar_linea();	

					var file_name = '16_'+cuenta+'_'+f.getFullYear()+''+(f.getMonth() +1)+''+f.getDate()+'.pdf';
					$.ajax({
						type:'POST',
						url:"actualizacion_datos.php",
						data:{
							cuenta:cuenta,
							usuario:'usuario',
							nombre:file_name,
							tipo:'agregar_scoring'
						},
						success:function(resp){
							console.log(resp);
							
							$.ajax({
								type:'POST',
								url:"scoring_pdf.php",
								data:{
									cuenta:cuenta,
									data:data,
									file_name : file_name
								},
								success:function(resp){
									//console.log(resp);
									window.location.replace("lista_scoring.php");
								}
							});
						}});					
				}else{

					// Verificador	
					console.log(resp)
					window.location.replace("lista_scoring.php");	
				}
			}
		});
	}

	function ampliacion_linea(usuario,cargo,funcion){

		var cuenta  = document.getElementById("cuenta").innerHTML;

		if(funcion=='A') asignar_linea();			

		$.ajax({
			type:'POST',
			url:"actualizacion_datos.php",
			data:{
				cuenta:cuenta,
				usuario:usuario,
				funcion:funcion,
				cargo:cargo
			},

			success:function(resp){
				console.log(resp);
				//firmar_scoring(usuario,cargo);	
			}
		});
	}

	function actualizar_datos(etiqueta,valor){

		var cuenta = document.getElementById("cuenta").innerHTML;
		document.getElementById(etiqueta).setAttribute("data-id", valor);

		$.ajax({
			type:'POST',
			url:"actualizacion_datos.php",
			data:{
				cuenta:cuenta,
				etiqueta: etiqueta,
				valor: valor
			},
			success:function(resp){
				$("#response").html(resp);
				console.log("etiqueta: " + etiqueta + ", valor: " + valor);
			}
		});
	}

	function between(x, min, max) {
		return x >= min && x <= max;
	}

	function format(x, thd, dec) {
		return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
	}

	function validacion_check(){

		var verificacion = document.getElementById("cuenta").getAttribute("data-verificacion");
		var analisis 	 = document.getElementById("cuenta").getAttribute("data-analisis");
		var veri 		 = document.getElementsByClassName("verificado");
		var anal 		 = document.getElementsByClassName("analizado");
		var v;
		var a;

		for (var i = 0; i < veri.length; i++) {
			v = verificacion.substring(i,i+1);
			veri[i].checked = ( v == '1') ? true : false;
		}

		for (var i = 0; i < anal.length; i++) {
			a = analisis.substring(i,i+1);
			anal[i].checked = ( a == '1') ? true : false;
		}

	}

	function consultar_puntos(){
 		
		validacion_check();
		var puntos_acumulados = 0;
		var total_puntos = document.getElementById('total_puntos');

		// Edad 
		var edad = parseInt(document.getElementById("edad").value);
		if (between(edad, 0,17))  puntos_acumulados += -50;
		if (between(edad, 18,20)) puntos_acumulados +=  -3;
		if (between(edad, 21,25)) puntos_acumulados +=  -2;
		if (between(edad, 26,30)) puntos_acumulados +=  -1;
		if (between(edad, 31,35)) puntos_acumulados +=   0;
		if (between(edad, 36,40)) puntos_acumulados +=   1;
		if (between(edad, 41,50)) puntos_acumulados +=   2;
		if (between(edad, 51,65)) puntos_acumulados +=   3;
		if (between(edad, 66,99)) puntos_acumulados +=  -1;

  		// Sexo 
  		var sexo = document.getElementById("sexo");
  		switch (sexo.value) {
  			case 'MASCULINO':
  			puntos_acumulados += 1;
  			break;
  			case 'FEMENINO':
  			puntos_acumulados += 3;
  			break;
  			default:
  			puntos_acumulados += -50;
  			break;
  		}	

 		// Estado Civil 
 		var estado_civil = document.getElementById("estado_civil");

 		if(estado_civil.value && estado_civil.value != estado_civil.getAttribute("data-id"))
 			actualizar_datos('estado_civil',estado_civil.value);

 		estado_civil.selectedIndex = estado_civil.getAttribute("data-id");
 		switch (estado_civil.value) {
 			case '1':
 			puntos_acumulados += 2;
 			break;
 			case '2':
 			puntos_acumulados += 1;
 			break;
 			case '3':
 			puntos_acumulados += -1;
 			break;
 			case '4':
 			puntos_acumulados += -1;
 			break;
 			case '5':
 			puntos_acumulados += -1;
 			break;						
 			default:
 			puntos_acumulados += -50;
 			break;
 		}		

		// Cantidad Hijos 
		var cant_hijos = document.getElementById("cant_hijos");
		if(cant_hijos.value != cant_hijos.getAttribute("data-id")) 
			actualizar_datos('cant_hijos',cant_hijos.value);
		if (cant_hijos.value == 0) puntos_acumulados += 4;
		if (cant_hijos.value == 1) puntos_acumulados += 3;
		if (cant_hijos.value == 2) puntos_acumulados += 2;
		if (cant_hijos.value >= 3) puntos_acumulados += 1;


		// Vivienda 
		var vivienda_index;
		var vivienda 	  = document.getElementById("vivienda");
		var vivienda_data = vivienda.getAttribute("data-id").trim();

		if(vivienda.value && vivienda.value != vivienda_data){
			actualizar_datos('vivienda',vivienda.value);
			vivienda.setAttribute("data-id",vivienda.value);
		}
			vivienda_data = vivienda.getAttribute("data-id").trim();

		if (vivienda_data == 'SIN DATOS') 	 	vivienda_index = 1;
		if (vivienda_data == 'PROPIA') 	 	vivienda_index = 2;
		if (vivienda_data == 'ALQUILADA') 	 	vivienda_index = 3;
		if (vivienda_data == 'DE LOS PADRES') 	vivienda_index = 4;
		if (vivienda_data == 'PRESTADA') 	 	vivienda_index = 5;

		vivienda.selectedIndex = vivienda_index;
			
		
		if (vivienda.value == 'SIN DATOS') puntos_acumulados 	+= 0;
		if (vivienda.value == 'PROPIA') puntos_acumulados 		+= 5;
		if (vivienda.value == 'ALQUILADA') puntos_acumulados 	+= -1;
		if (vivienda.value == 'DE LOS PADRES') puntos_acumulados += 3;
		if (vivienda.value == 'PRESTADA') puntos_acumulados 	+= 3;


		// Servicios Basicos 
		var servicios_basicos = document.getElementById("servicios_basicos");

		if(servicios_basicos.value && servicios_basicos.value != servicios_basicos.getAttribute("data-id"))
			actualizar_datos('servicios_basicos',servicios_basicos.value);

		servicios_basicos.selectedIndex = servicios_basicos.getAttribute("data-id");
		if (servicios_basicos.value == 2) puntos_acumulados += 3;
		if (servicios_basicos.value == 3) puntos_acumulados += 2;
		if (servicios_basicos.value == 4) puntos_acumulados += 1;


		// Conyugue 
		var conyuge = document.getElementById("conyuge");

		if(conyuge.value && conyuge.value != conyuge.getAttribute("data-id"))
			actualizar_datos('conyuge',conyuge.value);

		conyuge.selectedIndex = conyuge.getAttribute("data-id");
		if (conyuge.value == 2) puntos_acumulados += 1;
		if (conyuge.value == 3) puntos_acumulados += -1;


		// Telefono 
		var telefono = document.getElementById("telefono");
		var data_id = telefono.getAttribute("data-id");
		var vals = [];
		var telefono_valor = "";
		var control = 0;
		telefono.options[0].selected = false;

		for (var i=1, n=5;i<=n;i++) {
			control = (telefono.options[i].value == i && telefono.options[i].selected == true) ? 1:0;
			telefono_valor += (control == 1) ? "1" : "0";
		}

		if(telefono_valor!="00000" && telefono_valor != data_id){
			actualizar_datos('telefono',telefono_valor);
			document.getElementById('datos_telefono').value = telefono_valor;
		}	
		data_id = telefono.getAttribute("data-id");

		for (var i=1, n=5;i<=n;i++) {

			telefono.options[i].selected = (data_id.substr(i-1, 1) == 1) ? true : false;

			if(telefono.options[i].value == 1 && telefono.options[i].selected == true) puntos_acumulados += 3;
			if(telefono.options[i].value == 2 && telefono.options[i].selected == true) puntos_acumulados += 1;
			if(telefono.options[i].value == 3 && telefono.options[i].selected == true) puntos_acumulados += 2;	
			if(telefono.options[i].value == 4 && telefono.options[i].selected == true) puntos_acumulados += 2;	
			
		}

		// Situacion Laboral 
		var situacion_laboral = document.getElementById("situacion_laboral");

		if(situacion_laboral.value && situacion_laboral.value != situacion_laboral.getAttribute("data-id"))
			actualizar_datos('situacion_laboral',situacion_laboral.value);

		situacion_laboral.selectedIndex = situacion_laboral.getAttribute("data-id");
		if (situacion_laboral.value == 1) puntos_acumulados += 0;
		if (situacion_laboral.value == 2) puntos_acumulados += 5;
		if (situacion_laboral.value == 3) puntos_acumulados += 2;
		if (situacion_laboral.value == 4) puntos_acumulados += 2;
		if (situacion_laboral.value == 5) puntos_acumulados += 1;
		if (situacion_laboral.value == 6) puntos_acumulados += 0;
		if (situacion_laboral.value == 7) puntos_acumulados += -1;



		// Antiguedad Laboral 
		var antiguedad_lab = document.getElementById("antiguedad_lab");

		if(antiguedad_lab.value && antiguedad_lab.value != antiguedad_lab.getAttribute("data-id"))
			actualizar_datos('antiguedad_lab',antiguedad_lab.value);

		antiguedad_lab.selectedIndex = antiguedad_lab.getAttribute("data-id");
		if (antiguedad_lab.value == 1) puntos_acumulados += 0;
		if (antiguedad_lab.value == 2) puntos_acumulados += -5;
		if (antiguedad_lab.value == 3) puntos_acumulados += 0;
		if (antiguedad_lab.value == 4) puntos_acumulados += 1;
		if (antiguedad_lab.value == 5) puntos_acumulados += 2;
		if (antiguedad_lab.value == 6) puntos_acumulados += 3;
		if (antiguedad_lab.value == 7) puntos_acumulados += 4;
		if (antiguedad_lab.value == 8) puntos_acumulados += 5;


		// Mercado Laboral 
		var mercado_laboral = document.getElementById("mercado_laboral");

		if(mercado_laboral.value && mercado_laboral.value != mercado_laboral.getAttribute("data-id"))
			actualizar_datos('mercado_laboral',mercado_laboral.value);

		mercado_laboral.selectedIndex = mercado_laboral.getAttribute("data-id");
		if (mercado_laboral.value == 1) puntos_acumulados += 0;
		if (mercado_laboral.value == 2) puntos_acumulados += 5;
		if (mercado_laboral.value == 3) puntos_acumulados += 5;
		if (mercado_laboral.value == 4) puntos_acumulados += 5;
		if (mercado_laboral.value == 5) puntos_acumulados += 5;
		if (mercado_laboral.value == 6) puntos_acumulados += 3;
		if (mercado_laboral.value == 7) puntos_acumulados += 0;


		// In Situ
		var insitu = document.getElementById("insitu");

		if(insitu.value && insitu.value != insitu.getAttribute("data-id"))
			actualizar_datos('insitu',insitu.value);

		insitu.selectedIndex = insitu.getAttribute("data-id");
		if (insitu.value == 1) puntos_acumulados += 5;
		if (insitu.value == 2) puntos_acumulados += -50;


		// Faja
		var faja = document.getElementById("faja");

		if(faja.value == '0'){
			puntos_acumulados += 5;
		}else{
			var faja_informconf = faja.value.toLowerCase().charCodeAt(0)-96;	
			if (between(faja_informconf, 1,7)) 	 puntos_acumulados += 5; // A - G
			if (between(faja_informconf, 8,10))  puntos_acumulados += 4; // H - J
			if (between(faja_informconf, 11,12)) puntos_acumulados += 3; // K - L
			if (between(faja_informconf, 13,14)) puntos_acumulados += 1; // M - N
			if (between(faja_informconf, 15,25)) puntos_acumulados += -50; // O - Z
		}

		// Cuenta Bancarias
		var cuenta_bancaria 	= document.getElementById("cuenta_bancaria");
		var mas_cuenta = document.getElementById("mas_cuenta");

		if(cuenta_bancaria.value && cuenta_bancaria.value != cuenta_bancaria.getAttribute("data-id"))
			actualizar_datos('cuenta_bancaria',cuenta_bancaria.value);

		cuenta_bancaria.selectedIndex = cuenta_bancaria.getAttribute("data-id");
		if (cuenta_bancaria.value == 2) puntos_acumulados += 1;
		if (cuenta_bancaria.value == 3) puntos_acumulados += 2;
		if (cuenta_bancaria.value == 4) puntos_acumulados += 3;

		mas_cuenta.disabled = true;

		if(cuenta_bancaria.value && (cuenta_bancaria.value == 3 || cuenta_bancaria.value == 4)){

			mas_cuenta.disabled = false;
			if(mas_cuenta.getAttribute("data-id") == 2 && mas_cuenta.checked == false){

				if (mas_cuenta.value == 1){
					mas_cuenta.checked = true;
					mas_cuenta.setAttribute("data-id", 1);

				}else{
					mas_cuenta.setAttribute("data-id", 0);
				}
			}

			if (mas_cuenta.checked == false) mas_cuenta.value = 0;
			if (mas_cuenta.checked == true){
				mas_cuenta.value = 1;
				puntos_acumulados +=2;
			}  
			if (mas_cuenta.value   != mas_cuenta.getAttribute("data-id")) actualizar_datos('mas_cuenta',mas_cuenta.value);	
		}


		//Productos
		var producto = document.getElementById("producto");
		if(producto.value && producto.value != producto.getAttribute("data-id"))
			actualizar_datos('producto',producto.value);

		producto.selectedIndex = producto.getAttribute("data-id");
		if (producto.value == 2) puntos_acumulados += 2;
		if (producto.value == 3) puntos_acumulados += 1;

		//Mercado
		var mercado = document.getElementById("mercado");
		if(mercado.value && mercado.value != mercado.getAttribute("data-id"))
			actualizar_datos('mercado',mercado.value);

		mercado.selectedIndex = mercado.getAttribute("data-id");
		if (mercado.value == 2) puntos_acumulados += 2;
		if (mercado.value == 3) puntos_acumulados += 1;


		//Operacion
		var cantidad_cuota 	= document.getElementById("cantidad_cuota");

		if (between(cantidad_cuota.value, 1, 5)) puntos_acumulados +=  1;
		if (between(cantidad_cuota.value, 6, 8)) puntos_acumulados +=  3;
		if (between(cantidad_cuota.value, 9,12)) puntos_acumulados +=  4;
		if (between(cantidad_cuota.value,13,16)) puntos_acumulados +=  0;
		if (between(cantidad_cuota.value,17,25)) puntos_acumulados += -2;

		var entrega 		= document.getElementById("entrega");
		if(entrega.value >= 1){
			entrega.checked = true;
			puntos_acumulados += 1;
		}

		var monto_cuota 	= document.getElementById("monto_cuota");

		if (between(monto_cuota.value, 		0,199999)) 		 puntos_acumulados +=  4;
		if (between(monto_cuota.value, 200000,399999)) 		 puntos_acumulados +=  3;
		if (between(monto_cuota.value, 400000,549999)) 		 puntos_acumulados +=  2;
		if (between(monto_cuota.value, 550000,749999)) 		 puntos_acumulados +=  1;
		if (between(monto_cuota.value, 750000,799999)) 		 puntos_acumulados += -1;
		if (between(monto_cuota.value, 800000,999999)) 		 puntos_acumulados += -2;
		if (between(monto_cuota.value,1000000,1199999)) 	 puntos_acumulados += -3;
		if (between(monto_cuota.value,1200000,999999999999)) puntos_acumulados += -4;


		//Riesgo Solicitado
		var riesgo_solicitado = 0; 
		riesgo_solicitado = parseInt(cantidad_cuota.value*monto_cuota.value);
		document.getElementById("riesgo_solicitado_valor").innerHTML = format(riesgo_solicitado.toString());


		//Ingreso
		var salario_minimo =  2192839;

		var ingreso = document.getElementById("ingreso");
		if(ingreso.value && ingreso.value != ingreso.getAttribute("data-id"))
			actualizar_datos('ingreso',ingreso.value);

		if(ingreso.value){
			document.getElementById("ingreso_valor").innerHTML = format(ingreso.value);

			if (between(ingreso.value, salario_minimo, 2499999)) puntos_acumulados +=  0;
			if (between(ingreso.value, 2500000,2999999)) 	  	 puntos_acumulados +=  1;
			if (between(ingreso.value, 3000000,4999999)) 	  	 puntos_acumulados +=  2;
			if (between(ingreso.value, 5000000,999999999999))  	 puntos_acumulados +=  3;

		}else {
			puntos_acumulados += -50;
		}  


		// Mora interna
		var mora_interna = document.getElementById("mora_interna");
		if (mora_interna.value == "XXXX") puntos_acumulados +=  4;
		if (mora_interna.value == "XXX")  puntos_acumulados +=  3;
		if (mora_interna.value == "XX")   puntos_acumulados +=  2;
		if (mora_interna.value == "X")    puntos_acumulados += -4;
		if (mora_interna.value == "-X")   puntos_acumulados += -5;
		if (mora_interna.value == "-XX")  puntos_acumulados += -50;


		// Mora externa
		var mora_externa = document.getElementById("mora_externa");
		if(mora_externa.value && mora_externa.value != mora_externa.getAttribute("data-id"))
			actualizar_datos('mora_externa',mora_externa.value);

		mora_externa.selectedIndex = mora_externa.getAttribute("data-id");
		if (mora_externa.value == 2) puntos_acumulados +=   5;
		if (mora_externa.value == 3) puntos_acumulados +=   4;
		if (mora_externa.value == 4) puntos_acumulados +=   3;
		if (mora_externa.value == 5) puntos_acumulados +=  -3;
		if (mora_externa.value == 6) puntos_acumulados +=  -4;
		if (mora_externa.value == 7) puntos_acumulados += -50;


		// Deuda mensual
		var deuda_mensual = document.getElementById("deuda_mensual");
		if(deuda_mensual && deuda_mensual.value>0){
			document.getElementById("deuda_mensual_valor").innerHTML = format(deuda_mensual.value);
			
			if(deuda_mensual.value != deuda_mensual.getAttribute("data-id")){
				actualizar_datos('deuda_mensual',deuda_mensual.value);
			}
		}


		// Total deuda Externa
		var total_deuda_ex = document.getElementById("total_deuda_ex");
		if(total_deuda_ex && total_deuda_ex.value>0){
			document.getElementById("total_deuda_ex_valor").innerHTML =  format(total_deuda_ex.value);
			if(total_deuda_ex.value != total_deuda_ex.getAttribute("data-id")){
				actualizar_datos('total_deuda_ex',total_deuda_ex.value);	
			}
		}


		// Calculadora 
		var coheficiente = (deuda_mensual.value/ingreso.value).toFixed(2);

		// Cliente
		var cliente 	= document.getElementById("cliente");
		var nuevo_mundo = document.getElementById("nuevo_mundo");

		if (cliente.value == "CLIENTE"){
			puntos_acumulados 		+= 2;
			nuevo_mundo.disabled 	= true;
			nuevo_mundo.checked 	= false;

			if (nuevo_mundo == 1) actualizar_datos('nuevo_mundo',0);
			nuevo_mundo.value 		= 0;

			if (between(ingreso.value, 0, 4999999)){

				if (between(coheficiente,    0, 0.10)) 	puntos_acumulados += 5;
				if (between(coheficiente, 0.11, 0.20)) 	puntos_acumulados += 4;
				if (between(coheficiente, 0.21, 0.30)) 	puntos_acumulados += 3;
				if (between(coheficiente, 0.31, 0.40)) 	puntos_acumulados += 2;
				if (between(coheficiente, 0.41, 9999)) 	puntos_acumulados += -50;

			} else{

				if (between(coheficiente,    0, 0.10)) 	puntos_acumulados += 5;
				if (between(coheficiente, 0.11, 0.20)) 	puntos_acumulados += 4;
				if (between(coheficiente, 0.21, 0.30)) 	puntos_acumulados += 3;
				if (between(coheficiente, 0.31, 0.45)) 	puntos_acumulados += 2;
				if (between(coheficiente, 0.46, 9999)) 	puntos_acumulados += -50;
			} 

		}else{

			if(nuevo_mundo.getAttribute("data-id") == 2 && nuevo_mundo.checked == false){
				if (nuevo_mundo.value == 1){
					nuevo_mundo.checked = true;
					nuevo_mundo.setAttribute("data-id", 1);
				}else{
					nuevo_mundo.setAttribute("data-id", 0);
				}
			}

			if (nuevo_mundo.checked == false) nuevo_mundo.value = 0;
			if (nuevo_mundo.checked == true)  nuevo_mundo.value = 1;
			if (nuevo_mundo.value != nuevo_mundo.getAttribute("data-id")) actualizar_datos('nuevo_mundo',nuevo_mundo.value);			

			if (between(ingreso.value, 0, 4999999)){

				if (between(coheficiente,    0, 0.10)) 	puntos_acumulados += 5;
				if (between(coheficiente, 0.11, 0.20)) 	puntos_acumulados += 4;
				if (between(coheficiente, 0.21, 0.30)) 	puntos_acumulados += 3;
				if (between(coheficiente, 0.31, 0.35)) 	puntos_acumulados += 2;
				if (between(coheficiente, 0.36, 9999)) 	puntos_acumulados += -50;

			} else{

				if (between(coheficiente,    0, 0.10)) 	puntos_acumulados += 5;
				if (between(coheficiente, 0.11, 0.20)) 	puntos_acumulados += 4;
				if (between(coheficiente, 0.21, 0.30)) 	puntos_acumulados += 3;
				if (between(coheficiente, 0.31, 0.40)) 	puntos_acumulados += 2;
				if (between(coheficiente, 0.41, 9999)) 	puntos_acumulados += -50;
			} 
		}

		// Referencia Comercial
		var ref_comercial = document.getElementById("ref_comercial");
		if(ref_comercial.value && ref_comercial.value != ref_comercial.getAttribute("data-id"))
			actualizar_datos('ref_comercial',ref_comercial.value);
		ref_comercial.selectedIndex = ref_comercial.getAttribute("data-id");


		/*Capacidad y Limite prestable*/
		var limite_prestable;
		var capacidad;
		var endeudamiento;
		document.getElementById("limite_prestable_valor").innerHTML = '0';
		document.getElementById("capacidad_valor").innerHTML = '0';

		var coheficiente_cuota;
		if(puntos_acumulados<=40 && puntos_acumulados>31 ){
			//alert("atencion");
			if(nuevo_mundo.value == 1){
				endeudamiento 		= 0.12;
				coheficiente_cuota 	= 12;
				//alert("a");
			}

			if(ref_comercial.value == 1){
				endeudamiento 		= 0.15;
				coheficiente_cuota 	= 14;
				//alert("b");
			}

			if(mora_interna.value == 'XXXX' || mora_interna.value == 'XXX'){
				endeudamiento 		= 0.35;
				coheficiente_cuota 	= 12;
				//alert("c");	
			} 	 
		}else{
			endeudamiento = 0;
			coheficiente_cuota 	= 0;
		}

		if(puntos_acumulados>=41){
			if(nuevo_mundo.value == 1){
				endeudamiento 		= 0.30;
				coheficiente_cuota 	= 12;	 
			}

			if(ref_comercial.value == 1){
				endeudamiento 		= 0.35;	 
				coheficiente_cuota 	= 14;
			}

			if(mora_interna.value == 'XXXX' || mora_interna.value == 'XXX'){
				endeudamiento 		= 0.40;
				coheficiente_cuota 	= 12;	 
			}	
		}

		capacidad = (ingreso.value*endeudamiento)-deuda_mensual.value; 
		limite_prestable = capacidad*coheficiente_cuota;

		document.getElementById("capacidad_valor").innerHTML =  format(Math.round(capacidad).toString());

		if(limite_prestable>0){
			document.getElementById("limite_prestable_valor").innerHTML =  format(Math.round((limite_prestable)).toString());
		}else{
			puntos_acumulados += -50;
			document.getElementById("limite_prestable_valor").innerHTML =  format(Math.round((0)).toString());
		}

		if(puntos_acumulados<32){
			total_puntos.classList.remove("badge-success");
			total_puntos.classList.add("badge-danger");
		}else{
			total_puntos.classList.remove("badge-danger");
			total_puntos.classList.add("badge-success");
		}
		total_puntos.innerHTML = puntos_acumulados;
	}


