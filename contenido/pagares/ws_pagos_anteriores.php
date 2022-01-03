<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

function normalizar($variable){

	$originales = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
	$modificadas = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';

	$valor = trim($variable);
	$valor = utf8_decode($valor);
	$valor = strtr($valor, utf8_decode($originales), $modificadas);
	$valor = strtoupper($valor);
	$valor = preg_replace('/[^a-zA-Z0-9\']/', ' ', $valor);
	$valor = str_replace("'", '', $valor);
	$valor = str_replace("  ", ' ', $valor);
	$valor = utf8_encode($valor);
	$valor = mb_convert_encoding($valor, "UTF-8");
	return $valor;
}

try{
	define('usuario',' user=postgres');
	define('password',' password=postgres');
	define('port',' port=5432');
	define('host',' host=192.168.4.200');
	define('bd',' dbname=base_fc');

	$conexion = usuario.password.port.bd.host;
	$conectar = pg_connect("$conexion") or die("No se conecto al servidor");

	//$wsdl = 'http://gisa.com.py:1010/cph/apicphdat.aspx?wsdl';
	$wsdl = 'http://138.186.63.142:2121/Lotes/apicphdat.aspx?Wsdl';
	$soapclient = new SoapClient($wsdl, array('cache_wsdl' => WSDL_CACHE_NONE));	

	$init 					= new stdClass;
	$init->Wusu 			= "FACILAND";
	$init->Wpass 			= "F4c1L4D1A";
	$init->Wip  			= "190.128.235.138";
	$fecha_proceso 			= '2021-03-18';
print	$sql = "SELECT * from (SELECT 	lote.lote, 
	operacion ope,
	trim(split_part(split_part(aadocu,'-',1),' ',1)) cedula,  
	becta cuota, 
	fsd015.cumonn recibo,
	fsd015.cufech fecha,
	round(bkcapi) capital,
	round(bkinte) interes,
	round(bkmora+bkpuni) mora, 
	round(bkley+bkgtos+bkiva) otros,
	''::text forma,
	bkcaje cajero,
	cutime hora,
	''::text BoletaBanco,
	round(bkcapi+bkinte+bkmora+bkpuni+bkley+bkgtos+bkiva) total,	
	''::text Obs

	from 	fsd0172,
	fsd015,
	fsd0151, 
	pagares.operaciones, 
	pagares.lote, 
	fsd0011 
	
	where 
	fsd015.cumonn=fsd0151.cumonn
	and bknume=fsd015.cumonn
	and lote.lote=operaciones.lote
	and operacion=cuope1
	and fsd0172.beope1=operacion
	and cucuen=fsd0011.aacuen
	and fsd015.cuempr=1
	and cutipo!=80
	and (fsd0151.fopago!=10 and fsd0151.fopago!=7)
	and entidad=3
	and lote.modo=1
	and lote.lote>=263
	and trim(estado_wsdl)='APROBADO'
	and cucuot>0
	and (bkcaje!='PRONET' and bkcaje!='PAGOEXPRES') 
	and fsd015.cufech = '$fecha_proceso'::date
	union

	SELECT 	lote.lote, 
	operacion ope,
	split_part(trim(fsd0011.aadocu),'-',1) cedula,  
	becta cuota, 
	fsd015.cumonn recibo,
	fsd015.cufech fecha,
	round(bkcapi) capital,
	round(bkinte) interes,
	round(bkmora+bkpuni) mora, 
	round(bkley+bkgtos+bkiva) otros,
	''::text forma,
	bkcaje cajero,
	cutime hora,
	''::text BoletaBanco,
	round(bkcapi+bkinte+bkmora+bkpuni+bkley+bkgtos+bkiva) total,	
	''::text Obs

	from 	fsd0172,
	fsd015,
	fsd0151, 
	pagares.operaciones, 
	pagares.lote, 
	fsd0011 
	
	where 
	fsd015.cumonn=fsd0151.cumonn
	and bknume=fsd015.cumonn
	and lote.lote=operaciones.lote
	and operacion=cuope1
	and fsd0172.beope1=operacion
	and cucuen=fsd0011.aacuen
	and fsd015.cuempr=1
	and cutipo!=80
	and (fsd0151.fopago!=10 and fsd0151.fopago!=7)
	and entidad=3
	and lote.modo=1
	and lote.lote>=263
	and trim(estado_wsdl)='APROBADO'
	and cucuot>0
	and bkcaje='PRONET' 

	and case 
	when date_part('dow','$fecha_proceso'::date) = 1 then fsd015.cufech = '$fecha_proceso'::date-4
	when date_part('dow','$fecha_proceso'::date) = 2 then fsd015.cufech = '$fecha_proceso'::date-4
	when date_part('dow','$fecha_proceso'::date) = 3 then fsd015.cufech BETWEEN '$fecha_proceso'::date-4 AND '$fecha_proceso'::date-2
	when date_part('dow','$fecha_proceso'::date) = 4 then fsd015.cufech = '$fecha_proceso'::date-2
	when date_part('dow','$fecha_proceso'::date) = 5 then fsd015.cufech = '$fecha_proceso'::date-2
	end
	union
	SELECT 	lote.lote, 
	operacion ope,
	split_part(trim(fsd0011.aadocu),'-',1) cedula,  -
	becta cuota, 
	fsd015.cumonn recibo,
	fsd015.cufech fecha,
	round(bkcapi) capital,
	round(bkinte) interes,
	round(bkmora+bkpuni) mora, 
	round(bkley+bkgtos+bkiva) otros,
	''::text forma,
	bkcaje cajero,
	cutime hora,
	''::text BoletaBanco,
	round(bkcapi+bkinte+bkmora+bkpuni+bkley+bkgtos+bkiva) total,	
	''::text Obs

	from 	fsd0172,
	fsd015,
	fsd0151, 
	pagares.operaciones, 
	pagares.lote, 
	fsd0011 
	
	where 
	fsd015.cumonn=fsd0151.cumonn
	and bknume=fsd015.cumonn
	and lote.lote=operaciones.lote
	and operacion=cuope1
	and fsd0172.beope1=operacion
	and cucuen=fsd0011.aacuen
	and fsd015.cuempr=1
	and cutipo!=80
	and (fsd0151.fopago!=10 and fsd0151.fopago!=7)
	and entidad=3
	and lote.modo=1
	and lote.lote>=263
	and trim(estado_wsdl)='APROBADO'
	and cucuot>0
	and bkcaje='PAGOEXPRES' 

		and case 
	when date_part('dow','$fecha_proceso'::date) = 1 then fsd015.cufech = '$fecha_proceso'::date-4
	when date_part('dow','$fecha_proceso'::date) = 2 then fsd015.cufech BETWEEN '$fecha_proceso'::date-4 AND '$fecha_proceso'::date-2
	when date_part('dow','$fecha_proceso'::date) = 3 then fsd015.cufech = '$fecha_proceso'::date-2
	when date_part('dow','$fecha_proceso'::date) = 4 then fsd015.cufech = '$fecha_proceso'::date-2
	when date_part('dow','$fecha_proceso'::date) = 5 then fsd015.cufech = '$fecha_proceso'::date-2
	end
	order by 6,1,2) as datos_final where lote=399;";

	$query = pg_query($conectar, $sql);
	while($row  =  pg_fetch_array($query,NULL, PGSQL_ASSOC)){

		$init->Lotepgo  		= new stdClass;
		$init->Lotepgo->lote 	= $row["lote"];
		$init->Lotepgo->ope 	= $row["ope"];
		$init->Lotepgo->cedula 	= normalizar($row["cedula"]);
		$init->Lotepgo->recibo 	= $row["recibo"];
		$init->Lotepgo->fecha 	= $row["fecha"];
		$init->Lotepgo->cuota 	= $row["cuota"];

		$init->Lotepgo->capital	= $row["capital"];
		$init->Lotepgo->interes	= $row["interes"];
		$init->Lotepgo->mora	= $row["mora"];
		$init->Lotepgo->otros 	= $row["otros"];
		$init->Lotepgo->forma 	= $row["forma"];
		$init->Lotepgo->cajero 	= normalizar($row["cajero"]);
		$init->Lotepgo->hora 	= $row["hora"];
		$init->Lotepgo->BoletaBanco	= ''; 
		$init->Lotepgo->total 	= $row["total"];
		$init->Lotepgo->Obs 	= '';
		$resultado 				= $soapclient->PAGOS($init);	

	}

} catch(Exception $e){
	echo "<b>Error...</b><br>";
	echo $e->getMessage();
}

?>

