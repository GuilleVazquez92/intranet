<?php 
require('../../header.php');
require( CONTROLADOR . 'convenio.php');
$convenio = new Convenios();

if(isset($_POST['codigo']) && isset($_POST['cantidad'])){

	$codigo 	= $_POST['codigo']; 
	$cantidad 	= $_POST['cantidad']; 
	$convenio->stock_producto($codigo,$cantidad);

}

?>
<style>
	input.transparent-input{
		background-color:transparent !important;
		border:none !important;

	}

	.btn-circle {
		width: 20px;
		height: 20px;
		text-align: center;
		padding: 1px 0;
		font-size: 12px;
		line-height: 1.4285714;
		border-radius: 15px;
	}

	.btn-trasparent{
		color: #5F5448 !important;
	}

	.sin-borde{
		border: none;
	}

</style>
<div class="container-fluid">
	<br>
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
			<li class="breadcrumb-item active" aria-current="page">Lista de Productos</li>
		</ol>
	</nav>

	<?php 

	$products = $convenio->deposito_productos();

	if(isset($_POST['filtro'])){
		if(strlen($_POST['codigo'])+strlen($_POST['descripcion'])>=2){
			if(isset($_POST['codigo']) && $_POST['codigo']>=2010000){
				$filtro_add =  $_POST['codigo'];
				$products = array_filter($products, function ($item) use ($filtro_add) {
					return ($item['codigo'] == $filtro_add);
				});
			}else{

				$filtro_add =  strtoupper($_POST['descripcion']);
				$products = array_filter($products, function ($item) use ($filtro_add) {
					if (stripos($item['nombre_producto'], $filtro_add) !== false) {
						return true;
					}
					return false;
				});
			}
		}	
	}

	?>
	<form method="POST" action="">
		<div class="form-group row">
			<div class="col-sm-2">
				<input type="number" class="form-control form-control-sm mb-2" id="codigo" name="codigo" placeholder="Código" min="2010000" max="3000000">
			</div>
			<div class="col-sm-9">
				<input type="text" class="form-control form-control-sm mb-2" id="descripcion" name="descripcion" placeholder="Descripción">
			</div>
			<div class="col-sm-1">
				<button type="submit" class="btn btn-primary btn-sm mb-2" name="filtro">Buscar</button>
			</div>
		</div>
	</form>

	<?php 
	if(isset($filtro_add)){
		?>		
		<form method="POST" action="">
			<input type="text" hidden="hidden" name="codigo" value="">
			<input type="text" hidden="hidden" name="descripcion" value="">	 
			<div class="alert alert-success alert-dismissible fade show" role="alert">
				Filtro : <strong><?= $filtro_add; ?></strong>
				<button type="submit" class="close" aria-label="Cerrar" name="filtro">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
		</form>	
		<?php
	}
	?>
	

	<table class="table table-sm table-responsive-sm table-hover">
		<thead>
			<tr class="table-warning">
				<th>Código</th>
				<th>Descripción</th>
				<th class="text-right">Precio Lista</th>
				<th class="text-right">Cantidad</th>
				<th class="text-right">Solicitado</th>
			</tr>
		</thead>
		<tbody>	
			<?php
			foreach ($products as $product) {

				$datos = $product['codigo'].','.$product['nombre_producto'];

				?>		
				<tr>
					<td><?= $product['codigo'];?></td>
					<td><?= $product['nombre_producto'];?></td>
					<td class="text-right"><?= $product['precio_lista'];?></td>
					<td class="text-right">
						<?php 

						if($_COOKIE['cod_perfil']>9000){
							?> 		
							<form action="" method="POST">
								<input type="text" name='codigo' id="codigo" value="<?= $product['codigo'];?>" hidden>
								<input type="number" name="cantidad" id="cantidad" class="text-right" min="0" value="<?= $product['cantidad'];?>" onchange="this.form.submit()">
							</form>

							<?php
						}else{

							echo $product['cantidad'];
						}
						?>		
					</td>
					<td class="text-right">
						<button type="button" 
						class="btn btn-circle btn-sm btn-trasparent" 
						data-toggle="modal" 
						data-target="#exampleModal" 
						onclick="consultar_pedido('<?= $datos;?>')">
						<?= $product['solicitado'];?>
					</button>
				</td>
			</tr>	
			<?Php
		}
		?>
	</tbody>
</table>



<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Solicitud de Productos</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				<!--<button type="button" class="btn btn-primary">Save changes</button>-->
			</div>
		</div>
	</div>
</div>
</div>
<br>

<?php
require('../../footer.php'); 
?>
<script>
	function consultar_pedido(datos){

		var data = datos.split(',');
		$.ajax({
			type:'POST',
			url:"solicitud_producto.php",
			data:{
				codigo: data[0],
				producto : data[1]
			},
			success:function(resp){
				
				$(".modal-body").html(resp);
			}
		});
	}
</script>