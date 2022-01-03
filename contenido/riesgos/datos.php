<?php 
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require('../../header.php');
require( CONTROLADOR . 'riesgos.php');

	$listas = new Riesgos();
	$data = new Riesgos();
	$envio = new Riesgos();

	$coeficiente = 0;
	$limite_prestable = 0;
	$capacidad= 0;
	$endeudamiento=0;
	$puntos = 0;
	$coheficiente_cuota =0;
	
	$listas = $listas->listaScoring();
	
	foreach($listas as $lista)
	{
	$puntos = 0;
	$data->cuenta = $lista['cuenta'];


	$datos = $data->resumenScoring();
	
	

	

	// EDAD ------------------------------------
	if ($datos['edad'] <= 17) {
	$puntos += -50;
	}
	if ($datos['edad'] >17 && $datos['edad'] <= 20) {
	$puntos += -3;
	}
	if ($datos['edad'] >20 && $datos['edad'] <= 25) {
	$puntos += -2;
	}
	if ($datos['edad'] >25 && $datos['edad'] <= 30) {
	$puntos += -1;
	}
	if ($datos['edad'] >30 && $datos['edad'] <= 35) {
	$puntos += 0;
	}
	if ($datos['edad'] >35 && $datos['edad'] <= 40) {
	$puntos += 1;
	}
	if ($datos['edad'] >40 && $datos['edad'] <= 50) {
	$puntos += 2;
	}
	if ($datos['edad'] >50 && $datos['edad'] <= 65) {
	$puntos += 3;
	}
	if ($datos['edad'] >65 && $datos['edad'] <= 99) {
	$puntos += -1;
	}

	

// SEXO ------------------------------------

	if ($datos['sexo'] == 'MASCULINO' ) {
	$puntos += 1;
	}
	if ($datos['sexo'] =='FEMENINO' ) {
	$puntos += 3;
	}

// ESTADO CIVIL ------------------------------------

	if ($datos['estado_civil'] == 1 ) {
	$puntos += 2;
	}
	if ($datos['estado_civil'] == 2 ) {
	$puntos += 1;
	}
	if ($datos['estado_civil'] == 3 ) {
	$puntos += -1;
	}
	if ($datos['estado_civil'] == 4 ) {
	$puntos += -1;
	}
	if ($datos['estado_civil'] == 5 ) {
	$puntos += -1;
	}


// CANTIDAD DE HIJOS ------------------------------------

	if ($datos['cant_hijos'] == 0 ) {
	$puntos += 4;
	}
	if ($datos['cant_hijos'] == 1) {
	$puntos += 3;
	}
	if ($datos['cant_hijos'] == 2) {
	$puntos += 2;
	}
	if ($datos['cant_hijos'] >= 3) {
	$puntos += 1;
	}



// VIVIENDA ------------------------------------
	if ($datos['vivienda'] == 'PROPIA') {
	$puntos += 5;
	}
	if ($datos['vivienda'] == 'PRESTADA') {
	$puntos += 3;
	}
	if ($datos['vivienda'] == 'DE LOS PADRES  ') {
	$puntos += 3;
	}
	if ($datos['vivienda'] == 'ALQUILADA' ) {
	$puntos += -1;
	}

//14

// SERVICIOS BASICOS ------------------------------------
	
	if ($datos['servicios_basicos'] == 2) {
	$puntos += 3;
	}
	if ($datos['servicios_basicos'] == 3) {
	$puntos += 2;
	}
	if ($datos['servicios_basicos'] == 4) {
	$puntos += 1;
	}



// CONYUGE ------------------------------------
	
	if ($datos['conyuge'] == 2) {
	$puntos += 1;
	}
	if ($datos['conyuge'] == 3) {
	$puntos += -1;
	}

//14

// TELEFONO ------------------------------------
	
	if ($datos['telefono'] == 00001) {
	$puntos += 3;
	}
	if ($datos['telefono'] == 00010) {
	$puntos += 1;
	}
	if ($datos['telefono'] == 00011) {
	$puntos += 2;
	}
	if ($datos['telefono'] == 00100) {
	$puntos += 2;
	}

//SITUACION LABORAL ------------------------------------
	
	if ($datos['situacion_laboral'] == 2) {
	$puntos += 5;
	}
	if ($datos['situacion_laboral'] == 3) {
	$puntos += 2;
	}
	if ($datos['situacion_laboral'] == 4) {
	$puntos += 2;
	}
	if ($datos['situacion_laboral'] == 5) {
	$puntos += 1;
	}
	if ($datos['situacion_laboral'] == 7) {
	$puntos += -1;

	}


//ANTIGUEDAD LABORAL ------------------------------------
	
	if ($datos['antiguedad_lab'] == 2) {
	$puntos += -5;
	}
	if ($datos['antiguedad_lab'] == 4) {
	$puntos += 1;
	}
	if ($datos['antiguedad_lab'] == 5) {
	$puntos += 2;
	}
	if ($datos['antiguedad_lab'] == 6) {
	$puntos += 3;
	}
	if ($datos['antiguedad_lab'] == 7) {
	$puntos += 4;
	}
	if ($datos['antiguedad_lab'] == 8) {
	$puntos += 5;
	}

//MERCADO LABORAL ------------------------------------
	
	if ($datos['mercado_laboral'] == 2) {
	$puntos += 5;
	}
	if ($datos['mercado_laboral'] == 3) {
	$puntos += 5;
	}
	if ($datos['mercado_laboral'] == 4) {
	$puntos += 5;
	}
	if ($datos['mercado_laboral'] == 5) {
	$puntos += 5;
	}
	if ($datos['mercado_laboral'] == 6) {
	$puntos += 3;
	}
	

//IN SITU ------------------------------------
	
	if ($datos['insitu'] == 1) {
	$puntos += 5;
	}
	if ($datos['insitu'] == 2) {
	$puntos += -50;
	}

//FAJA ------------------------------------

	if ($datos['faja'] >= 'A' && $datos['faja'] <= 'G' ) {
	$puntos += 5;
	}
	if ($datos['faja'] >='H' && $datos['faja'] <= 'J' ) {
	$puntos += 4;
	}
	if ($datos['faja'] >='K' && $datos['faja'] <= 'L' ) {
	$puntos += 3;
	}
	if ($datos['faja'] >='M' && $datos['faja'] <= 'N' ) {
	$puntos += 1;
	}
	if ($datos['faja'] >='O' && $datos['faja'] <= 'Z' ) {
	$puntos += -50;
	}


//CUENTA BANCARIA ------------------------------------

	if ($datos['cuenta_bancaria'] == 2) {
	$puntos += 1;
	}
	if ($datos['cuenta_bancaria'] == 3) {
	$puntos += 2;
	}
	if ($datos['cuenta_bancaria'] == 4) {
	$puntos += 3;
	}


//PRODUCTOS ------------------------------------

	if ($datos['producto'] == 2) {
	$puntos += 2;
	}
	if ($datos['producto'] == 3) {
	$puntos += 1;
	}


//MERCADO ------------------------------------

	if ($datos['mercado'] == 2) {
	$puntos += 2;
	}
	if ($datos['mercado'] == 3) {
	$puntos += 1;
	}

	
//OPERACION ------------------------------------


	if ($datos['cantidad_cuota'] >= 1 && $datos['cantidad_cuota'] <= 5) {
	$puntos += 1;
	}
	if ($datos['cantidad_cuota'] >= 6 && $datos['cantidad_cuota'] <= 8) {
	$puntos += 3;
	}
	if ($datos['cantidad_cuota'] >= 9 && $datos['cantidad_cuota'] <= 12) {
	$puntos += 4;
	}
	if ($datos['cantidad_cuota'] >= 17 && $datos['cantidad_cuota'] <= 25) {
	$puntos += -2;
	}

	

//ENTREGA ------------------------------------

	if ($datos['entrega'] >= 1) {
	$puntos += 1;
	}

//MONTO CUOTA  ------------------------------------

	if ($datos['monto_cuota'] >= 0 && $datos['monto_cuota'] <= 199999) {
	$puntos += 4;
	}
	if ($datos['monto_cuota'] >= 200000 && $datos['monto_cuota'] >= 399999) {
	$puntos += 3;
	}
	if ($datos['monto_cuota'] >= 400000 && $datos['monto_cuota'] >= 549000) {
	$puntos += 2;
	}
	if ($datos['monto_cuota'] >= 550000 && $datos['monto_cuota'] >= 749000) {
	$puntos += 1;
	}
	if ($datos['monto_cuota'] >= 750000 && $datos['monto_cuota'] >= 799000) {
	$puntos += -1;
	}
	if ($datos['monto_cuota'] >= 800000 && $datos['monto_cuota'] >= 999999) {
	$puntos += -2;
	}
	if ($datos['monto_cuota'] >= 1000000 && $datos['monto_cuota'] >= 1199000) {
	$puntos += -3;
	}
	if ($datos['monto_cuota'] >= 1200000 && $datos['monto_cuota'] >= 99999999999) {
	$puntos += -4;
	}


//INGRESO ------------------------------------

	if ($datos['ingreso'] >= 2500000 && $datos['ingreso'] <= 2999999) {
	$puntos += 1;
	}
	if ($datos['ingreso'] >= 3000000 && $datos['ingreso'] <= 4999999) {
	$puntos += 2;
	}
	if ($datos['ingreso'] >= 5000000 && $datos['ingreso'] <= 99999999999) {
	$puntos += 3;
	}
	if ($datos['ingreso'] <= 0) {
	$puntos += -50;
	}


//MORA INTERNA 	 ------------------------------------

	if ($datos['mora_interna'] == 'XXXX') {
	$puntos += 4;
	}
	if ($datos['mora_interna'] == 'XXX') {
	$puntos += 3;
	}
	if ($datos['mora_interna'] == 'XX') {
	$puntos += 2;
	}
	if ($datos['mora_interna'] == 'X') {
	$puntos += -4;
	}
	if ($datos['mora_interna'] == '-X') {
	$puntos += -5;
	}
	if ($datos['mora_interna'] == '-XX') {
	$puntos += -50;
	}

//MORA EXTERNA 	 ------------------------------------

	if ($datos['mora_externa'] == 2) {
	$puntos += 5;
	}
	if ($datos['mora_externa'] == 3) {
	$puntos += 4;
	}	
	if ($datos['mora_externa'] == 4) {
	$puntos += 3;
	}
	if ($datos['mora_externa'] == 5) {
	$puntos += -3;
	}
	if ($datos['mora_externa'] == 6) {
	$puntos += -4;
	}
	if ($datos['mora_externa'] == 7) {
	$puntos += -50;
	}

//39
//CLIENTE RECURRENTE ------------------------------------

	if ($datos['ingreso']==0) {
		$datos['ingreso'] = 1;
	}
	$coeficiente = $datos['deuda_mensual'] / $datos['ingreso'];

	if($datos['cliente'] == 'CLIENTE'){
		$puntos += 2;

		if($datos['ingreso']>=0 && $datos['ingreso']<=4999999)
		{

			if ($coeficiente >= 0 && $coeficiente <= 0.10) {
			$puntos += 5;
			}
			if ($coeficiente >= 0.11 && $coeficiente <= 0.20) {
			$puntos += 4;
			}
			if ($coeficiente >= 0.21 && $coeficiente <= 0.30) {
			$puntos += 3;
			}
			if ($coeficiente >= 0.31 && $coeficiente <= 0.40) {
			$puntos += 2;
			}
			if ($coeficiente >= 0.41 && $coeficiente <= 9999) {
			$puntos += -50;
			}
		}else{
			if ($coeficiente >= 0 && $coeficiente <= 0.10) {
			$puntos += 5;
			}
			if ($coeficiente >= 0.11 && $coeficiente <= 0.20) {
			$puntos += 4;
			}
			if ($coeficiente >= 0.21 && $coeficiente <= 0.30) {
			$puntos += 3;
			}
			if ($coeficiente >= 0.31 && $coeficiente <= 0.45) {
			$puntos += 2;
			}
			if ($coeficiente >= 0.46 && $coeficiente <= 9999) {
			$puntos += -50;
			}

		
	}
	}else{


		if($datos['ingreso']>=0 && $datos['ingreso']<=4999999)
		{
			if ($coeficiente >= 0 && $coeficiente <= 0.10) {
			$puntos += 5;
			}
			if ($coeficiente >= 0.11 && $coeficiente <= 0.20) {
			$puntos += 4;
			}
			
			if ($coeficiente >= 0.21 && $coeficiente <= 0.30) {
			$puntos += 3;
			}
			if ($coeficiente >= 0.31 && $coeficiente <= 0.35) {
			$puntos += 2;
			}
			if ($coeficiente >= 0.36 && $coeficiente <= 9999) {
			$puntos += -50;
		}
	}else{

			if ($coeficiente >= 0 && $coeficiente <= 0.10) {
			$puntos += 5;
			}
			if ($coeficiente >= 0.11 && $coeficiente <= 0.20) {
			$puntos += 4;
			}
			if ($coeficiente >= 0.21 && $coeficiente <= 0.30) {
			$puntos += 3;
			}
			if ($coeficiente >= 0.31 && $coeficiente <= 0.40) {
			$puntos += 2;
			}
			if ($coeficiente >= 0.41 && $coeficiente <= 9999) {
			$puntos += -50;
			}
	
	}

	}

		$puntos +=2;
		if($puntos<=40){
			//alert("atencion");
			if($datos['nuevo_mundo'] == 1){
				$endeudamiento 			= 0.12;
				$coheficiente_cuota 	= 12;
				//alert("a");
			}

			if($datos['ref_comercial'] == 1){
				$endeudamiento 		= 0.15;
				$coheficiente_cuota 	= 14;
				//alert("b");
			}

			if($datos['mora_interna'] == 'XXXX' || $datos['mora_interna'] == 'XXX'){
				$endeudamiento 		= 0.35;
				$coheficiente_cuota 	= 12;
				//alert("c");	
			} 	 
		}

		if($puntos>=41){
			if($datos['nuevo_mundo'] == 1){
				$endeudamiento 		= 0.30;
				$coheficiente_cuota 	= 12;	 
			}

			if($datos['ref_comercial'] == 1){
				$endeudamiento 		= 0.35;	 
				$coheficiente_cuota 	= 14;
			}

			if($datos['mora_interna'] == 'XXXX' || $datos['mora_interna'] == 'XXX'){
				$endeudamiento 		= 0.40;
				$coheficiente_cuota 	= 12;	 
			}	
		}
		
		$capacidad = ($datos['ingreso']*$endeudamiento)-$datos['deuda_mensual']; 
		$limite_prestable = $capacidad*$coheficiente_cuota;

		if($limite_prestable<=1000){
			$limite_prestable = 0;
		}
		echo "cuenta:".$datos['cuenta']."<br>";
		//echo "puntos:".$puntos ."<br>";
		echo "limite:".$limite_prestable ."<br>";
		"br";

		$envio->cuenta = $datos['cuenta'];
		$envio->linea = $limite_prestable;
		$envio->cargarLineaScoring();
		continue;

}
	

?>

