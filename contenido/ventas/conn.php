<?Php
	session_start();

	if(!isset($_SESSION['verificado'])){ 
		$_SESSION['verificado'] = "OFF"; 
	}	

	define('usuario_v',' user=postgres');
	define('password_v',' password=postgres');
	define('port_v',' port=5432');
	define('bd_v',' dbname=base_fc');
	define('host_v',' host=192.168.4.200');

	$conexion = usuario_v.password_v.port_v.bd_v.host_v;
	$conectar	= pg_connect("$conexion") or die("No se conecto al servidor");
	
?>	