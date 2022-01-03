<?php  
	session_start();

	
	if(isset($_SESSION['user']) && isset($_SESSION['perfil'])){

		require_once('../../conn/conexion.php');


	}else{
		session_unset();
		echo '<meta http-equiv=refresh content=0;URL=../index.php>'; 
	}	
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
		<title>Intranet</title>
		<link rel="stylesheet" href="../../css/bootstrap.min.css">
		<link rel="stylesheet" href="../../css/estilo.css?v=<?= date('Ymdhs');?>">
	</head>	
	<body>
		<div class="container-fluid">
