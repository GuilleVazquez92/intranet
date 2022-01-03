<link rel="stylesheet" type="text/css" href="../../css/grilla.css">
<?Php
error_reporting(E_ALL);
ini_set('display_errors', '1');

//include ('../opening.php');
require('../../header.php');
include("conn.php");
$usuario = 'MIRNA';$_SESSION['usr_base'];

#### PROMO QUINCENAL  
?>
	<h2>Productos CODIGO -2-</h2>

<?Php

if (isset($_GET['agregar'])) {

	$codigo = $_GET['codigo'];
	$sql 	= "SELECT epcodi, (select count(*) FROM tef005 WHERE epcodi=$codigo+20000000)existe	FROM tef005 WHERE epacti='S' AND epcodi=".$codigo;
	$query 	= pg_query($sql);

	if(pg_num_rows($query)==1){

		$row 		= pg_fetch_array($query);
		$codigo2 	= $codigo+20000000;

		if($row[1]==0){

			## Crea el codigo
			$sql_1 = "INSERT INTO public.tef005(
			            epcodi, epdesc, epdescl, effami, efclas, efmarca, eflinea, efunid, 
			            efcodcon, efcodlis, epprelis, epdeta, epfoto, epstock, epstmin, 
			            epstres, epstexp, epstsol, epcosto, epprec1, epprec2, eftipo, 
			            efaltu, eflarg, efanch, efpeso, efmed, efpes, efcol, epcodpro, 
			            epmone, epmerser, epcomb, epiva, produlcom, epacti, gtiacod, 
			            epdatdep, epcat, epdatzon, epferi, epprefer, epgarantia, epcostreal, 
			            eppromcost, epzona, epobs, epstockalt)

			            SELECT $codigo2, epdesc, epdescl, effami, efclas, efmarca, eflinea, efunid, 
			            efcodcon, efcodlis, epprelis, epdeta, epfoto, 0, epstmin, 
			            epstres, epstexp, epstsol, epcosto, epprec1, epprec2, eftipo, 
			            efaltu, eflarg, efanch, efpeso, efmed, efpes, efcol, epcodpro, 
			            epmone, epmerser, epcomb, epiva, produlcom, epacti, gtiacod, 
			            epdatdep, epcat, epdatzon, epferi, epprefer, epgarantia, epcostreal, 
			            eppromcost, epzona, epobs, epstockalt 
			            FROM tef005
			            WHERE epcodi=$codigo";
			$query_1	= pg_query($sql_1);	

			if($query_1){
				echo "<div class='mensajeOK'>El se creo el codigo ".$codigo2." y ahora forma parte de la promoción CODIGO 2</div>";
			
			}else{
				echo "<div class='mensajeFAIL'>Sucedio un problema con el codigo ".$codigo2.", intentelo mas tarde...</div>";

			}  

		}else{
			
			$sql_1 		= "UPDATE tef005 SET epacti='S' WHERE epcodi=".$codigo2;
			$query_1	= pg_query($sql_1);	

			if($query_1){
				echo "<div class='mensajeOK'>El codigo ".$codigo2." ahora forma parte de la promoción CODIGO 2</div>";
			
			}else{
				echo "<div class='mensajeFAIL'>Sucedio un problema con el codigo ".$codigo2.", intentelo mas tarde...</div>";

			}
		}
	}else{

		echo "<div class='mensajeFAIL'>Sucedio un problema con el codigo ".$codigo.", verifique por favor e intenete de nuevo...</div>";

	}
}

#################################

if(isset($_GET['modificar']) && isset($_GET['editar'])){	

	$deposito 	= $_GET['deposito'];
	$cantidad 	= $_GET['cantidad'];
	$control 	= 0;
	$verica  	= 0;
	$verica_st 	= 0;

	if($_GET['editar']<20000000){   

		$codigo  = $_GET['editar']; // Codigo normal
		$codigo2 = $_GET['editar']+20000000;

		$sql 		= "SELECT epcodi FROM tef006 WHERE epcodi=$codigo AND dpcodi=$deposito AND epstact>=$cantidad;";
		$query 		= pg_query($sql);
		$verica_st 	= pg_num_rows($query);

		if($verica_st==1){ // stock necesario para la transaccion

			$sql 	= "SELECT epcodi FROM tef006 WHERE epcodi=$codigo2 AND dpcodi=$deposito;";  // VERIFICA QUE EXISTA EL CODIGO2 
			$query 	= pg_query($sql);
			$verica = pg_num_rows($query);

			if($verica==1){ //Update

				$sql_1 =  " UPDATE tef006 SET epstact=epstact+$cantidad WHERE epcodi=$codigo2 AND dpcodi=$deposito;
							UPDATE tef006 SET epstact=epstact-$cantidad WHERE epcodi=$codigo AND dpcodi=$deposito;";

			} else{ //Insert
			
				$sql_1 = "  INSERT INTO public.tef006(epcodi, dpcodi, epstact) VALUES ($codigo2, $deposito, $cantidad);
	    					UPDATE tef006 SET epstact=epstact-$cantidad WHERE epcodi=$codigo AND dpcodi=$deposito;";
			}

			$sql_1 .= " INSERT INTO public.tef029(tebfoper, teepcodi, tehora1, tefecha, tetipo, opcod, tefac, temov,testock, tedepecodi, testact, tedpcodi2, testact2, tedesc, teuser, teuserfec) 
						
						SELECT 1, $codigo,substring(current_time::text from 1 for position('.' in current_time::text)-1),current_date, 'Salida',0,'',$cantidad,
							(SELECT sum(epstact) FROM tef006 a,tef010 b WHERE (a.dpcodi=b.dpcodi or a.dpcodi+6000=b.dpcodi) AND dptipo=1 AND epcodi=$codigo), $deposito, 0,0,0,
							'Conversion codigo 2','$usuario', current_date 
						
						UNION

						SELECT 2, $codigo2,substring(current_time::text from 1 for position('.' in current_time::text)-1),current_date, 'Entrada',0,'',$cantidad,
							(SELECT sum(epstact) FROM tef006 a,tef010 b WHERE (a.dpcodi=b.dpcodi or a.dpcodi+6000=b.dpcodi) AND dptipo=1 AND epcodi=$codigo2), $deposito, 0,0,0,
							'Conversion codigo 2','$usuario', current_date;";

			pg_query("BEGIN;");

			$query_1 = pg_query($sql_1);
	
			if ($query_1) {

				pg_query("COMMIT;");
				echo "Se movio correctamente!!!";

			}else {

				pg_query("ROLLBACK;");
				echo "Sucedio un problema con el codigo!!!";

			}

		}else{ // no tiene el stock necesario, no procesar

			echo "Sucedio un problema con el codigo!!!";
		}
	
	}else {

		$codigo 	= $_GET['editar']-20000000;
		$codigo2  	= $_GET['editar']; // Codigo promocion
	
		$sql 		= "SELECT epcodi FROM tef006 WHERE epcodi=$codigo2 AND dpcodi=$deposito AND epstact>=$cantidad;";
		$query 		= pg_query($sql);
		$verica_st 	= pg_num_rows($query);

		if($verica_st==1){ // stock necesario para la transaccion
			
			$sql 	= "SELECT epcodi FROM tef006 WHERE epcodi=$codigo AND dpcodi=$deposito;";
			$query 	=  pg_query($sql);
			$verica =  pg_num_rows($query);

			if($verica==1){ // Update
	
				$sql_1 = "  UPDATE tef006 SET epstact=epstact+$cantidad WHERE epcodi=$codigo AND dpcodi=$deposito;
						    UPDATE tef006 SET epstact=epstact-$cantidad WHERE epcodi=$codigo2 AND dpcodi=$deposito;";

			}else{  // Insert

				$sql_1 = "  INSERT INTO public.tef006(epcodi, dpcodi, epstact) VALUES ($codigo, $deposito, $cantidad);
			   	    		UPDATE tef006 SET epstact=epstact-$cantidad WHERE epcodi=$codigo2 AND dpcodi=$deposito;";

			}

			$sql_1 .= "INSERT INTO public.tef029(tebfoper, teepcodi, tehora1, tefecha, tetipo, opcod, tefac, temov,testock, tedepecodi, testact, tedpcodi2, testact2, tedesc, teuser, teuserfec) 
						
						SELECT 1, $codigo,substring(current_time::text from 1 for position('.' in current_time::text)-1),current_date, 'Entrada',0,'',$cantidad,
							(SELECT sum(epstact) FROM tef006 a,tef010 b WHERE (a.dpcodi=b.dpcodi or a.dpcodi+6000=b.dpcodi) AND dptipo=1 AND epcodi=$codigo), $deposito, 0,0,0,
							'Conversion codigo 2','$usuario', current_date 
						
						UNION

						SELECT 2, $codigo2,substring(current_time::text from 1 for position('.' in current_time::text)-1),current_date, 'Salida',0,'',$cantidad,
							(SELECT sum(epstact) FROM tef006 a,tef010 b WHERE (a.dpcodi=b.dpcodi or a.dpcodi+6000=b.dpcodi) AND dptipo=1 AND epcodi=$codigo2), $deposito, 0,0,0,
							'Conversion codigo 2','$usuario', current_date;";

			pg_query("BEGIN;");

			$query_1 = pg_query($sql_1);
	
			if ($query_1) {

				pg_query("COMMIT;");
				echo "Se movio correctamente!!!";

			}else {

				pg_query("ROLLBACK;");
				echo "Sucedio un problema con el codigo!!!";

			}

		}else{	// no se tiene el stock necesario

			echo "Sucedio un problema con el codigo!!!";
	
		}
	}	

	$sql = "UPDATE tef005 SET epstock=cant 
			FROM ( 
					SELECT epcodi codigo,sum(epstact) cant FROM tef006 a,tef010 b 
					WHERE (a.dpcodi=b.dpcodi or a.dpcodi+6000=b.dpcodi) 
					AND dptipo=1
					GROUP BY epcodi
				) AS datos
			WHERE epcodi=codigo
			AND epcodi=$codigo;

			UPDATE tef005 SET epstock=cant 
			FROM ( 
					SELECT epcodi codigo,sum(epstact) cant FROM tef006 a,tef010 b 
					WHERE (a.dpcodi=b.dpcodi or a.dpcodi+6000=b.dpcodi) 
					AND dptipo=1
					GROUP BY epcodi
				) AS datos
			WHERE epcodi=codigo
			and epcodi=$codigo2;";

	$query = pg_query($sql);
}		

#################################

if(isset($_GET['quitar'])){

	$codigo = $_GET['quitar'];

	$sql 	= "UPDATE tef005 SET epacti='N' WHERE epcodi=".$codigo;
	$query 	= pg_query($sql);
	
	if($query){

		echo "<div class='mensajeOK'>El codigo ".$codigo." ya no forma parte de la promoción y fue desactivado</div>";	

	}else{

		echo "<div class='mensajeFAIL'>Sucedio un problema con el codigo ".$codigo.", intentelo mas tarde...</div>";	

	}

}


	$sql 	= "SELECT epcodi codigo,epdescl descripcion,epstock, epprelis precio FROM tef005 WHERE epcodi BETWEEN 22000000 AND 24000000  AND epacti='S' ORDER BY 1;";
	$query 	= pg_query($sql);
	
?>

<br />

<form method="GET" action="">
	<input type="number" name="codigo" step="1" min="2000000" max="4000000">	
	<input type="submit" name="agregar" value="Agregar">
</form>

<table width='95%'>
	<tr class="grilla_titulo">
		<td>Codigo</td>
		<td>Descripción</td>
		<td>Stock</td>
		<td>Precio</td>
		<td colspan="3" align="center">Acción</td>
	</tr>
<?Php
	while ($row = pg_fetch_array($query)) {
		$codigo = $row[0];
		$stock  = $row[2];
?>
	<tr class="grilla">	
		<td><?Php echo $codigo; ?></td>
		<td><?Php echo $row[1]; ?></td>
		<td align="center"><?Php echo $row[2]; ?></td>
		<td align="right"><?Php echo number_format($row[3],0,',','.'); ?></td>
		<td align="center">
				    <a href="?editar=<?Php echo $codigo;?>"> <img src="../../img/edit.png" title="Editar de Promoción"></a>	
			<?Php 
				if($stock==0){
			?>		
					<a href="?quitar=<?Php echo $codigo;?>"> <img src="../../img/fail.gif" title="Desactivar de Promoción"></a>
			<?Php
				}
			?>
			
		</td>

	</tr>	
<?Php
	 } 
?>
</table>


<br>
<br>

<?Php 
	if(isset($_GET["editar"])){

		if($_GET["editar"]<20000000){

			$codigo  = $_GET["editar"];
			$codigo2 = $_GET["editar"]+20000000;

		}else{

			$codigo  = $_GET["editar"]-20000000;
			$codigo2 = $_GET["editar"];

		}
	
?>
<h2>Codigo : <?Php echo $codigo2;?> </h2>
<table width="95%">
	<tr class="grilla_titulo">
		<td align="center" valign="middle" ><h2 style="color:white">NORMAL</h2></td>
		<td align="center" valign="middle" ><h2 style="color:white">PROMOCION</h2></td>
	</tr>
	<tr>
		<td width="50%" valign="top" bgcolor="white">
			<table width="100%">
				<tr class="grilla_titulo">
					<td>Deposito</td>
					<td>Estado</td>
					<td>Stock</td>
					<td>Accion</td>
				</tr>
	<?Php 
		$sql = "SELECT epcodi codigo,a.dpcodi cod_deposito, a.dpcodi||' '||dpdesc deposito,
						case when a.dpcodi<6000 then 'PEND.TRANS'
							else 'NORMAL'
						end estado,
					epstact stock 
					FROM TEF006 a,TEF010 b 
					WHERE (a.dpcodi=b.dpcodi or a.dpcodi+6000=b.dpcodi)
					AND epstact>0
					AND dptipo=1
					AND epcodi=$codigo
					ORDER BY 2";

		$query 	= pg_query($sql);
		while ($row = pg_fetch_array($query)) {
	?>		
				<tr class="grilla">
					<td><?Php echo $row['deposito']; ?></td>
					<td><?Php echo $row['estado']; ?></td>
					<td><?Php echo $row['stock']; ?></td>
					<td>
						<?Php
							if($row['cod_deposito']>6000){
						?>		
								<form action="" method="GET">
									<input type="number" name="editar" value="<?Php echo $row['codigo']; ?>" hidden / >
									<input type="number" name="deposito" value="<?Php echo $row['cod_deposito'];?>" hidden  / >
									<input type="number" name="cantidad" min="1" max="<?Php echo $row['stock']; ?>" />
									<input type="submit" name="modificar" value="Enviar" />					
								</form>
						<?Php 
							}
						?>
					</td>
				</tr>
	<?Php
		}
	?>		
			</table>
		</td>
		<td width="50%" valign="top" bgcolor="white">
			<table width="100%">
				<tr class="grilla_titulo">
					<td>Deposito</td>
					<td>Estado</td>
					<td>Stock</td>
					<td>Accion</td>
				</tr>
	<?Php 
		$sql = "SELECT epcodi codigo,a.dpcodi cod_deposito, a.dpcodi||' '||dpdesc deposito,
						case when a.dpcodi<6000 then 'PEND.TRANS'
							else 'NORMAL'
						end estado,
					epstact stock 
					FROM TEF006 a,TEF010 b 
					WHERE (a.dpcodi=b.dpcodi or a.dpcodi+6000=b.dpcodi)
					AND epstact>0
					AND dptipo=1
					AND epcodi=$codigo2
					ORDER BY 2";

		$query 	= pg_query($sql);
		while ($row = pg_fetch_array($query)) {
	?>		
				<tr class="grilla">
					<td><?Php echo $row['deposito']; ?></td>
					<td><?Php echo $row['estado']; ?></td>
					<td><?Php echo $row['stock']; ?></td>
					<td>
						<?Php
							if($row['cod_deposito']>6000){
						?>		
								<form action="" method="GET">
									<input type="number" name="editar" value="<?Php echo $row['codigo']; ?>" hidden / >
									<input type="number" name="deposito" value="<?Php echo $row['cod_deposito'];?>" hidden  / >
									<input type="number" name="cantidad" min="1" max="<?Php echo $row['stock']; ?>" />
									<input type="submit" name="modificar" value="Enviar" />					
								</form>
						<?Php 
							}
						?>
					</td>
				</tr>
	<?Php
		}

	?>		
			</table>			
		</td>
	</tr>
</table>


<?Php 
	}
?>
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
<br>
<br>
