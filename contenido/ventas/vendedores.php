<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$_COOKIE['cod_canal'] = 14;
*/

require('../../header.php');
require( CONTROLADOR.'ventas.php');
$datos = new Ventas();
?>
<br>
<div class="container-fluid">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item">
				<a href="">Inicio</a>
			</li>
			<li class="breadcrumb-item active" aria-current="page">Vendedores</li>
		</ol>
	</nav>
	<?php
	if($_COOKIE['cod_canal'] == 9999){ 
		?>
		<div class="row">
			<div class="col-sm-2">
				<div class="form-group">
					<label for="canal">Seleccione el canal</label>
					<form method='POST' action="">
						<select  id="canal" name="canal" class="form-control form-control-sm" onchange="this.form.submit();">
							<option value="9999">Todos...</option>
							<?php 
							foreach ($datos->lista_canales() as $key) {
								echo (isset($_POST['canal']) && $_POST['canal'] == $key['cod_canal']) ? 
								"<option value='{$key['cod_canal']}' selected>{$key['canal']}</option>" : "<option value='{$key['cod_canal']}'>{$key['canal']}</option>";
							}
							?>
						</select>
					</form>
				</div>
			</div>	
		</div>
		<?php 
	}
	?>
	<div class="table-responsive">
		<small>	
			<table class="table table-sm table-borderless table-hover">
				<thead class="bg-warning">
					<tr>
						<th class="text-left">Vendedor</th>
						<th>Canal</th>
						<th class="text-center" nowrap>Tramo/Grupo</th>
						<th class="text-center" nowrap>Carpetas Dia</th>
						<th class="text-center" nowrap>Acumulado Dia</th>
						<th class="text-center" nowrap>Meta Dia</th>
						<th class="text-center" nowrap>% Proy Dia</th>	
						<th class="text-center" nowrap>Acumulado Mes</th>
						<th class="text-center" nowrap>Meta Mes</th>
						<th class="text-center" nowrap>Proyeccion Mes</th>
						<th class="text-center" nowrap>% Proy Mes</th>
						<th class="text-center" nowrap>% Aprobacion</th>
					</tr>
				</thead>
				<tbody  class="table-light table-hover">
					<?php
					$data = $datos->lista_vendedores();	
					foreach ($data as $key){
						if(!isset($_POST['canal']) || (isset($_POST['canal']) && ($_POST['canal'] == $key['cod_canal'] || $_POST['canal'] == 9999))){
							$alcance_dia = ($key["meta_dia"] != 0) ? $key["neto_dia"]/$key["meta_dia"]*100 : 0;						
							?>
							<tr class="grilla">
								<td class="text-left" nowrap><?= $key["cod_vend"].' '.$key["vendedor"];?></td>
								<td class="text-left" nowrap><?= trim($key["canal"]);?></td>
								<td class="text-center" nowrap><?= $key["tramo"].' '.$key["grupo"];?></td>
								<td class="text-right" nowrap>5/<?= number_format($key["carpeta_dia"],0,',','.');?></td>
								<td class="text-right" nowrap><?= number_format($key["neto_dia"],0,',','.');?></td>
								<td class="text-right" nowrap><?= number_format($key["meta_dia"],0,',','.');?></td>
								<td class="text-right" nowrap><?= number_format($alcance_dia,1,',','.'); ?>%</td>
								<td class="text-right" nowrap><?= number_format($key["neto"],0,',','.');?></td>
								<?php 
								if($key["meta"]==0){
									echo '<td class="text-right" nowrap data-toggle="modal" data-target="#MetaModal" onclick="meta_cargar('.$key["cod_vend"].')">';

								}else{
									echo '<td class="text-right" nowrap>';

								}
								echo number_format($key["meta"],0,',','.');
								?>
							</td>
							<td class="text-right" nowrap><?= number_format($key["venta_proyectada"],0,',','.'); ?></td>
							<td class="text-right" nowrap><?= number_format($key["proyeccion"],1,',','.'); ?>%</td>
							<td class="text-right" nowrap><?= number_format($key["aprobacion"],1,',','.'); ?>%</td>
						</tr>
						<?Php
					}
				}
				?>
			</tbody>
		</table>
	</small>

	<!-- Modal -->
	<div class="modal fade" id="MetaModal" data-backdrop="static" tabindex="-1" aria-labelledby="MetaModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="MetaModalLabel">Cargar Meta</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" id="modal-body">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id="meta_guardar" onclick="meta_guardar()" disabled="disabled">Guardar</button>
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>

				</div>
			</div>
		</div>
	</div>
</div>		
<?php
require('../../footer.php');
?>
<script>

	function meta_cargar(cod_vend){
		$.ajax({
			type:'POST',
			url:"vendedores_meta.php",
			data:{
				cod_vend : cod_vend
			},
			success:function(resp){
				$("#modal-body").html(resp);	
			}
		});
	}

	function meta_guardar(){
		var cod_vend 	= $('#cod_vend').val();
		var meta_prod 	= parseInt($('#meta_prod').val());
		var meta_moto 	= parseInt($('#meta_moto').val());
		var meta_salud 	= parseInt($('#meta_salud').val());	
		var meta_total  = (meta_salud+meta_moto+meta_prod);
		$.ajax({
			type:'POST',
			url:"vendedores_meta.php",
			data:{
				cod_vend   : cod_vend,
				meta_prod  : meta_prod,
				meta_moto  : meta_moto,
				meta_salud : meta_salud,
				meta_total : meta_total
			},
			success:function(resp){
				location.reload();	
			}
		});
	}	
</script>