<?php 
require('../../controlador/main.php');
require( CONTROLADOR . 'pagares.php');
$pagares = new PAGARES();
$pagares->lote = $_POST['lote'];

if(isset($_POST['descripcion'])){
	$pagares->observacion = $_POST['descripcion'];
	$pagares->descripcion();
}

if(isset($_POST['modo'])){
	$pagares->modo = $_POST['modo'];
	$pagares->modo();
}

?>