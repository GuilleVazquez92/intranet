<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('../../header.php');
require( CONTROLADOR . 'ventas.php');
$data = new Ventas();

$data->canal = (isset($_POST['cod_canal'])) ? $_POST['cod_canal'] : $_COOKIE['cod_canal'];
$datos = $data->resumen_monitor();
$datos_01 = $data->resumen_400();
?>
<style>
	#chart_wrap {
		position: relative;
		padding-bottom: 100%;
		height: 0;
		overflow:hidden;
	}

	#chart_div {
		position: absolute;
		top: 0;
		left: 0;
		width:100%;
		height:220px;
	}
</style>

<br>
<div class="container-fluid">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item">
				<a href="">Inicio</a>
			</li>
			<li class="breadcrumb-item active" aria-current="page">Principal</li>
		</ol>
	</nav>
	<div class="row">
		<div class="col-md-3">
			<?php
			if($_COOKIE['cod_canal']==9999 or $_COOKIE['cod_canal']==14 or $_COOKIE['cod_canal']==22 or $_COOKIE['cod_canal']==18){ 

				?>
				<form method="POST">
					<div class="col-md-12 input-group mb-3">
						<div class="input-group-prepend">
							<label class="input-group-text" for="canales">Canal</label>
						</div>
						<select class="custom-select" id="cod_canal" name="cod_canal" onchange="this.form.submit();">
							<?php 
							if($_COOKIE['cod_canal']==9999){
								echo "<option value=9999>GLOBAL</option>";		
							}
							$canales = $data->lista_canales();
							foreach ($canales as $key){
								$selected = ($key['cod_canal'] == $data->canal) ? "selected":"";

								if($_COOKIE['cod_canal'] == 14 && ($key['cod_canal'] == 14 || $key['cod_canal'] == 15 )){
									?>	
									<option value="<?= $key['cod_canal'];?>" <?= $selected;?>><?= $key['canal'];?></option>
									<?php
								}

								if($_COOKIE['cod_canal'] == 22 && ($key['cod_canal'] == 22 || $key['cod_canal'] == 13 )){
									?>	
									<option value="<?= $key['cod_canal'];?>" <?= $selected;?>><?= $key['canal'];?></option>
									<?php
								}

								if($_COOKIE['cod_canal'] == 18 && ($key['cod_canal'] == 18 || $key['cod_canal'] == 16 )){
									?>	
									<option value="<?= $key['cod_canal'];?>" <?= $selected;?>><?= $key['canal'];?></option>
									<?php
								}		

								
								if($_COOKIE['cod_canal'] == 9999){
									?>
									<option value="<?= $key['cod_canal'];?>" <?= $selected;?>><?= $key['canal'];?></option>
									<?php 	
								}
							}
							?>
						</select>
					</div>
				</form>
				<?php
			}
			?>
		</div>
	</div>

	<div class="row">
		<div class="col-md-6">
			<div class="row">
				<div class="col-md-6 my-2">
					<div class="card">
						<div class="card-header">
							<h4>HOY</h4>
						</div>
						<div class="card-body">
							<h5 class="card-title mb-0">Gs.<?= number_format($datos['venta_dia'],0,',','.');?></h5>
							<p class="card-text"><small>Acumulado de hoy</small></p>
							<h5 class="card-title mb-0">Gs.<?= number_format($datos['meta_dia'],0,',','.');?></h5>
							<p class="card-text"><small>Objetivo hoy</small></p>
						</div>
					</div>
				</div>

				<div class="col-md-6 my-2">
					<div class="card">
						<div class="card-header">
							<h4>MES</h4>
						</div>
						<div class="card-body">
							<h5 class="card-title mb-0">Gs.<?= number_format($datos['venta_mes'],0,',','.');?></h5>
							<p class="card-text"><small>Acumulado del mes</small></p>
							<h5 class="card-title mb-0">Gs.<?= number_format($datos['meta'],0,',','.');?></h5>
							<p class="card-text"><small>Objetivo mes</small></p>
						</div>
					</div>
				</div>

				<div class="col-md-6 my-2">
					<div class="card">
						<div class="card-header">
							<h4>Proyecciones</h4>
						</div>
						<div class="card-body">
							<h5 class="card-title mb-0">Gs.<?= number_format($datos['proximo_objetivo'],0,',','.');?></h5>
							<p class="card-text"><small>Proximo objetivo del dia</small></p>
							<h5 class="card-title mb-0">Gs.<?= number_format($datos['venta_proyectada'],0,',','.');?></h5>
							<p class="card-text"><small>Proyección del mes</small></p>
						</div>
					</div>
				</div>

				<div class="col-md-6 my-2">
					<div class="card">
						<div class="card-header">
							<h4>Carpetas</h4>
						</div>
						<div class="card-body">
							<h5 class="card-title mb-0"><?= number_format($datos['carpetas_hoy'],0,',','.');?></h5>
							<p class="card-text"><small>Presentadas hoy</small></p>
							<h5 class="card-title mb-0"><?= number_format($datos['por_aprobados'],2,',','.');?>%</h5>
							<p class="card-text"><small>Aprobación del mes</small></p>
						</div>
					</div>
				</div>
			</div>			
		</div>

		<div class="col-md-3">
			<div class="row">
				<div id="grafico_1" style="display: block !important; margin: 0 auto !important;">
				</div>
			</div>
			<div class="row">
				<div id="grafico_2" style="display: block !important; margin: 0 auto !important;">
				</div>
			</div>
		</div>

		<div class="col-md-3">
			<div class="card">
				<ul class="list-group list-group-flush">
					<?php 
					foreach ($datos['estados'] as $key) {
						if ($key['estado'] == 0)
						{
							$key['estado'] = 999;
						}
						?>
						<li class="list-group-item py-2 tipo_operacion" data-toggle="modal" data-target="#modal-operaciones" data-canal="<?= $data->canal;?>" data-id="<?= $key['estado'];?>">
							<div class="row">
								<div class="col-md-4 text-truncate px-1"><small><?= $key['estado_descripcion'];?></small></div>
								<div class="col-md-3 text-right px-1"><small><?= number_format($key['estado_cantidad'],0,',','.');?></small></div>
								<div class="col-md-5 text-right px-1"><small><?= number_format($key['estado_neto'],0,',','.');?></small></div>
							</div>
						</li>
						<?php
					}
					?>
				</ul>
			</div>
		</div>
	</div>

	<div class="row">				
		<div class="col-md-3 my-2">
			<div class="card">
				<div class="card-header">
					<h4>Ventas Contado</h4>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<h6 class="card-title mb-0 mx-0">Gs.<?= number_format($datos['contado_dia'],0,',','.');?></h6>
							<p class="card-text"><small>Total día</small></p>
							<h6 class="card-title mb-0"><?= number_format($datos['contado_dia_participacion'],1,',','.');?>%</h6>
							<p class="card-text"><small>Participación</small></p>
						</div>
						<div class="col-md-6">
							<h6 class="card-title mb-0 mx-0">Gs.<?= number_format($datos['contado_mes'],0,',','.');?></h6>
							<p class="card-text"><small>Total mes</small></p>
							<h6 class="card-title mb-0"><?= number_format($datos['contado_mes_participacion'],1,',','.');?>%</h6>
							<p class="card-text"><small>Participación</small></p>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-3 my-2">
			<div class="card">
				<div class="card-header">
					<h4>Salud & Belleza</h4>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="card-body">
							<h6 class="card-title mb-0">Gs.<?= number_format($datos['salud_dia'],0,',','.');?></h6>
							<p class="card-text"><small>Total día</small></p>
							<h6 class="card-title mb-0"><?= number_format($datos['salud_dia_participacion'],1,',','.');?>%</h6>
							<p class="card-text"><small>Participación</small></p>
						</div>
					</div>
					<div class="col-md-6">
						<div class="card-body">
							<h6 class="card-title mb-0">Gs.<?= number_format($datos['salud_mes'],0,',','.');?></h6>
							<p class="card-text"><small>Total mes</small></p>
							<h6 class="card-title mb-0"><?= number_format($datos['salud_mes_participacion'],1,',','.');?>%</h6>
							<p class="card-text"><small>Participación</small></p>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div id="chart_wrap1" class="col-md-6 my-2">
			<?php
			
			$grafico = array();
			$grafico  = json_encode($datos['grafico']);
			
			$facturan_acumulada = "['Dia', 'Facturación'],";
			foreach ($datos['grafico'] as $key) {
				$facturan_acumulada .='["'.$key["dia_mes"].'",'.$key["dia_neto"].'],'; 
			}
			?>
			<div id="chart_div">
			</div>
		</div>
	</div>




	<div class="row">
		<div class="col-md-6">
			<div class="row">
				<div class="col-md-6 my-2">
					<div class="card">
						<div class="card-header">
							<h4>ENTREGA INICIAL 400</h4>
						</div>
						<div class="card-body">
							<h5 class="card-title mb-0">Gs.<?= number_format($datos_01['dia'],0,',','.');?></h5>
							<p class="card-text"><small>Acumulado de hoy</small></p>
							<h5 class="card-title mb-0">Gs.<?= number_format($datos_01['mes'],0,',','.');?></h5>
							<p class="card-text"><small>Acumulado mes</small></p>
						</div>
					</div>
				</div>

				<div class="col-md-6 my-2">
					<div class="card">
						<div class="card-header">
							<h4>ENTREGA INICIAL 401</h4>
						</div>
						<div class="card-body">
							<h5 class="card-title mb-0">Gs.<?= number_format($datos_01['dia_401'],0,',','.');?></h5>
							<p class="card-text"><small>Acumulado de hoy</small></p>
							<h5 class="card-title mb-0">Gs.<?= number_format($datos_01['mes_401'],0,',','.');?></h5>
							<p class="card-text"><small>Acumulado mes</small></p>
						</div>
					</div>
				</div>


		
			</div>			
		
		</div>

</div>


	<!-- Modal -->
	<div class="modal fade" id="modal-operaciones" tabindex="-1" aria-labelledby="modal-operaciones-label" aria-hidden="true">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modal-operaciones-label">Operaciones</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div id="carpetas_body" class="modal-body">
					
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					<!--<button type="button" class="btn btn-primary">Save changes</button> -->
				</div>
			</div>
		</div>
	</div>
<?php
require('../../footer.php');
?>

<script>

	$('.tipo_operacion').click(function(){

		var canal 	= $(this).data("canal");
		var tipo 	= $(this).data("id");

			$.ajax({
				type:'POST',
				url:"carpetas_detalles.php",
				datatype:"text",
				data:{
					canal : canal,
					tipo  : tipo
				},success:function(resp){
					$("#carpetas_body").html(resp);
					
				}
			});	
		})

	</script>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script type="text/javascript">

		google.charts.load('current', {'packages':['gauge']});
		google.charts.setOnLoadCallback(drawChart);

		function drawChart() {

			var data = google.visualization.arrayToDataTable([
				['Label', 'Value'],
				['% Hoy', <?= $datos['proyeccion_hoy'];?>]
				]);

			var data2 = google.visualization.arrayToDataTable([
				['Label', 'Value'],
				['% Mes', <?= $datos['proyeccion_mes'];?>]
				]);


			var options = {
				width: 210, 
				height: 210,
				redFrom: 0, 
				redTo: 80,
				yellowFrom:80, 
				yellowTo: 90,
				greenFrom:90, 
				greenTo: 100,
				minorTicks: 5
			};

			var chart = new google.visualization.Gauge(document.getElementById('grafico_1'));
			var chart2 = new google.visualization.Gauge(document.getElementById('grafico_2'));
			chart.draw(data, options);
			chart2.draw(data2, options);
        /*  
          setInterval(function() {
            data.setValue(0, 1, 15);
            chart.draw(data, options);
          }, 10000);
          */

      }

      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawVisualization);

      function drawVisualization() {

      	var data1  = google.visualization.arrayToDataTable([<?= $facturan_acumulada;?>]); 
      	var options = {
      		title : 'Venta por dia',
      		width: '90%', 
      		height: 210,
      		curveType: 'function',
      		animation:{startup: true,},
      		legend:{
      			position: 'bottom'
      		},
      		//hAxis: {textPosition : 'none',},
      	};

      	var chart3 = new google.visualization.LineChart(document.getElementById('chart_div'));
      	chart3.draw(data1, options);
      }
  </script>  