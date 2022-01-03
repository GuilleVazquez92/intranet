<?php 
//		error_reporting(E_ALL);
//		ini_set('display_errors', '1');
require('../../header.php');
require( CONTROLADOR . 'vendedores.php');
$vendedor = new Vendedores();
$vendedor->vendedor = $_COOKIE['usuario'];
?>
<div class="container">
	<br>
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
			<li class="breadcrumb-item active" aria-current="page">Hoja de Ruta</li>
		</ol>
	</nav>

	<?php
	require('../filtro_fecha.php');
	$gestiones = $vendedor->hoja_ruta($fecha_valor);

	echo '<small class="form-text text-muted">Gestor : '.strtoupper($_COOKIE['usuario']).'</small>';
	echo '<small class="form-text text-muted">Fecha : '.date("d-m-Y", strtotime($fecha_valor)).'</small>';

	?>		
	<small class="form-text text-muted">Cantidad de gestiones : <?= count($gestiones);?></small>
	<br>
	<div class="row">
		<?php
		for ($i=0; $i < count($gestiones); $i++) { 
			?>
			<div class="col-sm-12">
				<div class="card border-secondary mb-3">
					<div class="card-header"><?= $gestiones[$i]['cliente'].' cuenta: '.$gestiones[$i]['cuenta']; ?>
				</div>
				<div class="card-body text-secondary">
					<h6 class="card-title"><?= trim($gestiones[$i]['respuesta'])?></h6>
					<p class="card-text">La hora de la gestión fue a las <?= date("H:i:s", strtotime($gestiones[$i]['fecha']));?> y respondió; <?= $gestiones[$i]['gestion']; ?>, la próxima gestión será el día <?= date("d-m-Y", strtotime($gestiones[$i]['proximo_llamado']));?></p>
				</div>
			</div>
		</div>
		<?php 
	}
	?>
</div>
</div>
<?php
require('../../footer.php'); 
?>