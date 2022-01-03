<?php
try{

	//$wsdl ='http://gisa.com.py:1010/cph/apicphdat.aspx?wsdl';	
	$wsdl = 'http://138.186.63.142:2121/Lotes/apicphdat.aspx?Wsdl';
	$soapclient 	= new SoapClient($wsdl, array('cache_wsdl' => WSDL_CACHE_NONE));	

	if(defined('usuario') == false){

		define('usuario',' user=postgres');
		define('password',' password=postgres');
		define('port',' port=5432');
		define('host',' host=192.168.4.200');
		define('bd',' dbname=base_fc');
	}

	$conexion = usuario.password.port.bd.host;
	$conectar = pg_connect("$conexion") or die("No se conecto al servidor");

	$sql = "SELECT distinct(operaciones.lote) lote
	from pagares.operaciones, pagares.lote 
	where lote.lote=operaciones.lote 
	and entidad=3 
	and (estado_wsdl='PENDIENTE' or (estado_operacion=2 and estado_wsdl='') or estado_wsdl='' or estado_wsdl is null)
	and lote.lote>=263
	order by 1";
	$query = pg_query($conectar,$sql);
	if(pg_num_rows($query)!=0){
		while ($row = pg_fetch_array($query)) {
			
			unset($init);
			unset($data);	
			
			$init 			= new stdClass;
			$init->Wusu 	= "FACILAND";
			$init->Wpass 	= "F4c1L4D1A";
			$init->Wip 		= "?";
			$init->Wloten 	= $lote = $row['lote'];
			$init->Resp 	= "?";

			$datosWSDL = json_decode(json_encode($soapclient->VERLOTE($init)), true);
			$data = [];
			$data = $datosWSDL;

			$datos = $data["Wlote"]["Ops"]["OpsItem"];
			if($datos){
				foreach ($datos as $item) {
					$operacion 	=  $item["loteop"];
					$estado  	= $item["LOTERES"];

					if(strlen($estado)>0){
						$sql_update = "UPDATE pagares.operaciones SET estado_wsdl='$estado' 
						WHERE operacion=$operacion 
						AND lote=$lote 
						AND (estado_wsdl!='$estado' or estado_wsdl is null)";
						pg_query($conectar,$sql_update);
					}
				}
			}
		}
	}
} catch(Exception $e){
	echo "<b>Error...</b><br>";
	echo $e->getMessage();
}
?>

