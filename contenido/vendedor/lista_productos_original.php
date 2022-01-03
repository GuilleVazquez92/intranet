<?php 
	//	error_reporting(E_ALL);
	//	ini_set('display_errors', '1');
	require('../../header.php');

?>
	<div class="container">
	<br>
	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
	    <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
	    <li class="breadcrumb-item active" aria-current="page">Lista de Productos</li>
	  </ol>
	</nav>

	<?php 

		$data = file_get_contents("../../../../intranet/api/productos.json");
		$products = json_decode($data, true);

		if(isset($_POST['filtro'])){
			
			if(strlen($_POST['codigo'])+strlen($_POST['descripcion'])>=2){

				if(isset($_POST['codigo']) && $_POST['codigo']>=2000000){

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
		       <input type="number" class="form-control form-control-sm mb-2" id="codigo" name="codigo" placeholder="Código" min="2010000" max="4000000">
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
	

		<table class="table table-sm table-responsive-sm">
			<thead>
				<tr class="table-warning">
					<th>Código</th>
					<th>Descripción</th>
					<th>Stock</th>
					<th class="text-center">Precio</th>
				</tr>
			</thead>
			<tbody>	
			<?php
				foreach ($products as $product) { 
			?>		
			<tr>
				<td><?= $product['codigo'];?></td>
				<td><?= $product['nombre_producto'];?></td>
				<td class="text-center"><?= $product['stock'];?></td>
				<td class="text-right"><?= number_format($product['precio'],0,',','.');?></td>
			</tr>
			<?php
				}
			?>
			</tbody>
		</table>
	<?php
		echo '</div>';
		require('../../footer.php'); 
	?>
