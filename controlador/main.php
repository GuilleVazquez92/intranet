<?php 
	# Activa Session
	session_start();
	header('Cache-Control: no cache'); 

	# Definicion de rutas HTMLs
	define('HTTPS','https://intranet.facilandia.com.py/v5/');
	define('HTTP','http://intranet.facilandia.com.py/v5/');
	//define('LOCAL','http://localhost/intranet/');
	define('ROOT',HTTPS);  # habiliat al final

	# Definicion de rutas directorios

	define('APPLICATION', '/var/www/html/intranet/');  #produccion
	//define('APPLICATION', 'C:/xampp/htdocs/intranet/');  #local
	define('IMAGE', ROOT . 'image/');
	define('INCLUDES', ROOT . 'includes/');
	define('ARCHIVOS', ROOT . 'documentos/');
	define('CSS', ROOT . 'css/');
	define('JS', ROOT . 'js/');
	define('MODELO', APPLICATION . 'modelo/');
	define('CONTROLADOR', APPLICATION . 'controlador/');

	# Controla si el cookie expiro 
	if(!isset($_COOKIE['usuario'])){

		$http =  'https://'.$_SERVER["SERVER_NAME"].dirname($_SERVER['PHP_SELF'])."/";
		$https = 'https://'.$_SERVER["SERVER_NAME"].dirname($_SERVER['PHP_SELF'])."/";

		if(ROOT != $http || ROOT != $https){

			header("Location:".ROOT, true, 301);
			exit();
		}
	}else{
		$control = 0;
		$cant_menu = (isset($_SESSION['menu'])) ? count($_SESSION['menu']) : 0;

		if(basename($_SERVER["PHP_SELF"]) != "index.php"){

			if(basename($_SERVER["PHP_SELF"]) != "401.php"){

				for ($i=0; $i < $cant_menu ; $i++) {
					$control = ("/intranet/".$_SESSION['menu'][$i] == $_SERVER['PHP_SELF']) ? 1 : 0 ;
					if($control == 1){
						break;
					}
				}
				
				if($control==0){
					//header("Location:".ROOT.'401.php');
					//exit;
				}
			}
		}
	}

	# Variable
	$mensaje_error 	 = "";
	$mensaje_warning = "";
	$titulo 		 = "Intranet";

	# Login
	require( MODELO . 'config.php');
	$user = new UserLogin;
	
	# Proceso de Logeo del usuario 
	if(isset($_POST['usuario']) && isset($_POST['password']) && isset($_POST['logeo'])){

		$user->usuario  = $_POST['usuario'];
		$user->password = $_POST['password'];
		$mensaje  		= $user->login();

		if(isset($mensaje['warning'])){
			$user->logout();	
			$mensaje_warning = $mensaje['warning'];		
		
		}else{
			$user->logout();
			$mensaje_error 	 = $mensaje['error'];
		}
	}

	if(isset($_POST['logout']) && $_POST['logout']==1){
		$user->logout();
		header('Location: ' . ROOT);
	}
?>