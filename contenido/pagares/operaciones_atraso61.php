<?php 
require('../../header.php');
require( CONTROLADOR . 'pagares.php');
$pagares = new PAGARES();
$pagares->entidad = $_COOKIE['id'];
?>
<div class="container-fluid">
	<br>
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
			<li class="breadcrumb-item active" aria-current="page">Control de Lotes</li>
		</ol>
	</nav>
	<div class="table-responsive">
		<table class="table table-sm">
			<thead>
				<tr class="table-warning">
					<th>LOTE</th>
					<th>DESCRIPCION</th>
					<th>CUENTA</th>
					<th>DOCUMENTO</th>
					<th>CLIENTE</th>
					<th>OPERACION</th>
					<th>ATRASO</th>												
					<th>CANT CUOTA</th>
					<th>CUOTA</th>
					<th>BRUTO</th>
					<th>SALDO</th>
				</tr>
			</thead>	
			<tbody>
				<?php
				$ix = 0;
				$total_saldo = 0;
				$total_cuota = 0;
				$total_bruto = 0;
				foreach ($pagares->consultar_operaciones61() as $key => $datos[]) {
					$total_cuota += $datos[$ix]['monto_cuota']; 
					$total_bruto += $datos[$ix]['monto_bruto'];
					$total_saldo += $datos[$ix]['saldo_capital'];

					?>
					<tr>
						<td><?= $datos[$ix]['lote']?></td>
						<td><?= $datos[$ix]['descripcion']?></td>
						<td><?= $datos[$ix]['cuenta']?></td>
						<td><?= $datos[$ix]['documento']?></td>
						<td><?= $datos[$ix]['cliente']?></td>
						<td class="text-center"><?= $datos[$ix]['operacion']?></td>
						<td 
						<?php  
						
						if($datos[$ix]['atraso']>60){
							#rojo
							echo "class='text-center table-danger'";
						}elseif ($datos[$ix]['atraso']>30) {
							#amarillo
							echo "class='text-center table-warning'";
						}else{
							#verde
							echo "class='text-center table-success'";
						}


						?>	
						><?= number_format($datos[$ix]['atraso'],0,',','.');?></td>
						<td class="text-center"><?= number_format($datos[$ix]['cant_cuota'],0,',','.');?></td>
						<td class="text-right"><?= number_format($datos[$ix]['monto_cuota'],0,',','.');?></td>
						<td class="text-right"><?= number_format($datos[$ix]['monto_bruto'],0,',','.');?></td>
						<td class="text-right"><?= number_format($datos[$ix]['saldo_capital'],0,',','.');?></td>
					</tr>
					<?php
					$ix++;
				}
				?>
				<tr>
					<th colspan="6"></th>
					<th class="text-right"><?= number_format($total_cuota,0,',','.');?></th>			
					<th class="text-right"><?= number_format($total_bruto,0,',','.');?></th>
					<th class="text-right"><?= number_format($total_saldo,0,',','.');?></th>
					<th></th>
				</tr>
			</tbody>
		</table>
	</div>
</div>
	<?php
	require('../../footer.php');
	?>
