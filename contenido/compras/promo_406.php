<?Php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require('../../header.php');
require( CONTROLADOR . 'compras.php');
$data = new Compras();

?>
<br>
<div class="container-fluid">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
			<li class="breadcrumb-item active" aria-current="page">Promoción 406</li>
		</ol>
	</nav>

	<div class="form-group">
		<label for="codigo">Agregar a la promo: </label>
		<input type="number" id="codigo" name="codigo" step="1" min="2000000" max="5000000">
		<button type="button" class="btn btn-primary agregar">Agregar</button>
	</div>

	<div class="alert my-3" role="alert">
	</div>

	<table class="table">
		<thead>
			<tr>
				<th>Codigo</th>
				<th>Descripción</th>
				<th>Stock</th>
				<th>Precio</th>
				<th colspan="3" align="center">Acción</th>
			</tr>
		</thead>

		<?Php
		foreach ($data->productos_406() as $key) {
			?>
			<tr class="grilla">	
				<td><?= $key['codigo']; ?></td>
				<td><?= $key['descripcion']; ?></td>
				<td align="center"><?= $key['stock']; ?></td>
				<td align="right"><?= number_format($key['precio'],0,',','.'); ?></td>
				<td align="center">
					<img src="<?= IMAGE.'editar.png';?>" class="editar" title="Editar de Promoción" data-id="<?= $key['codigo'];?>">
					<?Php 
					if($key['stock']==0){
						?>
						<img src="<?= IMAGE.'fail.png'?>" class="desactivar" title="Desactivar de Promoción" data-id="<?= $key['codigo'];?>">		
						<?Php
					}
					?>
				</td>
			</tr>	
			<?Php
		} 
		?>
	</table>

	<!-- Modal -->
	<div class="modal fade" id="ProdcutosModal" tabindex="-1" aria-labelledby="ProdcutosModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-xl modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="ProdcutosModalLabel"></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div id="ProdcutosBody" class="modal-body">
					
				</div>

			<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				</div>
		</div>
	</div>
</div>
</div>
<?php
require('../../footer.php'); 
?>
<script>

	$(".agregar").click(function(){
		var valor = $('#codigo').val();
		$.ajax({
			type:'POST',
			url:"procesar_promo.php",
			data:{
				accion : "agregar_promo406",
				codigo : valor,
			},
			success:function(resp){
				//var response = JSON.parse(resp);
				//$(".alert").addClass(response['alert']).html(response['mensaje']);
				location.reload();			
			}
		});	
	});

	
	$(".editar").click(function(){
		
		var valor = $(this).data("id");

		$("#ProdcutosModalLabel").html('Editar Promo '+ valor);
		$('#ProdcutosModal').modal('toggle');
		
		$.ajax({
			type:'POST',
			url:"procesar_promo.php",
			data:{
				accion : "editar_promo406",
				codigo : valor
			},
			success:function(resp){
				$('#ProdcutosBody').html(resp);	

			}
		});	
	});

	$(".desactivar").click(function(){
		var valor = $(this).data("id");
		$.ajax({
			type:'POST',
			url:"procesar_promo.php",
			data:{
				accion : "quitar_promo406",
				codigo : valor
			},
			success:function(resp){
				//var response = JSON.parse(resp);
				//$(".alert").addClass(response['alert']).html(response['mensaje']);
				location.reload();			
			}
		});	
	});
</script>