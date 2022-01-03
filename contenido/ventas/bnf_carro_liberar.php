<?php 
		include('conn.php');

		$select0 = "";
		$select1 = "";
		$select2 = "";

		if(isset($_POST['estado'])){
			$estado = $_POST['estado'];
		}	


		if(isset($_GET['carro'])){

			$carro 	= $_GET['carro'];
			$estado = $_GET['estado'];

			$sql 	= "UPDATE tef012 SET tccarest=$estado WHERE tccarcod=$carro AND bcmed=500 AND tccarps='TARJETA' 
						AND (tccarest!=5 or tccarest!=6 or tccarest!=7);";
			$query 	= pg_query($sql);
		}

		switch ($estado) {
			case '1':
				 $select1 = 'selected';
				 $filtro = "tccarest between 4 and 5 and tcbandimp!='S' AND bcmed=500";
				break;

			case '2':
				 $select2 = 'selected';
				 $filtro = "tcbandimp='S' and tccarcod not in (select cuope1 from fsd015 where cuempr=1) and tcfactfec>=date_trunc('month', current_date) AND bcmed=800"; 
				break;

			case '3':
				 $select3 = 'selected';
				 $filtro = "tcbandimp='S' and tccarcod in (select cuope1 from fsd015 where cuempr=1) and tcfactfec>=date_trunc('month', current_date) AND bcmed=800"; 
				break;					

			default:
				$select0 = 'selected';
				$filtro = 'tccarest=80 AND bcmed=500';
				break; 
		}

 ?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<SCRIPT LANGUAGE="JavaScript">
	
		function popUp(URL) {
		day = new Date();
		id = day.getTime();
		eval("page" + id + " = window.open(URL, '" + id + "','width=1000,height=500,menubar=no,scrollbars=no,toolbar=no,location=no,directories=no,resizable=no,top=40,left=340');"
		);
		}

		function popUp_menor(URL) {
		day = new Date();
		id = day.getTime();
		eval("page" + id + " = window.open(URL, '" + id + "','width=700,height=400,menubar=no,scrollbars=no,toolbar=no,location=no,directories=no,resizable=no,top=40,left=340');"
		);
		}

	
	</script>
	<style type="text/css">
		*{
			font-size: 10px;
			font-family: Arial;
		}
		table{
			border-collapse: collapse;
		}
		th{
			background: #0092B2;
			color: white;
			padding: 5px;
		}
		td{
			padding: 3px 5px;
			background: white;
			height: 18px;
		}
		h1{
			font-size: 32px;
		}
	</style>
</head>
<body>


<center>
	<h1>CONTROL DE VENTA</h1>
</center>

Estado : 
<form method="POST" action="">
	<select onchange="this.form.submit()" name="estado">
		<option value="0" <?Php echo $select0;?> >Pendientes</option>
		<option value="1" <?Php echo $select1;?> >Liberados</option>
		<option value="2" <?Php echo $select2;?> >Facturado</option>
		<option value="3" <?Php echo $select3;?> >Vigente</option>
	</select>
</form>
<br />
<br />


	<table width="100%" border="1" cellpadding="0" cellpadding="0">
		<tr class="grilla_titulo">
			<th align="center">DOCUMENTO</th>
			<th width="18%">CLIENTE</th>
			<th align="center">ESTADO</th>
			<th colspan="2">CARRO</th>
			<th>FECHA</th>
			<?php 
				if($estado==2 or $estado==3){
			?>		
				<th align="center">FACTURA</th>
			<?Php
				}
			 ?>
			<th align="center">MONTO</th>
			<th align="center">EFECTIVO</th>
			<th align="center" width="18%">DETALLE</th>
			<th colspan="2" align="center"  width="18%">COMENTARIO</th>
			<?php 
				if($estado!=2 and $estado!=3){
			?>		
				<th colspan="2">ACCION</th>
			<?Php
				}
			 ?>



		</tr>
<?Php 

	$sql 	= "SELECT 	
					carro,
					tccarfec,
					replace(substring(tiempo::text from 0 for position('.' in tiempo::text)), 'days', 'dias y')||' horas' tiempo,
					case
						when tiempo< '2 days'::interval then 'verde'
						when tiempo< '3 days'::interval then 'amarillo'
					else 'rojo'
					end semaforo,
					tccarest,
					ci,
					nombres,
					fecha_factura,
					factura,
					tcpagoluga,
					bandera,
					detalle,
					estado,
					comentario,
					monto,
					efectivo
					FROM (
						SELECT tccarcod carro,tccarfec, now()-(tccarfec||' '||tccarhor)::timestamp tiempo,tccarest,aadocu ci,tccarmon/1.1 monto,tcnctot efectivo,
							trim(aanom) nombres,tcfactfec fecha_factura,trim(tcfact) factura, tcpagoluga, tcbandimp bandera, 
							upper(trim(substring(tcobseentr from 0 for position(' //' in tcobseentr)))) detalle,
							 case
								when (select estado from tarjeta_facilandia.tarjeta_cartera_clientes where ci=aadocu)='A' then 'ACTIVO'
								when (select estado from tarjeta_facilandia.tarjeta_cartera_clientes where ci=aadocu)='B' then 'BLOQUEADO'
								when (select estado from tarjeta_facilandia.tarjeta_cartera_clientes where ci=aadocu)='D' then 'DESISTIO'
								else 'INACTIVO'
							 end estado,(select comentario from tarjeta_facilandia.carros_obser where carro=tccarcod order by id desc limit 1) comentario
						FROM public.tef012, public.fsd0011 
						WHERE tccarcue=aacuen AND tccarps='TARJETA' AND tccarest!=7 AND tccarest>3 
						and $filtro
					) as datos
				order by 7,2,5;";

	$query 	= pg_query($sql);
	while($row = pg_fetch_array($query)){
		$carro 	= $row['carro'];
		$ci 	= $row['ci'];
		$link_condicion	= "javascript:popUp('detalles_clientes.php?id=$ci')";
		$link_comentario= "javascript:popUp_menor('cargar_comentario.php?id=$carro&modo=ventas')";

?>		
		<tr class="grilla">
			<td>
				<a href="<?php echo $link_condicion; ?>"><?Php echo $row['ci'];?></a>
			</td>
			<td><?Php echo $row['nombres'];?></td>
			<td align="center"><?Php echo $row['estado'];?></td>
			<td style="border-right:0;"><?Php 
					if($row['bandera']!='S'){
						echo "<img src='../../img/".$row['semaforo'].".png' title='".$row['tiempo']."' width='15px'/>";
					}
				?>	
			</td>
			<td style="border-left:0;">	<?Php echo $carro;?></td>
			<td align="center"><?Php echo $row['tccarfec'];?></td>
			<?php 
				if($estado==2 or $estado==3){
			?>		
					<td align="center"><?Php echo $row['factura'];?></td>
			<?Php
				}
			 ?>
			<td align="right"><?Php echo number_format($row['monto'],0,',','.');?></td>
			<td align="right"><?Php echo number_format($row['efectivo'],0,',','.');?></td>
			<td><?Php echo $row['detalle'];?></td>
			<td style="border-right:0">
				<?Php echo strtoupper($row['comentario']);?>
			</td>
			<td align="right" style="border-left:0">
				<?php 
					if($row['bandera']!='S'){
				 ?>
						<a href="<?php echo $link_comentario; ?>"><img src="../../img/edit.png" width='14px' title="Agregar" /></a>
				<?php 
					}
				 ?>
			</td>

			<?php 
				if($estado!=2 and $estado!=3){
			?>		
				<td align="center" style="border-right: 0;">

					<?php 
						if($row['estado']=='ACTIVO' and $row['bandera']!='S' and $row['tccarest']==80){
					?>		
							<a href="?carro=<?Php echo $carro;?>&estado=5">
								<img src="../../img/enable.png" width='14px' title="Liberar carro" /></a>
					<?php 
						}
					?>
				</td>
				<td style="border-left: 0;">	
					<?Php
						if($row['bandera']!='S'){  
					?>
						<a href="?carro=<?Php echo $carro;?>&estado=7">
							<img src="../../img/fail.gif" width='10px' title="Anular carro" /></a>	
					<?Php
						}	
					 ?>
				</td>
			<?Php
				}
			 ?>
		</tr>
<?Php 
	}
?>
	</table>
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

</body>
</html>
