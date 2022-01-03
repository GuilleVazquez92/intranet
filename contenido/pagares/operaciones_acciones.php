<?php 
require('../../controlador/main.php');
require( CONTROLADOR . 'pagares.php');
$data = new PAGARES();

// Agregar operacion
if(isset($_POST['cargar'])){

	$data->lote  		= $_SESSION['lote'] = $_POST['lote'];
	$data->operacion 	= $_POST['operacion'];
	$resultado = $data->cargar_operacion();

	if($resultado==0){
		echo '<div class="alert alert-danger" role="alert">No se encontró la operación o ya existe en un LOTE...</div>';
	
	}else{
		echo '<div class="alert alert-success" role="alert">Se agregó la operación correctamente...</div>';
	}
}

// Quitar operacion
if(isset($_POST['quitar'])){

	$data->lote  		= $_SESSION['lote'] = $_POST['lote'];
	$data->operacion 	= $_POST['operacion'];
	$resultado = $data->quitar_operacion();

	if($resultado==0){
		echo '<div class="alert alert-danger" role="alert">No se encontró la operación...</div>';
	
	}else{
		echo '<div class="alert alert-success" role="alert">Se quito la operación correctamente...</div>';
	}
}

if(isset($_POST['actualizar_pdf'])){

	$lote = $_POST['lote'];
	$operacion = $_POST['operacion'];
	$data->actualizar_pdf($lote,$operacion,1);
	$data->actualizar_pdf($lote,$operacion,2);
	$data->actualizar_pdf($lote,$operacion,3);
	$data->actualizar_lote();

}


?>