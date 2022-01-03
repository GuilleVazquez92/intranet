<?php 
require('../../header.php');
require( CONTROLADOR . 'pagares.php');
$data = new PAGARES();
$data->entidad = $_COOKIE['id'];
$datos_01 = $data->dash_01();
$datos_02 = $data->dash_02();
$datos_03 = $data->dash_03();
$datos_05 = $data->dash_05();
$datos_06 = $data->dash_06();
?>
<style>
	.centrar {
		display: flex;
		flex-flow: row wrap;
		justify-content: center;
		align-items: center;
		text-align: center;
	}

	#chart_div {
		display: inline-block;
		margin: 0 auto;
	}
	#chart_div1 {
		display: inline-block;
		margin: 0 auto;
	}

</style>
<div class="container">
	<br/>
	<div class="jumbotron">
		<h1 class="display-4"><?= $datos_01['descripcion'];?></h1>

		<div class="row">	
			<div class="col-sm-6">
				<div class="card">
					<div class="card-body text-center" >
						<h5 class="card-title">TOTAL CARTERA GLOBAL</h5>
						<div id="chart_div1"></div>
						<p class="card-text">Bruto de la Cartera: Gs.<?= number_format($datos_01['bruto'],0,',','.');?>
						<br>Saldo de Cartera: Gs.<?= number_format($datos_01['saldo'],0,',','.');?></p>
					</div>
				</div>
			</div>

			<div class="col-sm-6">
				<div class="card">
					<div class="card-body text-center" >
						<h5 class="card-title">ALCANCE DEL MES</h5>
						<div id="chart_div"></div>
						<p class="card-text">Objetivo: Gs.<?= number_format($datos_05[0],0,',','.');?>
						<br>Abonado: Gs.<?= number_format($datos_05[1],0,',','.');?></p>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6">
				<div class="card">
					<div class="card-body">
						<h5 class="card-title">RESUMEN DE LOTES</h5>

						<table class="table table-sm table-borderless">
							<tr>
								<th><p class="card-text">Cantidad de Lotes</p></th>
								<td class="text-right"><?= $datos_01['cant_lote'];?></td>
							</tr>
							<tr>	
								<th><p class="card-text">Cantidad de Operaciones</p></th>
								<td class="text-right"><?= $datos_01['cant_oper'];?></td>
							</tr>

						</table>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="card">
					<div class="card-body">
						<h5 class="card-title text-center">COBRANZA DEL MES BRUTA</h5>

						<table class="table table-sm table-borderless">
							<tr>
								<th><p class="card-text">Cantidad Operaciones</p></th>
								<td class="text-right"><?= $datos_06[0];?></td>
							</tr>
							<tr>						
								<th><p class="card-text">Total de Cobrado</p></th>
								<td class="text-right"><?= number_format($datos_06[1],0,',','.');?></td>
							</tr>
						</table>
					</div>
				</div>
			</div>			
		</div>
		<div class="row">
			<div class="col-sm-6">
				<div class="card">
					<div class="card-body">
						<h5 class="card-title">CUOTAS A VENCER A 7 DIAS</h5>
						<table class="table table-sm table-borderless">
							<tr>
								<th><p class="card-text">Cantidad de Operaciones</p></th>
								<td class="text-right"><?= $datos_03[0];?></td>
							</tr>
							<tr>	
								<th><p class="card-text">Monto a cobrar</p></th>
								<td class="text-right"><?= number_format($datos_03[1],0,',','.');?></td>
							</tr>
						</table>
					</div>
				</div>
			</div>

			<div class="col-sm-6">
				<div class="card">
					<div class="card-body">
						<h5 class="card-title">PAGOS PENDIENTES</h5>
						<table class="table table-sm table-borderless">
							<tr>
								<th><p class="card-text">Cantidad de Operaciones</p></th>
								<td class="text-right"><?= $datos_02[0];?></td>
							</tr>
							<tr>	
								<th><p class="card-text">Monto pendiente</p></th>
								<td class="text-right"><?= number_format($datos_02[1],0,',','.');?></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>

<!--	
	<p class="lead">This is a simple hero unit, a simple jumbotron-style component for calling extra attention to featured content or information.</p>
	<hr class="my-4">
	<p>It uses utility classes for typography and spacing to space content out within the larger container.</p>
	<a class="btn btn-primary btn-lg" href="#" role="button">Learn more</a>
-->
</div>
</div>

<?php
require('../../footer.php');
?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
	google.charts.load('current', {'packages':['gauge']});
	google.charts.setOnLoadCallback(drawChart);

	function drawChart() {

		var data = google.visualization.arrayToDataTable([
			['Label', 'Value'],
			['%Mes', 0]
			]);

		var data1 = google.visualization.arrayToDataTable([
			['Label', 'Value'],
			['%Global', 0]
			]);

		var options = {
			width: 180, height: 180,
			redFrom: 0, redTo: 80,
			yellowFrom:80, yellowTo: 90,
			greenFrom:90, greenTo: 100,
			minorTicks: 5,
			animation:{duration:2000}
		};

		var chart = new google.visualization.Gauge(document.getElementById('chart_div'));
		var chart1 = new google.visualization.Gauge(document.getElementById('chart_div1'));

		chart.draw(data, options);
		chart1.draw(data1, options);

		setInterval(function() {
			data.setValue(0, 1, <?= $datos_05[2];?>);
			chart.draw(data, options);
		}, 1000);

		setInterval(function() {
			data1.setValue(0, 1, <?= number_format(100-($datos_01['saldo']/$datos_01['bruto']*100),2,'.',',');?>);
			chart1.draw(data1, options);
		}, 1000);

	}
</script>
