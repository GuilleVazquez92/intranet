<?php 
	include('load.php');
	$venta = new Venta_Cartera; 

 ?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<title>Lotes</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">	
</head>
<body>
	<div class="container">
		<br>
		<h3>Lotes de Pagares</h3>
		<br>
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>Lote</th>
					<th>Entidad</th>
					<th>Fecha Envio</th>
					<th>Fecha Aceptacion</th>
					<th align="center">Plazo</th>
					<th align="center">Estado</th>
					<th align="center">Modo</th>
					<th align="center">Cant Operación</th>
					<th align="center">Acción</th>
				</tr>
			</thead>
			<tbody>
		<?php
			
			$datos = $venta->listar_lotes(); 
			for ($i=0; $i < count($datos); $i++) { 
		?>

				<tr>
					<td><?= $datos[$i]['id']; ?></td>
					<td><?= $datos[$i]['entidad']; ?></td>
					<td><?= $datos[$i]['fecha_envio']; ?></td>
					<td><?= $datos[$i]['fecha_acep']; ?></td>
					<td><?= $datos[$i]['plazo']; ?></td>
					<td><?= $datos[$i]['estado']; ?></td>
					<td><?= $datos[$i]['modo']; ?></td>
					<td><?= $datos[$i]['cant_oper']; ?></td>
					<td></td>
				</tr>
		<?php 
			} 
		?>
			</tbody>
		</table>
	</div>						


	<script
	  		src="https://code.jquery.com/jquery-3.4.1.min.js"
	  		integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
	  		crossorigin="anonymous">
	</script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" 
			integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" 
			crossorigin="anonymous">
	</script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" 
			integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" 
			crossorigin="anonymous">
	</script>
</body>
</html>