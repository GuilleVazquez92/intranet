<?php

require('../../controlador/main.php');
require( CONTROLADOR . 'ir_regularizacion.php');

$data = new REGULARIZACION();

$data->cuenta 		= $_POST['cuenta']; 
$data->tipo 		= $_POST['tipo_acuerdo'];
$data->monto_cuota 	= $_POST['monto_cuota'];
$data->plazo 		= $_POST['plazo'];
$data->canceladas 	= $_POST['canceladas'];
$data->fecha_acuerdo= $_POST['fecha_acuerdo'];
$data->fecha_1ervto = $_POST['fecha_1ervto'];
$data->reg_operacion_crear();

?>

