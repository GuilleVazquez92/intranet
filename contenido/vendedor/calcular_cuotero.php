<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Document</title>
</head>
<body>
	<?php 

	$monto_total 	 = $_POST['monto_total'];
	$monto_contado 	 = $_POST['monto_contado'];
	$monto_credito 	 = $monto_total - $monto_contado; 
	$cantidad_cuotas = $_POST['cantidad_cuotas'];
	$primer_pago	 = $_POST['primer_pago'];
	$tipo_oper 		 = $_POST['tipo_oper'];		 

	if($tipo_oper==406 || $tipo_oper==407){

		$primer_pago = 2;
		if($tipo_oper == 406 && $cantidad_cuotas < 5){
			$cantidad_cuotas = 5;
		}

		if($tipo_oper == 407 && $cantidad_cuotas < 8 ){
			$cantidad_cuotas = 8;	
		}
	}

	if($primer_pago == 1){ #Con entrega inicial
		$aplicar_tasa  = ($cantidad_cuotas>5) ? $cantidad_cuotas-5 : 0;

	}else{ #A 30 dias
		$aplicar_tasa  = ($cantidad_cuotas>4) ? $cantidad_cuotas-4 : 0;
	}

	if($aplicar_tasa>=1){
		$porcentaje 	= ($tipo_oper>=400) ? 4.5 : 2.5;
		$tasa 			= $aplicar_tasa * $porcentaje;
		$interes		=  ($monto_credito * $tasa/100);
		$interes 		+= ($interes*0.1);	
		$cuota 			= round((($monto_credito+$interes)/$cantidad_cuotas)+499,-3);
		$bruto 			= $cuota*$cantidad_cuotas; 
	}else{
		$interes		= 0;
	}
		$cuota 			= round((($monto_credito+$interes)/$cantidad_cuotas)+499,-3);
		$bruto 			= $cuota*$cantidad_cuotas;
		print_r('Cuotas de: Gs.'.number_format($cuota,0,',','.'));
		//print("Hola! aquí estará el valor cuota");
		?> 	
	</body>
	</html>


