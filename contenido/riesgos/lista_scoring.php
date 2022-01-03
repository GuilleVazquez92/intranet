<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');

require('../../header.php');
require( CONTROLADOR . 'riesgos.php');
$data = new Riesgos();
?>

<br>
<div class="container-fluid">

	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item">
				<a href="index.php">Inicio</a>
			</li>
			<li class="breadcrumb-item active" aria-current="page">Clientes Scoring</li>
		</ol>
	</nav>

	<div class="table-responsive">
		<table class="table table-hover table-sm">
			<thead class="table-warning">
				<th>CUENTA</th>
				<th>DOCUMENTO</th>
				<th>CLIENTE</th>
				<th class="text-center">ORIGEN</th>
				<th>TIPO</th>
				<th>USUARIO</th>
				<th>AVANCE</th>
			</thead>
			<tbody>
				<?php 
				$informacion = array();

				if($_COOKIE['cod_perfil'] == 15) 
					$_COOKIE['cod_perfil'] = 32;

				if($_COOKIE['cod_perfil'] == 32){
					$informacion = $data->scoring_analisis();
				}	

				if($_COOKIE['cod_perfil'] == 33){
					$informacion = $data->scoring_verificacion();
				}	

				foreach ($informacion as $datos) {
					?>			
					<tr>
						<td><?= $datos['cuenta'];?></td>
						<td><?= $datos['documento'];?></td>
						<td>
							<form method="POST" action="scoring.php">
								<input type="text" value="<?= $datos['cuenta'];?>" id="cuenta" name="cuenta" hidden>
								<input type="text" value="<?= $datos['tipo_solicitud'];?>" id="tipo_solicitud" name="tipo_solicitud" hidden>
								<button type="submit" class="btn btn-transparent btn-sm btn-block text-left"><?= $datos['cliente'];?></button>	
							</form>
						</td>
						<td class="text-center">
							<?php 
								if($datos['origen'] == 'WEB'){ ?>	
									<img src="<?= IMAGE .'crown.png';?>" alt="" width="16px" height="16px" title="Cliente WEB">
							<?php }	

								if($datos['origen'] == 'FUNCIONARIO'){ ?>	
									<img src="<?= IMAGE .'person.png';?>" alt="" width="16px" height="16px" title="Cliente WEB">
							<?php }	
							?>
						</td>
						<?php
						if($datos['tipo_solicitud']=='AMPLIACION DE LINEA'){
							?>
							<td class="bg-danger text-white">
								<div class="spinner-grow text-warning spinner-grow-sm" role="status"></div>
								<?= $datos['tipo_solicitud'];?>
							</td>
							<?php		
						}else{
							?>
							<td><?= $datos['tipo_solicitud'];?></td>
							<?php		
						} 
						?>	
						<td><?= $datos['usuario'];?></td>
						<td>
							<div class="progress">
								<div class="progress-bar" role="progressbar" style="width: <?= $datos['avance'];?>%;" aria-valuenow="<?= $datos['avance'];?>" aria-valuemin="0" aria-valuemax="100"><?= $datos['avance'];?>%</div>
							</div>
						</td>
					</tr>
					<?php
				}
				?>			
			</tbody>
		</table>
	</div>
	<?php
	require('../../footer.php');
	?>