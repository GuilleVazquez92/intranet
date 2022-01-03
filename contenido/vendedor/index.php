<?php 
	require('../../header.php');
	require( CONTROLADOR . 'vendedores.php');
	$vendedor = new Vendedores();
	$vendedor->vendedor = $_COOKIE['usuario'];
	$resumen = $vendedor->resumen();

?>
<div class="container">
	<br>
	<div class="row">
	
		<div class="col-md-4 mb-4">
			<?php 

				$file_image = APPLICATION."image/vendedor/".$resumen['cod_vendedor'].".jpg";
				if (file_exists($file_image)) {
			?>		
					<img src="<?= IMAGE ?>vendedor/<?= $resumen['cod_vendedor'] ?>.jpg" class="card-img rounded-circle" alt="...">
			<?php
				}else{
					if ($resumen['sexo'] == 'M') {
			?>			
						<img src="<?= IMAGE ?>icono_persona_m.png" class="card-img" alt="...">
			<?php
					}else{
			?>			
						<img src="<?= IMAGE ?>icono_persona_f.png" class="card-img" alt="...">
			<?php
					}
				}
			?>
	    </div>

	    <div class="col-md-4 mb-4">
	      <div class="card-body">
	        <h5 class="card-title text-center"><?= $resumen['nombre'];?></h5>
	        <p class="card-text text-center"><small class="text-muted">
	        	<?php  
	 
	        		$date = date_create($resumen['fecha_inicio']);
	        		$mes  = date_format($date,'n');
					$date = date_format($date, 'j # Y');
					
					$date = str_replace('*', ' del ', $date);
					switch ($mes) {
						case 1:
							$valor  = "de Enero del ";
							break;
						case 2:
							$valor  = "de Febrero del ";
						break;
						case 3:
							$valor  = "de Marzo del ";
						break;		
						case 4:
							$valor  = "de Abril del ";
						break;
						case 5:
							$valor  = "de Mayo del ";
						break;
						case 6:
							$valor  = "de Junio del ";
						break;
						case 7:
							$valor  = "de Julio del ";
						break;
						case 8:
							$valor  = "de Agosto del ";
						break;
						case 9:
							$valor  = "de Setiembre del ";
						break;																								
						case 10:
							$valor  = "de Octubre del ";
						break;
						case 11:
							$valor  = "de Noviembre del ";
						break;
						case 12:
							$valor  = "de Diciembre del ";
						break;						
					}
					$date = str_replace('#', $valor , $date);
					echo "Su código de vendedor es ".$resumen['cod_vendedor']."<br> desde el ".$date;
					?></small></p>
	        <p class="card-text text-center">
	        	Tipo de vendedor <?= $resumen['grupo']?><br>
	       		Categoría de <?= $resumen['categoria']?><br>
	       		Canal de ventas de <?= $resumen['canal']?><br>
	        	Experiencia de TRAMO <?= $resumen['tramo']?></p>
	      </div>
	    </div>

		<div class="col-md-4 mb-4">
	 		<div id="chart_div" style="width: 100%; height:100%;"></div>
	    </div>	

		<div class="card col-sm-4 mb-3 text-center">
			<div class="card-header">
				META
			</div>
			<ul class="list-group list-group-flush">
				<li class="list-group-item"><?= number_format($resumen['meta'],0,',','.');?></li>
			</ul>
		</div>

		<div class="card col-sm-4 mb-3 text-center">
			<div class="card-header">
				VENTA
			</div>
			<ul class="list-group list-group-flush">
				<li class="list-group-item"><?= number_format($resumen['venta'],0,',','.');?></li>
			</ul>
		</div>

		<div class="card col-sm-4 mb-3 text-center">
			<div class="card-header">
				PROYECCION
			</div>
			<ul class="list-group list-group-flush">
				<li class="list-group-item"><?= number_format($resumen['venta_proyectada'],0,',','.');?></li>
			</ul>
		</div>

	  <div class="col-sm-6 mb-3">
	    <div class="card">
	      <div class="card-body">
	        <h5 class="card-title text-center">Gs.<?= number_format($resumen['comision_estimada'],0,',','.');?></h5>
	        <p class="card-text text-center">Comisión Estimada</p>
	        <div class="text-center"><a href="#" class="btn btn-primary">Ver el resumen</a></div>
	      	<p class="card-text text-center"><small class="text-danger">La comisión estimada puede variar segun el estado de las operaciones</small></p>
	      </div>
	    </div>
	  </div>

	  <div class="col-sm-6 mb-3">
	    <div class="card">
	      <div class="card-body">
	        <h5 class="card-title text-center">Gs.<?= number_format($resumen['comision_pasada'],0,',','.');?></h5>
	        <p class="card-text text-center">Comisión Pasada</p>
	        <div class="text-center"><a href="#" class="btn btn-primary">Ver el resumen</a></div>
	      	<p class="card-text text-center"><small class="text-danger">La comisión pasada puede variar hasta el cierre de operaciones del mes anterior</small></p>
	      </div>
	    </div>
	  </div>

		<div class="card col-sm-4 mb-3 text-center">
			<div class="card-header">
				DIAS HABILITADOS
			</div>
			<ul class="list-group list-group-flush">
				<li class="list-group-item"><?= number_format($resumen['habilitado'],1,',','.');?></li>
			</ul>
		</div>

		<div class="card col-sm-4 mb-3 text-center">
			<div class="card-header">
				DIAS TRASCURRIDO
			</div>
			<ul class="list-group list-group-flush">
				<li class="list-group-item"><?= number_format($resumen['trascurrido'],1,',','.');?></li>
			</ul>
		</div>		

		<div class="card col-sm-4 mb-3 text-center">
			<div class="card-header">
				DIAS FALTANTES
			</div>
			<ul class="list-group list-group-flush">
				<li class="list-group-item"><?= number_format($resumen['falta'],1,',','.');?></li>
			</ul>
		</div>


	</div>
</div>
<?php require('../../footer.php'); ?>
   <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
   <script type="text/javascript">
     
      google.charts.load('current', {'packages':['gauge']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {

        var data = google.visualization.arrayToDataTable([
          ['Label', 'Value'],
          ['% Ventas', <?=$resumen['proyeccion'];?>]
        ]);

        var options = {
          width: 330, height: 330,
          redFrom: 0, redTo: 80,
          yellowFrom:80, yellowTo: 90,
          greenFrom:90, greenTo: 100,
          minorTicks: 5
        };

        var chart = new google.visualization.Gauge(document.getElementById('chart_div'));
        chart.draw(data, options);

        /*  
          setInterval(function() {
            data.setValue(0, 1, 15);
            chart.draw(data, options);
          }, 10000);
        */

      }
    </script>