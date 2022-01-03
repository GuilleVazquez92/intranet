<?php
class Vendedores extends Conexion
{
	public $cod_vendedor;	
	public $vendedor;
	public $cuenta;
	public $gestor;
	public $documento;
	public $estado;
	public $motivo;
	public $fecha_proximo;
	public $operacion;
	public $comentario;
	public $filtro;
	public $nombre;
	public $particular;
	public $comercial;
	public $telefono;
	public $celular;
	public $id;
	public $relacion;

	public function resumen(){

		$result = array();

		$db = $this->conn();
		$sql = "SELECT 
		cod_vendedor,
		datos.vendedor,
		sexo,
		grupo,
		a.categoria,
		datos.canal,
		datos.tramo,
		fecha_inicio,
		habilitado,
		trascurrido,
		habilitado-trascurrido falta,
		datos.meta,
		venta,
		case 
		when venta=0 or trascurrido=0 or habilitado=0 then 0
		else round(venta/trascurrido*habilitado)
		end venta_proyectada,
		case 
		when venta=0 or trascurrido=0 or habilitado=0 or datos.meta=0 then 0
		else round((venta/trascurrido*habilitado)/datos.meta*100,1)
		end proyeccion, 
		a.total_comision comision_estimada,
		b.total_comision comision_pasada

		from (
		select 	bzclav cod_vendedor, 
		trim(bznomb)vendedor,
		clsexo sexo, 
		bznive tramo,
		trim(ceqdesc) 
		grupo,
		trim(bccnom) canal,
		bzfchac fecha_inicio,
		coalesce((select round(sum(neto)) from operaciones_mes b where a.bzclav=b.vendedor and estado!='ANULADO' and fecha>=date_trunc('month', current_Date)),0) venta,
		coalesce((select round(obmeta) from objetivo b where a.bzclav=b.obbzclav and obfechai=date_trunc('month', current_Date)),0) meta,
		coalesce((select habil from fecha_empresa b where fecha=current_date),0) habilitado,
		coalesce((select trasn from fecha_empresa b where fecha=current_date),0) trascurrido      
		from fst062 a, fst076, com002, fst025, fsd050 
		where 
		a.equicodi=fst076.equicodi
		and equicana=bccana
		and equigrup=cequipo
		and bzcort=clusu
		and bzvact='S'
		and bzfchba is null
		and bzcort=upper('$this->vendedor')
		) as datos 
		LEFT JOIN vendedores.comisiones a ON cod_vendedor=a.cod_vend AND a.fecha=date_trunc('month',current_date)
		LEFT JOIN vendedores.comisiones b ON cod_vendedor=b.cod_vend AND b.fecha=date_trunc('month',current_date-30)";

		foreach ($db -> query($sql) as $row ) {
			$result['cod_vendedor']			=  $this->cod_vendedor = $row['cod_vendedor'];
			$result['nombre']				=  $row['vendedor'];
			$result['sexo']					=  $row['sexo'];
			$result['grupo'] 				=  $row['grupo'];
			$result['categoria'] 			=  $row['categoria'];
			$result['canal'] 				=  $row['canal'];
			$result['tramo']				=  $row['tramo'];
			$result['fecha_inicio']			=  $row['fecha_inicio'];
			$result['habilitado'] 			=  $row['habilitado'];
			$result['trascurrido'] 			=  $row['trascurrido'];
			$result['falta'] 				=  $row['falta'];
			$result['venta']				=  $row['venta'];
			$result['meta'] 				=  $row['meta'];
			$result['venta_proyectada'] 	=  $row['venta_proyectada'];
			$result['proyeccion'] 			=  $row['proyeccion'];
			$result['comision_estimada'] 	=  $row['comision_estimada'];
			$result['comision_pasada'] 		=  $row['comision_pasada'];
		}
		if($this->cod_vendedor>=98){
			setcookie("cod_vendedor",$this->cod_vendedor,time()+86400);	
			return $result;
		}else{
			header('Location:'.ROOT);
		}	

	}

	public function motivos(){

		$i = 0;
		$db = $this->conn();
		$sql = "SELECT bqtab cod_motivo, trim(bqcome) motivo FROM public.fst095 WHERE bqcla=1 ORDER BY 1;";
		foreach ($db -> query($sql) as $row ) {
			$result[$i]['cod_motivo']	=  $row['cod_motivo'];
			$result[$i]['motivo']		=  $row['motivo'];
			$i++;	
		}
		return $result;
	}

	public function guardar_gestion(){

		$result = 0;
		$db = $this->conn();

		$sql = "INSERT INTO public.tfinh21(
		aacuen, aacob, callfech, calltime, calllaps, callref, bqtab, 
		callprom, motcall, tipcall, callcob, callcobdes, callges, callgesdes, 
		callefec, callnive, callpc, callusr, callpgm, callhora, callnume, 
		callotr1, callotr2, callof, callhs, calltipcob, callfecpro, callempr)
		VALUES ($this->cuenta,0,current_date,substring(current_time::text from 1 for 8),0,'$this->comentario',$this->motivo,'$this->fecha_proximo',6,1,upper('$this->vendedor'),'',0,'',1,2,'S','','','','','','',0,substring(current_time::text from 1 for 8),0,'0001-01-01',0) 
		RETURNING 1 as result;";

		$resultado 	= $db -> query($sql) -> fetchAll();
		$result 	= $resultado[0]['result'];

		if($result==1){

			if(strtoupper(trim($this->vendedor))==strtoupper(trim($this->gestor))){

				$sql = "UPDATE public.btlk003 SET ptkfecpro='$this->fecha_proximo', ptkultge=current_date WHERE ptkcuen= $this->cuenta;";
				$db -> query($sql);

			}

			header('Location:'.ROOT.'contenido/vendedor/cartera.php'); 			
		}
	}

	public function cliente(){

		$result = array();	
		$db = $this->conn();
		$sql = "SELECT gestor,fsd0011.aacuen cuenta, trim(aanom) cliente,trim(aadocu) documento, linea_asignada, linea_disponible,linea_estado,linea_vencimiento,asituacion situacion, arecurrent recurrente, aestado estado,
		awtel1 telefono1, awcelu celular,awfax telefono2, trim(awcalle)||' '||awnume||' esq. '||trim(awesq) direccion, 'S' control 
		
		FROM fsd0011,fsd022
		LEFT JOIN (SELECT lincuen, linasig linea_asignada, linsald linea_disponible, 
		case 
		when linesta=1 then 'ACTIVO'
		when linesta=2 then 'VENCIDA'
		when linesta=3 then 'BLOQUEADA'
		end
		linea_estado, linfvto linea_vencimiento  from bfind35 where linesta=1) linea_credito on lincuen=aacuen 
		LEFT JOIN (SELECT ptkcuen, ptkgest gestor from btlk003) as gestion on ptkcuen=fsd022.aacuen
		WHERE fsd0011.aacuen=fsd022.aacuen
		AND fsd0011.aacuen=$this->cuenta";
		
		$result = $db->query($sql)->fetchAll();
		return $result;

	}

	public function cartera($filtro_documento,$filtro_cuenta,$filtro_cliente,$filtro_gestion,$pagina){

		$filtro = "";
		if($filtro_documento){
			$filtro .= " and trim(aadocu) like '%$filtro_documento'";
		}

		if($filtro_cuenta){
			$filtro .= " and ptkcuen=$filtro_cuenta";
		}

		if($filtro_gestion){
			switch ($filtro_gestion) {
				case 1:
				$filtro .= " and ptkfecpro=current_date";
				break;
				case 2:
				$filtro .= " and ptkfecpro=current_date-1";
				break;
				case 3:
				$filtro .= " and ptkfecpro<current_date-1";
				break;
				case 4:
				$filtro .= " and ptkfecpro>current_date";
				break;
			}
		}

		if($filtro_cliente){
			switch ($filtro_cliente) {
				/*case 0:
					
				break;**/
				case 1:
				$filtro .= " and (ptkdesc='PROSPECTO' or ptkdesc is null)";
				break;
				case 2:
				$filtro .= " and ptkdesc='ACTIVO'";
				break;
				case 3:
				$filtro .= " and ptkdesc='TRANSICION'";
				break;
				case 4:
				$filtro .= " and ptkdesc='INACTIVO'";
				break;						
			}


		}

		$result = array();
		$db = $this->conn();

		if(strlen($filtro_documento)>0){

			$sql = "SELECT 
			fsd0011.aacuen cuenta,
			aadocu documento,
			trim(aanom) cliente,gestor, 
			direccion,
			telefono1,
			telefono2,
			celular, 
			fecha_proximo,
			case 
			when aestado = 'ACTIVO' then 'alert alert-success'
			when aestado = 'INACTIVO' then 'alert alert-warning'
			else 'alert alert-danger'

			end estilo,
			aestado estado,
			asituacion situacion, 
			arecurrent recurrente,
			linasig asignada,
			linsald saldo,
			aausua origen

			from fsd0011
			LEFT JOIN (SELECT lincuen, linasig, linsald from bfind35 where linesta=1) linea_credito on lincuen=aacuen 
			LEFT JOIN (SELECT ptkcuen, ptkgest gestor,ptkfecpro fecha_proximo from  btlk003,fst062 where ptkgest=bzcort and bzvact='S' and bzfchba is null) as crm on ptkcuen=aacuen
			LEFT JOIN (SELECT aacuen, (awcalle||' '||awnume||' esq.'||awesq) direccion, awtel1 telefono1, awcelu celular, awtel2 telefono2 FROM public.fsd022) as particular on fsd0011.aacuen=particular.aacuen
			where split_part(trim(aadocu),'-',1)=split_part(trim('$filtro_documento'),'-',1)
			order by 1 limit 50 offset 0;";			
		}else{

			$sql  = "SELECT 
			ptkcuen cuenta,aadocu documento,ptknomcli cliente,ptkgest gestor, ptkdire1 direccion,ptktel1 telefono1, ptktel2 telefono2,ptkcelu celular, ptkfecpro fecha_proximo,ptkdesc estado,
			case 
			when ptkdesc = 'ACTIVO' then 'alert alert-success'
			when ptkdesc = 'INACTIVO' then 'alert alert-warning'
			else 'alert alert-danger'

			end estilo,
			asituacion situacion, 
			arecurrent recurrente,
			linasig asignada,
			linsald saldo,
			aausua origen

			from btlk003, fst062, fsd0011
			LEFT JOIN (SELECT lincuen, linasig, linsald from bfind35 where linesta=1) linea_credito on lincuen=aacuen 
			where ptkgest=bzcort
			and ptkcuen=aacuen
			and bzvact='S'
			and bzfchba is null
			and ptkgest=upper('$this->vendedor') 
			{$filtro} 
			order by 1 limit 50 offset 0;";			
		}

		$result =	$db->query($sql)->fetchAll();	
		return $result;		
	}




	public function cant_cartera(){

		$result = array();
		$db = $this->conn();

		$sql = "SELECT 1,'HOY' estado,(select count(*) from btlk003 where ptkgest=upper('$this->vendedor') and ptkfecpro=current_date) cantidad
		union
		select 2,'AYER',(select count(*) from btlk003 where ptkgest=upper('$this->vendedor') and ptkfecpro=current_date-1)
		union
		select 3,'VENCIDOS',(select count(*) from btlk003 where ptkgest=upper('$this->vendedor') and ptkfecpro<current_date-1)
		union
		select 4,'GESTIONADOS',(select count(*) from btlk003 where ptkgest=upper('$this->vendedor') and ptkfecpro>current_date)
		ORDER BY 1";
		
		$result = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	public function pendientes(){

		$i 	= 0;
		$result = array();
		$db = $this->conn();

		$sql = "SELECT fsd0011.aacuen cuenta,trim(aanom) cliente,bfope1 operacion,bfesta cod_estado, bjdesc estado,bfcant cantidad,bftcuo total,
		bzcort,
		trim(bccnom),
		(select (bhfech||' '||bhhora)::timestamp 
		from fsd0144 
		where bhope1=bfope1
		and bhempr=1 
		and bhest=bfesta
		order by (bhfech||' '||bhhora)::timestamp desc limit 1) fecha_motivo,
		case 
		when bfesta=12 
		then
		(select trim(bhobs)
		from fsd0144 
		where bhope1=bfope1
		and bhempr=1 
		and bhest=bfesta
		order by (bhfech||' '||bhhora)::timestamp desc limit 1)
		else ''
		end motivo

		FROM fsd0122, fst044, fsd0011, fst062, fst025
		WHERE bfesta=bjesta
		and bfvend=bzclav
		and fsd0122.aacuen=fsd0011.aacuen
		and bfcan=bccana
		and (bfesta between 3 and 5 or bfesta=50 or bfesta=12)  and bzcort=upper('$this->vendedor')
		order by bfesta;";

		foreach ($db -> query($sql) as $row ) {
			$result[$i]['cuenta'] 		= $row['cuenta'];
			$result[$i]['cliente'] 		= $row['cliente'];
			$result[$i]['operacion'] 	= $row['operacion'];
			$result[$i]['cod_estado']	= $row['cod_estado'];
			$result[$i]['estado'] 		= $row['estado'];
			$result[$i]['cantidad'] 	= $row['cantidad'];
			$result[$i]['total'] 		= $row['total'];
			$result[$i]['fecha_motivo'] = $row['fecha_motivo'];
			$result[$i]['motivo'] 		= $row['motivo'];
			$i++;
		}
		return $result;
	}

	public function condicionado(){

		$i 	= 0;
		$result = array();
		$db = $this->conn();

		$sql = "SELECT 
		fsd0011.aacuen cuenta,
		trim(aanom) cliente,
		bfope1 operacion,
		bfesta cod_estado, 
		bjdesc estado,
		bfcant cantidad,
		bftcuo total,
		bzcort vendedor,
		trim(bccnom) canal,
		(select (bhfech||' '||bhhora)::timestamp 
		
		from fsd0144 
		where bhope1=bfope1
		and bhempr=1 
		and bhest=bfesta
		order by (bhfech||' '||bhhora)::timestamp desc limit 1) fecha_motivo,
		case 
		when bfesta=12 
		then
		(select trim(bhobs)
		from fsd0144 
		where bhope1=bfope1
		and bhempr=1 
		and bhest=bfesta
		order by (bhfech||' '||bhhora)::timestamp desc limit 1)
		else ''
		end motivo

		FROM fsd0122, fst044, fsd0011, fst062, fst025
		WHERE bfesta=bjesta
		and bfvend=bzclav
		and fsd0122.aacuen=fsd0011.aacuen
		and bfcan=bccana
		and bfesta=12  
		and bfope1=$this->operacion
		order by bfesta;";

		$result = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
		return $result;
	}






	public function anuladas(){

		$i 	= 0;
		$result = array();
		$db = $this->conn();

		$sql = "SELECT fsd0011.aacuen cuenta,trim(aanom) cliente,bfope1 operacion,bfesta cod_estado, bjdesc estado,bfcant cantidad,bftcuo total,
		bzcort,
		trim(bccnom),
		(select (bhfech||' '||bhhora)::timestamp 
		from fsd0144 
		where bhope1=bfope1
		and bhempr=1 
		and bhest=bfesta
		order by (bhfech||' '||bhhora)::timestamp desc limit 1) fecha_motivo,
		(select trim(bhobs)
		from fsd0144 
		where bhope1=bfope1
		and bhempr=1 
		and bhest=bfesta
		order by (bhfech||' '||bhhora)::timestamp desc limit 1) motivo

		FROM fsd0122, fst044, fsd0011, fst062, fst025
		WHERE bfesta=bjesta
		and bfvend=bzclav
		and bfope1 in (select tcoperel from tef012 where tccarfec>=date_trunc('month', current_date))
		and fsd0122.aacuen=fsd0011.aacuen
		and bfcan=bccana
		and (bfesta=13 or bfesta=15)  
		and bzcort=upper('$this->vendedor')
		order by bfesta;";
		foreach ($db -> query($sql) as $row ) {
			$result[$i]['cuenta'] 		= $row['cuenta'];
			$result[$i]['cliente'] 		= $row['cliente'];
			$result[$i]['operacion'] 	= $row['operacion'];
			$result[$i]['cod_estado'] 	= $row['cod_estado'];
			$result[$i]['estado'] 		= $row['estado'];	
			$result[$i]['cantidad'] 	= $row['cantidad'];
			$result[$i]['total'] 		= $row['total'];
			$result[$i]['fecha_motivo'] = $row['fecha_motivo'];
			$result[$i]['motivo'] 		= $row['motivo'];
			$i++;
		}
		return $result;
	}

	public function buscar(){

		$i 	= 0;
		$result = array();
		$db = $this->conn();

		$sql  = "SELECT 
		ptkcuen cuenta,ptknomcli cliente, ptkdire1 direccion,ptktel1 telefono1, ptktel2 telefono2,ptkcelu celular, ptkfecpro fecha_proximo,ptkdesc estado,
		case 
		when ptkdesc = 'ACTIVO' then 'alert alert-success'
		when ptkdesc = 'INACTIVO' then 'alert alert-warning'
		else 'alert alert-danger'

		end estilo
		/*ptkultge fecha_ultima ,ptksituaci situacion,ptkrecurre recurrente*/
		from btlk003 
		where ptkdocu='$this->documento' 
		order by 1 limit 50 offset 0;";

		foreach ($db -> query($sql) as $row ) {
			$result[$i]['cuenta'] 		= $row['cuenta'];
			$result[$i]['cliente'] 		= $row['cliente'];	
			$result[$i]['direccion'] 	= $row['direccion'];
			$result[$i]['telefono1'] 	= $row['telefono1'];
			$result[$i]['telefono2'] 	= $row['telefono2'];
			$result[$i]['celular'] 		= $row['celular'];
			$result[$i]['fecha_proximo']= $row['fecha_proximo'];
			$result[$i]['estado'] 		= $row['estado'];
			$result[$i]['estilo'] 		= $row['estilo'];
			$i++;
		}
		
		return $result;				
	}


	public function levantar_condicionado(){

		$result = array();
		$db = $this->conn();

		$sql = "SELECT bfope1 FROM fsd0122 WHERE bfesta=12 and bfempr=1 and bfope1=$this->operacion;";

		foreach ($db -> query($sql) as $row ) {
			$valor 		= $row['bfope1'];
		}	

		if($valor>0){

			$sql = "UPDATE fsd0122 SET bfesta=3 WHERE bfesta=12 and bfempr=1 and bfope1=$this->operacion;";
			$db -> query($sql);

			$sql = "UPDATE fsd014 SET bjesta=3 WHERE bcope1=$this->operacion;";
			$db -> query($sql);


			$sql = "UPDATE fst074 SET bwresp=trim('$this->comentario') WHERE bboper=$this->operacion;";
			$db -> query($sql);

			$sql ="UPDATE tfing11 SET vgpen='RE',vgsitu=1  WHERE vgope1=$this->operacion;";
			$db -> query($sql);

			$sql = "INSERT INTO public.fsd0144(bhempr, bhsucu, bhope1, bhfech, bhhora, bhest, bhusu, bhprog, bhsitu, bhdet, bhobs, bhpc)
			SELECT 	bfempr,	bfsucu,	bfope1,	current_date, substr(current_time::text,1,8), 3, '$this->gestor', 'Pfsp0383', 3,
			'RESPUESTA CONDICIONADO', trim('$this->comentario'), '' FROM fsd0122 WHERE bfempr=1  and bfope1=$this->operacion;";
			$db -> query($sql);	

		}
	}

	public function gestiones(){
		$i 	= 0;
		$result = array();
		$db = $this->conn();

		$sql  = "SELECT 
		(callfech||' '||calltime)::timestamp fecha,
		tfinh21.aacuen cuenta,
		trim(bqcome) respuesta , 
		callref gestion,  
		callprom proximo_llamado,
		callcob gestor
		FROM tfinh21, fst095 
		WHERE  tfinh21.bqtab = fst095.bqtab
		AND tfinh21.aacuen = $this->cuenta 
		AND callpgm!='TTFINh21'
		ORDER BY (callfech||' '||calltime)::timestamp desc limit 6;";

		foreach ($db -> query($sql) as $row ) {

			$result[$i]['fecha'] 			= $row['fecha'];
			$result[$i]['respuesta'] 		= $row['respuesta'];
			$result[$i]['gestion'] 			= $row['gestion'];
			$result[$i]['proximo_llamado'] 	= $row['proximo_llamado'];
			$result[$i]['gestor'] 			= $row['gestor'];
			$i++;
		}
		return $result;
	}

	public function hoja_ruta($fecha){
		$i 	= 0;
		$result = array();
		$db = $this->conn();

		$sql  = "SELECT 
		(callfech||' '||calltime)::timestamp fecha,
		fsd0011.aacuen cuenta,
		trim(aanom) cliente, 
		trim(bqcome) respuesta , 
		callref gestion,  
		callprom proximo_llamado,
		callcob gestor
		FROM tfinh21, fst095, fsd0011 
		WHERE tfinh21.bqtab=fst095.bqtab
		AND tfinh21.aacuen=fsd0011.aacuen
		AND callcob = upper('$this->vendedor') 
		AND callpgm!='TTFINh21'
		AND callfech= '$fecha'
		ORDER BY (callfech||' '||calltime)::timestamp desc;";

		foreach ($db -> query($sql) as $row ) {

			$result[$i]['fecha'] 			= $row['fecha'];
			$result[$i]['cuenta'] 			= $row['cuenta'];
			$result[$i]['cliente'] 			= $row['cliente'];
			$result[$i]['respuesta'] 		= $row['respuesta'];
			$result[$i]['gestion'] 			= $row['gestion'];
			$result[$i]['proximo_llamado'] 	= $row['proximo_llamado'];
			$result[$i]['gestor'] 			= $row['gestor'];
			$i++;
		}
		return $result;
	}


	public function prospecto_buscar(){

		$result = array();
		$db = $this->conn();

		$sql  = "SELECT ptkcuen cuenta,ptknomcli cliente, ptkdire1 direccion,ptkdire2 comercial,ptktel1 telefono,ptkcelu celular
		from btlk003 
		where trim(ptkdocu)='$this->documento' LIMIT 1;";

		foreach ($db -> query($sql) as $row ) {
			$result['cuenta'] 		= $row['cuenta'];
			$result['cliente'] 		= $row['cliente'];	
			$result['direccion'] 	= $row['direccion'];
			$result['comercial'] 	= $row['comercial'];
			$result['telefono'] 	= $row['telefono'];
			$result['celular'] 		= $row['celular'];
		}
		return $result;	
	}

	public function prospecto_crear(){

		$result = 0;
		$db = $this->conn();

		$sql = "UPDATE fst021 SET azulti=azulti+1 WHERE azcont = 1 RETURNING azulti AS cuenta;";
		foreach ($db -> query($sql) as $row ) {
			$cuenta = $row['cuenta'];
		}

		$sql = "SELECT equicana canal, equisupe supervisor 
		FROM fst062, fst076 
		WHERE fst062.equicodi=fst076.equicodi AND bzcort=upper('$this->gestor');";

		foreach ($db -> query($sql) as $row ) {
			$canal 		= $row['canal'];
			$supervisor = $row['supervisor'];
		}

		$result = "entra hasta el prospecto final";

		$sql = "INSERT INTO btlk003(
		ptkcode, ptkgest, ptkcuen, ptkdire1, ptkdire2, ptktel1, ptktel2, ptkcelu, ptknomcli, ptkmonto, ptkfecpro, ptkesta, ptkcanl, ptkcanc, 
		ptkcanm, ptkultge, ptknive, ptkloca, ptkempr, ptksala, ptkmon1, ptkmon2, ptkmon3, ptkplaz, ptkcuot, ptktasa, ptkfecasi, ptkotr1, 
		ptkotr2, ptkin, ptkdocu, ptksituaci, ptkrecurre, ptkfeccrea, ptksuperv)
		SELECT $canal,upper('$this->gestor'),$cuenta,'$this->particular','$this->comercial','$this->telefono','0','$this->celular','$this->nombre',0,
		current_date,'',0,0,0,current_date,0,'','',0,0,0,0,0,0,0,'0001-01-01'::date,'',0,'','$this->documento','','','0001-01-01'::date,$supervisor
		RETURNING ptkcuen AS id;";

			foreach ($db -> query($sql) as $row ) {
			$result 	= $row['id'];
		}

	
		$sql = "INSERT INTO fsd0011(
            aacuen, aadocu, agdocu, aanom, aaape1, aaape2, aaape3, aanom1, 
            aanom2, ahpais, aaresi, aasexo, aafech, aknive, aqclub, aavivi, 
            aavehi, aahijo, aaesta, aaregi, bknive, ansect, alacti, aafeco, 
            aausua, aafina, aapubl, atfoto, atfirm, atdire, aanatu, aaestn, 
            aadocc, aatipc, aacodi, aanive, bkesta, aafecv, aaruc, aacat1, 
            aacat2, aacat3, aafecc, bltipo, blnomb, blfech, blempl, baruc, 
            blpate, bmdest, blambi, aahipo, aatasa, aahipcob, afaja, aaso, 
            aban, acobr, acali, ainf, adenun, taso, asituacion, arecurrent, 
            aestado, aclasificacion, atipo)
    	SELECT $cuenta,'$this->documento',1,'$this->nombre', '', '', '', '', 
            '',586,0, '', '0001-01-01', 1, 0, 0, 
            0, 0, 0, 0, 0, 0, 0,current_date, 
            '', 0, 0, '', '', '', 0, '', 
            '', 0, '$this->documento', 0,'','0001-01-01', '', 0, 
            0, 0, '0001-01-01', '', '', '0001-01-01', 0, '', 
            0, 0, 0, 0, 0, 0, '', 0, 
            '', 0, '', 'N', '', '', '', '', 
            'PROSPECTO','','';";

		foreach ($db -> query($sql) as $row ) {
			$row['id'] = 0;
			$result 	= $row['id'];
		}	


		$sql = "INSERT INTO public.fsd022(
            aacuen, awcorr, aidept, apciud, ajbarr, awcalle, awnume, awesq, 
            awtel1, awcelu, awfax, awemai, awexpl, awbipp, awtel2, aaplan, 
            awprop, awdesd, awapto, aaloca, awinte, awcelut, awbajat, awnewcel, 
            awcelfin, codalter)
    		SELECT $cuenta, 1, 0, 0, 0, '', 0, '', 
            '','$this->celular', 0, '', 0, 0, '', '', 
            '', '0001-01-01', '', '', 0, '', '', 0, 
            '', 0;";

            foreach ($db -> query($sql) as $row ) {
			$row['id'] = 0;
			$result 	= $row['id'];
		}

		$sql = "INSERT INTO public.fsd023(
            aacuen, bacorr, bacalle, aaempr, amcarg, aaifec, baplan, basala, 
            baemai, baesq, bafax, banume, batel1, baint, baofic, aidept, 
            ajbarr, apciud, basecc, bahord, bahora, bahor1, bahor2, bdempco, 
            codalter)
   			 SELECT $cuenta, 1, '', '', 0, '0001-01-01',0, 0, 
            '', '', 0, 0, 0, 0, '', 0, 
            0, 0, '', '', '', '', '', 0, 
            0;";

             foreach ($db -> query($sql) as $row ) {
			$row['id'] = 0;
			$result 	= $row['id'];
		}

		return $result;
	}
	

	public function lista_debito($valor){

		$result = array();
		$db 	= $this->conn();
		$sql 	= "SELECT biasoc cod_empr, binomb empresa FROM public.fsd033 WHERE biband='$valor';";
		$result = $db->query($sql)->fetchAll();
		return $result;

	}

	public function lista_productos($tipo_filtro,$filtro_stock,$detalle_carrito,$contenido,$pagina){

		//$_SESSION['tipo_filtro'],$_SESSION['filtro_stock'],$_SESSION['detalle_carrito']['filtro'],$contenido,$pagina
		/*'3', 0, 'T', '0', '1'*/

		$result = array();
		$db 	= $this->conn();
		$sql 	= "SELECT 
		epcodi codigo, 
		epdescl nombre_producto, 
		epprelis precio, 
		20 max_desc, 
		CASE 
		WHEN effami=34 THEN 'M'
		ELSE 'N'
		END	 tipo_filtro, 
		epstock stock 
		FROM public.tef005 
		WHERE epacti='S' 
		ORDER BY 1;";


		$result = $db->query($sql)->fetchAll();
		return $result;
	}

	public function lista_productos_gral($filtro_stock, $contenido, $familia, $pagina){

		$filtro = "";
		if($filtro_stock==1){
			$filtro .= " AND epstock>0";
		}

		$contenido = explode('_', $contenido);
		if($contenido[0] == 1){
			$filtro .= " AND epcodi=$contenido[1]";
		}

		if($contenido[0] == 2){
			if(isset($contenido[1])){
				if(strlen($contenido[1])>4){
					$filtro .= " AND epdescl like '%{$contenido[1]}%'";
				}else{
					$filtro .= " AND epdescl like '{$contenido[1]}%'";
				}	
			}
		}

		if($familia!=0){
			$filtro .= " AND tef005.effami=$familia";
		}

		$result = array();
		$db 	= $this->conn();
		$sql 	= "SELECT 
		epcodi codigo, 
		epdescl nombre_producto, 
		epprelis precio, 
		20 max_desc, 
		CASE 
		WHEN effami=34 THEN 'M'
		ELSE 'N'
		END	 tipo_filtro, 
		epstock stock 
		FROM public.tef005 
		WHERE epacti='S'
		{$filtro} 
		ORDER BY 1;";


		$result = $db->query($sql)->fetchAll();
		return $result;
	}

	public function lista_familias(){
		
		$result = array();
		$db 	= $this->conn();
		$sql 	= "SELECT effami cod_familia, trim(efdesc) familia FROM public.tef001 ORDER BY 1;";
		$result = $db->query($sql)->fetchAll();
		return $result;
	}

	public function referencias(){

		$result = array();
		$db 	= $this->conn();
		$sql = "SELECT arcorr id, arnom1 referencia, arrel1 relacion, artel1 telefono FROM public.fsd036 where aacuen = $this->cuenta;";
		$result = $db->query($sql)->fetchAll();
		return $result;



	}

	public function referencias_agregar(){

		$db 	= $this->conn();
		$sql = "INSERT INTO public.fsd036(aacuen, arcorr, arnom1, arrel1, artel1)
		select $this->cuenta, (select coalesce(max(arcorr)+1,1) from fsd036 where aacuen=$this->cuenta), '$this->nombre', '$this->relacion', '$this->telefono';";
		$result = $db->query($sql);

	}

	public function referencias_quitar(){

		$db 	= $this->conn();
		$sql 	= "DELETE FROM public.fsd036 where aacuen=$this->cuenta and arcorr=$this->id;";
		$result = $db->query($sql);

	}


	public function medios(){

		$result = array();
		$db 	= $this->conn();
		$sql 	= "SELECT bcmed cod_medio, trim(bcmnom) medio FROM public.fst026 WHERE bccond='ACTIVA' order by 1;";
		$result = $db->query($sql)->fetchAll();
		return $result;

	}

	public function buscar_descripcion($codigo){

		$result = array();
		$db 	= $this->conn();
		$sql 	= "SELECT epdescl FROM tef005 WHERE epcodi=$codigo;";
		$result = $db->query($sql)->fetchAll();
		return $result;
	}

	public function operaciones($variable){

		if(isset($this->cuenta)){
			$filtro = "AND fsd0011.aacuen=$this->cuenta";	
		}else{
			$filtro = "AND fsd0122.bfvend=$this->cod_vendedor AND bffchv>=date_trunc('month',current_date-60)";
		}
		

		switch ($variable) {
			case 'proceso':
			$filtro .= " AND (bfesta=3 or bfesta=4 or bfesta=5 or bfesta=50 or bfesta=12 or bfesta=20)";
			break;
			
			case 'facturadas':
			$filtro .= " AND (bfesta=6 or bfesta=7)";
			break;

			case 'canceladas':
			$filtro .= " AND (bfesta=10)";
			break;

			case 'otras':
			$filtro .= " AND (bfesta=15 or bfesta=13 or bfesta=21)";
			break;		
		}


		$db 	= $this->conn();
		$sql 	= "SELECT 
		bfope1 operacion, 
		fsd0011.aacuen||' '||trim(aanom) cliente,
		'CREDITO',
		bjcor estado,
		bfult atraso,
		bftcuo total,
		bfvcta cuota_valor, 
		bfcant cuota_cant,
		bfpend cuota_pend, 
		bfpres+bfinte saldo,
		bsituacion calificacion, 
		bfvend cod_vend, 
		bffchv fecha_valor,
		fsd0011.aacuen cuenta,
		trim(origen) origen
		FROM fsd0011,fst044,fsd0122
		LEFT JOIN (select tcoperel,tccarps origen from tef012) as origen on tcoperel=bfope1
		WHERE fsd0011.aacuen=fsd0122.aacuen
		AND bfesta=bjesta
		AND bfope1>0
		{$filtro}
		ORDER BY bffchv desc";
		$result = $db->query($sql)->fetchAll();
		return $result;
	}

	public function operacion_detalles(){
		$result = array();
		$db 	= $this->conn();
		$sql 	= "SELECT cuota,bfvcta valor,vencimiento,estado,pagado, atraso, capital, mora+punitorio mora, gasto, iva, total 
		from fsd0122,public.get_mora(bfope1, current_date,bftasa) 
		where bfempr=1 and bfope1=$this->operacion";
		$result = $db->query($sql)->fetchAll();
		return $result;

	}

	public function verificaciones(){
		$result = array();
		$db 	= $this->conn();
		$sql 	= "SELECT vgfech fecha, vgobse verificacion, vgusua gestor FROM public.tfing10 WHERE aacuen=$this->cuenta ORDER BY 1 desc;";
		$result = $db->query($sql)->fetchAll();
		return $result;
	}

	public function verificaciones_detalle($fecha){
		$result = array();
		$db 	= $this->conn();
		$sql 	= "SELECT 
						case 
							when vgrefdtip='C' then 'Comercial'
							when vgrefdtip='P' then 'Personal'
							when vgrefdtip='O' then 'Otros'
							else 'Comercial'   
						end	tipo, 
						vgrefdper nombre, 
						vgrefdtel telefono, 
						vgrefddet detalle, 
						vgrefdver verificado
  					FROM public.tfing101 
  					WHERE aacuen=$this->cuenta 
  					AND vgfech='$fecha'
					ORDER BY vgrefdid;";
		$result = $db->query($sql)->fetchAll();
		return $result;


	}

	public function adjuntos(){

		$db 	= $this->conn();
		$sql 	= "SELECT aafecdoc fecha, bfinac4.aacladoc tipo,trim(aaclades) documento, trim(aadocpath) archivo,1 ruta 
		FROM public.bfinac4,public.fst094 
		WHERE bfinac4.aacladoc=fst094.aacladoc AND aacuen=$this->cuenta
		union 
		SELECT * from (
		SELECT inffech fecha,9999, 'INFORMCONF',trim(infarch) archivo, 2 ruta 
		FROM public.fsta003 
		WHERE (aacuen,inffech) 
		in (select aacuen, max(inffech) from public.fsta003 where aacuen=$this->cuenta group by 1)
		) as informconf
		order by 2;";
		$result = $db->query($sql)->fetchAll();
		return $result;
	}	

	public function lista_documentos(){

		$db 	= $this->conn();
		$sql 	= "SELECT aacladoc tipo, aaclades nombre FROM public.fst094 WHERE aacladoc<=14 or aacladoc=17 ORDER BY 1";
		$result = $db->query($sql)->fetchAll();
		return $result;

	}

	public function agregar_documento(){

		$db 	= $this->conn();
		$sql 	= "INSERT INTO public.bfinac4(aacuen, aafecdoc, aacladoc, aadocpath, aadocesta, aadocusu, aadocwrk, aadochor, aadocdat2, aadocobs)
		VALUES ($this->cuenta,current_date, $this->filtro, '$this->nombre', 0,'WEB','WEB', split_part(current_time::text,'.',1), 0, 'Cargado por Web');";
		$db->query($sql);
	} 


	public function datos_basicos(){
	
		$db 	= $this->conn();
		$sql = "SELECT 
					trim(aadocu) ruc,
					aanom1 nombre1,
					aanom2 nombre2,
					aaape1 apellido1, 
					aaape2 apellido2,
					aaape3 apellido3,
					aafech fecha_nac,
					round((current_date-aafech)/365) edad,
					aasexo sexo, 
					aaesta cod_estado_civil,
					aaregi reg_conyugal,
					ahpais cod_pais,
					alacti cod_actividad,
					ansect cod_sector 

				FROM fsd0011 
				WHERE aacuen=$this->cuenta";
		$result = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
		return $result;

	}

	public function datos_particular(){
			$db 	= $this->conn();
			$sql = "SELECT aacuen, awcorr, 

			aidept cod_departamento_part, 
			apciud cod_ciudad_part, 
			ajbarr cod_barrio_part, 
			awcalle calle_part, 
			awnume numero_part, 
			awesq esquina_part, 
       		awtel1 telefono_part, 
       		awcelu celular_part, 
       		awfax fax_part, 
       		awemai email_part, 
       		awinte grupo_part,
       		awprop, 
       		awdesd habita_part

  			FROM public.fsd022
  			WHERE aacuen=$this->cuenta";
		$result = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
		return $result;
	}

	public function datos_laboral(){

		$db 	= $this->conn();
		$sql = "SELECT 	aacuen, 
						bacorr, 
						bacalle calle_laboral, 
						aaempr empresa_laboral, 
						amcarg cod_cargo_laboral, 
						aaifec fecha_laboral, 
						baplan, 
						basala  ingreso_laboral, 
				       baemai email_laboral, 
				       baesq esquina_laboral, 
				       bafax fax_laboral, 
				       banume numero_laboral, 
				       batel1 telefono_laboral, 
				       baint, 
				       baofic, 
				       aidept cod_departamento_laboral, 
				       ajbarr, 
				       apciud cod_ciudad_laboral, 
				       basecc, 
				       bahord horario_laboral1, 
				       bahora horario_laboral2, 
				       bdempco, 
				       codalter
				  FROM public.fsd023
				  WHERE aacuen=$this->cuenta 
				  limit 1;";
		
		$result = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
		return $result;		  
	}

	public function pais($seleccion){

		$db 	= $this->conn();
		$sql 	= "SELECT 
					ahpais cod_pais, 
					ahnomb pais, 
					case 
						when ahpais=$seleccion then 'selected' 
						else '' 
					end selected 
					FROM public.fst002 
					WHERE ahnac='1'";
		$result = $db->query($sql)->fetchAll();
		return $result;
	}

	public function departamento($seleccion){

		$db 	= $this->conn();
		$sql 	= "SELECT aidept cod_departamento, ainomb departamento,case 
						when aidept=$seleccion then 'selected' 
						else '' 
					end selected FROM public.fst004";
		$result = $db->query($sql)->fetchAll();
		return $result;
	}

	public function ciudad($seleccion, $seleccion1){

		$db 	= $this->conn();
		$sql 	= "SELECT apciud cod_ciudad, apnomb ciudad,
					case 
						when apciud=$seleccion1 and aidept=$seleccion then 'selected' 
						else '' 
					end selected 
					FROM public.fst003
					WHERE aidept=$seleccion;";
		$result = $db->query($sql)->fetchAll();
		return $result;
	}

	public function barrio($seleccion, $seleccion1,$seleccion2){

		$db 	= $this->conn();
		$sql 	= "SELECT ajbarr cod_barrio, ajnomb barrio, 
					case 
						when apciud=$seleccion1 and aidept=$seleccion and ajbarr=$seleccion2 then 'selected' 
						else '' 
					end selected
					FROM public.fst0051 
					WHERE aidept=$seleccion and apciud=$seleccion1;";
		$result = $db->query($sql)->fetchAll();
		return $result;
	}

	public function cargo($seleccion){

		$db 	= $this->conn();
		$sql 	= "SELECT 
						amcarg cod_cargo, 
						amnomb cargo,
						case 
							when amcarg=$seleccion then 'selected' 
						else '' 
					end selected
							 FROM public.fst008 WHERE amantcod>0;";
		$result = $db->query($sql)->fetchAll();
		return $result;
	}







	public function sector($seleccion){

		$db 	= $this->conn();
		$sql 	= "SELECT 
					ansect cod_sector, 
					annomb sector, 
					case 
						when ansect=$seleccion then 'selected' 
						else '' 
					end selected 
					FROM public.fst009
					WHERE ansect between 100 and 700";
		$result = $db->query($sql)->fetchAll();
		return $result;
	}


	public function actividad($seleccion){

		$db 	= $this->conn();
		$sql 	= "SELECT 
					alacti cod_actividad, 
					alnomb actividad, 
					case 
						when alacti=$seleccion then 'selected' 
						else '' 
					end selected 
					FROM public.fst007";
		$result = $db->query($sql)->fetchAll();
		return $result;
	}

	public function consultar_producto($codigo){

		$db 	= $this->conn();
		$sql 	= "SELECT 
					coalesce(epdescl,'') epdescl 
					FROM tef005
					WHERE epcodi=$codigo";
		$result = $db->query($sql)->fetchAll();
		return $result;
	}


	public function lead_linea_credito(){

		$db 	= $this->conn();
		$sql 	= "SELECT 
					ROW_NUMBER () OVER (ORDER BY cuenta) lead,	
					cuenta,
					trim(aanom) cliente, 
					activo, 
					fecha_ingresado fecha, 
					'Pendiente' estado,
					awtel1 telefono, 
					'AMPLIACION DE LINEA' mensaje
					FROM riesgos.clientes_ampliacion, fsd0011,fsd022 
					WHERE fsd0011.aacuen=cuenta
					and fsd0011.aacuen=fsd022.aacuen
					and estado='P' 
					and fecha_ingresado>=date_trunc('month',current_date-60)
					and cuenta in (
							select ptkcuen 
							from btlk003, fst062 
							where trim(ptkgest)=upper('$this->vendedor')
						);";
		$result = $db->query($sql)->fetchAll();
		return $result;

	}


	public function modificar_datos(){

		

		$db 	= $this->conn();
		$campo = $this->campo;
		$tabla = $this->tabla;
		
		$sql 	= "UPDATE public.$tabla
   		SET $campo ='$this->valor'
   		WHERE aacuen = $this->cuenta;";
		
	
		$result = $db->query($sql)->fetchAll();
		return $result;
	}
}
?>	