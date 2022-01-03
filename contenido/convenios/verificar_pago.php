<?php 
	if(isset($_POST['movimiento']) && isset($_POST['orden'])){

		require('../../controlador/main.php');
		require( CONTROLADOR . 'convenio.php');

		$convenio = new Convenios();
		$convenio->movimiento= $_POST['movimiento'];
		$convenio->orden 	 = $_POST['orden'];
		$convenio->usuario 	 = $_POST['usuario'];
		$convenio->estado 	 = $_POST['estado'];
		$convenio->verificar_pago();
	}
?>

