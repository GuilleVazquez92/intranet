<?php 
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

require('../../controlador/main.php');
require( CONTROLADOR.'ventas.php');
$data = new Ventas();
$data->cod_vend = $_POST['cod_vend'];

if(isset($_POST['meta_total'])){

	$data->meta_prod 	= $_POST['meta_prod'];
	$data->meta_moto 	= $_POST['meta_moto'];
	$data->meta_salud 	= $_POST['meta_salud'];
	$data->meta_total 	= $_POST['meta_total'];
	$data->cargar_meta();
}

$vendedor = $data->consultar_vendedor_meta();
?>
<div class="container">
	<h4 class="text-white bg-primary p-2"><?= $data->cod_vend.' - '.$vendedor['vendedor']; ?></h4>

	<table class="table table-bordered">
		<tr>
			<td>TIPO VENDEDOR:</td>
			<td><?= $vendedor['equipo']?></td>
		</tr>
		<tr>
			<td>TRAMO:</td>
			<td><?= $vendedor['tramo']?></td>
		</tr>
		<tr>
			<td>META PISO:</td>
			<td>Gs. <?= number_format($vendedor['piso'],0,',','.')?></td>
		</tr>
		<tr>
			<td>PRODUCTO:</td>
			<td>
				<input type="number" id="cod_vend" value="<?= $data->cod_vend;?>" hidden>
				<input type="number" id="piso" value="<?= $vendedor['piso'];?>" hidden>
				<input type="number" id="meta_prod" class="meta" value="0" step="500000"/>
			</td>
		</tr>
		<tr>
		<td>SALUD:</td>
			<td>
				<input type="number" id="meta_salud" class="meta" value="0" step="500000"/>
			</td>
		</tr>
		<tr>
			<td>MOTO:</td>
			<td>
				<input type="number" id="meta_moto" class="meta" value="0" step="500000"/>
			</td>
		</tr>

		<tr>
			<td>TOTAL</td>
			<td>
				<input type="number" id="meta_total" value="0" readonly="readonly" disabled="disabled" />
			</td>
		</tr>

	</table>
</div>
<script>
		$(".meta").change(function(){

			var piso		= parseInt($('#piso').val());
			var meta_prod 	= parseInt($('#meta_prod').val());
			var meta_moto 	= parseInt($('#meta_moto').val());
			var meta_salud 	= parseInt($('#meta_salud').val());	
			var meta_total  = (meta_salud+meta_moto+meta_prod);
			$('#meta_total').val(meta_total);

			if(meta_total>=piso){
				$("#meta_guardar").prop('disabled',false);

			}else{
				$("#meta_guardar").prop('disabled',true);
			}
		});
</script>