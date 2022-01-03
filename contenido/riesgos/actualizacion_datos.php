<?php 
if(isset($_POST["cuenta"])){

	require('../../controlador/main.php');
	require( CONTROLADOR . 'riesgos.php');
	$data = new Riesgos();
	$data->cuenta = $_POST["cuenta"];

	if(isset($_POST["etiqueta"])){

		$tabla_direccion = array(
			"vivienda");

		$tabla_fsd0011 	 = array(
			"estado_civil",
			"cant_hijos");

		$tabla_scoring 	 = array(
			"nuevo_mundo",
			"servicios_basicos",
			"conyuge","telefono",
			"situacion_laboral",
			"antiguedad_lab",
			"mercado_laboral",
			"insitu", 
			"cuenta_bancaria",
			"mas_cuenta",
			"producto",
			"mercado",
			"ingreso",
			"mora_externa",
			"deuda_mensual",
			"total_deuda_ex",
			"ref_comercial"
		);

		$etiqueta 	= $_POST['etiqueta'];
		$valor 		= $_POST['valor'];
		$campo 		= "aacuen";

		foreach ($tabla_fsd0011 as $datos) {
			if($_POST['etiqueta']==$datos){
				$tabla 		= "fsd0011";
				$etiqueta 	= ($_POST['etiqueta'] == "estado_civil") ? "aaesta" : "aahijo";
				break;
			}
		}

		foreach ($tabla_scoring as $datos) {
			if($_POST['etiqueta'] == $datos){
				$tabla = "riesgos.datos_scoring";
				$campo = "cuenta";
				break;
			}
		}

		foreach ($tabla_direccion as $datos) {
			if($_POST['etiqueta'] == $datos){
				$tabla = "fsd022";
				$etiqueta = "awprop";
				break;
			}
		}

		if($tabla){
			print $data->actualizacion_datos($tabla,$etiqueta,$valor,$campo);		
		}

	}

	if($_POST['cuenta'] && $_POST['dato'] && $_POST['zona']){
				
		$data->check_validacion($_POST['cuenta'],$_POST['zona'],$_POST['dato']);	
	}

	if($_POST["usuario"] && $_POST["cargo"]){

		$data->firmar_scoring($_POST["usuario"],$_POST["cargo"]);
	}
	
	if($_POST['cuenta'] && $_POST['zona'] && $_POST['zona']=='linea_credito'){
		
		$data->cuenta 		 = $_POST['cuenta'];
		$data->linea_credito = $_POST['linea_credito'];
		$data->linea_credito();
	}


	if($_POST["usuario"] && $_POST["cargo"] && $_POST["funcion"]){
	
		$data->ampliacion_linea($_POST['cuenta'],$_POST['funcion']);	
	}

	if($_POST["usuario"] && $_POST["tipo"]){
				
		$data->agregar_scoring($_POST['usuario'],$_POST['nombre']);	
	}
}	
?>