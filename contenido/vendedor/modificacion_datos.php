<?php
require('../../controlador/main.php');
require( CONTROLADOR . 'vendedores.php');
$datos = new Vendedores();
$datos->cuenta  = $_POST['cuenta'];
$datos->campo = $_POST['campo'];
$datos->valor = $_POST['valor'];

switch ($_POST['zona']) {
	case 1:
		$datos->tabla = 'fsd0011';
		break;
	case 2:
		$datos->tabla = 'fsd022';
		break;
	case 3:
		$datos->tabla = 'fsd023';
		break;				
}
$datos->modificar_datos();
?>
