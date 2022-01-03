<?php 

	if(isset($_POST['carro']) && $_POST['carro']>0){

		include('conn.php');
		$carro    = $_POST['carro'];
		$efectivo = 0;

		if($_POST['forma']=='normal'){

			$cant 	= $_POST['cant_normal'];
			$cuota 	= $_POST['cuota_normal'];
			$monto	= $_POST['post_normal'];

		}elseif ($_POST['forma']=='promo') {

			$cant 	= $_POST['cant_promo'];
			$cuota 	= $_POST['cuota_promo'];
			$monto	= $_POST['post_promo'];

		}else{

			$cant 		= $_POST['cant_promo'];
			$cuota 		= $_POST['cuota_promoF'];
			$monto		= $_POST['post_promoF'];
			$efectivo	= $_POST['efectivo'];

		}

		$detalle = "Pasar en POST : ".number_format($monto,'0',',','.')." en $cant pagos, cuotas de ".number_format($cuota,'0',',','.')."  // ";

		if($efectivo>0){
		$detalle = "Pasar en POST : ".number_format($monto,'0',',','.')." en $cant pagos, cuotas de ".number_format($cuota,'0',',','.')." PROMO F: $efectivo Gs // ";	
			# Update para PROMO F
			//80000003
			$sql = "UPDATE tef0121 SET tccarpre=precio,tccartli=total 
				FROM (
					select tef012.tccarcod carro,tccarite codigo,
						round(($monto-$efectivo)*(tccartli::numeric/tccarmon::numeric)/tccarcan) precio, 
						round(($monto-$efectivo)*(tccartli::numeric/tccarmon::numeric)) total
					from tef012,tef0121 
					where tef012.tccarcod=tef0121.tccarcod
					and tef012.tccarcod= $carro
				) AS DATOS, tef012
				WHERE tef0121.tccarcod=carro AND tef0121.tccarcod=tef012.tccarcod 
				AND tccarite=codigo
				AND tccarest=50;

				UPDATE tef012 SET tccarmon=$monto, tccarcon=$monto,tccarest=80,tcnctot=$efectivo,tccarps='TARJETA',	tcobseentr='$detalle'||tcobseentr 
				WHERE tccarcod=$carro and tccarest=50;";


		}else{
			# Update Normal y Promo

			$sql = "UPDATE tef0121 SET tccarpre=precio,tccartli=total 
				FROM (
					select tef012.tccarcod carro,tccarite codigo,
						round($monto*(tccartli::numeric/tccarmon::numeric)/tccarcan) precio, 
						round($monto*(tccartli::numeric/tccarmon::numeric)) total
					from tef012,tef0121 
					where tef012.tccarcod=tef0121.tccarcod
					and tef012.tccarcod= $carro
				) AS DATOS, tef012
				WHERE tef0121.tccarcod=carro AND tef0121.tccarcod=tef012.tccarcod 
				AND tccarite=codigo
				AND tccarest=50;

				UPDATE tef012 SET tccarmon=$monto, tccarcon=$monto,tccarest=80,tccarps='TARJETA',
				tcobseentr='$detalle'||tcobseentr 
				WHERE tccarcod=$carro and tccarest=50;";

		}

		$query = pg_query($sql);
		if($query){

			$sql_1 = "INSERT INTO tarjeta_facilandia.tarjeta_gestion_clientes(ci, fecha, prox_llamada, gestor, referencia, respuesta)
					  select aadocu,(tccarfec||' '||tccarhor)::timestamp,tccarfec,gestor,'Gestion automatica, carro cargado numero:'||tccarcod,65 
							from tef012,fsd0011,tarjeta_facilandia.tarjeta_cartera_clientes 
							where aacuen=tccarcue 
							and ci=aadocu
							and bcmed=500 
							and tccarest!=7
							and tccarest>=4
							and tccarps='TARJETA'
							and tccarcod=$carro";
			pg_query($sql_1);					
			echo  "Se ejecuto correctamente el cambio...";

		}		
	}
 ?>

