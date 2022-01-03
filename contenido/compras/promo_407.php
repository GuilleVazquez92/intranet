<?Php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require('../../header.php');
require( CONTROLADOR . 'compras.php');
$data = new Compras();
include("conn.php");

?>
<br>
<div class="container-fluid">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
			<li class="breadcrumb-item active" aria-current="page">Promoción 407</li>
		</ol>
	</nav>

	<?Php

#####Agregar

	if (isset($_GET['agregar'])) {

		$codigo = $_GET['codigo'];
		$sql 	= "SELECT epcodi FROM tef005 WHERE epacti='S' AND epcodi=".$codigo;
		$query 	= pg_query($sql);

		if(pg_num_rows($query)==1){

			$sql_1 		= "UPDATE tef005 SET epferi='S' WHERE epcodi=".$codigo;
			$query_1	= pg_query($sql_1);

			if($query_1){

				echo "<div class='mensajeOK'>El codigo ".$codigo." ahora forma parte de la promoción SEMANAL</div>";

			}else{
				echo "<div class='mensajeFAIL'>Sucedio un problema con el codigo ".$codigo.", intentelo mas tarde...</div>";
			}

		}else{

			echo "<div class='mensajeFAIL'>Sucedio un problema con el codigo ".$codigo.", verifique por favor e intenete de nuevo...</div>";

		}
	}

##### Editar

	if (isset($_GET['editar'])){

		if(isset($_GET['contado']) && isset($_GET['credito'])){

			$codigo 	= $_GET['editar'];
			$contado 	= $_GET['contado'];
			$credito 	= $_GET['credito'];

			$sql = "UPDATE promocion SET contado=$contado, credito=$credito WHERE codigo=".$codigo;
			$query 	= pg_query($sql);

			if($query){

				echo "<div class='mensajeOK'>El precio del codigo ".$codigo." ha sido modificado</div>";
			}

		}else{

			$marca = $_GET['editar'];

		}
	}

##### Eliminar

	if(isset($_GET['quitar'])){

		$codigo = $_GET['quitar'];

		$sql 	= "DELETE FROM public.promocion WHERE codigo=".$codigo.";";
		$sql   .= "UPDATE public.tef005 SET epferi='N' WHERE epcodi=".$codigo.";";
		$query 	= pg_query($sql);

		if($query){

			echo "<div class='mensajeOK'>El codigo ".$codigo." ya no forma parte de la promoción SEMANAL</div>";	

		}else{

			echo "<div class='mensajeFAIL'>Sucedio un problema con el codigo ".$codigo.", intentelo mas tarde...</div>";	

		}
	}

##### Activar

	if(isset($_GET['activar'])){

		$codigo 	= $_GET['activar'];
		$estado 	= ($_GET['estado']=='S') ? 'N':'S';
		$estadoRes 	= ($estado=='S') ? 'ACTIVADO':'DESACTIVADO';

		$sql 	= "UPDATE public.promocion SET activo='$estado' WHERE codigo=".$codigo.";";
		$sql   .= "UPDATE public.tef005 SET epferi='$estado' WHERE epcodi=".$codigo.";";

		$query 	= pg_query($sql);

		if($query){

			echo "<div class='mensajeOK'>El codigo ".$codigo." actualmente esta ".$estadoRes."</div>";	

		}else{

			echo "<div class='mensajeFAIL'>Sucedio un problema con el codigo ".$codigo.", intentelo mas tarde...</div>";	

		}
	}

## FIN

############################################################################################################################################################################
#	Tabla

	$sql 	= "SELECT codigo, epdescl descripcion, promocion, contado, credito, vencimiento, activo FROM public.promocion, public.tef005 WHERE epcodi=codigo AND epacti='S' ORDER BY 1;";
	$query 	= pg_query($sql);
	?>

	<form method="GET" action="">
		<input type="number" name="codigo" step="1" min="2000000">	
		<input type="submit" name="agregar" value="Agregar">
	</form>
	<br>
	<table class="table">
		<tr class="grilla_titulo">
			<td>Codigo</td>
			<td>Descripción</td>
			<td>Contado</td>
			<td>Crédito</td>
			<td colspan="3" align="center">Acción</td>
		</tr>
		<?Php
		while ($row = pg_fetch_array($query)) {

			$codigo = $row[0];
			$estado = $row['activo'];

			?>
			<form action="" method="GET">
				<tr class="grilla">	
					<td><?Php echo $codigo; ?></td>
					<td><?Php echo $row['descripcion']; ?></td>
					<?Php 
					if(isset($marca)){

						if ($marca==$codigo) {
							?>			
							<td align="right">
								<input type="number" name="editar" value="<?Php echo $codigo; ?>" hidden />
								<input class="input-montos" type="number" name="contado" value="<?Php echo $row['contado']; ?>">
							</td>
							<td align="right">
								<input class="input-montos" type="number" name="credito" value="<?Php echo $row['credito']; ?>">
							</td>
							<td align="center">
								<input type="image" src="<?= IMAGE.'check_in.png'; ?>" alt="submit" name="editar" title="Aceptar Precio de la Promoción" />
							</td>	
							<td align="center">
								<input type="image" src="<?= IMAGE.'fail.png'; ?>" name="reset" onclick="reset();" title="Cancelar"/>	
							</td>
							<td align="center">	</td>
							<?Php
						}else {
							?>	
							<td align="right">
								<?Php echo number_format($row['contado'],0,',','.'); ?>
							</td>
							<td align="right">
								<?Php echo number_format($row['credito'],0,',','.'); ?>
							</td>
							<td align="center">	</td>	
							<td align="center">	</td>
							<td align="center">	</td>	
							<?Php
						}

					}else{
						?>
						<td align="right">
							<?Php echo number_format($row['contado'],0,',','.'); ?>
						</td>
						<td align="right">
							<?Php echo number_format($row['credito'],0,',','.'); ?>
						</td>
						<td align="center">
							<a href="?editar=<?Php echo $codigo;?>"> <img src="<?= IMAGE.'editar.png'; ?>" title="Editar Precio de la Promoción"></a>
						</td>	
						<td align="center">

							<?Php	if($estado=='S'){ ?>

								<a href="?activar=<?Php echo $codigo;?>&estado=<?Php echo $estado;?>"><img src="<?= IMAGE.'green_ball.png'; ?>" width='20px' title="Promoción Activa"></a>	

								<?Php	}else{ ?>

									<a href="?activar=<?Php echo $codigo;?>&estado=<?Php echo $estado;?>"><img src="<?= IMAGE.'red_ball.png'; ?>" width='20px' title="Promoción Inactiva"></a>

									<?Php	} ?>	

								</td>
								<td align="center">
									<a href="?quitar=<?Php echo $codigo;?>"> <img src="<?= IMAGE.'fail.png'; ?>" title="Eliminar de la Promoción"></a>	
								</td>	
								<?Php
							}
						}
						?>	
					</tr>	
				</form>
			</table>
		</div>

		<?php 
		require('../../footer.php');
		?>