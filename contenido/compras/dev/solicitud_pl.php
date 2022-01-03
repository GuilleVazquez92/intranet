<?php 
	setcookie("usuario","kdoldan",time()+86400);
	require('../../header.php');
	require( CONTROLADOR . 'compras.php');
	$compras = new Compras();

	if(isset($_POST['update'])){
		echo "entra";
		foreach ($_POST['positions'] as $position) {
			$index 		 = $position[0];
			$nuevo_orden = $position[1];

			echo $sql = "UPDATE fsd014 SET bcaux1=$nuevo_orden WHERE baempr=1 AND bcope1=$index";
			$db = $compras->conn();
			$db -> query($sql);
		}
		exit('success');
	}
?>
	<style>
		.btn-circle {
			  width: 20px;
			  height: 20px;
			  text-align: center;
			  padding: 1px 0;
			  font-size: 12px;
			  line-height: 1.428571429;
			  border-radius: 15px;
			}
	</style>
	<br>
	<div class="container-fluid">
		<nav aria-label="breadcrumb">
		  <ol class="breadcrumb">
		  <!--  <li class="breadcrumb-item"><a href="index.php">Inicio</a></li> -->
		    <li class="breadcrumb-item active" aria-current="page">Solicitados a Compras</li>
		  </ol>
		</nav>

	<div class="container">
		<br>
	  	<div class="card">
	  		<div class="card-body">
			  	<table width="100%">
			  		<tr>
			  			<th width="15%">Operación</th>
			  			<th width="35%">Cliente</th>
			  			<th width="25%" class="text-right">Monto</th>
			  			<th width="25%" class="text-right">Acumulado</th>
			  		</tr>
				</table>
			</div>
		</div>

			<div id="widget">
				<?php
					$acumulado = 0;
					$datos = $compras->orden_consultar();
					for ($i=0; $i < count($datos); $i++) {
					$acumulado += $datos[$i]['neto'];	
				?>
					<div 	data-index="<?= $datos[$i]['operacion'];?>" 
							data-position="<?= $datos[$i]['posicion'];?>" 
							class="card" 
							role="alert">
						<div class="card-body">
						  	<table width="100%">
								<tr>
									<td width="15%"><?= $datos[$i]['operacion']; ?></td>
									<td width="35%"><?= $datos[$i]['cuenta'].' - '.$datos[$i]['cliente'];?></td>
									<td width="25%" class="text-right"><?= number_format($datos[$i]['neto'],0,',','.'); ?></td>
									<td width="25%" class="text-right"><?= number_format($acumulado,0,',','.'); ?></td>
								</tr>
						  	</table>	
					  	</div>		
					</div>
				<?php
					}	
				 ?>
			</div>
	</div>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
	<br>
<?php 
	require('footer.php'); 
?>

