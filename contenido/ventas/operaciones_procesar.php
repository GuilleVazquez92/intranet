<?php
	require('../../controlador/main.php');
	require( CONTROLADOR . 'ventas.php');
	$data = new Ventas();
	$data->operacion = $_POST['operacion'];
	$data->usuario 	 = $_POST['usuario'];

	if(isset($_POST['estado'])){
		$data->estado 	 = $_POST['estado'];
		$data->agendado  = $_POST['agendado'];
		print json_encode($data->cambiar_estado());

	}

	if(isset($_POST['desistir'])){
		$data->desistir  = $_POST['desistir'];
		print json_encode($data->desistir_operacion());
	}

	
	if(isset($_POST['confirmar'])){
		$data->confirmar  = $_POST['confirmar'];
		
		if(isset($_POST['contencion'])){
			$data->contencion  = $_POST['contencion'];
		}
		print json_encode($data->confirmar_venta());
	}

	if(isset($_POST['comentario'])){
		$data->comentario  = $_POST['comentario'];
		print json_encode($data->agregar_comentario());
	}

	if(isset($_POST['productos'])){

		$datos = $data->detalle_productos();
		$resultado = "";
		foreach ($datos as $key) {
			$resultado .= "<small>".$key['producto_corto']."</small><br>";
		}
		print $resultado;
	}

?>