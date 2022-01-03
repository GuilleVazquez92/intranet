<?php
class Riesgos extends Conexion{

	public $cuenta;
	public $tipo_solicitud;
	public $linea_credito;

	public function scoring_verificacion(){

		$result = array();
		$db 	= $this->conn();
		$sql 	= "SELECT 
						2, bffchd,fsd0011.aacuen cuenta, 
						trim(aadocu) documento, 
						trim(aanom) cliente,
						trim(origen) origen, 
						'SOLICITUD DE COMPRA' tipo_solicitud,
						usuario,
						case
							when LENGTH(REPLACE(avance,'0',''))=4 then 100
							when LENGTH(REPLACE(avance,'0',''))=3 then 75
							when LENGTH(REPLACE(avance,'0',''))=2 then 50
							when LENGTH(REPLACE(avance,'0',''))=1 then 25
							else 0
						end avance

					FROM fsd0011,fsd0122 
					LEFT JOIN(SELECT tcoperel,tccarcue,tccarps origen FROM tef012 WHERE tcoperel>0) as origen on tcoperel=bfope1 and tccarcue=fsd0122.aacuen
					LEFT JOIN(SELECT cuenta,usr_veri usuario, check_veri avance FROM riesgos.datos_scoring) as scoring on scoring.cuenta=fsd0122.aacuen
					WHERE fsd0011.aacuen=fsd0122.aacuen 
					AND bfempr=1 AND bfesta=3				
					
union
SELECT 
						2, bffchd,fsd0011.aacuen cuenta, 
						trim(aadocu) documento, 
						trim(aanom) cliente,
						trim(origen) origen, 
						'SOLICITUD DE COMPRA - CODEDUDOR' tipo_solicitud,
						usuario,
						case
							when LENGTH(REPLACE(avance,'0',''))=4 then 100
							when LENGTH(REPLACE(avance,'0',''))=3 then 75
							when LENGTH(REPLACE(avance,'0',''))=2 then 50
							when LENGTH(REPLACE(avance,'0',''))=1 then 25
							else 0
						end avance

					FROM fsd0011,fsd0122 
					LEFT JOIN(SELECT tcoperel,tccarcue,tccarps origen FROM tef012 WHERE tcoperel>0) as origen on tcoperel=bfope1 and tccarcue=fsd0122.aacuen,
					fsd0121
					LEFT JOIN(SELECT cuenta,usr_veri usuario, check_veri avance FROM riesgos.datos_scoring) as scoring on scoring.cuenta=fsd0121.aacuen
					WHERE fsd0011.aacuen=fsd0121.aacode 
					and fsd0122.aacuen=fsd0121.aacuen 
					and fsd0122.bfempr=fsd0121.bfempr
					and fsd0122.bfope1=fsd0121.bfope1	
					AND fsd0122.bfempr=1 
					AND bfesta=3				

					UNION
					SELECT 1,fecha_ingresado,clientes_ampliacion.cuenta, 
							trim(aadocu) documento, 
							trim(aanom) cliente,
							'WEB' origen, 
							'AMPLIACION DE LINEA' tipo_solicitud,
							usuario,
							case
								when LENGTH(REPLACE(avance,'0',''))=4 then 100
								when LENGTH(REPLACE(avance,'0',''))=3 then 75
								when LENGTH(REPLACE(avance,'0',''))=2 then 50
								when LENGTH(REPLACE(avance,'0',''))=1 then 25
								else 0
							end avance
							
					  FROM riesgos.clientes_ampliacion,fsd0011
					  LEFT JOIN(SELECT cuenta,usr_veri usuario, check_veri avance FROM riesgos.datos_scoring WHERE check_veri is null or check_veri!='1111') as scoring on scoring.cuenta=fsd0011.aacuen
					WHERE aacuen=clientes_ampliacion.cuenta and estado='P' and fecha_ingresado>=current_date-15
					ORDER BY 1,2";
		
		$result = $db->query($sql)->fetchAll();		
		return $result;
	}

	public function scoring_analisis(){
	
		$result = array();
		$db 	= $this->conn();
		$sql 	= "SELECT 
						2, bffchd,fsd0011.aacuen cuenta, 
						trim(aadocu) documento, 
						trim(aanom) cliente,
						trim(origen) origen, 
						'SOLICITUD DE COMPRA' tipo_solicitud,
						usuario,
						case
							when LENGTH(REPLACE(avance,'0',''))=3 then 100
							when LENGTH(REPLACE(avance,'0',''))=2 then 67
							when LENGTH(REPLACE(avance,'0',''))=1 then 33
							else 0
						end avance

					FROM fsd0011,fsd0122 
					LEFT JOIN(SELECT tcoperel,tccarcue,tccarps origen FROM tef012 WHERE tcoperel>0) as origen on tcoperel=bfope1 and tccarcue=fsd0122.aacuen
					LEFT JOIN(SELECT cuenta,usr_anal usuario, check_anal avance FROM riesgos.datos_scoring) as scoring on scoring.cuenta=fsd0122.aacuen
					WHERE fsd0011.aacuen=fsd0122.aacuen 
					AND bfempr=1 AND bfesta=4				
					
					UNION

					SELECT 
						2, bffchd,fsd0011.aacuen cuenta, 
						trim(aadocu) documento, 
						trim(aanom) cliente,
						trim(origen) origen, 
						'SOLICITUD DE COMPRA - CODEDUDOR' tipo_solicitud,
						usuario,
						case
							when LENGTH(REPLACE(avance,'0',''))=3 then 100
							when LENGTH(REPLACE(avance,'0',''))=2 then 67
							when LENGTH(REPLACE(avance,'0',''))=1 then 33
							else 0
						end avance

					FROM fsd0011,fsd0122 
					LEFT JOIN(SELECT tcoperel,tccarcue,tccarps origen FROM tef012 WHERE tcoperel>0) as origen on tcoperel=bfope1 and tccarcue=fsd0122.aacuen,
					fsd0121
					LEFT JOIN(SELECT cuenta,usr_anal usuario, check_anal avance FROM riesgos.datos_scoring) as scoring on scoring.cuenta=fsd0121.aacuen
					WHERE fsd0011.aacuen=fsd0121.aacode 
					and fsd0122.aacuen=fsd0121.aacuen 
					and fsd0122.bfempr=fsd0121.bfempr
					and fsd0122.bfope1=fsd0121.bfope1	
					AND fsd0122.bfempr=1 
					AND bfesta=4				

					UNION
					SELECT 1,fecha_ingresado,clientes_ampliacion.cuenta, 
							trim(aadocu) documento, 
							trim(aanom) cliente,
							'WEB' origen, 
							'AMPLIACION DE LINEA' tipo_solicitud,
							usuario,
							case
								when LENGTH(REPLACE(avance,'0',''))=3 then 100
								when LENGTH(REPLACE(avance,'0',''))=2 then 67
								when LENGTH(REPLACE(avance,'0',''))=1 then 33
								else 0
							end avance
							
					  FROM riesgos.clientes_ampliacion,fsd0011
					  LEFT JOIN(SELECT cuenta,usr_anal usuario, check_anal avance FROM riesgos.datos_scoring WHERE check_veri='1111') as scoring on scoring.cuenta=fsd0011.aacuen
					WHERE aacuen=clientes_ampliacion.cuenta and estado='P' and fecha_ingresado>=current_date-15
					ORDER BY 1,2";
		
		$result = $db->query($sql)->fetchAll();		
		return $result;		
	}

	public function resumen(){

		$result = array();
		$db 	= $this->conn();

		$sql = "INSERT INTO riesgos.datos_scoring(cuenta, servicios_basicos, conyuge, telefono, situacion_laboral, antiguedad_lab, mercado_laboral, insitu, nuevo_mundo, cuenta_bancaria, mas_cuenta, producto, mercado, ingreso, mora_externa, deuda_mensual, total_deuda_ex, ultima_verificacion, verificador, ultimo_analisis, analista, usr_veri, usr_anal, check_veri, check_anal, ref_comercial, tipo_solicitud)
    			VALUES ($this->cuenta,1,1,'00000',1,1,1,0,0,1,0,0,0,0.00,0,0.00,0.00,'','','','','$_COOKIE[usuario]','','0000','0000',0,'$this->tipo_solicitud')
    			ON CONFLICT DO NOTHING;";
    	$db->query($sql);

		$sql = "SELECT 
					fsd0011.aacuen cuenta,
					trim(split_part(split_part(trim(aadocu),'-',1),' ',1)) documento, 
					trim(aanom) nombre_cliente, 
					round((current_date-aafech)/365) edad,
					CASE 
						WHEN aasexo='M' THEN 'MASCULINO'
						WHEN aasexo='F' THEN 'FEMENINO'
						ELSE ''
					END sexo,
					aaesta estado_civil, 
					cant_hijos,
					vivienda,
					monto_cuota,
					faja,
					archivo,
					ult_consulta,
					current_date-ult_consulta dias_ult_consulta, 
					cantidad_cuota,
					regexp_replace(asituacion,'[a-zA-Z]', 'X','g') mora_interna,
					CASE 
						WHEN length(replace(trim(asituacion),'-',''))>=2 THEN 'CLIENTE'
						ELSE 'NUEVO' 
					END cliente,
					entrega,
					scoring.*
				
				FROM fsd0011
				LEFT JOIN (SELECT aacuen,trim(infarch) archivo, inffech ult_consulta, substring(regexp_replace(infobs, '[^a-zA-Z]', '', 'g') from 5 for 1) faja
							FROM public.fsta003 
							WHERE (aacuen,inffech) in (select aacuen, max(inffech) from public.fsta003 where aacuen=$this->cuenta group by 1)
				) as faja on faja.aacuen=fsd0011.aacuen

				LEFT JOIN(SELECT distinct(fsd0122.aacuen) entrega from fsd0171, fsd0122 where fsd0122.aacuen=fsd0171.aacuen and beope1=bfope1 and be1vto<=current_date+5 and becta<=1 and (bfesta=3 or bfesta=4 or bfesta=12)) as entrega on entrega=fsd0011.aacuen			
				
				LEFT JOIN (SELECT aacuen, sum(bfvcta) monto_cuota, max(bfcant) cantidad_cuota 
								FROM fsd0122 
								WHERE bfempr=1 AND (bfesta=3 or bfesta=4 or bfesta=12) 
								GROUP BY 1

								) as operaciones ON operaciones.aacuen=fsd0011.aacuen
				
				LEFT JOIN (SELECT aacuen, awinte cant_hijos,awprop vivienda FROM fsd022) as basico ON basico.aacuen=fsd0011.aacuen
				LEFT JOIN (SELECT cuenta, servicios_basicos, conyuge, telefono, situacion_laboral, antiguedad_lab, mercado_laboral, insitu, nuevo_mundo, cuenta_bancaria, mas_cuenta, producto, mercado, ingreso, mora_externa, deuda_mensual, total_deuda_ex, ultima_verificacion, verificador, ultimo_analisis, analista, usr_veri, usr_anal, check_veri, check_anal, ref_comercial, tipo_solicitud FROM riesgos.datos_scoring) as scoring ON scoring.cuenta=fsd0011.aacuen

				WHERE fsd0011.aacuen=$this->cuenta;";

		$result = $db->query($sql)->fetchAll();		
		return $result[0];	 
	}

	public function actualizacion_datos($tabla,$etiqueta,$valor,$campo){

		$db 	= $this->conn();
		$sql 	= "UPDATE $tabla SET $etiqueta='$valor' WHERE $campo=$this->cuenta;";
		if($db->query($sql)){
			return "Se actulizo los datos del cliente";
		}
	}

	public function check_validacion($cuenta,$zona,$valor){
		
		$db 	= $this->conn();
		$sql 	= "UPDATE riesgos.datos_scoring SET $zona='$valor' WHERE cuenta=$cuenta;";
		if($db->query($sql)){
			return "Se actulizo los datos del cliente";
		}
	}

	public function firmar_scoring($usuario,$cargo){

  		switch ($cargo) {
  			case 'verificador':
  				$campo1 = "ultima_verificacion";
  				$campo2 = "verificador";
  				break;
  			
  			case 'analista':
  				$campo1 = "ultimo_analisis";
  				$campo2 = "analista";
   				break;
  		}

		$db 	= $this->conn();
		$sql 	= "UPDATE riesgos.datos_scoring SET $campo1=split_part(now()::text,'.',1), $campo2='$usuario' WHERE cuenta=$this->cuenta;";
		if($db->query($sql)){
			return "Se actulizo los datos del cliente";
		}
	}

	public function linea_credito(){

		$db 	= $this->conn();
		$sql = "INSERT INTO public.bfind35(lincuen, linasig, linsald, linfval, linfult, linfvto, linesta, linobse, lintipo, linnomb, lindocu, linsupe, linvend, linacti, linclas, linusu)
					SELECT 	
					aacuen,
					$this->linea_credito,
					$this->linea_credito-COALESCE((select round(sum(bfpres+bfinte)) from fsd0122 z where z.aacuen=a.aacuen and bfempr=1 and bfesta between 3 and 7 ),0),
					current_date,
					current_date,
					current_date+190,
					1,
					'',
					1,
					aanom, 	
					aadocu, 
					0,
					0,
					'',
					'',
					''
				FROM fsd0011 a 
				WHERE  a.aacuen=$this->cuenta
				ON CONFLICT (lincuen) 
				DO UPDATE SET linsald=excluded.linsald,linasig=excluded.linasig, linesta=excluded.linesta, linfvto=excluded.linfvto where bfind35.linsald!=excluded.linsald;";
		$db->query($sql);

		// $sql = "UPDATE public.bfind35
		// 		SET linasig=$this->linea_credito
		// 		WHERE lincuen=$this->cuenta;";

		// $db->query($sql);
	}

	public function ampliacion_linea($cuenta,$funcion){

		$db 	= $this->conn();
		$sql = "UPDATE riesgos.clientes_ampliacion SET fecha_finalizado=now(), estado='$funcion' WHERE cuenta=$cuenta AND estado='P';";
		$db->query($sql);
	}

	public function agregar_scoring($usuario,$nombre){

		$db 	= $this->conn();
		$sql = "INSERT INTO public.bfinac4(aacuen, aafecdoc, aacladoc, aadocpath, aadocesta, aadocusu, aadocwrk, aadochor, aadocdat1, aadocdat2, aadocobs)
		 		VALUES ($this->cuenta,current_date,16,'$nombre',0,'$usuario','INTRANET',split_part(current_time::text,'.',1),'',0,'')";
		$db->query($sql); 		

	}
	public function resumenScoring(){

		$result = array();
		$db 	= $this->conn();

		$sql = "SELECT 
					fsd0011.aacuen cuenta,
					trim(split_part(split_part(trim(aadocu),'-',1),' ',1)) documento, 
					trim(aanom) nombre_cliente, 
					round((current_date-aafech)/365) edad,
					CASE 
						WHEN aasexo='M' THEN 'MASCULINO'
						WHEN aasexo='F' THEN 'FEMENINO'
						ELSE ''
					END sexo,
					aaesta estado_civil, 
					cant_hijos,
					vivienda,
					monto_cuota,
					faja,
					archivo,
					ult_consulta,
					current_date-ult_consulta dias_ult_consulta, 
					cantidad_cuota,
					regexp_replace(asituacion,'[a-zA-Z]', 'X','g') mora_interna,
					CASE 
						WHEN length(replace(trim(asituacion),'-',''))>=2 THEN 'CLIENTE'
						ELSE 'NUEVO' 
					END cliente,
					entrega,
					scoring.*
				
				FROM fsd0011
				LEFT JOIN (SELECT aacuen,trim(infarch) archivo, inffech ult_consulta, substring(regexp_replace(infobs, '[^a-zA-Z]', '', 'g') from 5 for 1) faja
							FROM public.fsta003 
							WHERE (aacuen,inffech) in (select aacuen, max(inffech) from public.fsta003 where aacuen=$this->cuenta group by 1)
				) as faja on faja.aacuen=fsd0011.aacuen

				LEFT JOIN(SELECT distinct(fsd0122.aacuen) entrega from fsd0171, fsd0122 where fsd0122.aacuen=fsd0171.aacuen and beope1=bfope1 and be1vto<=current_date+5 and becta<=1 and (bfesta=3 or bfesta=4 or bfesta=12)) as entrega on entrega=fsd0011.aacuen			
				
				LEFT JOIN (SELECT aacuen, sum(bfvcta) monto_cuota, max(bfcant) cantidad_cuota 
								FROM fsd0122 
								WHERE bfempr=1 AND (bfesta=3 or bfesta=4 or bfesta=12) 
								GROUP BY 1

								) as operaciones ON operaciones.aacuen=fsd0011.aacuen
				
				LEFT JOIN (SELECT aacuen, awinte cant_hijos,awprop vivienda FROM fsd022) as basico ON basico.aacuen=fsd0011.aacuen
				LEFT JOIN (SELECT cuenta, servicios_basicos, conyuge, telefono, situacion_laboral, antiguedad_lab, mercado_laboral, insitu, nuevo_mundo, cuenta_bancaria, mas_cuenta, producto, mercado, ingreso, mora_externa, deuda_mensual, total_deuda_ex, ultima_verificacion, verificador, ultimo_analisis, analista, usr_veri, usr_anal, check_veri, check_anal, ref_comercial, tipo_solicitud FROM riesgos.datos_scoring) as scoring ON scoring.cuenta=fsd0011.aacuen

				WHERE fsd0011.aacuen=$this->cuenta;";

		$result = $db->query($sql)->fetchAll();		
		return $result[0];	 


	}



	public function listaScoring(){

		$db 	= $this->conn();
		$sql = "SELECT cuenta
			FROM riesgos.datos_scoring;";
		$result = $db->query($sql)->fetchAll();

		return $result;

	}

	public function cargarLineaScoring(){

		$db 	= $this->conn();
		$sql = "INSERT INTO riesgos.linea_scoring(
			cuenta, linea,saldo_cuota,saldo_total,deuda_mensual,deuda_total,cuota)
		VALUES ($this->cuenta, $this->linea,$this->saldo_cuota,$this->saldo_total,
				$this->deuda_mensual, $this->deuda_total,$this->cuota);";
		$result = $db->query($sql);


	}

		public function deuda(){

		$db 	= $this->conn();
		$sql = "SELECT bevcta cuota,SUM(bevcta) total
				FROM public.fsd0171
				where aacuen = $this->cuenta
				and beesta = 'P'
				group by aacuen, bevcta
				;";
		$result = $db->query($sql)->fetchAll();

		
		return $result;

	}
}



?>