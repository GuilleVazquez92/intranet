<?php 
	include('load.php');

	$preventa = new Preventa;

	if(isset($_POST['add']))
	{
		$preventa->operacion = $_POST['add'];
		$preventa->add();
	}

	if(isset($_POST['delete']))
	{
		$preventa->operacion = $_POST['delete'];
		$preventa->delete();
	}

	if(isset($_POST['marcar']))
	{
		$preventa->operacion = $_POST['marcar'];
		$preventa->estado 	 = $_POST['estado'];
		$preventa->marcar();
	}

	if(isset($_POST['vender']))
	{
		$preventa->lote    = $_POST['lote'];
		$preventa->entidad = $_POST['entidad'];
		$preventa->modo    = $_POST['modo'];
		$preventa->vender();
	}


?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<title>Pre Venta</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
	<div class="container">
		<br>
		<h3>Preventa de Pagares</h3>
		<br>
		<form action="" class="form" method="POST">
			<label for="operacion">Operación : </label>
			<input type="numeric" name="add" placeholder="Ingrese operacion" />
			<button class="btn btn-primary" type="submit">Agregar</button>
		</form>
		<br>
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>Operacion</th>
					<th>Cuenta</th>
					<th>Documento</th>
					<th>Cliente</th>
					<th align="center">Faja </th>
					<th align="center">Cant</th>
					<th align="center">Cuota</th>
					<th align="center">Total</th>
					<th align="center">Acción</th>
				</tr>
			</thead>
			<tbody>
	<?php
		$total = 0;
		$datos = $preventa->listar_preventa(); 
		for ($i=0; $i < count($datos); $i++) { 
			$total += $datos[$i]['total'];

			$estado = '';
			if($datos[$i]['estado']==1){
				$estado = 'checked';
			}
	?>				
				<tr>
					<td><?= $datos[$i]['operacion']; ?></td>
					<td><?= $datos[$i]['cuenta']; ?></td>
					<td><?= $datos[$i]['documento']; ?></td>
					<td><?= $datos[$i]['cliente']; ?></td>
					<td align="center"><?= $datos[$i]['faja']; ?></td>
					<td align="center"><?= $datos[$i]['cantidad']; ?></td>
					<td align="right"><?= number_format($datos[$i]['cuota'],0,',','.'); ?></td>
					<td align="right"><?= number_format($datos[$i]['total'],0,',','.'); ?></td>
					<td align="center"><input type="checkbox" id="<?= $datos[$i]['operacion']; ?>" onchange="marcar(<?= $datos[$i]['operacion']; ?>);" <?= $estado;?>></td>
				</tr>
	<?php 
		}
	?>		
				<tr>
					<th colspan="7">Total : </th>
					<td align="right"><b><?= number_format($total,0,',','.'); ?></b></td>
					<td align="center">
						<form action="" method="POST">
							<button type="submit" name="delete" class="btn btn-danger">Quitar</button>
						</form>	
					</td>
				</tr>

			</tbody>
		</table>
		
			<h6>Descargas</h6>
			<button class="btn btn-warning" role="button">Cabecera</button>
			<button class="btn btn-warning" role="button">Cuotero</button>
		<hr>
		<br>
		<br>
		<h3>Venta de Pagares</h3>
		<br>

		<form action="" class="form-horizontal" method="POST">
			<div class="form-group">
					<?php 
						$datos = $preventa->listar_lotes(); 
						for ($i=0; $i < count($datos); $i++) {
							$prox = $datos[$i]['id'];	
						}
					?>
				<label for="lote" class="col-sm-2 control-label">Próximo lote</label>
				<div class="col-sm-4">
					<input type="text" name="lote" class="form-control" readonly="readonly" value="<?= $prox+1;?>">					
				</div>
			</div>
			<div class="form-group">
				<label for="entidad" class="col-sm-2 control-label">Seleccione la entidad</label>
				<div class="col-sm-4">
					<select name="entidad" id="" class="form-control" required >
						<option value=""></option>
						<?php 
							$datos = $preventa->listar_entidad(); 
							for ($i=0; $i < count($datos); $i++) {
						?>					
						<option value="<?= $datos[$i]['id'];?>"><?= $datos[$i]['descripcion'];?></option>
						<?php  
							}
						?>				
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="entidad" class="col-sm-2 control-label">Seleccione el modo</label>
				<div class="col-sm-4">
					<select name="modo" id="" class="form-control" required>
						<option value=""></option>
						<?php 
							$datos = $preventa->listar_modo(); 
							for ($i=0; $i < count($datos); $i++) {
						?>					
						<option value="<?= $datos[$i]['id'];?>"><?= $datos[$i]['descripcion'];?></option>
						<?php  
							}
						?>				
					</select>
				</div>
			</div>			
			<div class="form-group">
				<input type="checkbox" required="" name="vericado">
				<label for="vericado">He verificado correctamente todas las operaciones</label>
			</div>
			<div class="form-group">
				<button type="submit" class="btn btn-warning" class="form-control" name="vender">Vender</button>	
			</div>
		</form>
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
	<script>
		function marcar(id)
		{
			
			var valor = document.getElementById(id).checked;

			if(valor==true){
			
				valor = 1;
			
			}else{
			
				valor = 0;
			
			}

			$.ajax({
				url: 'preventa.php',
				method: 'POST',
				dataType: 'text',
				data:{
					marcar:id,
					estado:valor

				}, success: function(response){
					console.log(response);

				}
			});
		}		
	</script>	

</body>
</html>