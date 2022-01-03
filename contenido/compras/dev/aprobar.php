<?php 
	setcookie("usuario","kdoldan",time()+86400);
	require('../../header.php');
	require( CONTROLADOR . 'compras.php');
	$compras = new Compras();

	if(isset($_POST['update'])){

		foreach ($_POST['positions'] as $position) {
			$index 		 = $position[0];
			$nuevo_orden = $position[1];

			$sql = "UPDATE fsd014 SET bcaux1=$nuevo_orden WHERE baempr=1 AND bcope1=$index";
			$db = $compras->conn();
			$db -> query($sql);
		}
		exit('success');
	}

	if(isset($_POST['posicion'])){
		
			$posicion = $_POST['posicion'];
			$db = $compras->conn();
			$sql = "INSERT INTO public.fsd014pl(bcope1, bcaux0, bcaux1, bcaux2, usuario, bbfecha, bbhora, opcion)
					SELECT operacion,99,1,0,'hugo',current_date,substring(current_time::text,1,8),'' 
					FROM web_operaciones_estado, fsd014 
					WHERE fsd014.bcope1=operacion 
					AND (estado=50 or estado=5)
					AND bcaux1<=$posicion
					and (select bcaux1 from fsd014pl where bcope1=operacion order by (bbfecha||' '||bbhora)::timestamp desc limit 1)=10
					order by case when bcaux1 = 0 then 99 else bcaux1 end  asc ON CONFLICT(bcope1, bcaux0, bcaux1) DO NOTHING;";
			$db -> query($sql);
		//exit('success');
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
			  			<th width="20%" class="text-right">Monto</th>
			  			<th width="15%" class="text-right">Acumulado</th>
			  			<th width="15%" class="text-center">Acción</th>
			  		</tr>
				</table>
			</div>
		</div>

			<div id="widget1">
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
									<td width="20%" class="text-right"><?= number_format($datos[$i]['neto'],0,',','.'); ?></td>
									<td width="15%" class="text-right"><?= number_format($acumulado,0,',','.'); ?></td>
									<td width="15%" class="text-center">
										<form action="" method="POST">
											<input type="text" name="posicion" value="<?= $datos[$i]['posicion'];?>" hidden>			
											<button type="submit" class="btn btn-primary btn-sm">Aprobar</button>
										</form>
									</td>
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

