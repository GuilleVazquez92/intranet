<?php

require_once('../../controlador/main.php');
require_once( CONTROLADOR . 'vendedores.php');
$vendedor = new Vendedores();

$cuenta = $_SESSION['cuenta'];
$data 	= (isset($_SESSION['detalle_carrito'][$cuenta])) ? unserialize($_SESSION['detalle_carrito'][$cuenta]) : array();


if(isset($_POST['tipo_venta']) && isset($_POST['cuenta'])){	

	$_SESSION['tipo_venta'][$cuenta] = $_POST['tipo_venta'];
	$_SESSION['clase_venta'] 		 = $_POST['clase_venta'];
}

$max_descuento = (isset($_SESSION['tipo_venta'][$cuenta]) && $_SESSION['tipo_venta'][$cuenta]==1) ? 25 : 10;

# Agregar itemns al carro
if(isset($_POST['codigo']) && isset($_POST['accion']) && $_POST['accion']=='agregar'){
	
	$_SESSION['detalle_carrito']['filtro'] = $_POST['tipo_filtro'];

	$new = array(
			'codigo' 	=> $_POST['codigo'],
			'precio' 	=> $_POST['precio']*$_POST['cantidad'],
			'cantidad' 	=> $_POST['cantidad'],
			'descuento' => 0,
			'max_des' 	=> $_POST['max_des']
		);
	
	$existe = 0;

	for ($i=0; $i <count($data); $i++) { 
		if($data[$i]['codigo'] == $_POST['codigo']){
			$existe = 1;
			break;
		}
	}


	if($existe==0 || !isset($_SESSION['detalle_carrito'][$cuenta])){
		
		array_push($data,$new);
	}
}

# Quitar items del carro 
if(isset($_POST['codigo']) && isset($_POST['accion']) && $_POST['accion']=='quitar'){
	for ($i=0; $i <count($data); $i++) { 
		if($data[$i]['codigo'] == $_POST['codigo']){
			unset($data[$i]);
			$data = array_values($data);
			break;
		}
	}
}

# Modificar items del carro 
if(isset($_POST['codigo']) && isset($_POST['accion']) && $_POST['accion']=='modificar'){

	for ($i=0; $i <count($data); $i++) { 
		if($data[$i]['codigo'] == $_POST['codigo']){
			$data[$i]['cantidad']  = $_POST['cantidad'];
			$data[$i]['descuento'] = $_POST['descuento'];
			break;
		}
	}
}

#########################################################################################




$_SESSION['detalle_carrito'][$cuenta] = serialize($data);

# Despliegue del carrito 
echo "<div id='cantidad_carrito' class='d-none'>".count($data)."</div>";

if(isset($data) && count($data)>0){
	?>
	<input type="text" value="<?= $_SESSION['detalle_carrito']['filtro']?>" id="tipo_filtro_prod" hidden>
	<?php
	$sub_total = 0;
	$total = 0;

	for ($i=0; $i < count($data); $i++){

		$descripcion = $vendedor->buscar_descripcion($data[$i]['codigo']);
		$sub_total 	 = round(($data[$i]['precio']-($data[$i]['precio']*$data[$i]['descuento']/100))+499,-3)*$data[$i]['cantidad'];
		$total += $sub_total;

		?>

		<div class="card card-body my-1">
			<div class="row">
				<div class="col-10">
					<small class="font-weight-bold"><?= $data[$i]['codigo'].' '.$descripcion[0]['epdescl'];?></small>
				</div>
				<div class="col-2 text-right">
					<img src="<?= IMAGE.'fail.png';?>" title="Quitar producto" onclick="producto_quitar(<?= $data[$i]['codigo'];?>)">
				</div>
			</div>
			<div class="row">		
				<div class="col-6 col-md-9 col-lg-10"><small>Precio</small></div>
				<div class="col-6 col-md-3 col-lg-2 text-right input-group input-group-sm">
					<input type="text" class="form-control text-right mb-2" value="<?= number_format($data[$i]['precio'],0,',','.');?>" readonly>
				</div>
			</div>
			<div class="row">		
				<div class="col-6 col-md-9 col-lg-10"><small>Cantidad</small></div>
				<div class="col-6 col-md-3 col-lg-2 input-group input-group-sm">
					<input type="number" class="form-control text-right mb-2" id="cantidad<?= "_".$data[$i]['codigo'];?>" value="<?= $data[$i]['cantidad'];?>" min="1" step="1" onchange="producto_modificar(<?= $data[$i]['codigo'];?>)">
				</div>
			</div>
			<div class="row">		
				<div class="col-6 col-md-9 col-lg-10"><small>Descuento</small></div>
				<div class="col-6 col-md-3 col-lg-2 input-group input-group-sm ">
					<div class="input-group-prepend mb-2">
						<span class="input-group-text">%</span>
					</div>
					<input type="number" class="form-control text-right mb-2" id="descuento<?= "_".$data[$i]['codigo'];?>" value="<?= $data[$i]['descuento'];?>" onchange="producto_modificar(<?= $data[$i]['codigo'];?>)" aria-label="Small" aria-describedby="inputGroup-sizing-sm" step="0.01" min="0" 
					max="<?= $data[$i]['max_des'];?>"<?= ($data[$i]['max_des']==0) ? "readonly":"";?>>
				</div>
			</div>
			<div class="row bg-light text-dark">		
				<div class="col-6 col-md-9 col-lg-10">
					<small class="font-weight-bold">Total</small>
				</div>
				<div class="col-6 col-md-3 col-lg-2 text-right">
					<small class="font-weight-bold"><?= number_format($sub_total,0,',','.');?></small>
				</div>
			</div>
		</div>
		<?php
	}
	?>
	<div class="card card-body my-1">
		<div class="row bg-warning text-dark">	
			<div class="col-6 col-md-9 col-lg-10">TOTAL</div><input type="text" name="" id="monto_total" value="<?= $total;?>" hidden>
			<div class="col-6 col-md-3 col-lg-2 text-right font-weight-bold"><?= number_format($total,0,',','.');?></div>
		</div>
	</div>
	<script>
		verificar_carro('S');	
	</script>	
	<?php
}else{
	$_SESSION['detalle_carrito']['filtro'] = "T";
	?>
	<script>
		verificar_carro('N');	
	</script>
	<div class="alert alert-warning text-center" role="alert">El carrito se encuentra vacío</div>

	<?php
}
?>
<div class="d-none" id="detalle_carrito"><?= $_SESSION['detalle_carrito'][$cuenta]; ?></div>


<script>

	var carro =	document.getElementById('cantidad_carrito').innerHTML;

	if(carro>5){
		//$('#agregar_productos').dialog('Hasta 5 productos');
		alert("El carrito alcanzo su limite de 5 productos");
		$('#agregar_productos').prop('disabled', true);

	}else{

		if(carro==5){
			$('#agregar_productos').prop('disabled', true);	
		}else{
			$('#agregar_productos').prop('disabled', false);			
		}
	}



	function producto_agregar(datos){
		var res = datos.split("_");
		var cant_cuota = document.getElementById('cant_cuota');
		var clase_venta = document.getElementById('clase_venta').value;

		$.ajax({
			type:'POST',
			url:"carro_detalle.php",
			data:{
				accion 		: 'agregar',
				codigo 		: res[0],
				precio 		: res[1],
				max_des 	: res[2],
				tipo_filtro : res[3],
				cantidad 	: 1,
				descuento 	: 0
			},
			success:function(resp){

				$("#detalle_carro_body").hide().html(resp).fadeIn();
				$('#ModalDetalleCarrito').modal('hide');

				cant_cuota.max=16;

				if(res[3]=='M'){
					cant_cuota.max=25;					
				}

				if(clase_venta==3 && res[3]=='P'){

					cant_cuota.value=12;
					cant_cuota.min=5;
					document.getElementById('primer_pago').value = 2;
					document.getElementById('primer_pago').disabled = true;
				}

				if(cant_cuota.max<cant_cuota.value){
					//document.getElementById('cant_cuota').value = cant_cuota.max;
				}
			}
		});
	}
	
	function producto_quitar(datos){
		$.ajax({
			type:'POST',
			url:"carro_detalle.php",
			data:{
				accion : 'quitar',
				codigo : datos	
			},
			success:function(resp){
				$('#detalle_carro_body').hide().html(resp).fadeIn();
				$('#ModalDetalleCarrito').modal('hide');
			}
		});
	}

	function producto_modificar(codigo){
		
		var cantidad = document.getElementById('cantidad_' + codigo).value;
		var descuento = document.getElementById('descuento_' + codigo);
		var tipo_venta = document.getElementById('tipo_venta').value;

		var valor 	= parseInt(descuento.value);
		var maximo 	= parseInt(descuento.max);

		var desc;		
		desc = (valor > maximo) ? maximo : valor;
		if(valor < 0){ desc = 0; }

		$.ajax({
			type:'POST',
			url:"carro_detalle.php",
			data:{
				accion : 'modificar',
				codigo : codigo,
				cantidad : cantidad,
				descuento : desc
			},
			success:function(resp){
				$("#detalle_carro_body").hide().html(resp).fadeIn();
				$('#ModalDetalleCarrito').modal('hide');
			}
		});
	}


</script>

