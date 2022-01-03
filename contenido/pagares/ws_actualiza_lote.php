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
	$conectar	= pg_connect("$conexion") or die("No se conecto al servidor");

	//$wsdl ='http://gisa.com.py:1010/cph/apicphdat.aspx?wsdl';	
	$wsdl = 'http://138.186.63.142:2121/Lotes/apicphdat.aspx?Wsdl';
	$soapclient = new SoapClient($wsdl, array('cache_wsdl' => WSDL_CACHE_NONE));

	$i =  0;
	$sql_principal = "SELECT lote.lote, operacion,coalesce(cumonn,2) cumonn
						from pagares.lote,pagares.operaciones 
							left join (select cuope1,max(cumonn) cumonn from fsd015 where cuempr=1 group by 1 having sum(cucapi+cuinte)>0) as datos on cuope1=operacion

						where lote.lote=operaciones.lote 
						and entidad=3 
						and (estado_wsdl='PENDIENTE' or estado_wsdl='APROBADO')
						and coalesce(cumonn,2)!=movimiento
						order by 1";

	$query_principal = pg_query($conectar,$sql_principal);
	while ($datos = pg_fetch_array($query_principal)) {

		$lote = $datos['lote'];
		$operacion = $datos['operacion'];
		$movimiento = $datos['cumonn'];

		unset($init);
		unset($operaciones);
		unset($cuotas);

		$init 					= new stdClass;
		$init->Wusu 			= "FACILAND";
		$init->Wpass 			= "F4c1L4D1A";
		$init->Wip  			= "190.128.235.138";

		$sql = "SELECT 
		fsd0122.bfope1 loteop, 
		trim(split_part(split_part(aadocu,'-',1),' ',1)) lotedoc, 
		fsd0011.aanom1 lotenom1, 
		fsd0011.aanom2 lotenom2, 
		fsd0011.aaape1 loteape1, 
		fsd0011.aaape2 loteape2,
		trim(fsd0011.aanom1)||' '||trim(fsd0011.aanom2)||' '||trim(fsd0011.aaape1)||' '||trim(fsd0011.aaape2) lotenomc,		
		fsd0011.aafech lotefecnac,
		coalesce(fsd0011.aasexo,'') lotesexo, 
		apnomb loteciudp,
		substring(fsd022.awcalle||' y '||awesq,1,60) lotedirp, 
		case when length(fsd022.awcelu)>6 then (regexp_replace(trim(fsd022.awcelu), '[^0-9/]', '','g')) else '' end  lotecelu,
		case when length(fsd022.awtel1)>6 then (regexp_replace(trim(fsd022.awtel1), '[^0-9/]', '','g')) else '' end  lotetelp,
		case when length(fsd022.awtel2)>6 then (regexp_replace(trim(fsd022.awtel2), '[^0-9/]', '','g')) else '' end  lotetel2,
		datos.cargo lotecargo,
		datos.laboral loteempr,
		datos.fecha_ingreso lotefeclab,		
		datos.salario lotesala,
		datos.ciudad_lab loteciudl,
		substring(datos.direccion_lab,1,60) lotedirl,
		case when length(datos.telefono_lab)>6 then (regexp_replace(trim(datos.telefono_lab), '[^0-9/]', '','g')) else '' end lotetell,
		fsd0122.bfmone lotemone,	
		round(fsd0122.bfcapi) lotecap, 
		round(fsd0122.bftint) loteint, 
		round(fsd0122.bftcuo) lotepagare,
		fsd0122.bffchv lotefecval, 
		fsd0122.bffchd+30 lotefecvto,
		fsd0122.bfplaz loteplaz, 
		fsd0122.bfcant lotectas, 
		fsd0122.bfpend lotepend, 
		round(fsd0122.bfpres) lotesalc, 
		round(fsd0122.bfinte) lotesali, 
		fsd0011.afaja lotefaja,
		'CREDITO'::text lotemod,
		''::text loteelect,
		0 loteesta,
		coalesce(producto,'') producto,
		coalesce(cant_vigente,0) cant_vigente,
		coalesce(maximo_atraso,0) maximo_atraso,
		coalesce(promedio_atraso,0) promedio_atraso,
		coalesce(saldo_consolidado,0) saldo_consolidado	

		FROM base_fc.public.fsd0011 fsd0011 
		left join (select a.aacuen,trim(amnomb) cargo, trim(aaempr) laboral,aaifec fecha_ingreso,round(basala) salario,trim(apnomb) ciudad_lab,upper(trim(bacalle)||' y '||trim(baesq)) direccion_lab,batel1 telefono_lab 
		from fsd023 a 
		left join fst008 on a.amcarg=fst008.amcarg
		left join fst003 on a.apciud=fst003.apciud and a.aidept=fst003.aidept
		where (aacuen,bacorr) = (select aacuen,max(bacorr) from fsd023 b where a.aacuen=b.aacuen  group by 1)
		) datos ON datos.aacuen=fsd0011.aacuen
		left join (select cuenta,sum(dias)/count(*) promedio_atraso 
		from (
		select fsd0171.aacuen cuenta, case when befcpg-be1vto>0 then befcpg-be1vto else 0 end dias 
		from fsd0171, fsd0122 
		where beempr=bfempr and beempr=1 and fsd0122.aacuen=fsd0171.aacuen and beope1=bfope1 and beesta='C' and bfesta=7
		) as datos group by cuenta
		) as y on fsd0011.aacuen=y.cuenta,	

		base_fc.public.fsd0122 fsd0122 left join (select aacuen,bfope1, count(*) cant_vigente, round(sum(bfpres+bfinte)) saldo_consolidado, max(bfmax) maximo_atraso from fsd0122 where bfempr=1 and bfesta=7 group by aacuen,bfope1) as global on fsd0122.aacuen=global.aacuen and fsd0122.bfope1=global.bfope1, 
		base_fc.public.fsd022 fsd022 left join fst003 on fsd022.apciud=fst003.apciud and fsd022.aidept=fst003.aidept,
		pagares.operaciones
		WHERE fsd0122.aacuen = fsd0011.aacuen 
		AND fsd0011.aacuen = fsd022.aacuen
		AND fsd0122.bfope1=operacion 
		AND operaciones.lote=$lote and operaciones.operacion=$operacion;";

		$query = pg_query($conectar, $sql);
		while($row  =  pg_fetch_array($query,NULL, PGSQL_ASSOC)){

			$sql_cuota = "SELECT 	
			fsd0171.becta lotecta, 
			round(fsd0171.bevcta) lotecval, 
			round(fsd0171.beacta) loteccap, 
			round(fsd0171.beicta) lotecint, 
			fsd0171.be1vto lotecvto,
			beesta lotecest, 
			befchp lotepago,
			case 
			when beesta='P' AND befchp-be1vto<0 then 0
			else befchp-be1vto
			end  loteatra, 
			round(besalc) lotecapp,
			round(besali) loteintp

			FROM public.fsd0122 fsd0122, public.fsd0171 fsd0171
			WHERE fsd0171.aacuen = fsd0122.aacuen 
			AND fsd0171.beope1 = fsd0122.bfope1 
			AND bfempr = beempr
			AND bfope1 = $operacion
			AND becta  > 0    	
			ORDER BY fsd0171.beope1, fsd0171.becta";
			$query_cuota = pg_query($conectar, $sql_cuota); 

			$cuotas = array();
			while ($fetch = pg_fetch_array($query_cuota)) {
				$cuotas[] = array(
					'lotecta'	=> $fetch['lotecta'],
					'lotecval'	=> $fetch['lotecval'],
					'loteccap'	=> $fetch['loteccap'],
					'lotecint'	=> $fetch['lotecint'],
					'lotecvto'	=> $fetch['lotecvto'],
					'lotepago' 	=> $fetch['lotepago'],
					'lotecest' 	=> $fetch['lotecest'],
					'loteatra'  => $fetch['loteatra'],
					'lotecapp' 	=> $fetch['lotecapp'],
					'loteintp'	=> $fetch['loteintp']
				);
			}

			$operaciones[] = 
			array(
				'loteop' 	=> $row["loteop"],
				'lotedoc' 	=> normalizar($row["lotedoc"]),
				'lotedoc' 	=> normalizar($row["lotedoc"]),
				'lotenom1' 	=> normalizar($row["lotenom1"]),
				'lotenom2' 	=> normalizar($row["lotenom2"]),
				'loteape1' 	=> normalizar($row["loteape1"]),
				'loteape2' 	=> normalizar($row["loteape2"]),
				'lotenomc' 	=> normalizar($row["lotenomc"]),
				'lotefecnac'=> $row["lotefecnac"],
				'lotesexo' 	=> normalizar($row["lotesexo"]),
				'loteciudp' => normalizar($row["loteciudp"]),
				'lotedirp' 	=> normalizar($row["lotedirp"]),
				'lotecelu' 	=> normalizar($row["lotecelu"]),
				'lotetelp' 	=> normalizar($row["lotetelp"]),
				'lotetel2' 	=> normalizar($row["lotetel2"]),
				'lotecargo' => normalizar($row["lotecargo"]),
				'loteempr' 	=> normalizar($row["loteempr"]),
				'lotedirl' 	=> normalizar($row["lotedirl"]),
				'lotefeclab'=> $row["lotefeclab"],
				'lotesala' 	=> $row["lotesala"],
				'loteciudl' => normalizar($row["loteciudl"]),
				'lotetell' 	=> normalizar($row["lotetell"]),
				'lotemone' 	=> $row["lotemone"],
				'lotecap' 	=> $row["lotecap"],
				'loteint' 	=> $row["loteint"],
				'lotepagare'=> $row["lotepagare"],
				'lotefecval'=> $row["lotefecval"],
				'lotefecvto'=> $row["lotefecvto"],
				'loteplaz' 	=> $row["loteplaz"],
				'lotectas' 	=> $row["lotectas"],
				'lotepend' 	=> $row["lotepend"],
				'lotesalc' 	=> $row["lotesalc"],
				'lotesali' 	=> $row["lotesali"],
				'lotefaja' 	=> normalizar($row["lotefaja"]),
				'lotemod' 	=> normalizar($row["lotemod"]),
				'loteelect' => $row["loteelect"],
				'loteesta' 	=> $row["loteesta"],
				'lotevalop' => 0,
				'LOTEDESC'  => $row["producto"],
				'LoteSalTot'=> $row["saldo_consolidado"],
				'LoteMaXA'	=> $row["maximo_atraso"],
				'LoteProA'	=> $row["promedio_atraso"],
				'LoteCantOp'=> $row["cant_vigente"],
				'LoteOtrDat1'=> 0,	
				'LoteOtrDat2'=> 0,
				'ctas' 		=> $cuotas,
				'LOTERES' 	=> '' 
			);
		}

		$init->Wlote 			= new stdClass;
		$init->Wlote->LoteCod 	= $lote;
		$init->Wlote->LoteFech 	= date('Y-m-d');
		$init->Wlote->LoteUsr 	= 'FACILANDIA';
		$init->Wlote->LoteDesc 	= '';
		$init->Wlote->lotetotal = 0;

		$init->Wlote->Ops 		   = new stdClass;
		$init->Wlote->Ops->OpsItem = $operaciones;
		$init->Wdet 			   = "";

		$soapclient->ACTUALIZARLOTE($init);	
		$sql = "UPDATE pagares.operaciones SET movimiento=$movimiento WHERE lote=$lote and operacion=$operacion;";
		pg_query($conectar, $sql);

		$i++;
		echo $i;
	}
} catch(Exception $e){
	echo "<b>Error...</b><br>";
	echo $e->getMessage();
}
?>

