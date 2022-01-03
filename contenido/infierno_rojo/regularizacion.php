<?php 
require('../../header.php');
require( CONTROLADOR . 'ir_regularizacion.php');
$data = new REGULARIZACION();
?>



<div class="container">
	<br>
	<br>
	<div class="form-group row">
		<label for="cuenta" class="col-sm-2 col-form-label col-form-label-sm">Cuenta</label>
		<div class="col-sm-4">
			<input type="number" class="form-control form-control-sm" id="cuenta" min="0" step="1">
		</div>
	</div>

	<div class="form-group row">
		<label for="tipo_acuerdo"  class="col-sm-2 col-form-label col-form-label-sm">Tipo de Acuerdo</label>
		<div class="col-sm-4">
			<select class="form-control form-control-sm" id="tipo_acuerdo">
				<option value="0"></option>
				<option value="1">Acuerdos</option>
				<option value="2">Judiciales</option>
			</select>
		</div>
	</div>	

	<div class="form-group row">
		<label for="monto_cuota" class="col-sm-2 col-form-label col-form-label-sm">Monto Cuota</label>
		<div class="col-sm-4">
			<input type="number" class="form-control form-control-sm" id="monto_cuota" min="0" step="1">
		</div>
	</div>

	<div class="form-group row">
		<label for="plazo" class="col-sm-2 col-form-label col-form-label-sm">Plazo Acordado</label>
		<div class="col-sm-4">
			<input type="number" class="form-control form-control-sm" id="plazo" min="0" step="1">
		</div>
	</div>

	<div class="form-group row">
		<label for="canceladas" class="col-sm-2 col-form-label col-form-label-sm">Cuotas Canceladas</label>
		<div class="col-sm-4">
			<input type="number" class="form-control form-control-sm" id="canceladas" min="0" step="1">
		</div>
	</div>

	<div class="form-group row">
		<label for="fecha_acuerdo" class="col-sm-2 col-form-label col-form-label-sm">Fecha Acuerdo</label>
		<div class="col-sm-4">
			<input type="date" class="form-control form-control-sm" id="fecha_acuerdo" min="0" step="1">
		</div>
	</div>

	<div class="form-group row">
		<label for="fecha_1ervto" class="col-sm-2 col-form-label col-form-label-sm">Fecha 1er Vencimiento</label>
		<div class="col-sm-4">
			<input type="date" class="form-control form-control-sm" id="fecha_1ervto" min="0" step="1">
		</div>
	</div>

	<div class="form-group row">
		<div class="col-sm-2">
			<button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#exampleModal" onclick="reg_consultar()">Aceptar</button>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Resumen</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="resumen">

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-primary" id="btn_procesar" onclick="reg_procesar()">Procesar</button>
			</div>
		</div>
	</div>
</div>

<?php
require('../../footer.php');
?>

<script>
	function reg_consultar(){
		
		var cuenta 			= parseInt(document.getElementById('cuenta').value);
		var tipo_acuerdo	= parseInt(document.getElementById('tipo_acuerdo').value);
		var monto_cuota 	= parseInt(document.getElementById('monto_cuota').value);
		var plazo 			= parseInt(document.getElementById('plazo').value);
		var canceladas 		= parseInt(document.getElementById('canceladas').value);
		var fecha_acuerdo 	= document.getElementById('fecha_acuerdo').value;
		var fecha_1ervto 	= document.getElementById('fecha_1ervto').value;

		var verificacion    = 0;
		var d1 				= new Date(fecha_acuerdo);
		var d2 				= new Date(fecha_1ervto);
		var cant_dias 		= (d2.getTime()-d1.getTime())/86400000;
		var mensaje 		= '';

		document.getElementById('btn_procesar').style.display = "none";

		if(!cuenta && verificacion==0){
			alert("Ingrese un número de CUENTA.");
			verificacion = 1;
		}

		if((!tipo_acuerdo || tipo_acuerdo == '0') && verificacion==0){
			alert("Seleccione un TIPO DE ACUERDO.");
			verificacion = 1;
		}


		if(!monto_cuota && verificacion==0){
			alert("El MONTO DE LA CUOTA está vacía!!!.");
			verificacion = 1;
		}

		if(!plazo && verificacion==0){
			alert("El PLAZO DE LA OPERACION está vacío.");
			verificacion = 1;
		}

		if(!canceladas && verificacion==0){
			canceladas = 0;
			//alert("La cantidad de CUOTAS CANCELADAS está vacía.");
			//verificacion = 1;
		}

		if(!fecha_acuerdo && verificacion==0){
			alert("La FECHA DEL ACUERDO no es correcta.");
			verificacion = 1;
		}

		if(!fecha_1ervto && verificacion==0){
			alert("La fecha del 1er. VENCIMIENTO no es correcta.");
			verificacion = 1;
		}

		if((canceladas>=plazo) && verificacion==0){
			alert("El PLAZO DE LA OPERACION no puede ser menor o igual a la cantidad de CUOTAS CANCELADAS.");
			verificacion = 1;
		}

		if(d1.getTime()>d2.getTime() && verificacion==0){
			alert("La FECHA DEL ACUERDO no puede ser mayor a la fecha del 1er. VENCIMIENTO.");
			verificacion = 1;
		}

		
		if(cant_dias>65 && verificacion==0){
			alert("La fecha del 1er. VENCIMIENTO no puede ser superior a los 45 días de la FECHA DEL ACUERDO, usted cargo en "+ cant_dias + " días.");
			verificacion = 1;
		}


		if(verificacion!=1){

		//	document.getElementById('btn_procesar').style.display = "initial";
		document.getElementById('resumen').innerHTML = mensaje;

		$.ajax({
			type:'POST',
			url:"reg_consultar.php",
			data:{
				cuenta: cuenta, 
				tipo_acuerdo: tipo_acuerdo,
				monto_cuota: monto_cuota,
				plazo: plazo,
				canceladas: canceladas,
				fecha_acuerdo: fecha_acuerdo,
				fecha_1ervto: fecha_1ervto 
			},
			success:function(resp){
				$("#resumen").html(resp);
				setTimeout(function(){ 

					var cantidad_texto = document.getElementById('prueba').innerHTML;
					var numero = cantidad_texto.length;
					if(numero>0){
						document.getElementById('btn_procesar').style.display = "initial";	
					}else{
						alert('Verifique el número de cuenta del cliente.');	
					}
				}, 1000);	
			}
		});
	}else{

		mensaje = '<div class="alert alert-danger" role="alert">Algunos errores en los datos, verifique antes de poder continuar.</div';
		document.getElementById('btn_procesar').style.display = "none";
		document.getElementById('resumen').innerHTML = mensaje;

	}
}

function reg_procesar(){

	var r = confirm("Esta seguro que desea continuar la acción!!!");

	if (r == true) {

		var cuenta 			= parseInt(document.getElementById('cuenta').value);
		var tipo_acuerdo	= parseInt(document.getElementById('tipo_acuerdo').value);
		var monto_cuota 	= parseInt(document.getElementById('monto_cuota').value);
		var plazo 			= parseInt(document.getElementById('plazo').value);
		var canceladas 		= parseInt(document.getElementById('canceladas').value);
		var fecha_acuerdo 	= document.getElementById('fecha_acuerdo').value;
		var fecha_1ervto 	= document.getElementById('fecha_1ervto').value;

		$.ajax({
			type:'POST',
			url:"reg_procesar.php",
			data:{
				cuenta: cuenta, 
				tipo_acuerdo: tipo_acuerdo,
				monto_cuota: monto_cuota,
				plazo: plazo,
				canceladas: canceladas,
				fecha_acuerdo: fecha_acuerdo,
				fecha_1ervto: fecha_1ervto 
			},
			success:function(resp){
					//$("#resumen").html(resp);
					document.getElementById('btn_procesar').style.display = "none";
				}
			});

	} else {
		document.getElementById('btn_procesar').style.display = "none";
	}
}
</script>