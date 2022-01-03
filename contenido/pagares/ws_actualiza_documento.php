<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

try{
	define('usuario',' user=postgres');
	define('password',' password=postgres');
	define('port',' port=5432');
	define('host',' host=192.168.4.200');
	define('bd',' dbname=base_fc');
	$conexion = usuario.password.port.bd.host;
	$conectar	= pg_connect("$conexion") or die("No se conecto al servidor");

	//$wsdl ='http://gisa.com.py:1010/cph/apicphdat.aspx?wsdl';	
	$wsdl = 'http://138.186.63.142:2121/Lotes/apicphdat.aspx?Wsdl';
	$soapclient = new SoapClient($wsdl, array('cache_wsdl' => WSDL_CACHE_NONE));

	$sql = "SELECT datos.* 
	FROM
		(
			SELECT 
			operaciones.lote lote,
			fsd0122.bfope1 pagare, 
			trim(split_part(split_part(aadocu,'-',1),' ',1)) cedula,
			'C'::text tipo,
			coalesce(file_cedula,'') documento
			FROM 
			base_fc.public.fsd0011 fsd0011, 
			base_fc.public.fsd0122 fsd0122,
			pagares.operaciones
			WHERE fsd0122.aacuen = fsd0011.aacuen 
			AND fsd0122.bfope1=operaciones.operacion 
			AND check_cedula=0
			AND (trim(estado_wsdl)='PENDIENTE' or trim(estado_wsdl)='APROBADO')

			union
			SELECT 
			operaciones.lote lote,
			fsd0122.bfope1 pagare, 
			trim(split_part(split_part(aadocu,'-',1),' ',1)) cedula,
			'I'::text tipo,
			coalesce(file_informconf,'') documento
			FROM 
			base_fc.public.fsd0011 fsd0011, 
			base_fc.public.fsd0122 fsd0122,
			pagares.operaciones
			WHERE fsd0122.aacuen = fsd0011.aacuen 
			AND fsd0122.bfope1=operaciones.operacion 
			AND check_informconf=0
			AND (trim(estado_wsdl)='PENDIENTE' or trim(estado_wsdl)='APROBADO')

			union
			SELECT 
			operaciones.lote lote,
			fsd0122.bfope1 pagare, 
			trim(split_part(split_part(aadocu,'-',1),' ',1)) cedula,
			'P'::text tipo,
			coalesce(file_pagare,'') documento
			FROM 
			base_fc.public.fsd0011 fsd0011, 
			base_fc.public.fsd0122 fsd0122,
			pagares.operaciones
			WHERE fsd0122.aacuen = fsd0011.aacuen 
			AND fsd0122.bfope1=operaciones.operacion 
			AND check_pagare=0
			AND (trim(estado_wsdl)='PENDIENTE' or trim(estado_wsdl)='APROBADO')	

		) as datos, pagares.lote
	where lote.lote=datos.lote
	and entidad=3
	and length(documento)>0 
	order by 4,2";

	$query = pg_query($conectar, $sql);
	while($row  =  pg_fetch_array($query,NULL, PGSQL_ASSOC)){

		$init 					= new stdClass;
		$init->Wusu 			= "FACILAND";
		$init->Wpass 			= "F4c1L4D1A";
		$init->Wip  			= "190.128.235.138";
		$init->Lote 			= $lote = $row['lote'];
		$init->Pagare 			= $operacion = $row['pagare'];
		$init->Cedula 			= $row['cedula'];
		$init->Wtipo 			= $row['tipo'];
		$init->Documento 		= $row['documento'];

		$resultado = $soapclient->DOCUMENTOS($init);
		$respuesta = (json_decode(json_encode($resultado), true));

		if($respuesta['Resp']==0){
			$campo = "";
			switch (trim($row['tipo'])) {
				case 'C':
				$campo = 'check_cedula=1';
				break;
				case 'I':
				$campo = 'check_informconf=1';
				break;				
				case 'P':
				$campo = 'check_pagare=1';
				break;
			}

			$sql_bandera = "UPDATE pagares.operaciones SET ".$campo." WHERE lote=$lote AND operacion=$operacion;";
			pg_query($sql_bandera);
		}
		unset($init);
	}
	echo "listo";
} catch(Exception $e){
	echo "<b>Error...</b><br>";
	echo $e->getMessage();
}
?>

