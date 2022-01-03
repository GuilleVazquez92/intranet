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
			<li class="breadcrumb-item active" aria-current="page">Promoción 600</li>
		</ol>
	</nav>

	<div class="alert my-3" role="alert">
	</div>
	<div class="table-responsive">
		<table class="table table-sm">
			<thead>
				<tr class="table-warning">
					<th>Codigo</th>
					<th>Descripción</th>
					<th class="text-center">Estado</th>
					<th>Precio</th>
					<th colspan="3" align="center">Acción</th>
				</tr>
			</thead>

			<?Php
			$datos = $data->productos_promo600();
			foreach ($datos as $key) {
				?>
				<tr class="table-active">	
					<th><?= $key['codigo']; ?></th>
					<th><?= $key['descripcion']; ?></th>
					<th class="text-center">
						<img src="<?= ($key['estado']=='S') ? IMAGE.'green_ball.png':IMAGE.'red_ball.png'; ?>" title="Estado de Promoción" width="18px">
					</th>
					<th align="right"><?= number_format($key['precio'],0,',','.'); ?></th>
					<th align="center">
						<img src="<?= IMAGE.'editar.png';?>" class="editar" title="Editar de Promoción" data-id="<?= $key['codigo'];?>">
					</th>
				</tr>
				<?php 
				foreach (json_decode($key['productos'],true) as $prod) {
					?>	
					<tr>
						<td colspan="1"></td>
						<td colspan="3" class="pl-5"><?= $prod['promocion'].' '.$prod['descripcion'];?></td>
					</tr>
					<?php
				}
			} 
			?>
		</table>
	</div>
	<!-- Modal -->
	<div class="modal fade" id="ProdcutosModal" data-backdrop="static" tabindex="-1" aria-labelledby="ProdcutosModalLabel" aria-hidden="true">
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
	$(".editar").click(function(){

		var codigo = $(this).data("id");

		$("#ProdcutosModalLabel").html('Editar Promo '+ codigo);
		$('#ProdcutosModal').modal('toggle');

		$.ajax({
			type:'POST',
			url:"procesar_promo.php",
			data:{
				accion : "editar_promo600",
				codigo : codigo
			},
			success:function(resp){
				
				$('#ProdcutosBody').html(resp);
				console.log(resp);	

			}
		});	
	});

	$('#ProdcutosModal').on('hidden.bs.modal', function (event) {
   		location.reload();
	})

</script>