<?php
	include 'conn.php';
?>	
<head>
	<link rel="stylesheet" type="text/css" href="css/auditoria.css">
</head>
<div id="body">	
<br />
	<h1>STOCK CRUZADO</h1>
<br/ >

<?Php

$control = 0;
$control_proceso=0;
$salida[0] = 0;
$entrada[0] = 0;
$salida['costo'] = 0;
$entrada['costo'] = 0;
$cantidad = 0;
$diferencia = 0;
$responsable = "";

$msg_deposito = $msg_cantidad = $msg_salida = $msg_entrada = $msg_responsable = $msg_diferencia="";
$hide_deposito = $hide_cantidad = $hide_salida = $hide_entrada = $hide_responsable = "";

if(isset($_POST['enviar']) or isset($_POST['procesar'])){

	$deposito 		= $_POST['deposito'];
	$salida[0] 		= $_POST['salida'];
	$entrada[0]		= $_POST['entrada'];
	$cantidad 		= $_POST['cantidad'];
	$diferencia 	= $_POST['diferencia'];
	$responsable 	= $_POST['responsable'];
echo "entra 1";
	if(isset($_POST['procesar'])){
echo "entra 2";
		$sql 	= "select max(id)+1 from producto_cruzado";
		$query 	= pg_query($conectar,$sql);
		$id 	= pg_fetch_array($query);
				
	echo 	$sql = "INSERT INTO producto_cruzado(fecha, salida, entrada,deposito, cantidad, diferencia, reponsable,operacion)
    			VALUES (current_date,$salida[0],$entrada[0],$deposito,$cantidad,$diferencia,'$responsable',0);

    			UPDATE tef006 SET epstact=epstact-$cantidad WHERE epcodi=$salida[0] and dpcodi=$deposito;
    			UPDATE tef006 SET epstact=epstact+$cantidad WHERE epcodi=$entrada[0] and dpcodi=$deposito;

    			UPDATE tef005 set epstock=epstock-$cantidad WHERE epcodi=$salida[0];
    			UPDATE tef005 set epstock=epstock+$cantidad WHERE epcodi=$entrada[0];

    			INSERT INTO tef029(tebfoper, teepcodi, tehora1, tefecha, tetipo, opcod, tefac, temov, testock, tedepecodi, testact, tedpcodi2, testact2, tedesc, teuser, teuserfec)
				select $id[0] tebfober,epcodi teepcodi,to_char(current_timestamp, 'HH12:MI:SS') tehora1,current_date tefecha,'Salida' tetipo,0 opcod,'' tefac,$cantidad temov,epstock testock, 
				$deposito tedepecodi,(select epstact from tef006 where epcodi=$salida[0] and dpcodi=$deposito) testact,0,0,'Stock Cruzado','AUDITORIA',current_date
				from tef005 where epcodi=$salida[0]; 

				INSERT INTO tef029(tebfoper, teepcodi, tehora1, tefecha, tetipo, opcod, tefac, temov, testock, tedepecodi, testact, tedpcodi2, testact2, tedesc, teuser, teuserfec)
				select $id[0] tebfober,epcodi teepcodi,to_char(current_timestamp, 'HH12:MI:SS') tehora1,current_date tefecha,'Entrada' tetipo,0 opcod,'' tefac,$cantidad temov,epstock testock, 
				$deposito tedepecodi,(select epstact from tef006 where epcodi=$entrada[0] and dpcodi=$deposito) testact,0,0,'Stock Cruzado','AUDITORIA',current_date
				from tef005 where epcodi=$entrada[0];";

    	$query = pg_query($conectar,$sql);		
echo "entra3";    	
    	if($query){
    		echo 'ok';
    		header( 'Location: index.php' );

    	}

	}	

	if($salida==$entrada){

		echo '<span class="error">El codigo de SALIDA no puede ser igual al codigo de ENTRADA...</span>';
		$control = 1;

	}

	if($responsable<>NULL){

		$hide_responsable = 'hidden';
		$msg_responsable = '<span class="ok">'.$responsable.'</span>';

	}	

	if($deposito==0){

		$msg_deposito = '<span class="error">Seleccione un DEPOSITO valido...</span>';
		$control = 1;

	} else{

		$hide_deposito = 'hidden';

		# Verifica existencia del producto si existe y su stock - SALIDA	

		$sql = "SELECT a.epcodi codigo,a.epdescl descripcion_salida,(select b.epstact from tef006 b where a.epcodi=b.epcodi and dpcodi=$deposito) stock_salida, epcosto costo from tef005 a where a.epcodi=$salida[0];";
		$query = pg_query($conectar,$sql);

		if(pg_num_rows($query)>0){

			$salida = pg_fetch_array($query);
			$msg_salida = '<span class="ok">'.$salida[0].' - '.$salida[1].'</span>';
			$hide_salida = 'hidden';

			if($salida['stock_salida'] < $cantidad){

				$msg_cantidad = '<span class="error">La cantidad de SALIDA no es suficiente en el deposito...</span>';
				$control = 1;

			} else {

				$msg_cantidad  = '<span class="ok">'.$cantidad.'</span>';
				$hide_cantidad = 'hidden';

			}

		} else{
			
			$msg_salida = '<span class="error">El codigo ingresado no existe...</span>';
			$control = 1;

		}

		# Verifica existencia del producto si existe y su stock -	ENTRADA

		$sql = "SELECT a.epcodi codigo,a.epdescl descripcion_entrada,(select b.epstact from tef006 b where a.epcodi=b.epcodi and dpcodi=$deposito) stock_entrada, epcosto costo from tef005 a where a.epcodi=$entrada[0];";
		$query = pg_query($conectar,$sql);

		if(pg_num_rows($query)>0){

			$entrada = pg_fetch_array($query);
			$msg_entrada = '<span class="ok">'.$entrada[0].' - '.$entrada[1].'</span>';
			$hide_entrada = 'hidden';

			# Verifica que el codigo ENTRADA exista en el deposito
			if($entrada['stock_entrada']==NULL){

				$sql = "INSERT INTO tef006(epcodi, dpcodi, epstact) VALUES ($entrada[0], $deposito, 0);";
				pg_query($conectar,$sql);

			}
		} else{
			
			$msg_entrada = '<span class="error">El codigo ingresado no existe...</span>';
			$control = 1;

		}

	}

	$diferencia = ($salida['costo']*$cantidad)-($entrada['costo']*$cantidad);
	$msg_diferencia = '<span class="ok">'.number_format($diferencia,0,',','.').'</span>';

	if($control==0){

		# TODOS LOS CONTROLES OK ... RECONFIRMACION 
		
		$control_proceso = 1;

	} else{

			echo '<span class="error">No se proceso el pedido</span>';

	}

}

?>

	<form action="" method="POST">
		<table width="80%" cellspacing="0" cellpadding="0">
			<tr>
				<td>
					Deposito afectado
				</td>
				<td>
					<select name="deposito" class="caja_input" <?Php echo $hide_deposito; ?>>
						<?php
							$sql = "SELECT  dpcodi,dpdesc FROM tef010 WHERE dptipo=1 and dpcodi in (select asdepcodi from fst015le) ORDER by 1;";

							echo '<option value="0"> --- Seleccione un deposito --- </option>';

							$query = pg_query($conectar,$sql);
							while($row = pg_fetch_array($query)){

								if($deposito==$row[0]){
					
									$seleccionado = "selected";	
									$msg_deposito = '<span class="ok">'.$row[0].' - '.$row[1].'</span>';
								
								} else{

									$seleccionado = "";
					
								}
					
								echo '<option value="'.$row[0].'"'.$seleccionado.'>'.$row[0].' - '.$row[1].'</option>';
								
							}
						?>
					</select><?Php echo $msg_deposito; ?>	
				</td>	
			</tr>
			<tr>
				<td>
					Salida:
				</td>
				<td>
					<input type="number" name="salida" class="caja_input" min="2000000" value="<?Php echo $salida[0]; ?>" <?Php echo $hide_salida; ?> />
						<?Php echo $msg_salida; ?>
				</td>	
			</tr>	
			<tr>
				<td>
					Entrada:
				</td>
				<td>
					<input type="number" name="entrada" class="caja_input" min="2000000" value="<?Php echo $entrada[0]; ?>" <?Php echo $hide_entrada; ?> />
					<?Php echo $msg_entrada; ?>
				</td>	
			</tr>
			<tr>
				<td>
					Cantidad:
				</td>
				<td>
					<input type="number" name="cantidad" class="caja_input" min="1" value="<?Php echo $cantidad; ?>" <?Php echo $hide_cantidad; ?> />
					<?Php echo $msg_cantidad; ?>
				</td>	
			</tr>
			<tr>
				<td>
					Diferencia:
				</td>
				<td>
					<input type="number" name="diferencia" class="caja_input" value="<?Php echo $diferencia; ?>" hidden />
					<?Php echo $msg_diferencia; ?>
				</td>	
			</tr>
			<tr>
				<td>
					Responsable:
				</td>
				<td>
					<input type="text" name="responsable" class="caja_input" value="<?Php echo $responsable; ?>" <?Php echo $hide_responsable; ?> required />
					<?Php echo $msg_responsable; ?>
				</td>	
			</tr>
		</table>
<?php
	
	if($control_proceso==0){

		echo  '<button type="submit" name="enviar" value="enviar" class="caja_submit">Enviar</button>';
		echo  '<button type="reset" value="cancel" class="caja_submit">Cancel</button>';

	} else{	
	
		echo '<button type="submit" name="procesar" value="procesar" class="caja_submit">Procesar</button>';
		echo '<button class="caja_submit" onclick="window.location=">Cancel</button>';

		echo '<br /><span class="error">ATENCION : EL SIGUIENTE PROCESO REALIZARA CAMBIOS EN STOCK DE LOS PRODUCTOS...</span>';
	}

?>

	</form>

	<br>
	<table class="grilla" width="100%" cellspacing="0" cellpadding="0">
		<tr class="cabecera">
			<td align="center">Id</td>
			<td align="center">Fecha</td>
			<td align="center">Deposito</td>
			<td align="center">Cantidad</td>
			<td align="center">Salida</td>
			<td align="center">Entrada</td>
			<td align="center">Diferencia</td>
			<td align="center">Responsable</td>
			<td align="center">Operacion</td>
			<td align="center">Valor</td>
		</tr>
<?php
	
	$sql = "SELECT id, fecha, salida||' - '||(select epdescl from tef005 where salida=epcodi ) salida, entrada||' - '||(select epdescl from tef005 where entrada=epcodi ) entrada, cantidad, diferencia, reponsable, operacion, (select bftcuo from fsd0122 where operacion=bfope1) valor,deposito 
			FROM producto_cruzado ORDER BY 1 DESC;";
	$query = pg_query($conectar,$sql);
	while($row = pg_fetch_array($query)){

?>
		<tr class="grilla_cruzado">
			<td align="center"><?Php echo $row['id']; ?></td>
			<td align="center"><?Php echo date('d-m-Y',strtotime($row['fecha'])); ?></td>
			<td align="center"><?Php echo $row['deposito']; ?></td>
			<td align="center"><?Php echo $row['cantidad']; ?></td>
			<td align="left"><?Php echo $row['salida']; ?></td>
			<td align="left"><?Php echo $row['entrada']; ?></td>
			<td align="right"><?Php echo number_format($row['diferencia'],0,',','.'); ?></td>
			<td align="left"><?Php echo strtoupper($row['reponsable']); ?></td>
			<td align="center"><?Php echo $row['operacion']; ?></td>
			<td align="right"><?Php echo number_format($row['valor'],0,',','.'); ?></td>
		</tr>
<?php		
	}
?>
	</table>	

<div>
