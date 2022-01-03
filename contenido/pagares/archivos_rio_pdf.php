<?php

define('usuario',' user=postgres');
define('password',' password=postgres');
define('port',' port=5432');
define('host',' host=192.168.4.200');
define('bd',' dbname=base_fc');
$conexion = usuario.password.port.bd.host;
$conectar	= pg_connect("$conexion") or die("No se conecto al servidor");

$lote = $_GET['lote'];

	$ruta_salida_doc = "../../documentos/documentos/";
	$ruta_salida_inf = "../../documentos/informconf/";
	$ruta_entrada    = "../../documentos/archivos";

	mkdir($ruta_entrada."/".$lote."/", 0777);

	chmod($ruta_entrada, 0777);
	$ruta_entrada 	= $ruta_entrada."/".$lote."/";
	chmod($ruta_entrada, 0777);

	$sql = "SELECT fsd0011.aacuen cuenta,trim(aadocu) documento, trim(aadocpath) archivo 
				FROM (
				SELECT fsd0122.aacuen cuenta,max(aafecdoc||' '||aadochor)::timestamp fecha
				FROM pagares.operaciones, fsd0122, bfinac4
				WHERE operacion=bfope1
				AND fsd0122.aacuen=bfinac4.aacuen
				AND operaciones.lote= $lote
				AND aacladoc=1
				GROUP BY 1
			) AS datos,bfinac4, fsd0011
			WHERE bfinac4.aacuen = cuenta 
			AND bfinac4.aacuen=fsd0011.aacuen	
			AND (aafecdoc||' '||aadochor)::timestamp=fecha;";

	$query = pg_query($conectar,$sql);
	while ($row = pg_fetch_array($query)) {
		
		$archivo_origen   = $ruta_salida_doc.$row['archivo'];
		$archivo_destino  = $ruta_entrada.$row['documento'].'_ci.pdf';
		copy($archivo_origen, $archivo_destino);

	}		

	$sql = "SELECT fsd0011.aacuen cuenta,trim(aadocu) documento, trim(aadocpath) archivo 
				FROM (
				SELECT fsd0122.aacuen cuenta,max(aafecdoc||' '||aadochor)::timestamp fecha
				FROM pagares.operaciones, fsd0122, bfinac4
				WHERE operacion=bfope1
				AND fsd0122.aacuen=bfinac4.aacuen
				AND operaciones.lote= $lote
				AND aacladoc=4
				GROUP BY 1
			) AS datos,bfinac4, fsd0011
			WHERE bfinac4.aacuen = cuenta 
			AND bfinac4.aacuen=fsd0011.aacuen	
			AND (aafecdoc||' '||aadochor)::timestamp=fecha;";

	$query = pg_query($conectar,$sql);
	while ($row = pg_fetch_array($query)) {
		
		$archivo_origen   = $ruta_salida_doc.$row['archivo'];
		$archivo_destino  = $ruta_entrada.$row['documento'].'_in_lab.pdf';
		copy($archivo_origen, $archivo_destino);

	}		

	$sql = "SELECT fsd0011.aacuen cuenta,trim(aadocu) documento, trim(aadocpath) archivo 
				FROM (
				SELECT fsd0122.aacuen cuenta,max(aafecdoc||' '||aadochor)::timestamp fecha
				FROM pagares.operaciones, fsd0122, bfinac4
				WHERE operacion=bfope1
				AND fsd0122.aacuen=bfinac4.aacuen
				AND operaciones.lote= $lote
				AND aacladoc=6
				GROUP BY 1
			) AS datos,bfinac4, fsd0011
			WHERE bfinac4.aacuen = cuenta 
			AND bfinac4.aacuen=fsd0011.aacuen	
			AND (aafecdoc||' '||aadochor)::timestamp=fecha;";

	$query = pg_query($conectar,$sql);
	while ($row = pg_fetch_array($query)) {
		
		$archivo_origen   = $ruta_salida_doc.$row['archivo'];
		$archivo_destino  = $ruta_entrada.$row['documento'].'_in_iva.pdf';
		copy($archivo_origen, $archivo_destino);

	}	

	$sql = "SELECT fsd0011.aacuen cuenta,trim(aadocu) documento, trim(aadocpath) archivo 
				FROM (
				SELECT fsd0122.aacuen cuenta,max(aafecdoc||' '||aadochor)::timestamp fecha
				FROM pagares.operaciones, fsd0122, bfinac4
				WHERE operacion=bfope1
				AND fsd0122.aacuen=bfinac4.aacuen
				AND operaciones.lote= $lote
				AND aacladoc=7
				GROUP BY 1
			) AS datos,bfinac4, fsd0011
			WHERE bfinac4.aacuen = cuenta 
			AND bfinac4.aacuen=fsd0011.aacuen	
			AND (aafecdoc||' '||aadochor)::timestamp=fecha;";

	$query = pg_query($conectar,$sql);
	while ($row = pg_fetch_array($query)) {
		
		$archivo_origen   = $ruta_salida_doc.$row['archivo'];
		$archivo_destino  = $ruta_entrada.$row['documento'].'_in_ren.pdf';
		copy($archivo_origen, $archivo_destino);

	}	

	$sql = "SELECT cuenta,trim(aadocu) documento, trim(infarch) archivo 
				FROM (
				SELECT fsd0122.aacuen cuenta,max(inffech) fecha
				FROM public.fsta003, pagares.operaciones, fsd0122
				WHERE operacion=bfope1
				AND fsd0122.aacuen = fsta003.aacuen 
				AND operaciones.lote= $lote
				GROUP BY 1
			) AS datos, fsta003, fsd0011
			WHERE cuenta=fsta003.aacuen 
			AND cuenta=fsd0011.aacuen
			AND fecha=inffech;";

	$query = pg_query($conectar,$sql);
	while ($row = pg_fetch_array($query)) {
		
		$archivo_origen   = $ruta_salida_inf.$row['archivo'];
		$archivo_destino  = $ruta_entrada.$row['documento'].'_in.pdf';
		copy($archivo_origen, $archivo_destino);

	}

?>