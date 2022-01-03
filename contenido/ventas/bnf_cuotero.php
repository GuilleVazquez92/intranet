<?php 
	function cuotas_de($monto,$clase,$entrega,$cant_cuota){
	//echo "<script>alert('$monto,$clase,$condicion,$entrega,$cant_cuota')</script>";

			$aux 		= ($entrega==0) ? 5 : 4;
			$tasa 		= ($clase!=31001) ? 4.5 : 2.5;

			switch ($clase) {
				case '31001':
					$tasa = 2.5;
					break;
	
				case '9':
					$tasa = 4;
					$aux  = 0;
					break;	
				
				default:
					$tasa = 4.5;
					break;
			}


			$porcentaje = $tasa*($cant_cuota-$aux);

			if($cant_cuota-$aux<0){
				// 5 Cuotas sin intereses
				$valor_cuota = ($cant_cuota==1) ? $monto : $valor_cuota = round($monto/$cant_cuota+499,-3);

			}else{

				$calculo = 	round(($monto+($monto*$porcentaje/100)*1.1)/$cant_cuota+499,-3);

				if($clase!=31001){
					//Productos en general
					$valor_cuota = ($cant_cuota>36) ? 0 : $calculo;

				}else{
					// Motos
					$valor_cuota = ($cant_cuota>36) ? 0 : $calculo;

				}
			}

		return $valor_cuota;
	}

	//echo cuotas_de(500000,9,0,12);

?>