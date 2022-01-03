<?php
class PAGARES extends Conexion{

	public $lote;
	public $entidad;
	public $operacion;
	public $movimiento;
	public $cuota;
	public $usuario;
	public $modo;
	public $plazo;
	public $observacion;
	public $estado;
	public $verificado;

	public function crear_lote(){

		$db = $this->conn();
		$sql = "INSERT INTO pagares.lote(lote, entidad, fecha_envio,  plazo, modo, observacion, estado)
		VALUES ((SELECT max(lote)+1 lote FROM pagares.lote), $this->entidad, current_date, $this->plazo, $this->modo, '$this->observacion','N');";
		$db -> query($sql);		 

	}

	public function quitar_lote(){
		$db = $this->conn();
		$sql = "DELETE FROM pagares.lote WHERE lote=$this->lote;";
		$db -> query($sql);
	}

	public function abrir_lote(){
		$db = $this->conn();
		$sql = "UPDATE pagares.lote SET estado='A' WHERE lote=$this->lote;";
		$db -> query($sql);
	}

	public function cerrar_lote(){
		$db = $this->conn();
		$sql = "UPDATE pagares.lote SET estado='C' WHERE lote=$this->lote;";
		$db -> query($sql);
	}

	public function consultar_lote($entidad){

		$i 	= 0;
		$db = $this->conn();			
		$result = array();
		$filtro = ($entidad!=9999) ? "AND lote.entidad=$entidad" : "";
	
		$sql = "SELECT lote.lote, 
		lote.entidad,
		fecha_envio, 
		entidad.descripcion,
		observacion, 
		case
			when verificado=1 then 5*cuota_cab
			when verificado=2 then 3*cuota_cab
			when verificado=3 then 4*cuota_cab
			when verificado=5 then 12*cuota_cab
			else monto_lote
		end	 monto,
		pagos.pago,
		verificado,
		case
			when verificado=1 then 5
			when verificado=2 then 3
			when verificado=3 then 4
			when verificado=5 then 12
			else plazo
		end	plazo, 
		(select count(*) from pagares.operaciones where operaciones.lote=lote.lote) cant_operacion,
		lote.estado,
		modo_descrip modo
		
		FROM pagares.lote
		left join(select operaciones.lote, sum(bfvcta) cuota_cab from pagares.operaciones,fsd0122 where bfope1=operacion and bfempr=1 group by 1) as operaciones on operaciones.lote=lote.lote
		left join (SELECT pago_clientes.lote, sum(capital) pago
						FROM pagares.pago_clientes, pagares.lote, pagares.operaciones
						where pago_clientes.lote=lote.lote
						and lote.lote=operaciones.lote
						and pago_clientes.operacion=operaciones.operacion
						and fecha_pago>=fecha_envio
						group by 1) as pagos on lote.lote=pagos.lote
		, pagares.modo, pagares.entidad
		WHERE lote.modo=modo.id
		AND lote.entidad=entidad.entidad
		$filtro
		ORDER BY lote DESC;"; 
		
		$result = $db->query($sql)->fetchAll();
		return $result;
	}

	public function consultar_abiertos(){

		$i 	= 0;
		$db = $this->conn();			
		$result = array();
		echo	$sql = "SELECT lote, 
		lote.entidad,
		entidad.descripcion
		FROM pagares.lote, pagares.modo, pagares.entidad
		WHERE lote.modo=modo.id
		AND lote.entidad=entidad.entidad
		AND estado!='C'
		ORDER BY lote DESC;"; 
		
		foreach ($db -> query($sql) as $row ) {
			$result[$i]['lote'] 			= $row['lote'];
			$result[$i]['entidad'] 			= $row['entidad'];
			$result[$i]['descripcion'] 		= $row['descripcion'];
			$i++;
		}
		return $result;
	}	
	/****************************************************************/
# OPERACIONES
	public function consultar_operaciones(){
		$i 	= 0;
		$db = $this->conn();			
		$result = array();
		
		if ($this->lote == 572){
		$sql = "SELECT 	operaciones.lote, 
		fsd0011.aacuen cuenta, 
		aadocu documento, 
		aanom cliente,
		estado_operacion,
		estado_descripcion, 
		operaciones.operacion, 
		bfult atraso,
		lote_origen origen, 
		case 
			when verificado_anterior=1 then 5
			when verificado_anterior=2 then 3
			when verificado_anterior=3 then 4
			when verificado_anterior=5 then 12
			else bfcant
		 end cant_cuota, 
		bfvcta valor_cuota, 
		case 
			when verificado_anterior=1 then 5*bfvcta
			when verificado_anterior=2 then 3*bfvcta
			when verificado_anterior=3 then 4*bfvcta
			when verificado_anterior=5 then 12*bfvcta
			else bftcuo
		 end valor_operacion, 

		coalesce(capital_cobrado,0) valor_abonado,
		 
		case 
			when verificado_anterior=1 then (5*bfvcta)-coalesce(capital_cobrado,0)
			when verificado_anterior=2 then (3*bfvcta)-coalesce(capital_cobrado,0)
			when verificado_anterior=3 then (4*bfvcta)-coalesce(capital_cobrado,0)
			when verificado_anterior=5 then (12*bfvcta)-coalesce(capital_cobrado,0)
			else bfpres+bfinte
		 end saldo_capital, 
		
		bfesta estado,
		length(file_cedula) file_cedula,
		length(file_informconf) file_informconf,
		length(file_pagare) file_pagare,
		prox_venc,
		
		case
			when prox_venc-current_date <=15 then 1
			else 0  
		end marca	

		FROM pagares.lote,pagares.estado_operacion,pagares.operaciones 
		left join (SELECT lote, operacion, sum(capital) capital_cobrado FROM pagares.pago_clientes where verificado='S' group by 1,2) b on operaciones.operacion=b.operacion
		, fsd0011, fsd0122
		LEFT JOIN(SELECT beope1,min(be1vto) prox_venc FROM fsd0171 WHERE beempr=1 AND beesta='P' GROUP BY beope1) as proximo on beope1=bfope1 
		WHERE fsd0122.aacuen=fsd0011.aacuen
		AND fsd0122.bfope1=operaciones.operacion
		AND operaciones.estado_operacion=estado_operacion.id
		AND lote.lote=operaciones.lote
		AND bfempr=1
		AND operaciones.lote=572;";


		}else{

		$sql = "SELECT 	operaciones.lote, 
		fsd0011.aacuen cuenta, 
		aadocu documento, 
		aanom cliente,
		estado_operacion,
		estado_descripcion, 
		operaciones.operacion, 
		bfult atraso, 
		case 
			when verificado=1 then 5
			when verificado=2 then 3
			when verificado=3 then 4
			when verificado=5 then 12
			else bfcant
		 end cant_cuota, 
		bfvcta valor_cuota, 
		case 
			when verificado=1 then 5*bfvcta
			when verificado=2 then 3*bfvcta
			when verificado=3 then 4*bfvcta
			when verificado=5 then 12*bfvcta
			else bftcuo
		 end valor_operacion, 

		coalesce(capital_cobrado,0) valor_abonado,
		 
		case 
			when verificado=1 then (5*bfvcta)-coalesce(capital_cobrado,0)
			when verificado=2 then (3*bfvcta)-coalesce(capital_cobrado,0)
			when verificado=3 then (4*bfvcta)-coalesce(capital_cobrado,0)
			when verificado=5 then (12*bfvcta)-coalesce(capital_cobrado,0)
			else bfpres+bfinte
		 end saldo_capital, 
		
		bfesta estado,
		length(file_cedula) file_cedula,
		length(file_informconf) file_informconf,
		length(file_pagare) file_pagare,
		prox_venc,
		
		case
			when prox_venc-current_date <=15 then 1
			else 0  
		end marca	

		FROM pagares.lote,pagares.estado_operacion,pagares.operaciones
		left join (SELECT lote, operacion, sum(capital) capital_cobrado FROM pagares.pago_clientes where verificado='S' group by 1,2) b on operaciones.operacion=b.operacion
		, fsd0011, fsd0122
		LEFT JOIN(SELECT beope1,min(be1vto) prox_venc FROM fsd0171 WHERE beempr=1 AND beesta='P' GROUP BY beope1) as proximo on beope1=bfope1 
		WHERE fsd0122.aacuen=fsd0011.aacuen
		AND fsd0122.bfope1=operaciones.operacion
		AND operaciones.estado_operacion=estado_operacion.id
		AND lote.lote=operaciones.lote
		AND bfempr=1
		AND operaciones.lote=$this->lote;";

		}


		$result= $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $result;

	}

	public function consultar_pagos(){
		$i 	= 0;
		$db = $this->conn();			
		$result = array();
		$sql = "SELECT 
					fsd0122.aacuen cuenta, 
					aanom cliente,
					pago_clientes.operacion, 
					pago_clientes.movimiento, 
					cuota, 
					fecha_pago, 
					cajero, 
					capital, 
					mora, 
					pago_clientes.verificado, 
					usuario, 
					fecha_verificacion		
					FROM pagares.pago_clientes, fsd0011, fsd0122,pagares.lote, pagares.operaciones
					LEFT JOIN (SELECT lote, operacion, sum(capital) capital_cobrado FROM pagares.pago_clientes where verificado='S' group by 1,2) b on operaciones.lote=b.lote and operaciones.operacion=b.operacion	
					WHERE fsd0122.aacuen=fsd0011.aacuen 
					AND fsd0122.bfope1=pago_clientes.operacion
					AND fsd0122.bfope1=operaciones.operacion
					AND operaciones.operacion=pago_clientes.operacion
					AND lote.lote=pago_clientes.lote
					AND bfempr=1 
					AND fecha_pago>=fecha_envio
					--AND (fecha_verificacion is null or fecha_verificacion::date>=current_date-60) 
					AND (case 
						when lote.verificado=1 then 5*bfvcta
						when lote.verificado=2 then 3*bfvcta
						when lote.verificado=3 then 4*bfvcta
						when lote.verificado=5 then 12*bfvcta
						else bftcuo
					 end -coalesce(capital_cobrado,0))>0
					AND pago_clientes.lote=$this->lote 
					ORDER BY fecha_pago DESC;";

		$result = $db->query($sql)->fetchAll();
		return $result;
	}

	public function aprobar_pago(){

		$db  = $this->conn();
		$sql = "UPDATE pagares.pago_clientes 
		SET verificado='S', usuario=upper('$this->usuario'), fecha_verificacion=now() 
		WHERE lote=$this->lote
		AND operacion=$this->operacion
		AND movimiento=$this->movimiento
		AND cuota=$this->cuota";
		$db->query($sql);

	}	

	public function cargar_operacion(){
		$result = 0;
		$db  = $this->conn();			
		$sql = "SELECT bfope1 operacion
		FROM fsd0122 
		WHERE bfempr=1 AND (bfesta=7 OR bfesta=10)
		AND bfope1 NOT IN (SELECT operacion FROM pagares.operaciones)
		AND bfope1 NOT IN (SELECT operacion FROM convenios.operacion_detalle)
		AND bfope1 = $this->operacion;";

		if($db->query($sql)->rowCount()==1){

			$sql = "UPDATE operaciones.lote SET estado='A' WHERE lote=$this->lote";
			$db->query($sql);

			$sql = "INSERT INTO pagares.operaciones(lote, operacion, estado,fecha_insercion) 
			VALUES ($this->lote, $this->operacion,'P', current_date) 
			RETURNING 1;";
			$result =  $db->query($sql)->rowCount();	

			$sql = "INSERT INTO pagares.pago_clientes(lote, operacion, movimiento, cuota, fecha_pago, cajero, capital,mora)
			select lote,operacion,bknume movimiento,becta cuota, bkfcp fecha_pago,bkcaje cajero,sum(bkcapi+bkinte) capital, sum(bkmora+bkpuni+bkley+bkgtos+bkiva+bkgasabog) mora 
			from (
			select max(bfempr) empresa,operaciones.lote, operacion 
			from pagares.operaciones,fsd0122 
			where operacion=bfope1
			group by operacion,operaciones.lote
			order by 2,1,3) as datos, fsd0172
			WHERE operacion=beope1
			and beempr=empresa
			and beope1=$this->operacion
			and becta>0
			group by lote, operacion, bkfcp, bkcaje,becta, bknume
			order by 2, 3
			ON CONFLICT(lote, operacion, movimiento, cuota) DO NOTHING;";
			$db->query($sql);
			$this->actualizar_lote();
		}
		return $result;
	}

	public function quitar_operacion(){
		$result = 0;
		$db  = $this->conn();			
		$sql = "DELETE FROM pagares.operaciones WHERE lote=$this->lote AND operacion=$this->operacion RETURNING 1;";
		$result =  $db->query($sql)->rowCount();
		$this->actualizar_lote();		
		return $result;
	}

	public function actualizar_lote(){

		$db  = $this->conn();
		$sql = "DELETE FROM pagares.pago_clientes WHERE (lote,operacion) not in (select lote,operacion from pagares.operaciones);";
		$db->query($sql);

		$sql = "UPDATE pagares.lote SET cant_operaciones=cantidad FROM (select lote, count(*) cantidad from pagares.operaciones group by 1) as datos 
		WHERE lote.lote=datos.lote and cant_operaciones!=cantidad and lote.lote=$this->lote;";
		$db->query($sql);

		$sql = "UPDATE pagares.lote SET pago=capital FROM (SELECT lote, sum(capital) capital FROM pagares.pago_clientes group by 1) as datos 
		WHERE lote.lote=datos.lote and pago!=capital and lote.lote=$this->lote;";
		$db->query($sql);

		$sql = "UPDATE pagares.lote SET monto_lote=monto_operaciones 
		FROM (select datos1.lote, sum(bftcuo) monto_operaciones from (
		select max(bfempr) empresa,operaciones.lote, operacion 
		from pagares.operaciones,fsd0122 
		where operacion=bfope1
		group by operacion,operaciones.lote
		order by 2,1,3) as datos1, fsd0122 where operacion=bfope1 and bfempr=empresa
		group by 1
		) as datos 
		WHERE lote.lote=datos.lote and monto_lote!=monto_operaciones and lote.lote=$this->lote;";
		$db->query($sql);


		$sql = "UPDATE pagares.operaciones SET producto=datos.producto
					FROM (
					SELECT 
						operacion, producto.producto
					  FROM pagares.operaciones 
					LEFT JOIN (SELECT tcoperel,epdesc producto FROM tef012,tef0121, tef005 
							WHERE tef012.tccarcod=tef0121.tccarcod 
								and tccarite=epcodi
							order by tcoperel,tccartli 
						) as producto on tcoperel=operacion 	

					WHERE (length(operaciones.producto)=0 or operaciones.producto is null) and lote = $this->lote
					) as datos 
					WHERE operaciones.operacion=datos.operacion;";
		$db->query($sql);
	}


	public function modificar_estado(){

		$db 	= $this->conn();		
	

		if($this->estado == 3){
		
		$sql 	= "UPDATE pagares.operaciones SET verificado_anterior= $this->verificado,lote=572,estado_operacion=4, lote_origen = $this->lote  WHERE lote=$this->lote AND operacion=$this->operacion;";
		$db->query($sql);
		
		}else{
				$sql 	= "UPDATE pagares.operaciones SET estado_operacion=$this->estado WHERE lote=$this->lote AND operacion=$this->operacion;";
		$db->query($sql);
		}
	}



	/****************************************************************/

	public function consultar_entidad(){
		$i 	= 0;
		$db = $this->conn();			
		$result = array();
		$sql = "SELECT entidad, descripcion FROM pagares.entidad ORDER BY 2;";
		foreach ($db->query($sql) as $row ) {
			$result[$i]['entidad'] 		= $row['entidad'];
			$result[$i]['descripcion'] 	= $row['descripcion'];
			$i++;
		}
		return $result;		
	}

	public function consultar_modo(){
		$i 	= 0;
		$db = $this->conn();			
		$result = array();
		$sql = "SELECT id, modo_descrip FROM pagares.modo ORDER BY 1;";
		foreach ($db -> query($sql) as $row ) {
			$result[$i]['id']			= $row['id'];
			$result[$i]['modo_descrip'] = $row['modo_descrip'];			
			$i++;
		}
		return $result;		
	}

	/** EXPORTAR **/
	public function exportar_cab_lote(){

		$result = array();
		$db = $this->conn();			

		$sql = "SELECT 
				fsd0122.bfope1 operacion, 
				trim(split_part(split_part(aadocu,'-',1),' ',1)) documento, 
				fsd0011.aanom1 nombre1, 
				fsd0011.aanom2 nombre2, 
				fsd0011.aaape1 apellido1, 
				fsd0011.aaape2 apellido2,
				trim(fsd0011.aanom1)||' '||trim(fsd0011.aanom2)||' '||trim(fsd0011.aaape1)||' '||trim(fsd0011.aaape2) nombre_completo,		
				fsd0011.aafech fecha_nac,
				fsd0011.aasexo sexo, 
				apnomb ciudad_part,
				substring(fsd022.awcalle||' y '||awesq,1,60) direccion_part, 
				case when length(fsd022.awcelu)>6 then (regexp_replace(trim(fsd022.awcelu), '[^0-9/]', '','g')) else '' end  celular_part,
				case when length(fsd022.awtel1)>6 then (regexp_replace(trim(fsd022.awtel1), '[^0-9/]', '','g')) else '' end  telefono1_part,
				case when length(fsd022.awtel2)>6 then (regexp_replace(trim(fsd022.awtel2), '[^0-9/]', '','g')) else '' end  telefono2_part,
				datos.cargo,
				datos.laboral,
				datos.fecha_ingreso,		
				datos.salario,
				datos.ciudad_lab,
				substring(datos.direccion_lab,1,60) direccion_lab,
				case when length(datos.telefono_lab)>6 then (regexp_replace(trim(datos.telefono_lab), '[^0-9/]', '','g')) else '' end telefono_lab,
				fsd0122.bfmone moneda,	
				round(fsd0122.bfcapi) capital, 
				round(fsd0122.bftint) interes, 
				round(fsd0122.bftcuo) total_operacion,
				fsd0122.bffchv fecha_oper, 
				fsd0122.bffchd+30 fecha_1vto,
				fsd0122.bfplaz plazo, 
				fsd0122.bfcant cuotas_cant, 
				fsd0122.bfpend cuotas_pend,
				fsd0122.bfult atraso, 
				round(fsd0122.bfpres) saldo_capital, 
				round(fsd0122.bfinte) saldo_interes, 
				fsd0011.afaja informconf,
				'CREDITO'::text modalida,
				''::text submodalida,
				''::text estado,
				''::text pl_electronica,
				bfmax maximo_atraso
				/*coalesce(maximo_atraso,0) maximo_atraso*/
				 /*,				
				coalesce(producto,'') producto,
				coalesce(cant_vigente,0) cant_vigente,
				coalesce(promedio_atraso,0) promedio_atraso,
				coalesce(saldo_consolidado,0) saldo_consolidado	
				*/
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
				
				base_fc.public.fsd0122 fsd0122, 
					/*left join (select aacuen,bfope1, count(*) cant_vigente, round(sum(bfpres+bfinte)) saldo_consolidado, max(bfmax) maximo_atraso from fsd0122 where bfempr=1 and bfesta=7 group by aacuen,bfope1) as global on fsd0122.aacuen=global.aacuen and fsd0122.bfope1=global.bfope1,*/

				base_fc.public.fsd022 fsd022 
						left join fst003 on fsd022.apciud=fst003.apciud and fsd022.aidept=fst003.aidept,
				pagares.operaciones
				WHERE fsd0122.aacuen = fsd0011.aacuen 
				AND fsd0011.aacuen = fsd022.aacuen
				AND fsd0122.bfope1=operacion 
				AND fsd0122.bfesta=7
				AND operaciones.lote=$this->lote
				ORDER BY 1;";

		$result = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	public function exportar_det_lote(){
		$i 	= 0;
		$db = $this->conn();			
		$result = array();

		$sql = "SELECT 	
					beope1 operacion,
					fsd0171.becta numero_cuota, 
					round(fsd0171.bevcta) monto_cuota, 
					round(fsd0171.beacta) capital, 
					round(fsd0171.beicta) interes, 
					fsd0171.be1vto vencimiento,
					beesta estado, 
					befchp pago,
					case 
						when beesta='P' AND befchp-be1vto<0 then 0
						else befchp-be1vto
					end  atraso

				FROM public.fsd0122 fsd0122, public.fsd0171 fsd0171
				WHERE fsd0171.aacuen = fsd0122.aacuen 
				AND fsd0171.beope1 = fsd0122.bfope1 
				AND bfempr = beempr
				AND bfope1 in (select operacion from pagares.operaciones where lote =$this->lote)
				AND becta  > 0    	
				ORDER BY fsd0171.beope1, fsd0171.becta";

		$result = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);		
		return $result;
	}

	public function actualizar_pdf($lote,$operacion,$tipo){

		$db = $this->conn();			
		$result = array();

		if ($tipo == 1 || $tipo == 3) {

			$ruta = ARCHIVOS."documentos/";

			if($tipo == 1){
				$clase = 1;
				$base64= "file_cedula";
				$check = "check_cedula";

			}else{
				$clase = 18;  #tipo 18 Pagares
				$base64= "file_pagare";
				$check = "check_pagare";
			}

			$sql = "SELECT 	trim(aadocpath) archivo 
					FROM (
						SELECT fsd0122.aacuen cuenta,max(aafecdoc||' '||aadochor)::timestamp fecha
						FROM pagares.operaciones, fsd0122, bfinac4
						WHERE operacion=bfope1
						AND fsd0122.aacuen=bfinac4.aacuen
						AND operaciones.lote=$lote
						AND operaciones.operacion=$operacion
						AND aacladoc=$clase
						GROUP BY 1
					) AS datos,bfinac4
					WHERE bfinac4.aacuen = cuenta 
					AND (aafecdoc||' '||aadochor)::timestamp=fecha;";
		
		}else{
			$ruta = ARCHIVOS."informconf/";
			$base64= "file_informconf";
			$check = "check_informconf";
			
			$sql = "SELECT trim(infarch) archivo 
						FROM (
						SELECT fsd0122.aacuen cuenta,max(inffech) fecha
						FROM public.fsta003, pagares.operaciones, fsd0122
						WHERE operacion=bfope1
						AND fsd0122.aacuen=fsta003.aacuen 
						AND operaciones.lote=$lote
						AND operaciones.operacion=$operacion
						GROUP BY 1
					) AS datos, fsta003
					WHERE cuenta=fsta003.aacuen 
					AND fecha=inffech;";
		}

		$result = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
		$file = $ruta.$result['archivo'];

		if($file = base64_encode(file_get_contents($file))){

			$sql = "UPDATE pagares.operaciones SET $base64='$file' WHERE lote=$lote AND operacion=$operacion;";
			$db->query($sql);
		}
	}

		public function ver_pdf($lote,$operacion,$tipo){

				switch ($tipo) {
					case 1:
						$campo = 'file_cedula';
						break;
					
					case 2:
						$campo = 'file_informconf';	
						break;
					case 3:
						$campo = 'file_pagare';
						break;
				}
			
			$db = $this->conn();			
			$result = array();	
			$sql 	= "SELECT $campo FROM pagares.operaciones WHERE lote=$lote and operacion=$operacion";
			$result =  $db->query($sql)->fetchAll();
			return $result;	

		}


		public function descripcion(){

			$db = $this->conn();			
			$sql 	= "UPDATE pagares.lote SET observacion='$this->observacion' WHERE lote=$this->lote;";
			$result =  $db->query($sql);
		}

		public function modo(){

			$db = $this->conn();			
			$sql 	= "UPDATE pagares.lote SET verificado=$this->modo WHERE lote=$this->lote;";
			$result =  $db->query($sql);
		}


		/*******************************************************************/
#Dashborad

		public function dash_01(){

			$db = $this->conn();			
			$result = array();

			$sql = "SELECT trim(d.descripcion) descripcion,
			(select count(lote) from pagares.lote where entidad=a.entidad ) cant_lote,
			count(*) cant_oper,
			round(sum(bfvcta)) cuota_cab,
			round(sum(bftcuo)) bruto,
			round(sum(bfpres+bfinte)) saldo 
			FROM pagares.lote a, pagares.operaciones b, fsd0122 c, pagares.entidad d
			WHERE a.lote=b.lote
			AND b.operacion=bfope1
			AND a.entidad=d.entidad
			AND a.entidad=$this->entidad
			GROUP BY a.entidad,d.descripcion;";

			$result =  $db->query($sql)->fetchAll();
			return $result[0];
		}

		public function dash_02(){

			$db = $this->conn();			
			$result = array();

			$sql = "SELECT 
			count(*) cantidad,
			sum(capital+mora) cobrado 
			FROM pagares.pago_clientes a, pagares.lote b
			WHERE  a.lote=b.lote 
			AND a.verificado='N' 
			AND entidad=$this->entidad";

			$result =  $db->query($sql)->fetchAll();
			return $result[0];
		}

		public function dash_03(){

			$db = $this->conn();			
			$result = array();

			$sql = "SELECT count(*)cant_operacion,sum(bevcta) monto_apagar 
			FROM fsd0171 a, pagares.lote b, pagares.operaciones c
			WHERE a.beope1=c.operacion
			AND b.lote=c.lote
			AND be1vto BETWEEN current_date and current_date+7
			AND beesta='P'
			AND entidad=$this->entidad;";

			$result = $db->query($sql)->fetchAll();
			return $result[0];
		}

		public function dash_04(){

			$sql = "SELECT bfult atraso,
			count(*) cantidad,
			sum(bfvcta) monto 
			FROM fsd0122 c, pagares.lote a, pagares.operaciones b
			WHERE bfope1=operacion
			AND a.lote=b.lote
			AND entidad=$this->entidad
			group by 1
			order by 1;";

			foreach ($db -> query($sql) as $row) {
				$result[$i]['atraso']	= $row['atraso'];
				$result[$i]['cantidad']	= $row['cantiad'];
				$result[$i]['monto']	= $row['monto'];
				$i++;
			}
			return $result;
		}

		public function dash_05(){

			$db = $this->conn();			
			$sql = "SELECT sum(cartera) cartera, sum(cobranza) cobranza, round(sum(cobranza)/sum(cartera)*100,2) 
			FROM (
			SELECT datos.entidad,datos.operacion,datos.cartera cartera, sum(capital) cobranza 
			FROM (
			SELECT a.entidad,b.operacion,bfvcta cartera
			FROM pagares.lote a, pagares.operaciones b, fsd0122 c
			WHERE a.lote=b.lote
			AND b.operacion=c.bfope1
			AND bffchv < date_trunc('month', current_date)
			AND bfempr = 1
			AND bfesta=7	
			and a.entidad=$this->entidad

			UNION

			SELECT a.entidad,b.operacion,bfvcta cartera
			FROM pagares.lote a, pagares.operaciones b, fsd0122 c
			WHERE a.lote=b.lote
			AND b.operacion=c.bfope1
			AND bffchv < date_trunc('month', current_date)
			AND bfempr = 1
			AND bfesta=10	
			AND a.entidad=$this->entidad
			AND (bfope1,bfempr) in (SELECT beope1,beempr FROM fsd0171 WHERE beope1=bfope1 AND beempr=bfempr AND beesta='C' GROUP BY beope1,beempr HAVING max(befchp)>=date_trunc('month',current_date))
			) AS datos
			LEFT JOIN pagares.pago_clientes pagos
			ON datos.operacion=pagos.operacion
			AND fecha_pago>=date_trunc('month', current_date)
			GROUP BY datos.entidad, datos.operacion, cartera
		) AS final";

		$result = $db->query($sql)->fetchAll();
		return $result[0];
	}

	public function dash_06(){

		$db = $this->conn();			
		$sql = "SELECT count(*) cantidad, sum(capital+mora) cobranza FROM pagares.pago_clientes pagos, pagares.lote a
		where pagos.lote=a.lote 
		AND fecha_pago>=date_trunc('month', current_date)
		AND entidad=$this->entidad";

		$result = $db->query($sql)->fetchAll();
		return $result[0];
	}



}		