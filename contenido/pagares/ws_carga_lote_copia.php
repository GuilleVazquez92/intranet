<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
require('../../controlador/wsdl.php');

	$data = new WSDL();
	$data->lote = $_GET['lote'];
	$data->entidad = 3;
	$data->accion = 'AgregarLote';
	$datos = $data->detalle_operaciones();
	var_dump($datos);	

?>

