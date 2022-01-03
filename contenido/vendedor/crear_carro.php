<?php 

require('../../header.php');
require( CONTROLADOR . 'vendedores.php');

$vendedor = new Vendedores();
$vendedor->vendedor = $_COOKIE['usuario'];

unset($_SESSION["detalle_carrito"]);
unset($_SESSION["tipo_venta"]);
unset($_SESSION["clase_venta"]);

$_SESSION['detalle_carrito']['filtro'] = 'T';

if(isset($_POST['cuenta'])){

	$_SESSION['cuenta']  = $_POST['cuenta']; 
	$_SESSION['cliente'] = $_POST['cliente'];	
}

$cuenta  = $_SESSION['cuenta'];  
$cliente = $_SESSION['cliente']; 

?>

<input type="text" id="cod_vendedor" value="<?= $_COOKIE['rol'];?>" hidden>
<input type="text" id="cuenta" value="<?= $cuenta;?>" hidden>
<div class="d-none" id="api_key"><?= API_KEY ?></div>

<br>

<div class="container">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
			<li class="breadcrumb-item"><a href="gestion.php">Gestión</a></li>
			<li class="breadcrumb-item active" aria-current="page">Cargar Carro</li>
		</ol>
	</nav>

	<div class="contenedor_carro" id="contenedor_carro">
		<h4><?= $cuenta.' '.$cliente;?></h4>

		<!-- Forma de pago -->	
		<p class="px-2 py-1 my-1 bg-secondary text-white rounded" role="button" aria-controls="forma_pago">Forma de Pago</p>
		<div class="card card-body">
			<div class="input-group" id="forma_pago">
				<select class="custom-select custom-select-sm" id="tipo_venta" name="tipo_venta" onchange="forma_pago()">
					<option value="0">Seleccione una opción</option>
					<option value="1">Contado</option>
					<option value="2">Crédito</option>
				</select>
			</div>
		</div>


		<!-- Tipo de pago -->	
		<p class="px-2 py-1 my-1 bg-secondary text-white rounded" aria-controls="clase_venta">Clase de Venta</p>
		<div class="card card-body d-none" id="clase_venta_id">
			<div class="input-group">
				<select class="custom-select custom-select-sm" id="clase_venta" name="clase_venta" onchange="clase_venta()">
					<option value="0">Seleccione una opción</option>
					<optgroup label="Contado" id="clase_contado" class="d-none">
						<option value="1">Normal</option>
						<option value="2">Promoción</option>
					</optgroup>

					<optgroup label="Crédito" id="clase_credito" class="d-none">
						<option value="3">Normal</option>
					</optgroup>
					<optgroup label="Promociones" id="clase_promos" class="d-none">
						<option value="5">Especial (406)</option>
						<option value="4">Semanal (407)</option>
					</optgroup>

					<optgroup label="Debitos" id="clase_debito" class="d-none">
						<option value="6">Funcionario</option>
						<option value="7">Debito Automático</option>
						<option value="8">Asociaciones</option>
					</optgroup>

					<optgroup label="Crédito Especial" id="clase_especial" class="d-none">
						<option value="9">15 días</option>
						<option value="10">30 días</option>
						<option value="11">45 días</option>
						<option value="12">60 días</option>
					</optgroup>
				</select>
			</div>
		</div>

		<!-- Debitos Automaticos -->	
		<div id="debito_box" class="d-none">
			<p class="px-2 py-1 my-1 bg-secondary text-white rounded">Débitos</p>
			<div class="card card-body d-none" id="debito_id">
				<div class="input-group" id="debito"></div>
			</div>
		</div>


		<!-- Detalle del carro -->	
		<p class="px-2 py-1 my-1 bg-secondary text-white rounded">Detalle del Carro</p>
		<div class="card card-body d-none" id="detalle_carro">
			<button type="button" id="agregar_productos" class="btn btn-sm btn-block btn-outline-warning mb-2" onclick="lista_prod();">
				Seleccionar Producto
			</button>
			<div id="detalle_carro_body">
			</div>
		</div>


		<div class="d-none" id="pie">
			<div class="row d-none" id="tipo_credito_pie">

				<!-- Cantidad de cuotas -->	
				<div class="col-md-4">
					<p class="px-2 py-1 my-1 bg-secondary text-white rounded" id="id_cant_cuota">Cantidad de cuotas</p>
					<div class="card card-body">
						<div class="input-group input-group-sm">
							<input  type="number" class="form-control" id="cant_cuota" value="0" min="1" max="16" step="1" onchange="calcular_cuota()">
						</div>
					</div>					
				</div>	

				<!-- Primer Pago -->	
				<div class="col-md-4">
					<p class="px-2 py-1 my-1 bg-secondary text-white rounded" id="id_primer_pago">Primer Pago</p>
					<div class="card card-body">			
						<select class="custom-select custom-select-sm" id="primer_pago" name="primer_pago" onchange="primer_pago()">
							<option value="0">Seleccione una opción</option>
							<option value="1">Con la entrega</option>
							<option value="2">A los 30 días</option>
						</select>		
					</div>
				</div>

				<!-- Monto de Entrega -->
				<div class="col-md-4">
					<p class="px-2 py-1 my-1 bg-secondary text-white rounded" id="id_entrega">Monto de Entregas</p>
					<div class="card card-body">
						<div class="input-group input-group-sm">
							<input type="number" class="form-control" id="entrega" name="entrega" value="0" onchange="calcular_cuota()">
						</div>
					</div>	
				</div>

			</div>

			<div class="card card-body m-2">
				<h2 class="text-center" id="monto_cuotero">					
				</h2>
			</div>

			<!-- Medio -->	
			<p class="px-2 py-1 my-1 bg-secondary text-white rounded" id="id_medio">Medio</p>
			<div class="card card-body">			
				<select class="custom-select custom-select-sm" id="medio" name="medio" onchange="">
					<option value="0">Seleccione una opción</option>
					<?php
					foreach ($vendedor->medios() as $medio) {
						?>		
						<option value="<?= $medio['cod_medio']; ?>"><?= $medio['medio']; ?></option>
						<?php					
					}
					?>
				</select>		
			</div>

			<!-- Lugar de entrega -->	
			<p class="px-2 py-1 my-1 bg-secondary text-white rounded">Lugar de Entrega</p>
			<div class="card card-body">
				<textarea class="form-control" id="lugar_entrega" name="lugar_entrega" rows="3" onchange=""> </textarea>
			</div>	

			<button type="button" class="btn btn-sm btn-primary" onclick="cargar_carro();">ENVIAR</button>
			<br>
			<br>
		</div>	




	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="ModalDetalleCarrito" tabindex="-1" aria-labelledby="ModalDetalleCarritoLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ModalDetalleCarritoLabel">Lista de Productos</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="resultado">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<div id="contenedor_resultado"></div>





<?php 
require('../../footer.php');
?>

<script>

	function verificar_carro(valor){

		if(valor=='S'){
			document.getElementById('pie').classList.remove('d-none');

		}else{
			document.getElementById('pie').classList.add('d-none');
		}
	}

	function calcular_cuota(){

		var tipo_venta 		= document.getElementById('tipo_venta').value;
		var clase_venta 	= document.getElementById('clase_venta').value;
		var tipo_oper;
		var cantidad_cuotas = document.getElementById('cant_cuota').value;
		var primer_pago 	= document.getElementById('primer_pago').value;
		var entrega 		= document.getElementById('entrega').value;
		var monto_total		= document.getElementById('monto_total').value;
		var tipo_filtro_prod= document.getElementById('tipo_filtro_prod').value;

		switch(clase_venta){
			case '3':
			if(tipo_filtro_prod=='N'){
				tipo_oper = 400;
				if(primer_pago==2){
					tipo_oper = 401;
				}	
			}

			if(tipo_filtro_prod=='M'){
				tipo_oper = 300;
				if(primer_pago==2){
					tipo_oper = 301;
				}						
			}

			if(tipo_filtro_prod=='P'){
				tipo_oper = 406;
			}
			break;

			case '4':
			tipo_oper = 407;
			break;

			case '5':
			tipo_oper = 406;
			break;

			case '6':
			if(tipo_filtro_prod=='N'){
				tipo_oper = 402;
			}

			if(tipo_filtro_prod=='M'){
				tipo_oper = 302;
			}
			break;

			case '7':
			if(tipo_filtro_prod=='N'){
				tipo_oper = 415;
			}

			if(tipo_filtro_prod=='M'){
				tipo_oper = 315;
			}
			break;

			case '8':
			if(tipo_filtro_prod=='N'){
				tipo_oper = 410;
			}

			if(tipo_filtro_prod=='M'){
				tipo_oper = 310;
			}
			break;
		}

		$.ajax({
			type:'POST',
			url:"calcular_cuotero.php",
			data:{
				monto_total 	 :monto_total,
				monto_contado 	 :entrega,
				monto_credito 	 :monto_total - entrega, 
				cantidad_cuotas  :cantidad_cuotas,
				primer_pago	 	 :primer_pago,
				tipo_oper 		 :tipo_oper
			},
			success:function(resp){
				$("#monto_cuotero").hide().html(resp).fadeIn('slow');
			}
		});
	}


	function forma_pago(){

		var tipo_venta = document.getElementById('tipo_venta');
		tipo_venta.disabled = true;

		if(tipo_venta.value == 1){
			document.getElementById("clase_contado").classList.remove('d-none');

		}else{
			document.getElementById("clase_credito").classList.remove('d-none');
			document.getElementById("clase_promos").classList.remove('d-none');
			document.getElementById("clase_debito").classList.remove('d-none');
			document.getElementById("tipo_credito_pie").classList.remove('d-none');
		}

		document.getElementById("clase_venta_id").classList.remove('d-none');
				//document.getElementById('tipo_venta_change').classList.remove('d-none');
				//document.getElementById('tipo_venta_help').classList.remove('d-none');

				$('#clase_venta_id').collapse('show');

			}

			function clase_venta(){

				var cuenta = document.getElementById('cuenta').value;
				var tipo_venta = document.getElementById('tipo_venta');
				var clase_venta = document.getElementById('clase_venta');
				var debito;
				clase_venta.disabled = true;

				if(clase_venta.value>=4  && clase_venta.value<=8){

					document.getElementById('primer_pago').value = 2;
					document.getElementById('primer_pago').disabled = true;

					if(clase_venta.value!=5){
//						document.getElementById('entrega').disabled = true;	
}

if(clase_venta.value==4){

	var cantidad_cuotas = document.getElementById('cant_cuota');
	cantidad_cuotas.value = 8;
	cantidad_cuotas.min = 8;
	cantidad_cuotas.max = 16;
}	
}

if(clase_venta.value>=6  && clase_venta.value<=8){
	switch(clase_venta.value){
		case '6':
		debito = "F";
		break;
		case '7':
		debito = "D";
		break;
		case '8':
		debito = "A";
		break;
	}

	/** Debito Automaticos**/
	document.getElementById('debito_box').classList.remove('d-none');
	document.getElementById('debito_id').classList.remove('d-none');

	$('#debito_box').collapse('show');
	$.ajax({
		type:'POST',
		url:"lista_empresa_debito.php",
		data:{
			accion : 'buscar',
			debito : debito
		},
		success:function(resp){
			$("#debito").hide().html(resp).fadeIn('slow');
		}
	});
}else{
	document.getElementById('detalle_carro').classList.remove('d-none');
	$('#detalle_carro').collapse('show');
}

$.ajax({
	type:'POST',
	url:"carro_detalle.php",
	data:{
		accion : 'tipo_venta',
		cuenta : cuenta,
		tipo_venta : tipo_venta.value,
		clase_venta: clase_venta.value
	},
	success:function(resp){
		$("#detalle_carro_body").hide().html(resp).fadeIn('slow');
		$('#ModalDetalleCarrito').modal('hide');
	}
});
}

function primer_pago(){

	var primer_pago = document.getElementById('primer_pago').value;

	if (primer_pago==1) {
		document.getElementById('entrega').value 	= 0;
		document.getElementById('entrega').disabled = true;
	}else{

		document.getElementById('entrega').disabled = false;
	}
	calcular_cuota();
}

function lista_debito(){
	document.getElementById("debito_automatico").disabled = true;
	document.getElementById('detalle_carro').classList.remove('d-none');
	$('#detalle_carro').collapse('show');
}

function forma_pago_change(){

	alert('cambiar forma');
}

function lista_prod(){

	var clase_venta = document.getElementById('clase_venta');
	var aso 		= document.getElementById("debito_automatico");

	$.ajax({
		type:'POST',
		url:"lista_productos_carro.php",
		data:{
			clase_venta: clase_venta.value,
			contenido:"0", 
			pagina : 1
		},
		success:function(resp){
			$("#resultado").hide().html(resp).fadeIn('slow');
			$('#ModalDetalleCarrito').modal('show');
		}
	});
}

function cargar_carro(){
	var api_key 		= document.getElementById('api_key').innerHTML;
	var cuenta 			= document.getElementById('cuenta').value;
	var cod_vendedor 	= document.getElementById('cod_vendedor').value;
	var detalle_carrito = document.getElementById('detalle_carrito').innerHTML;
	var tipo_venta 		= document.getElementById('tipo_venta').value;
	var clase_venta 	= document.getElementById('clase_venta').value;
	var lugar_entrega 	= document.getElementById('lugar_entrega').value;
	var medio 			= document.getElementById('medio').value;
	var tipo_oper;

	lugar_entrega = "WA:" + lugar_entrega ;

	if(medio==0){
		medio = 1;
	}


	switch (tipo_venta) {
		case '1':
			//alert('contado');	
			$.ajax({
				type:'POST',
				url:"https://intranet.facilandia.com.py/api/v2/index.php/carrito",
				headers: {Authorization: api_key,
					'Content-Type': 'application/x-www-form-urlencoded'},
					data:{
						accion : 'agregar',
						cuenta : cuenta,
						cod_vendedor : cod_vendedor,
						detalle_carrito : detalle_carrito,
						tipo_venta : tipo_venta,
						medio : medio,
						lugar_entrega : lugar_entrega
					},
					success:function(resp){
						$("#contenedor_resultado").html("resp");
						window.location.replace("gestion.php");
					}
				});
			break;

			case '2':
			var cantidad_cuotas = document.getElementById('cant_cuota').value;
			var primer_pago 	= document.getElementById('primer_pago').value;
			var entrega 		= document.getElementById('entrega').value;
			var tipo_filtro_prod= document.getElementById('tipo_filtro_prod').value;
			var aso 			= 0;
			var agencia 		= 240;

			switch(clase_venta){

				case '3':
				if(tipo_filtro_prod=='N'){
					tipo_oper = 400;
					if(primer_pago==2){
						tipo_oper = 401;
					}	
				}

				if(tipo_filtro_prod=='M'){
					tipo_oper = 300;
					if(primer_pago==2){
						tipo_oper = 301;
					}						
				}

				if(tipo_filtro_prod=='P'){
					tipo_oper = 406;
				}
				break;

				case '4':
				tipo_oper = 407;
				break;

				case '5':
				tipo_oper = 406;
				break;

				case '6':
				if(tipo_filtro_prod=='N'){
					tipo_oper = 402;
				}

				if(tipo_filtro_prod=='M'){
					tipo_oper = 302;
				}
				break;

				case '7':
				if(tipo_filtro_prod=='N'){
					tipo_oper = 415;
				}

				if(tipo_filtro_prod=='M'){
					tipo_oper = 315;
				}
				break;

				case '8':
				if(tipo_filtro_prod=='N'){
					tipo_oper = 410;
				}

				if(tipo_filtro_prod=='M'){
					tipo_oper = 310;
				}
				break;
			}

			if(clase_venta>=6 && clase_venta<=8){
				agencia = 250;
				aso = document.getElementById("debito_automatico").value;
			}

			$.ajax({
				type:'POST',
				url:"https://intranet.facilandia.com.py/api/v2/index.php/carrito",
				headers: {Authorization: api_key,
					'Content-Type': 'application/x-www-form-urlencoded'},
					data:{
						accion : 'agregar',
						cuenta : cuenta,
						cod_vendedor : cod_vendedor,
						detalle_carrito : detalle_carrito,
						tipo_venta : tipo_venta,
						cantidad_cuotas : cantidad_cuotas,
						primer_pago : primer_pago,
						monto_entrega : entrega,
						medio : medio,
						lugar_entrega : lugar_entrega, 
						tipo_oper : tipo_oper, 
						agencia : agencia,
						aso : aso
					},
					success:function(resp){
						//console.log(resp);
						//$("#contenedor_resultado").html(resp);
						window.location.replace("gestion.php");
					},
					error:function(resp){
						console.log(resp);
					}
				});
			break;
		}
	}	
</script>
