<?php 
//	error_reporting(E_ALL);
//	ini_set('display_errors', '1');

	require('../../header.php');
	require( CONTROLADOR . 'vendedores.php');

	$vendedor = new Vendedores();
	$vendedor->vendedor = $_COOKIE['usuario'];

	$filtro = "";

	if(isset($_SESSION['filtro']) || isset($_GET['filtro'])){

		//echo $_SESSION['filtro'];
		$filtro = $vendedor->filtro = (isset($_GET['filtro'])) ? $_GET['filtro'] : $_SESSION['filtro'];
	}
	
	$cantidad = $vendedor->cant_cartera();

	if(isset($_POST['documento']) && strlen($_POST['documento'])>1){

		$vendedor->documento = $_POST['documento'];
		$buscar = $vendedor->buscar();
		$_POST['documento'] = '';
	}

	unset($_SESSION);	
?>
<!--
				$result[$i]['fecha_proximo']= $row['fecha_proximo'];
-->
	<br>
	<div class="container">
		<form action="cartera.php" method="POST" class="form-inline">
			<div class="input-group input-group-sm mb-3">
	  			<label for="documento" class="sr-only">Documento:</label> 
			  	<input type="text" name="documento" id="documento" class="form-control" aria-label="Small" aria-describedby="inputGroup-sizing-sm" placeholder="Ingrese Documento">
			  <div class="input-group-append">
			    <span class="input-group-text" id="inputGroup-sizing-sm">
			    	<button type="submit" class="btn btn-transparent btn-sm">Buscar</button>
			    </span>
			  </div>
			</div>
		</form>
	<!--</div>-->

		<div class="navbar navbar-expand-lg">
			<div class="nav-item">
				<form action="cartera.php" method="GET" class="form-inline">
					<button name="filtro" class="btn btn-sm btn-info" type="submit" value="HOY">
						<small class="form-text text-light">HOY(<?= $cantidad[0]['cantidad'] ;?>)</small></button>
				</form>			
			</div>
					<div class="nav-item">
				<form action="cartera.php" method="GET" class="form-inline">
					<button name="filtro" class="btn btn-sm btn-info" type="submit" value="AYER">
						<small class="form-text text-light">AYER(<?= $cantidad[1]['cantidad'] ;?>)</small></button>
				</form>			
			</div>
			<div class="nav-item">
				<form action="cartera.php" method="GET" class="form-inline">
					<button name="filtro" class="btn btn-sm btn-info" type="submit" value="VENCIDOS">
						<small class="form-text text-light">VENC.(<?= $cantidad[2]['cantidad'] ;?>)</small></button>
				</form>			
			</div>
			<div class="nav-item">
				<form action="cartera.php" method="GET" class="form-inline">
					<button name="filtro" class="btn btn-sm btn-info" type="submit" value="GESTIONADOS">
						<small class="form-text text-light">GEST.(<?= $cantidad[3]['cantidad'] ;?>)</small></button>
				</form>			
			</div>
		</div>

<!--<div class="container">-->
	<div style="padding:10px !important">

		<div id="widget">
			<?php
				if(isset($buscar) && count($buscar)>0){
				
					for ($i=0; $i < count($buscar); $i++) {	
				?>
						<div class="<?= $buscar[$i]['estilo'];?>">
							<form action="gestion.php" method="GET">	
								<input 	type="hidden" 
										name="filtro" 
										value="<?= $filtro;?>" />

								<button id="<?= $buscar[$i]['cuenta'];?>"
										name="cuenta" 
										class="btn btn-vinculo" 
										type="submit"
										value="<?= $buscar[$i]['cuenta'];?>">
											<?= $buscar[$i]['cuenta'].' - '.$buscar[$i]['cliente'];?>
								</button>	
								<small class="form-text text-muted">
									<?= ucwords(strtolower($buscar[$i]['direccion']))?><br>
									<b>teléfono : </b><?= $buscar[$i]['telefono1'];?><br>
									<b>teléfono : </b><?= $buscar[$i]['telefono2'];?><br>
									<b>celular : </b><?= $buscar[$i]['celular'];?>
								</small>
							
							</form>
						</div>
				<?php
						}
				}else{
				
					$cartera = $vendedor->cartera();
					for ($i=0; $i < count($cartera); $i++) {	
				?>
						<div class="<?= $cartera[$i]['estilo'];?>">
						<!--<div 	data-index="<?= $cartera[$i]['carro'];?>" 
								data-position="<?= $result[$i]['posicion'];?>" 
								class="<?= $result[$i]['clase'];?>" 
								role="alert"> -->
						
							<form action="gestion.php" method="GET">	
								<input 	type="hidden" 
										name="filtro" 
										value="<?= $filtro;?>" />

								<button id="<?= $cartera[$i]['cuenta'];?>"
										name="cuenta" 
										class="btn btn-vinculo" 
										type="submit"
										value="<?= $cartera[$i]['cuenta'];?>">
											<?= $cartera[$i]['cuenta'].' - '.$cartera[$i]['cliente'];?>
								</button>	
								<small class="form-text text-muted">
									<?= ucwords(strtolower($cartera[$i]['direccion']))?><br>
									<b>teléfono : </b><?= $cartera[$i]['telefono1'];?><br>
									<b>teléfono : </b><?= $cartera[$i]['telefono2'];?><br>
									<b>celular : </b><?= $cartera[$i]['celular'];?>
								</small>
							
							</form>
						</div>
			<?php
					}
				}	
			 ?>
		</div>


	</div>
</div>
<?php require('../../footer.php'); ?>