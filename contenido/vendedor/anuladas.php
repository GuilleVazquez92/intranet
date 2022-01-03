<?php 
	//	error_reporting(E_ALL);
	//	ini_set('display_errors', '1');
	require('../../header.php');
	require( CONTROLADOR . 'vendedores.php');

	$vendedor = new Vendedores();
	$vendedor->vendedor = $_COOKIE['usuario'];
	$anuladas = $vendedor->anuladas();
	unset($_SESSION);	
?>
<br>
<div class="container">
	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
	    <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
	    <li class="breadcrumb-item active" aria-current="page">Operaciones Anuladas y Rechazadas</li>
	  </ol>
	</nav>
	<?php
		for ($i=0; $i < count($anuladas); $i++) {

			$class_div = ($anuladas[$i]['cod_estado']==15) ? 'alert alert-warning':'alert alert-danger';
			echo '<div class="'.$class_div.'">';
	?>
			<table class="table table-sm">
				<tr>
					<td colspan="3"><span class="text-primary"><?= $anuladas[$i]['cuenta'].' - '.$anuladas[$i]['cliente'];?></span></td>
				</tr>
				<tr>
					<td><span class="text-muted"><b>operacion</b></span></td>
					<td align="left" colspan="2"><?= ucwords(strtolower($anuladas[$i]['operacion']))?></td>
				</tr>							
				<tr>
					<td><span class="text-muted"><b>estado</b></span></td>
					<td align="left" colspan="2"><?= $anuladas[$i]['estado'];?></td>
				</tr>							
				<tr>
					<td><span class="text-muted"><b>total<b></span></td>
					<td align="right" colspan="2"><?= number_format($anuladas[$i]['total'],0,',','.') ?></td>
				</tr>
				<tr>
					<td><span class="text-muted"><b>motivo</b></span></td>
					<td align="left" colspan="2"><?= $anuladas[$i]['fecha_motivo'].' '.$anuladas[$i]['motivo'];?></td>
				</tr>							
			</table>	
		</div>
	<?php
		}	
	 ?>
</div>
<?php 
	require('../../footer.php'); 
?>