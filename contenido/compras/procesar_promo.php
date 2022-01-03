<?php
if($_POST['accion']){


	require('../../controlador/main.php');
	require( CONTROLADOR . 'compras.php');
	$data = new Compras();


	if($_POST['accion'] == 'agregar_promo406'){

		$data->codigo 	= $_POST['codigo'];
		$resultado 		=  $data->agregar_promo406();
		$result = ($resultado>0) ? array('alert' => 'alert-success', 'mensaje'=> 'Se proceso correctamente la solicitud.') : array('alert' => 'alert-danger', 'mensaje'=> 'Error al procesar la solicitud.');
		print_r(json_encode($result));
	}

	if($_POST['accion'] == 'quitar_promo406'){

		$data->codigo 	= $_POST['codigo'];
		$resultado 		=  $data->quitar_promo406();
		$result = ($resultado>0) ? array('alert' => 'alert-success', 'mensaje'=> 'Se proceso correctamente la solicitud.') : array('alert' => 'alert-danger', 'mensaje'=> 'Error al procesar la solicitud.');
		print_r(json_encode($result));
	}

	if($_POST['accion'] == 'modificar_promo406'){

		$data->codigo 	= $_POST['codigo'];
		$data->deposito = $_POST['deposito'];
		$data->stock 	= $_POST['stock'];
		$data->modificar_promo406();		

	}

	if($_POST['accion'] == 'editar_promo406'){

		?>
		<div class="card-deck">
			<div class="card">
				<div class="card-body">
					<h5 class="card-title">Normal</h5>
					<table class="table">
						<tr class="grilla_titulo">
							<td>Deposito</td>
							<td>Estado</td>
							<td>Stock</td>
							<td>Accion</td>
						</tr>
						<?php
						$codigo_normal = ($_POST['codigo']-20000000); 
						$producto_normal = $data->consultar_promo406($codigo_normal);
						foreach ($producto_normal as $key) {
							?>	
							<tr>
								<td><?= $key['deposito'];?></td>
								<td><?= $key['estado'];?></td>
								<td><?= $key['stock'];?></td>
								<td>
									<?php
									if($key['cod_deposito']>6000){	
										?>		
										<input type="number" class="inp_modificar" id="<?= 'inp_'.$key['codigo'].'_'.$key['cod_deposito'];?>" min="1" max="<?= $key['stock'];?>" />
										<button class="btn btn-sm btn-primary btn_modificar" id="<?= 'btn_'.$key['codigo'].'_'.$key['cod_deposito'];?>" disabled >Enviar</button>
										<?php
									}
									?>									
								</td>									
							</tr>	
							<?php
						}
						?>
					</table>	
				</div>
			</div>
			<div class="card">
				<div class="card-body">
					<h5 class="card-title">Promoción</h5>
					<table class="table">
						<tr class="grilla_titulo">
							<td>Deposito</td>
							<td>Estado</td>
							<td>Stock</td>
							<td>Accion</td>
						</tr>
						<?php
						$codigo_promo = $_POST['codigo'];
						$producto_promo = $data->consultar_promo406($codigo_promo);
						foreach ($producto_promo as $key) {
							?>	
							<tr>
								<td><?= $key['deposito'];?></td>
								<td><?= $key['estado'];?></td>
								<td><?= $key['stock'];?></td>
								<td>
									<?php
									if($key['cod_deposito']>6000){	
										?>		
										<input type="number" class="inp_modificar" id="<?= 'inp_'.$key['codigo'].'_'.$key['cod_deposito'];?>" min="1" max="<?= $key['stock'];?>" />
										<button class="btn btn-sm btn-primary btn_modificar" id="<?= 'btn_'.$key['codigo'].'_'.$key['cod_deposito'];?>" disabled >Enviar</button>
										<?php
									}
									?>
								</td>									
							</tr>	
							<?php
						}
						?>
					</table>
				</div>
			</div>
		</div>
		<?php
	}

	if($_POST['accion'] == 'editar_promo600' || $_POST['accion'] == 'activar_600' || $_POST['accion'] == 'agregar_600' || $_POST['accion'] == 'quitar_600'){

		$data->codigo = $_POST['codigo'];

		if($_POST['accion'] == 'activar_600'){
			$data->target = $_POST['target'];
			$data->activar_600();
		}
		
		if($_POST['accion'] == 'agregar_600'){
			$data->target = $_POST['target'];
			$data->agregar_600();
		}
		
		if($_POST['accion'] == 'quitar_600'){
			$data->target = $_POST['target'];
			$data->quitar_600();
		}

		$datos = $data->consultar_promo600();
		$estado = (isset($datos[0]['estado']) && $datos[0]['estado']=='S') ? 'checked' : '';

		?>

		<div class="custom-control custom-switch">
			<input type="checkbox" class="custom-control-input" id="activar_600" data-id="<?= $_POST['codigo'];?>" <?= $estado;?>>
			<label class="custom-control-label" for="activar_600">Promoción Activa</label>
		</div>
		<br>
		<div class="input-group mb-3">
			<input type="number" class="form-control" id="agregar_600" data-id="<?= $_POST['codigo'];?>" placeholder="Agregar producto a la promo" min="2000000" max="6000000" aria-label="Agregar producto" aria-describedby="button-addon2">
			<div class="input-group-append">
				<button class="btn btn-outline-secondary agregar_600" type="button" id="button-addon2">Agregar</button>
			</div>
		</div>

		<div class="table-responsive">
			<table class="table table-sm">
				<thead>
					<tr class="table-warning">
						<th>Código</th>
						<th>Descipción</th>
						<th class="text-center">Acción</th>
					</tr>
				</thead>
				<tbody>
					<?php 

					foreach ($datos as $key){
						?>
						<tr>
							<td><?= $key['codigo'];?></td>
							<td><?= $key['descripcion'];?></td>
							<td class="text-center"><img src="<?= IMAGE.'fail.png'; ?>" title="Quitar de la Promoción" class="quitar_600" data-id="<?= $_POST['codigo'];?>" data-target="<?= $key['codigo'];?>"></td>
						</tr>
						<?php 
					}
					?>
				</tbody>		
			</table>
		</div>
		<?Php
	}
}
?>

<script type="text/javascript">
	
	$(".inp_modificar").change(function(){

		var input 	= $(this).attr('id');
		var boton 	= input.replace('inp_', '#btn_');	

		if(this.value>=1 && this.value<=this.max){
			$(boton).prop("disabled",false);
		}else{
			$(boton).prop("disabled",true);
		}
	});

	$(".btn_modificar").click(function(){

		var boton 	 = $(this).attr('id');
		var input 	 = boton.replace('btn_', '#inp_');

		var stock 	 = $(input).val();
		var codigo 	 = input.split('_')[1];
		var deposito = input.split('_')[2];
		
		$.ajax({
			type:'POST',
			url:"procesar_promo.php",
			data:{
				accion : "modificar_promo406",
				codigo : codigo,
				deposito: deposito,
				stock : stock
			},
			success:function(resp){
				//console.log(resp);
				location.reload();			
			}
		});
	});

	$("#activar_600").change(function(){

		var codigo = $(this).data('id');
		var target = ($(this).is(':checked')) ? 'S':'N';

		$.ajax({
			type:'POST',
			url:"procesar_promo.php",
			data:{
				accion : "activar_600",
				codigo : codigo,
				target: target
			},
			success:function(resp){
				
			}
		});
	})

	$(".agregar_600").click(function(){

		var codigo 	= $("#agregar_600").data('id');
		var target 	= $("#agregar_600").val();

		$.ajax({
			type:'POST',
			url:"procesar_promo.php",
			data:{
				accion : "agregar_600",
				codigo : codigo,
				target: target
			},
			success:function(resp){
				$('#ProdcutosBody').html(resp);
			}
		});
	});

	$(".quitar_600").click(function(){

		var codigo 	= $(this).data('id');
		var target 	= $(this).data('target');
		$.ajax({
			type:'POST',
			url:"procesar_promo.php",
			data:{
				accion : "quitar_600",
				codigo : codigo,
				target: target
			},
			success:function(resp){
				$('#ProdcutosBody').html(resp);
			}
		});
	});
</script>