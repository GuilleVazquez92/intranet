<?php 
	require('../../header.php');
	require( CONTROLADOR . 'convenio.php');
	$convenio = new Convenios();
	$convenio->id = $_COOKIE['id'];
	$filtro_fecha = "disabled";

?>
	<br>
	<div class="container-fluid">
		<nav aria-label="breadcrumb">
		  <ol class="breadcrumb">
		    <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
		    <li class="breadcrumb-item active" aria-current="page">Operaciones Pendientes</li>
		  </ol>
		</nav>
		<?php 
			require('filtro_fecha.php');
		?>
		<table class="table table-sm">
			<thead>				
			<tr class="table-warning">
				<th scope="col">DOCUMENTO</th>
				<th scope="col">CUENTA</th>
				<th scope="col" class="text-center">OPERACION</th>
				<th scope="col" class="text-center">ESTADO</th>
				<th scope="col" class="text-center">CANTIDAD</th>
				<th scope="col" class="text-right">CUOTA</th>
				<th scope="col" class="text-right">TOTAL</th>
			</tr>
			</thead>
			<tbody>	
			<?php
				
				$cuota = 0;
				$total = 0;

				$datos = $convenio->pendientes();
				for ($i= 0; $i < count($datos); $i++) {
					$cuota += $datos[$i]['cuota']; 
					$total += $datos[$i]['total'];
			
			?>
			<tr>
				<td><?= $datos[$i]['documento'];?></td>	
				<td><?= $datos[$i]['cuenta'].' - '.$datos[$i]['cliente'];?></td>
				<td class="text-center"><?= ucwords(strtolower($datos[$i]['operacion']))?></td>
				<td class="text-center"><?= $datos[$i]['estado'];?></td>
				<td class="text-center"><?= number_format($datos[$i]['cantidad'],0,',','.') ?></td>
				<td class="text-right"><?= number_format($datos[$i]['cuota'],0,',','.') ?></td>
				<td class="text-right"><?= number_format($datos[$i]['total'],0,',','.') ?></td>
			</tr>							
			<?php
				}	
			 ?>
			 <tr class="table-warning">
				<th colspan="5">TOTAL</th>
				<th class="text-right"><?= number_format($cuota,0,',','.') ?></th>
				<th class="text-right"><?= number_format($total,0,',','.') ?></th>
			</tr>
			</tbody>
		</table>
	</div>
	<?php 
		require('../../footer.php'); 
	?>