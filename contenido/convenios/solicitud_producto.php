<?php 
if(isset($_POST['codigo'])){

	require('../../controlador/main.php');
	require( CONTROLADOR . 'convenio.php');
	$solicita 	= new Convenios();
	$solicita->usuario= $_COOKIE['usuario'];
	$codigo 	= $solicita->codigo = $_POST['codigo'];
	$producto 	= $_POST['producto'];

	if(isset($_POST['accion']) && $_POST['accion']=='producto_agregar'){

		$solicita->cantidad = $_POST['cantidad'];
		$solicita->cuota = $_POST['cuota'];	
		$solicita->producto_agregar();	
	}

	if(isset($_POST['accion']) && $_POST['accion']=='producto_quitar'){

		$solicita->pedido = $_POST['pedido'];
		$solicita->producto_quitar();	
	}

	if(isset($_POST['accion']) && $_POST['accion']=='producto_aprobar'){

		$solicita->pedido = $_POST['pedido'];
		$solicita->cantidad = $_POST['cantidad'];
		$solicita->producto_aprobar();	
	}

	if(isset($_POST['accion']) && $_POST['accion']=='producto_rechazar'){

		$solicita->pedido = $_POST['pedido'];
		$solicita->producto_rechazar();	
	}

	?>
	<div class="modal-contenido">
		<h5><span id="data_producto"><?=$producto;?></span><span id="data_codigo" class="badge badge-secondary ml-2"><?=$codigo;?></span></h5>
		<?php 
		if($_COOKIE['cod_perfil']<9000){
			?>
			<div class="form-inline">
				<div class="input-group input-group-sm mb-3 mr-sm-2">
					<label class="mr-2" for="cantidad">Cantidad</label>
					<input type="number" class="form-control" id="cantidad" placeholder="" step="1" min="1" max="100">
				</div>
				<div class="input-group input-group-sm mb-3 mr-sm-2">
					<label class="mr-2" for="cuota">Cuotas</label>
					<input type="number" class="form-control" id="cuota" placeholder="" step="1" min="1" max="25">
				</div>
				<button type="submit" class="btn btn-success btn-sm mb-3" onclick="producto_agregar();">Solicitar</button>
			</div>
			<?php  
		}
		?>
		<div class="table table-sm table-responsive-sm">
			<table class="table">
				<thead>
					<tr class="table-warning">
						<th>Fecha</th>
						<th>Estado</th>
						<th>Usuario</th>
						<th class="text-center">Cuotas</th>
						<th class="text-center">Cantidad</th>
						<th class="text-center">Aprobado</th>
						<th>Fecha Apr.</th>
						<th>Usuario Apr.</th>
						<th class="text-center">Acción</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$data = $solicita->productos_pedidos();
					foreach ($data as $producto) {
						$pedido = $producto['pedido'];
						?>				
						<tr>
							<td><?= $producto['fecha'];?></td>
							<td><?= $producto['estado'];?></td>
							<td><?= $producto['usuario'];?></td>
							<td class="text-center"><?= $producto['cuota'];?></td>
							<td class="text-center"><?= $producto['cantidad'];?></td>
							<td class="text-center">
								<?php 
								if($producto['estado']=='Pendiente'){

									if($_COOKIE['cod_perfil']>9000){

										$id = 'cantidad'.$pedido;	
										echo '<input type="number" class="input-sm" id="'.$id.'" step="1" min="1" max="'.$producto['cantidad'].'" value="'.$producto['cantidad'].'">';

									}else{
										echo $producto['cantidad_aprob'];
									}
								}
								?>
							</td>
							<td><?= $producto['fecha_aprob'];?></td>
							<td><?= $producto['usuario_aprob'];?></td>
							<td class="text-center">
								<?php 
								if($producto['estado']=='Pendiente'){

									if($_COOKIE['cod_perfil']>9000){

										echo '<button class="btn btn-circle btn-sm btn-success" title="Aprobar" onclick="producto_aprobar('.$pedido.')"></button>
										<button class="btn btn-circle btn-sm btn-danger" title="Rechazar" onclick="producto_rechazar('.$pedido.')"></button>';

									}else{
										echo '<button class="btn btn-circle btn-sm btn-danger" title="Quitar" onclick="producto_quitar('.$pedido.')"></button>';
									}
								}
								?>							
							</td>
						</tr>
						<?php  
					}
					?>				
				</tbody>
			</table>
		</div>
	</div>
	<div class="d-flex justify-content-center">
		<div class="modal-esperar spinner-border text-success" role="status">
			<span class="sr-only">Loading...</span>
		</div>
	</div>	
	<?php 
}
?>
<script>

	$(".modal-esperar").fadeIn(500);
	$(".modal-contenido").hide();

	setTimeout(function() {
		$(".modal-esperar").fadeOut(500);
		$(".modal-contenido").fadeIn(1000);
	}, 500);	

	
	function producto_agregar(){

		var accion 	 = "producto_agregar";
		var codigo 	 = document.getElementById('data_codigo').innerHTML;
		var producto = document.getElementById('data_producto').innerHTML;
		var cantidad = document.getElementById('cantidad').value;
		var cuota = document.getElementById('cuota').value;

		if(cuota > 0 && cantidad > 0){
			$.ajax({
				type:'POST',
				url:"solicitud_producto.php",
				data:{
					accion : accion,
					codigo: codigo,
					producto: producto,
					cuota : cuota,
					cantidad: cantidad
				},
				success:function(resp){
					//location.reload();
					$(".modal-body").html(resp);	
				}
			});
		}else{
			alert("Verifique la cantidad o las cuotas...");
		} 
	}

	function producto_quitar(pedido){

		var accion 	 = "producto_quitar";
		var codigo 	 = document.getElementById('data_codigo').innerHTML;
		var producto = document.getElementById('data_producto').innerHTML;

		$.ajax({
			type:'POST',
			url:"solicitud_producto.php",
			data:{
				accion : accion,
				pedido : pedido,
				codigo : codigo,
				producto: producto
			},
			success:function(resp){
				$(".modal-body").html(resp);	
			}
		});
	}

	function producto_aprobar(pedido){

		var id 		 = 'cantidad' + pedido;
		var accion 	 = "producto_aprobar";
		var codigo 	 = document.getElementById('data_codigo').innerHTML;
		var producto = document.getElementById('data_producto').innerHTML;
		var cantidad = document.getElementById(id).value;

		$.ajax({
			type:'POST',
			url:"solicitud_producto.php",
			data:{
				accion : accion,
				pedido : pedido,
				codigo : codigo,
				producto: producto,
				cantidad: cantidad
			},
			success:function(resp){
				$(".modal-body").html(resp);	
			}
		});
	}

	function producto_rechazar(pedido){

		var accion 	 = "producto_rechazar";
		var codigo 	 = document.getElementById('data_codigo').innerHTML;
		var producto = document.getElementById('data_producto').innerHTML;

		$.ajax({
			type:'POST',
			url:"solicitud_producto.php",
			data:{
				accion : accion,
				pedido : pedido,
				codigo : codigo,
				producto: producto
			},
			success:function(resp){
				$(".modal-body").html(resp);	
			}
		});
	}

</script>