<?php

//require('../../controlador/main.php');
//require( CONTROLADOR . 'pagares.php');
//$data = new PAGARES();
//header("Content-type:application/pdf");
//$data->visualizar_pdf($_GET['lote'],$_GET['operacion'],$_GET['tipo_file']);



function foto($lote,$operacion,$tipo_file) {

	define('usuario',' user=postgres');
	define('password',' password=postgres');
	define('port',' port=5432');
	define('host',' host=192.168.4.200');
	define('bd',' dbname=base_fc');

	$conexion = usuario.password.port.bd.host;
	$conectar	= pg_connect("$conexion") or die("No se conecto al servidor");

	$sql = "SELECT $tipo_file FROM pagares.operaciones WHERE lote=$lote and operacion=$operacion";
	$query = pg_query($conectar,$sql);


	while($row = pg_fetch_array($query)){
		$imagen = $row[0];
	}	

	$imagen = base64_decode($imagen);
	header("Content-type:application/pdf");
	echo $imagen;
}

foto($_GET['lote'],$_GET['operacion'],$_GET['tipo_file']);


?>