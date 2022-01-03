<?php 
require('../../controlador/main.php');
require( CONTROLADOR . 'compras.php');

$compra = new Compras();

$compra->codigo = $_POST['codigo'];
$compra->costo = $_POST['costo'];

$compra->editarCosto();






/*$pagares = new PAGARES();
$pagares->lote = $_POST['lote'];

if(isset($_POST['descripcion'])){
	$pagares->observacion = $_POST['descripcion'];
	$pagares->descripcion();
}

if(isset($_POST['modo'])){
	$pagares->modo = $_POST['modo'];
	$pagares->modo();
}
*/
?>