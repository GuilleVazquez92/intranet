<?php 
// 	error_reporting(E_ALL);
 //	ini_set('display_errors', '1');

	if(isset($_POST['operacion'])){

		require('../../controlador/main.php');
		require( CONTROLADOR . 'convenio.php');

		$convenio = new Convenios();
		$convenio->operacion = $_POST['operacion'];
?>
		
		<h6>Operación : <?= $_POST['operacion'] ;?></h6>

		<table class="table table-sm table-bordered table-responsive-sm">
			<thead>
				<tr class="table-warning">
					<th>Fecha</th>
					<th>Usuario</th>
					<th>Lote</th>
					<th>Estado</th>
				</tr>
			</thead>

			<tbody>
				<?php 
					$datos = $convenio->pagares_historial();
					for ($i=0; $i < count($datos); $i++) {

						$fecha = date_create($datos[$i]['fecha']);		
				?>
				<tr>
					<td><?= date_format($fecha,"d-m-Y");?></td>
					<td><?= $datos[$i]['usuario']; ?></td>
					<td><?= $datos[$i]['lote'];?></td>
					<td><?= $datos[$i]['estado'];?></td>
				</tr>
				<?php
					}
				?>
			</tbody>
		</table>
<?php
		
	}
?>

