<?php
$capital = $_POST['tramo'];
$capital = $_POST['capital'];
$interes = $_POST['interes'];
$a_refinanciar = $_POST['capital']+$_POST['interes'];
?>
<div class="table-responsive-sm">
	<table class="table table-sm table-borderless ml-3">
		<tr>
			<th width="15%">Saldo a Financiar</th>
			<th width="10%" class="text-right">
				<?= number_format($a_refinanciar,0,',','.');?>
			</th>
			<th></th>
		</tr>
	</table>

	<table class="table table-sm table-hover">
		<tr class="bg-warning">
			<th class="text-center">#</th>
			<th class="text-right">Cuota</th>
			<th class="text-right">Capital</th>
			<th class="text-right">Interes</th>
			<th class="text-right">Total Financiación</th>
			<th class="text-center"></th>
		</tr>
		<?php
		for ($i=2; $i <= 18; $i++) { 

			$cantidad_cuota	= $i;
			$valor_cuota	= round(($a_refinanciar/$i)+499,-3);
			$valor_total 	= $valor_cuota*$i;
			$valor_capital	= $capital;
			$valor_interes	= $valor_total-$capital;
			$valor_tasa		= $cantidad_cuota*3;	
			$valor_check 	= $valor_cuota.','.$valor_total.','.$valor_capital.','.$valor_interes.','.$valor_tasa.','.$cantidad_cuota;
			?>
			<tr>
				<td class="text-center"><?= $i; ?></td>
				<td class="text-right"><?= number_format($valor_cuota,0,',','.'); ?></td>
				<td class="text-right"><?= number_format($valor_capital,0,',','.'); ?></td>
				<td class="text-right"><?= number_format($valor_interes,0,',','.'); ?></td>
				<td class="text-right"><?= number_format($valor_total,0,',','.'); ?></td>
				<td class="text-right">
					<input class="form-check-input radio checkbox_cuotero" type="radio" name="cuoteros" value="<?= $valor_check;?>"	required="required">
				</td>
			</tr>
			<?php
		}		
		?>		
	</table>
	<?php
	if($_POST['tramo']>=2 && $_POST['tramo']<=21){
		?>	 
		<p class="text-right mr-2">
			<button class="btn btn-warning btn-sm" id="btn_prcesar" type="button" onclick="nueva_operacion();" disabled>Procesar</button>
		</p>
		<?php
	}
	?>	
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$(".checkbox_cuotero").change(function(){
			$("#btn_prcesar").prop('disabled',false);
		});
	});
</script>