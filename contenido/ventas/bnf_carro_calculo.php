<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<link rel="stylesheet" type="text/css" href="../../css/grilla.css">
	<style type="text/css">
		table tr td{
			padding: 2px 10px;
		}

		h1{
			font-family: Arial;
		}

		table.cabeceras{
			border-collapse: collapse;
		}
		table.cabeceras tr{
			background: white;
			font-size: 12px;
			font-family: Arial;
			color: #5F5448;
		}

		table.cabeceras tr td{
			padding: 4px 6px;
		}


		tr.grilla_blanca td{
			background: white;
			font-size: 12px;
			font-family: Arial;
			color: #5F5448;
		}

		input:read-only {
			background-color: #a8a8a8;
		}

	</style>
	<center>
		<h1>CARRO CONVERSION</h1>

		<?php
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);

		$cuenta = 999999999999;
		include('bnf_cuotero.php');
		include('conn.php');

		$entrega 				= 1;
		$ingresar_post 			= 0;
		$ingresar_post_promo 	= 0;

		$tasa 		= 13;
		$anual  	= $tasa/100;
		$mes 		= round(($anual/12), 6);
	$monto		= 0; # precio lista
	$efectivo	= 0; 
	$plazo 		= 0;
	$valor 		= 0;
	$linea	  	= 0;


	if(isset($_POST['carro']) && $_POST['carro']>0){

		$carro 		= $_POST['carro'];

		$sql_pre ="UPDATE tef0121 SET tccarpre=round(((tcpreclist/tccarcan)-((tcpreclist/tccarcan)*15/100))+499,-3), tccartli=round(((tcpreclist/tccarcan)-((tcpreclist/tccarcan)*15/100))+499,-3)*tccarcan
		FROM tef012,tef005,tef009, fsd0011,tarjeta_facilandia.tarjeta_cartera_clientes 
		WHERE tef012.tccarcod=tef0121.tccarcod
		AND tccarite=epcodi
		AND tef012.tccarcue=aacuen
		AND ci=aadocu
		AND tef005.eftipo=tef009.eftipo
		AND epcodi<10000000
		AND bcmed=500
		AND tccarest=50
		AND eftipmarg>0
		AND tef012.tccarcod in (select tccarcod from tef0121 group by 1 having sum(tcpordes)=0)
		AND tef012.tccarcod=$carro;

		UPDATE tef012 set tccarmon=total,tccarcon=total from (
		select tef012.tccarcod carro, sum(tccartli) total 
		FROM tef012,tef0121,tef005,tef009, fsd0011,tarjeta_facilandia.tarjeta_cartera_clientes 
		WHERE tef012.tccarcod=tef0121.tccarcod
		and tccarite=epcodi
		and tef012.tccarcue=aacuen
		and ci=aadocu
		and tef005.eftipo=tef009.eftipo
		and epcodi<10000000
		and bcmed=500
		and tccarest=50
		and eftipmarg>0
		and tef012.tccarcod in (select tccarcod from tef0121 group by 1 having sum(tcpordes)=0)
		group by 1) as datos
		where tccarcod=carro
		and tccarcod=$carro;";
		$query_pre = pg_query($sql_pre);			

		if($query_pre){

			$sql   = "SELECT tccarcod carro,tccarmon monto,linea_credito
			FROM tef012, fsd0011,tarjeta_facilandia.tarjeta_cartera_clientes 
			WHERE tccarcue=aacuen
			AND ci=aadocu
			AND tccarcod in (select tccarcod from tef0121 group by 1 having sum(tcpordes)=0)
			AND bcmed=500
			AND tccarest=50 
			AND tccarcod=".$_POST['carro'];

			$query 	= pg_query($sql);
			$row 	= pg_fetch_array($query);
			$carro 	= $row['carro'];
		}
	}

	if(isset($_POST['monto']) && isset($_POST['plazo'])){

		$monto 		= $row['monto'];
		$plazo 		= $_POST['plazo'];
		$efectivo 	= $_POST['efectivo'];
		$linea 		= $row['linea_credito'];

	}

	$valor_cuota = 0;
	$valor_cuota_promo = 0;
	$plazo_nuevo = 0;

	if($plazo!=0){
		$plazo_nuevo= $plazo+2;
# Calculo de la operacion
		$valor 		= cuotas_de($monto,0,1,$plazo)*$plazo; 

		$var_25 = $valor*40/100;

		if($var_25<$efectivo){
			$efectivo = round($var_25,-4);

		}

		$total 		= $valor / ((pow((1+$mes), $plazo)-1)/($mes*pow((1+$mes), $plazo)))*$plazo;
		$porcentaje = 1-($valor/$total);
		$ingresar  	= $valor-($valor*$porcentaje);

# Operacion Normal 
		$ingresar_post 			= $ingresar;
		$valor_cuota   			= $ingresar / ((pow((1+$mes), $plazo)-1)/($mes*pow((1+$mes), $plazo)));

# Operacion + 2 cuotas
		$ingresar_post_promo	= $ingresar * 0.98952876;
		$valor_cuota_promo 		= $valor_cuota*$plazo/$plazo_nuevo;

# Operacion PromoF	
		for ($i=0; $i < 1000; $i++) { 

			$valor_promoF 			= cuotas_de($efectivo,9,1,$plazo)*$plazo/0.96;
			$ingresar_post_promoF 	= $ingresar_post_promo+$valor_promoF;

			$valor_cuota_promoF 	= ($ingresar_post+$valor_promoF) / ((pow((1+$mes), $plazo)-1)/($mes*pow((1+$mes), $plazo))); 
			$valor_cuota_promoF 	= $valor_cuota_promoF*$plazo/($plazo_nuevo);

			if($ingresar_post_promoF<=$linea) break;

			$efectivo -= 10000;

		}

	}

	if($efectivo<0)	$efectivo = 0;

	?>
	<form method="POST" action="">
		<table>
			<tr>
				<td>Numero de Carro :</td>
				<td><input type="number" name="carro" id="carro" value="<?Php echo $carro;?>" placeholder="Numero de Carro"></td>
			</tr>
			<tr>
				<td>Plazo de financiacion:</td>
				<td><input type="number" name="plazo" id="plazo" value="<?Php echo $plazo;?>" placeholder="Cantidad cuotas"></td>
			</tr>
			<tr>
				<td>Monto en efectivo:</td>
				<td><input type="number" name="efectivo" id="efectivo" value="<?Php echo $efectivo;?>" placeholder="Monto del efectivo"></td>
			</tr>
			<tr>
				<td>Saldo de linea :</td>
				<td><input type="number" name="linea" id="linea" value="<?Php echo round($linea);?>" placeholder="Saldo de linea" readonly></td>
			</tr>
			<tr>
				<td>Monto del carro :</td>
				<td><input type="number" name="monto" id="monto" value="<?Php echo round($monto);?>" placeholder="Monto del carro" readonly></td>
			</tr>

			<tr>
				<td colspan="2"><input type="submit" name="enviar"></td>
			</tr>								
		</table>
	</form>

	<br>
	<br>

	<form method="POST" action="bnf_carro_convertir.php">

		<input type="text" name="carro" value="<?Php echo $carro; ?>" hidden />
		<input type="text" name="cant_normal" value="<?Php echo $plazo; ?>" hidden />	
		<input type="text" name="cant_promo" value="<?Php echo $plazo_nuevo; ?>" hidden/>
		<input type="text" name="efectivo" value="<?Php echo $efectivo; ?>" hidden/>

		<input type="text" name="cuota_normal" value="<?Php echo round($valor_cuota+499,-3); ?>" hidden />	
		<input type="text" name="post_normal" value="<?Php echo round($ingresar_post+499,-3); ?>" hidden />

		<input type="text" name="cuota_promo" value="<?Php echo round($valor_cuota_promo+499,-3); ?>" hidden />	
		<input type="text" name="post_promo" value="<?Php echo round($ingresar_post_promo+499,-3); ?>" hidden />

		<input type="text" name="cuota_promoF" value="<?Php echo round($valor_cuota_promoF+499,-3); ?>" hidden />	
		<input type="text" name="post_promoF" value="<?Php echo round($ingresar_post_promoF+499,-3); ?>" hidden />


		<table border="1" cellpadding="0" cellspacing="0" width="80%" class="grilla_normal">
			<tr class="grilla_titulo">
				<td width="20%" align="center"></td>
				<td width="20%" align="center">EFECTIVO</td>
				<td width="20%" align="center">CANT CUOTAS</td>
				<td width="20%" align="center">MONTO.COUT</td>
				<td width="20%" align="center">POST</td>
			</tr>
			<tr class="grilla_blanca">	
				<td><input type="radio" name="forma" value='normal' /> NORMAL</td>
				<td></td>
				<td align="center"><?Php echo $plazo;?></td>
				<td align="center">Gs. <?Php echo number_format(round($valor_cuota+499,-3),0,',','.');?></td>
				<td align="center">Gs. <?Php echo number_format(round($ingresar_post+499,-3),0,',','.');?></td>
			</tr> 	
			<tr class="grilla_blanca">
				<td><input type="radio" name="forma" checked="checked" value='promo'/>PROMO + 2 Cuotas</td>
				<td></td>
				<td align="center"><?Php echo $plazo_nuevo;?></td>
				<td align="center">Gs. <?Php echo number_format(round($valor_cuota_promo+499,-3),0,',','.');?></td>
				<td align="center">Gs. <?Php echo number_format(round($ingresar_post_promo+499,-3),0,',','.');?></td>
			</tr>
			<?php 
			if($efectivo>0){
				?>
				<tr class="grilla_blanca">
					<td><input type="radio" name="forma" checked="checked" value='promoF'/>PROMO F</td>
					<td align="center">Gs. <?Php echo number_format(round($efectivo),0,',','.');?></td>
					<td align="center"><?Php echo $plazo_nuevo;?></td>
					<td align="center">Gs. <?Php echo number_format(round($valor_cuota_promoF+499,-3),0,',','.');?></td>
					<td align="center">Gs. <?Php echo number_format(round($ingresar_post_promoF+499,-3),0,',','.');?></td>
				</tr>
				<?php 
			}
			?>
		</table>
		<br>
		<?php 
		if($ingresar_post>0){
			?>
			<input type="submit" name="convertir" value="Convertir Carro">
			<?php

		} 
		?>
	</form>

	<br>
	<br>

</center>
</body>
</html>
