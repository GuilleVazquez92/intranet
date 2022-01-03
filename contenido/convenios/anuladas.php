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

	<div style="padding:10px !important">
		<h6>Operaciones Anuladas</h6>
		<div id="widget">
			<?php
				for ($i=0; $i < count($anuladas); $i++) {

			?>
				<div class="alert alert-success">
					
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
					</table>	
				</div>

			<?php
				}	
			 ?>
		</div>


	</div>
</div>
<?php require('../../footer.php'); ?>