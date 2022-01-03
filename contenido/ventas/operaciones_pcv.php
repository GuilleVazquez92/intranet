<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//var_dump($_COOKIE);
require('../../header.php');
require( CONTROLADOR . 'ventas.php');
$datos = new Ventas();

$_POST['estado'] = 50;
$_GET['seleccion'] = $selected = (!isset($_GET['seleccion'])) ? 999 : $_GET['seleccion'];
?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<br>
<div class="container-fluid">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item">
				<a href="">Inicio</a>
			</li>
			<li class="breadcrumb-item active" aria-current="page">Operaciones</li>
		</ol>
	</nav>
	<?php 
	$resumen  = $datos->resumen_pl();
	?>
	<div class="row">
		<div class="col-sm-3">

			<?php
			if(isset($_COOKIE['cod_canal']) && $_COOKIE['cod_canal']==9999){
				?>
				<div class="row mt-2">
					<div class="col-12">
						<div class="form-group row">
							<label for="canal" class="col-sm-3 col-form-label">Canal:</label>
							<div class="col-sm-9">
								<select id="canal" name='seleccion' class="form-control form-control-sm" onchange="/*this.form.submit();*/">
									<option value=999>Todos...</option>
									<?php
									foreach ($datos->lista_canales() as $key) {
										echo (isset($_POST['canal']) && $_POST['canal'] == $key['cod_canal']) ? 
										"<option value='{$key['cod_canal']}' selected>{$key['canal']}</option>" : "<option value='{$key['cod_canal']}'>{$key['canal']}</option>";
									}
									?>	
								</select>
								<!--<input type="text" name="estado" value="<?= $datos->estado_operacion; ?>" hidden />-->
							</div>
						</div>
					</div>
				</div>
				<?php
			}
			?>
			<div class="row">
				<div class="col-12">
					<form action="" method="GET" class=''>
						<div class="form-group row">
							<label for="motivo" class="col-sm-3 col-form-label">Motivo:</label>
							<div class="col-sm-9">
								<select id="motivo" name='seleccion' class="form-control form-control-sm" onchange="this.form.submit();">
									<option value=999>Todos...</option>
									<?php
									$motivos = $datos->listar_motivo();
									foreach ($motivos as $key) {
										$valor = ($key['id'] == $selected) ? "selected" : "";
										echo "<option value={$key['id']} {$valor}>{$key['motivo']}</option>";	
									}
									?>	
								</select>
							<!--	<input type="text" name="estado" value="<?= $datos->estado_operacion; ?>" hidden /> -->
							</div>
						</div>
					</form>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-6">
					<div class="card text-center">
						<div class="card-body">
							<h5 class="card-title">CONTADO</h5>
							<p class="card-text"><?= $resumen['CONTADO']['cantidad'];?></p>
						</div>
						<div class="card-footer">
							<small class="text-muted">Gs.<?= number_format($resumen['CONTADO']['neto'],0,',','.');?></small>
						</div>
					</div>
				</div>

				<div class="col-lg-6">
					<div class="card text-center">
						<div class="card-body">
							<h5 class="card-title">CREDITO</h5>
							<p class="card-text"><?= $resumen['CREDITO']['cantidad'];?></p>
						</div>
						<div class="card-footer">
							<small class="text-muted">Gs.<?= number_format($resumen['CREDITO']['neto'],0,',','.');?></small>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-sm-3 mt-5">
			<div id="donutchart"></div>	
		</div>

		<div class="col-sm-3">
			
			<div class="table-responsive-sm shadow p-3 mb-5 bg-white rounded">
				<h5>Ventas</h5>	
				<small>
					<table class="table table-sm table-borderless">
						<thead class="table-dark">
							<tr>
								<th>Carpeta</th>
								<th>Cantidad</th>
								<th>Monto Neto</th>
							</tr>
						</thead>
						<tbody class="table-light">
							<?php 

							$ventas_cantidad = 0;
							$ventas_neto 	 = 0;

							for ($i=0; $i <= 4 ; $i++){

								$ventas_cantidad 	+= $resumen['motivos'][$i]['cantidad'];
								$ventas_neto		+= $resumen['motivos'][$i]['neto']

								?>
								<tr>
									<td><?= $resumen['motivos'][$i]['motivo']; ?></td>
									<td align="center"><?= $resumen['motivos'][$i]['cantidad']; ?></td>
									<td align="right"><?= number_format($resumen['motivos'][$i]['neto'],0,',','.'); ?></td>
								</tr>
								<?php 
							} 
							?>
						</tbody>
						<tfoot class="table-dark">
							<tr>
								<td>TOTAL</td>
								<td align="center"><?= $ventas_cantidad; ?></td>
								<td align="right"><?= number_format($ventas_neto,0,',','.'); ?></td>
							</tr>
						</tfoot>	
					</table>
				</small>
			</div>
		
		</div>
		<div class="col-sm-3">
			
			<div class="table-responsive-sm shadow p-3 mb-5 bg-white rounded">
				<h5>Comercialización</h5>
				<small>
					<table class="table table-sm table-borderless">
						<thead class="table-dark">
							<tr>
								<th>Carpeta</th>
								<th>Cantidad</th>
								<th>Monto Neto</th>
							</tr>
						</thead>
						<tbody class="table-light">
							<?php 

							$comer_cantidad = 0;
							$comer_neto 	= 0;

							for ($i=5; $i <= 9 ; $i++) { 
								switch ($i) {
									case 5:
									$etiqueta_color = "class='table-danger'";
									break;
									case 6:
									$etiqueta_color = "class='table-info'";
									break;
									case 7:
									$etiqueta_color = "class='table-warning'";
									break;						
									case 8:
									$etiqueta_color = "class='table-success'";
									break;
									case 9:
									
								}

								$comer_cantidad 	+= $resumen['motivos'][$i]['cantidad'];
								$comer_neto 		+= $resumen['motivos'][$i]['neto']

								?>
								<tr <?=	$etiqueta_color; ?>>
									<td><?= $resumen['motivos'][$i]['motivo']; ?></td>
									<td align="center"><?= $resumen['motivos'][$i]['cantidad']; ?></td>
									<td align="right"><?= number_format($resumen['motivos'][$i]['neto'],0,',','.'); ?></td>
								</tr>
								<?php 
							} 
							?>
						</tbody>
						<tfoot class="table-dark">
							<tr>
								<td>TOTAL</td>
								<td align="center"><?= $comer_cantidad; ?></td>
								<td align="right"><?= number_format($comer_neto,0,',','.'); ?></td>
							</tr>							
						</tfoot>	
					</table>
				</small>
			</div>
		
		</div>
	</div>

	<div class="table-responsive">
		<small>	
			<table class="table table-sm table-borderless table-hover" cellspacing="0" cellpadding="0">
				<thead class="bg-warning">
					<tr>
						<th>Entrada</th>
						<th>Motivo</th>
						
						<th colspan="2">Operación</th>
						<th colspan="2">Forma</th>
						<th colspan="2" class="text-center">Confirmado en:</th>
						<th colspan="2">Agendado</th>
						<th>Cliente</th>
						<th>Vendedor</th>
						<th>MontoNeto</th>
					</tr>
				</thead>
				<tbody  class="table-light table-hover">
					<?php 
					$actual = strtotime (date("d-m-Y H:i:s"));
					$data = $datos->consulta_pl();
					
				 	$count = count($data['data']);
					for ($i=0; $i < $count; $i++) 
					{ 

						if(($_GET['seleccion'] == $data['data'][$i]['cod_motivo'] && $_GET['seleccion'] != 999) || $_GET['seleccion'] == 999){

							switch ($data['data'][$i]['cod_motivo']) {
								case '1':
								$motivo_estilo = "table-warning";
								break;

								case '5':
								$motivo_estilo = "table-success";
								break;

								case '6':
								case '10':
								$motivo_estilo = "table-danger";
								break;

								default:
								$motivo_estilo = "table-default";
								break;
							}

							echo ($data['data'][$i]['estado'] == 5) ? "<tr class='table-success'>" : "<tr>";		
							?>

							<td nowrap="nowrap"><?= date("d-m H:i", strtotime($data['data'][$i]['aprobacion']));?></td>
							<td nowrap="nowrap" class="<?= $motivo_estilo;?>"><?= $data['data'][$i]['motivo'];?></td>
							<td class="text-right" nowrap="nowrap">		
								<?php
								if($_COOKIE['cod_perfil'] == 3 && $data['data'][$i]['cod_motivo'] == 0 ){  
									$semaforo = semaforo(strtotime(str_replace('/', '-', date("d-m-Y H:i:s", strtotime($data['data'][$i]['aprobacion'])))),1);
									?>
									<div class="spinner-grow spinner-grow-sm <?= $semaforo;?>" role="status"></div>	
									<?php	
								}

								if($_COOKIE['cod_perfil'] == 12 && $data['data'][$i]['cod_motivo'] == 9 ){ ?>
									<a title="Tiempo trascurrido: <?= $data['data'][$i]['solicitado_trascurrido'];?>">
										<div class="spinner-grow spinner-grow-sm <?= $data['data'][$i]['solicitado_semaforo'];?>" role="status"></div>
									</a>	
									<?php	
								}
								?>	
							</td>
							<td>
								<a class="consultar_operacion" id="<?= $data['data'][$i]['operacion'];?>" data-toggle="modal" data-target="#Modal"><?= $data['data'][$i]['operacion'];?></a>
							</td>

							<td nowrap="nowrap">
								<span data-id="<?= $data['data'][$i]['operacion'];?>" class="popover_productos">
									<?= $data['data'][$i]['forma'];?>
								</span>	
							</td>
							<td valign="middle">
								<?php if($data['data'][$i]['origen'] == 'WEB'){ ?>	
									<img src="<?= IMAGE .'crown.png';?>" alt="" width="16px" height="16px">
								<?php }	?>
							</td>	
							<td nowrap="nowrap" align="right" <?= (strlen($data['data'][$i]['etiqueta'])>=1)?($data['data'][$i]['etiqueta']=='72hs')?'class="table-warning"':'class="table-primary"':'';?>>
								<input 	type="checkbox" class="marca_confirmado" 
								value="<?= $data['data'][$i]['confirmado']; ?>" 
								<?php 
								if($data['data'][$i]['confirmado'] == 1){
									echo 'checked';
								}else{
									if(strlen($data['data'][$i]['etiqueta'])==0){
										echo 'disabled'; 
									}
								}
								?>
								>
							</td>
							<td nowrap="nowrap" align="left" <?= (strlen($data['data'][$i]['etiqueta'])>=1)?($data['data'][$i]['etiqueta']=='72hs')?'class="table-warning"':'class="table-primary"':'';?>>
								<?= $data['data'][$i]['etiqueta'];?>
							</td>
							<td nowrap="nowrap" align="right">
								<?php if(!is_null($data['data'][$i]['agendamiento'])){?>
									<a title="Agendado para el : <?= date("d-m-Y H:i:s", strtotime($data['data'][$i]['agendamiento']));?>">
										<div class="spinner-grow spinner-grow-sm <?= $data['data'][$i]['agendamiento_semaforo'];?>" role="status"></div>
									</a>
								<?php }?>
							</td>
							<td nowrap="nowrap"><?= $data['data'][$i]['agendamiento_vencimiento'];?></td>
							<td nowrap="nowrap"><?= $data['data'][$i]['cuenta'].' '.$data['data'][$i]['cliente'];?></td>
							<td nowrap="nowrap">
								<a data-toggle="tooltip" data-placement="bottom" title="<?= $data['data'][$i]['canal'];?>"><?= $data['data'][$i]['cod_vend'].' '.$data['data'][$i]['vendedor'];?></a></td>
								<td nowrap="nowrap" align="right"><?= number_format($data['data'][$i]['neto'],0,',','.');?></td>
							</tr>
							<?php 
						}
					}
					;?>
				</tbody>
			</table>
		</small>	
	</div>	
</div>
<br>
<br>

<div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable modal-lg"  role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Detalle Operación</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div id="modal-body" class="modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<?php 
require('../../footer.php');
?>

<script>

	$(document).ready(function(){

		$('.popover_productos').click(function(){
			
			$(this).popover('dispose');
			var id = $(this).attr("data-id");
			var selector = '[data-id='+id+']';

			$.ajax({
				type:'POST',
				url:"operaciones_procesar.php",
				datatype:"text",
				data:{
					operacion : id,
					productos : ''
				},
				success:function(resp){
					$(selector).popover({title: "Productos", content: resp, html: true, placement: "right"});
					$(selector).popover('show');
					setTimeout(popover_close,3000,id);
				}
			});
			function popover_close(id){
				var valor = '[data-id='+id+']';
				$(valor).popover('hide');	
			}
		});


		$(".marca_confirmado").change(function(){

			$(this).prop('checked', false);

			if($(this).val() == 1)
				$(this).prop('checked', true);
		});

		$(".consultar_operacion").click(function(){

			var operacion = this.id;
			$.ajax({
				type:'POST',
				url:"operaciones_detalle.php",
				data:{
					operacion : operacion,
				},
				success:function(resp){
					$("#modal-body").html(resp);	
				}
			});
		});	
	});
</script>	

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
	google.charts.load("current", {packages:["corechart"]});
	google.charts.setOnLoadCallback(drawChart);
	function drawChart() {
		var data = google.visualization.arrayToDataTable([
			['Estado', 'Cantidad'],
			['LLEGANDO', 	<?= $data['agendado']['atrasado'];?>],
			['EN ESPERA', 	<?= $data['agendado']['en_espera'];?>],
			['A TIEMPO',	<?= $data['agendado']['a_tiempo'];?>]
			]);

		var options = {
			title: 'Estado Agendado',
			pieHole: 0.4,
			backgroundColor:'#FCFCF9',
			slices: {
				0: { color: '#DC3912' },
				1: { color: '#FF9900' },
				2: { color: '#109618' }
			},
			titleTextStyle:{
				fontSize: 18,
				color :'#5F5448',
				bold: false
			}
		};
		var chart = new google.visualization.PieChart(document.getElementById('donutchart'));
		chart.draw(data, options);
	}
</script>