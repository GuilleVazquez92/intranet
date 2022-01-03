
<?php

if(isset($_POST['tipo']) && isset($_POST['tramo'])){

	require('../../controlador/main.php');
	require( CONTROLADOR . 'ir.php');
	
	$ir = new IR();
	$ir->tipo 	= $_POST['tipo'];
	$ir->tramo 	= $_POST['tramo'];
	$ir->cuenta = $_POST['cuenta'];
	$ir->entrega = $_POST['entrega'];

	$datos_nuevos = $ir->recalcular_cuenta();
	
	if(count($datos_nuevos)>0){
		
		$cabezon = $_POST['bruto']-$datos_nuevos['capital'];
		?>
		<div class="table-responsive-sm">
			<table class="table table-sm table-borderless ml-3">
				<tr>
					<th width="15%">Entrega Inicial</th>
					<td width="10%" class="text-right"><?= number_format($datos_nuevos['entrega'],0,',','.'); ?>
					<input type="text" id="entrega2" value="<?= $datos_nuevos['entrega'];?>" hidden="hidden">
				</td>
				<td></td>
			</tr>
			<tr>
				<th>Saldo a Financiar</th><td class="text-right"><?= number_format($datos_nuevos['capital']-$datos_nuevos['entrega'],0,',','.'); ?></td>
			</tr>
			<tr>	
				<th>Cabezon</th><td class="text-right"><?= number_format($cabezon,0,',','.'); ?></td>
			</tr>
		</table>

		<table class="table table-sm table-hover">
			<tr class="bg-warning">
				<th class="text-center">#</th>
				<th class="text-right">Cuota</th>
				<th class="text-right">Total Financiación</th>
				<th class="text-center"></th>
			</tr>
			<?php
			for ($i=1; $i <= count($datos_nuevos)-4; $i++) { 

				$cantidad_cuota	= $datos_nuevos[$i]['cantidad'];
				$valor_cuota 	= $datos_nuevos[$i]['valor_cuota'];
				$valor_capital	= $datos_nuevos['capital']-$datos_nuevos['entrega'];
				$valor_tasa		= $datos_nuevos['tasa'];	
				$valor_total 	= $datos_nuevos[$i]['valor_total'];
				$cod_oper		= $datos_nuevos['cod_oper'];			
				$valor_check 	= $valor_capital.','.$valor_tasa.','.$cantidad_cuota.','.$valor_cuota.','.$valor_total.','.$cabezon.','.$cod_oper;
				?>
				<tr>
					<td class="text-center"><?= $cantidad_cuota; ?></td>
					<td class="text-right"><?= number_format($valor_cuota,0,',','.'); ?></td>
					<td class="text-right"><?= number_format($valor_total,0,',','.'); ?></td>
					<td class="text-right">
						<input class="form-check-input radio" type="radio" name="cuoteros" value="<?= $valor_check;?>"	required="required">
					</td>
				</tr>
				<?php
			}		
			?>		
		</table>
		<p class="text-right">
			<button class="btn btn-warning btn-sm" type="button" onclick="nueva_operacion();">Procesar</button>
		</p>	
	</div>
	<?php
}
}
?>	
