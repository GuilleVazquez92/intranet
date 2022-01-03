<?php
class IR extends Conexion{

	public $cuenta;
	public $operacion;
	public $tipo;
	public $tramo;
	public $grupo;
	public $valor_capital;
	public $valor_tasa;
	public $valor_cuota;
	public $valor_total;
	public $cantidad_cuota;
	public $cabezon;
	public $entrega;
	public $cod_oper;
	public $abogado;

	public function consultar_cliente(){

		$db = $this->conn();			
		$result = array();

		$sql = "SELECT 
		a.aacuen cuenta, 
		a.aadocu documento, 
		trim(a.aanom) cliente,
		trim(b.awcalle)||' Y '||trim(b.awesq) direccion_part,
		b.awtel1 telefono_part,
		b.awcelu celular_part,
		(select trim(apnomb) from fst003 z where b.aidept=z.aidept and b.apciud=z.apciud) ciudad_part,
		c.aaempr laboral, 
		trim(c.bacalle)||' Y '||trim(c.baesq) direccion_lab,
		c.batel1 telefono_lab,
		d.thtram tramo,
		d.thgest gestor,
		a.adenun,
		a.asituacion calificacion
		FROM fsd0011 a
		LEFT JOIN fsd022 b ON a.aacuen= b.aacuen  
		LEFT JOIN fsd023 c ON a.aacuen= c.aacuen
		LEFT JOIN tfinh44 d ON a.aacuen= d.thcuen and d.thempr=2
		WHERE a.aacuen =  $this->cuenta
		LIMIT 1;";

		if($this->cuenta!="" && $db->query($sql)->rowCount()>0 ){

			foreach ($db -> query($sql) as $row ) {

				$result['cuenta'] 			= $row['cuenta'];
				$result['documento'] 		= $row['documento'];	
				$result['cliente'] 			= $row['cliente'];	
				$result['direccion_part'] 	= $row['direccion_part'];	
				$result['telefono_part'] 	= $row['telefono_part'];	
				$result['celular_part'] 	= $row['celular_part'];	
				$result['ciudad_part'] 		= $row['ciudad_part'];					
				$result['laboral'] 			= $row['laboral'];	
				$result['direccion_lab'] 	= $row['direccion_lab'];	
				$result['telefono_lab'] 	= $row['telefono_lab'];	
				$result['tramo'] 			= $row['tramo'];	
				$result['gestor'] 			= $row['gestor'];
				$result['calificacion'] 	= $row['calificacion'];
				$result['adenun'] 			= $row['adenun'];
			}
		}
		return $result;		
	}

	public function consultar_gestores(){
		$i 					= 0;
		$db 	= $this->conn();			
		$result = array();
		$sql = "SELECT 
		case 
		when grupo=1 then 'NORMAL'
		when grupo=2 then 'JUDICIAL'
		else '' 
		end	grupo, tramo, gestor, descripcion, cant_cli, cant_ope, cuota_cab, cartera, recupero, meta
		FROM ir.view_gestores;";
		
		foreach($db->query($sql) as $row ){
			$result[$i]['grupo']		= $row['grupo'];
			$result[$i]['tramo']		= $row['tramo'];
			$result[$i]['gestor']		= $row['gestor'];
			$result[$i]['descripcion']	= $row['descripcion'];
			$result[$i]['cant_cli']		= $row['cant_cli'];
			$result[$i]['cant_ope']		= $row['cant_ope'];
			$result[$i]['cuota_cab']	= $row['cuota_cab'];
			$result[$i]['cartera']		= $row['cartera'];
			$result[$i]['recupero']		= $row['recupero'];	
			$result[$i]['meta']			= $row['meta'];

			$i++;	
			$result['cant_registros']		= $i;	
		}
		return $result;
	}

	public function consultar_grupo(){
		$db 	= $this->conn();			
		$result = array();
		$grupo = ($this->grupo=='NORMAL') ? 1 : 2;
		$sql = "SELECT 
		sum(cant_cli) cant_cli, 
		sum(cant_ope) cant_ope, 
		sum(cuota_cab)cuota_cab, 
		sum(cartera) cartera, 
		sum(recupero) recupero, 
		sum(meta) meta 
		
		FROM ir.view_gestores 
		WHERE grupo=$grupo;";
		
		foreach($db->query($sql) as $row){
			$result['cant_cli']		= $row['cant_cli'];
			$result['cant_ope']		= $row['cant_ope'];
			$result['cuota_cab']	= $row['cuota_cab'];
			$result['cartera']		= $row['cartera'];
			$result['recupero']		= $row['recupero'];	
			$result['meta']			= $row['meta'];

		}
		return $result;
	}


	public function consultar_tramo(){
		$db 	= $this->conn();			
		$result = array();
		$sql = "SELECT 
		sum(cant_cli) cant_cli, 
		sum(cant_ope) cant_ope, 
		sum(cuota_cab)cuota_cab, 
		sum(cartera) cartera, 
		sum(recupero) recupero, 
		sum(meta) meta

		FROM ir.view_gestores 
		WHERE tramo= $this->tramo;";
		
		foreach($db->query($sql) as $row){
			$result['cant_cli']		= $row['cant_cli'];
			$result['cant_ope']		= $row['cant_ope'];
			$result['cuota_cab']	= $row['cuota_cab'];
			$result['cartera']		= $row['cartera'];
			$result['recupero']		= $row['recupero'];	
			$result['meta']			= $row['meta'];
		}
		return $result;
	}

	public function dias_habiles(){

		$result = array();
		$db  = $this->conn();
		$sql = "SELECT habil habiles, trasn trascurrido FROM public.fecha_empresa WHERE fecha=current_date;";
		$result = $db->query($sql)->fetchAll();
		return $result;

	}

	public function operaciones_consultar(){

		$result = array();
		$db = $this->conn();			
		$i 	= 0;

		$sql = "SELECT 
		aacuen cuenta, 
		bfope1 operacion, 
		bfcant cuotas_cant, 
		bfpend cuotas_pend,  
		round(bfvcta) monto_cuota,
		round(bfpres+bfinte) saldo, 
		round(bftcuo)monto,
		jjabog abogado,
		(select sum(total) from get_mora(bfope1,current_date,bfcant*3)) monto_mora
		FROM fsd0122 
		WHERE bfesta=7 
		AND bfempr= 2
		AND aacuen NOT IN (select aacuen from fsd0122 where bfempr=2 and (bfoper between 100 and 199) and bfesta=7) 
		AND aacuen = $this->cuenta;";

		if($this->cuenta!="" && $db->query($sql)->rowCount()>0 ){

			foreach ($db -> query($sql) as $row ) {

				$result[$i]['cuenta'] 		= $row['cuenta'];	
				$result[$i]['operacion'] 	= $row['operacion'];
				$result[$i]['cuotas_cant'] 	= $row['cuotas_cant'];
				$result[$i]['cuotas_pend'] 	= $row['cuotas_pend'];
				$result[$i]['monto_cuota'] 	= $row['monto_cuota'];
				$result[$i]['saldo'] 		= $row['saldo'];
				$result[$i]['monto'] 		= $row['monto'];
				$result[$i]['monto_mora'] 	= $row['monto_mora'];
				$result[$i]['abogado'] 		= $row['abogado'];
				$i++;
			}
		}
		return $result;
	}

	public function operaciones_pendientes(){

		$result = array();
		$db = $this->conn();			
		$i 	= 0;

		$sql = "SELECT 
		aacuen cuenta, 
		bfope1 operacion,
		bfoper tipo, 
		bfcant cuotas_cant, 
		bfpend cuotas_pend,  
		round(bfvcta) monto_cuota,
		round(bfpres+bfinte) saldo, 
		round(bftcuo)monto,
		jjabog abogado
		FROM fsd0122 
		WHERE bfesta=6 
		AND bfempr= 2
		AND bfoper between 100 and 199 
		AND aacuen = $this->cuenta;";

		if($this->cuenta!="" && $db->query($sql)->rowCount()>0 ){

			foreach ($db -> query($sql) as $row ) {

				$result[$i]['cuenta'] 		= $row['cuenta'];	
				$result[$i]['operacion'] 	= $row['operacion'];
				$result[$i]['tipo'] 	= $row['tipo'];
				$result[$i]['cuotas_cant'] 	= $row['cuotas_cant'];
				$result[$i]['cuotas_pend'] 	= $row['cuotas_pend'];
				$result[$i]['monto_cuota'] 	= $row['monto_cuota'];
				$result[$i]['saldo'] 		= $row['saldo'];
				$result[$i]['monto'] 		= $row['monto'];
				$result[$i]['abogado'] 		= $row['abogado'];
				$i++;
			}
		}
		return $result;
	}

	public function operaciones_aprobar(){

		$result = array();
		$db = $this->conn();
		$i = 0;

		$sql = "SELECT a.aacuen cuenta, aanom cliente, bfope1 operacion, bfoper tipo,
		case
		when bfoper = 105 then 'REFINANCIADO'
		when bfoper = 106 then 'HOMOLOGADO'
		when bfoper = 107 then 'PROMOCION'
		else 'NORMAL' 
		end tipo_descripcion, bftcuo bruto,bfcant cantidad, bfvcta cuota, 
		(select count(*) from fsd0122 z where bfempr=2 and bfesta=7 and a.aacuen=z.aacuen) cant_oper,
		(select thgest from tfinh44 z where thempr=2 and thcuen=fsd0011.aacuen) gestor
		FROM fsd0122 a, fsd0011 
		WHERE a.aacuen=fsd0011.aacuen AND bfempr=2 AND bfesta=6 AND bfvcta>0 AND bfoper BETWEEN 105 AND 110;";

		foreach ( $db -> query($sql) as $row ) {

			$result[$i]['cuenta'] 	 =  $row['cuenta'];
			$result[$i]['cliente'] 	 =  $row['cliente'];
			$result[$i]['operacion'] =  $row['operacion'];
			$result[$i]['tipo'] 	 =  $row['tipo'];
			$result[$i]['tipo_descripcion'] =  $row['tipo_descripcion'];
			$result[$i]['bruto'] 	 =  $row['bruto'];
			$result[$i]['cantidad']	 =  $row['cantidad'];
			$result[$i]['cuota']	 =  $row['cuota'];
			$result[$i]['cant_oper'] =  $row['cant_oper'];
			$result[$i]['gestor']  	 =  $row['gestor'];
			$i++;
		}
		return $result;
	}

	public function operacion_consultar(){

		$result 	= array();
		$db 		= $this->conn();			

		$sql = "SELECT 
		fsd0122.aacuen cuenta,
		trim(aadocu) documento,  
		trim(aanom) cliente,
		bfope1 operacion,
		bfvcta monto_cuota, 
		round(bftcuo)monto, 
		bffchv fecha,
		(select min(be1vto) from fsd0171 where beempr=2 and beope1=fsd0122.bfope1 and becta=1) p_vencimiento,
		(select max(be1vto) from fsd0171 where beempr=2 and beope1=fsd0122.bfope1) vencimiento
		FROM fsd0122, fsd0011 
		WHERE fsd0122.aacuen=fsd0011.aacuen
		AND bfempr= 2 
		AND bfesta= 6
		AND fsd0122.aacuen = $this->cuenta LIMIT 1;";

		if($this->cuenta!="" && $db->query($sql)->rowCount()>0 ){
			foreach ($db -> query($sql) as $row ) {
				$result['cuenta'] 		= $row['cuenta'];	
				$result['cliente'] 		= $row['cliente'];
				$result['documento'] 	= $row['documento'];
				$result['operacion']	= $row['operacion'];
				$result['monto'] 		= $row['monto'];
				$result['monto_cuota']	= $row['monto_cuota'];
				$result['fecha'] 		= $row['fecha'];
				$result['p_vencimiento']= $row['p_vencimiento'];
				$result['vencimiento'] 	= $row['vencimiento'];
			}
		}
		return $result;
	}

	public function cuotero_consultar(){
		$i  = 0;
		$db = $this->conn();			
		$result = array();
		$sql = "SELECT
		becta cuota, 
		be1vto vencimiento,
		bevcta monto 
		FROM fsd0171 
		WHERE beempr=2 
		AND   beope1=$this->operacion;";

		if($this->cuenta!="" && $db->query($sql)->rowCount()>0 ){
			foreach ($db -> query($sql) as $row ) {
				$result[$i]['cuota']		= $row['cuota'];
				$result[$i]['vencimiento'] 	= $row['vencimiento'];
				$result[$i]['monto'] 		= $row['monto'];
				$i++;
			}
		}
		return $result;
	}


	public function recalcular_cuenta(){

		$db = $this->conn();			
		$result = array();

		$sql = "SELECT 
		cod_operacion cod_oper,
		tasa_financiacion,
		cuota_minima,
		sum((select sum(total) from get_mora(bfope1,current_date,bfpend*tasa_recalculo))) nuevo_capital

		FROM fsd0122,ir.tipo_operaciones 
		WHERE bfempr = 2  
		AND	bfesta 	= 7 
		AND aacuen 	= $this->cuenta
		AND tipo 	= $this->tipo 
		
		GROUP BY 1,2,3;";

		if($this->tipo!="" && $db->query($sql)->rowCount()>0 ){
			foreach ($db -> query($sql) as $row ) {

				$result['cod_oper'] 		= $row['cod_oper'];
				$tasa 						= $result['tasa']			= ($row['tasa_financiacion']/100);	
				$capital 					= $result['capital'] 		= $row['nuevo_capital'];	
				$minimo 					= $result['minimo'] 		= $row['cuota_minima'];	

			}

			$entrega  = $result['entrega']  = $this->entrega;

			for ($i=1; $i <= 48 ; $i++) { 
				$var 		 	= (($capital-$entrega)*($tasa*$i))+($capital-$entrega);
				$valor_cuota	= round((((($var-($capital-$entrega))*0.1+$var)/$i)+499),-3);

				if($valor_cuota>=$minimo){

					$result[$i]['cantidad'] 	= $i;
					$result[$i]['valor_cuota']	= $valor_cuota;
					$result[$i]['valor_total'] 	= $valor_cuota*$i;

				}else{
					break;	
				}
			}
		}
		return $result;		
	}

	public function crear_operacion(){

		$db 	= $this->conn();			
		$result = array();

		$sql = "UPDATE public.fst021 SET azulti = azulti+1 WHERE azcont=5000 RETURNING azulti operacion;";

		$operacion 		= $db -> query($sql) -> fetchAll();
		$operacion  	= $this->operacion = $operacion[0]['operacion'];
		$cuenta 		= $this->cuenta;
		$cod_oper 		= $this->cod_oper;
		$solicitado		= $this->valor_capital;
		$saldo_capital	= $this->valor_capital;
		$saldo_interes  = $this->valor_total-$this->valor_capital;		  		
		$capital 		= $this->valor_capital + ($saldo_interes*0.1);
		$cant_dias 		= $this->cantidad_cuota*31;
		$cuota 			= $this->valor_cuota;

// Crear operacion de credito
		$sql = "INSERT INTO public.fsd0122(
		bfempr, bfsucu, bfope1, bfope2, aacuen, bfmone, bfmod, bftrn, bfrubr, bfgara, bfoper, bfultd, bfrubo, bfsoli, bfente, bfterc, 
		bfcapi, bfcons, bfefch, bfplaz, bfliqu, bfobs, bffchv, bffchd, bf1vto, bftcuo, bfcobd, bfsusp, bftint, bfdeve, bfadev, bfcant, 
		bfdevm, bfpend, bfvcta, bfcod, bfvend, bfcobr, bfesta, bfproc, bftasa, bfpres, bfinte, bfgtos, bfley, bfcomi, bfsegu, bfperi, 
		bfmed, bfcan, bfsup, bfform, bfcheq, bfcta, bfpaga, bfvist, bfcali, bfslec, bfcod1, bfcod2, bfcod3, bfreg, bfmax, bfult, bftefe, 
		bfsitu, bfclas, bftip, bfpla, bfrubc, bffin, bftipper, bfopmig, bfagen, bfcat1, bfcat2, bfcat3, bffcho, bfbanc, bfasoc, bfcodi, 
		bfprevact, bfprevant, bfporl1, bfporl2, bfporl3, bftitu, bfporl4, bfporl5, bfbloc, bffchc, bfvendan,jjabog)

		VALUES (2,1,$this->operacion,0,$this->cuenta,6900,100,1,0,1,$cod_oper,'0001-01-01',0,$solicitado,0,0,$saldo_capital+($saldo_interes*0.1),0,1,
		$cant_dias, $solicitado,'',current_date,current_date,'0001-01-01',$this->valor_total+$this->cabezon,0,0,$saldo_interes,0,$saldo_interes,
		$this->cantidad_cuota,0,$this->cantidad_cuota,$cuota,0,119,0,6,' ',$this->valor_tasa,$saldo_capital+$this->cabezon,$saldo_interes,0,
		$saldo_interes*0.1,0,0,1,10,19,96,' ', 0 ,0,1,'Vista','','',0,0,0,0,0,0,0,1,1,'A',0,0,0,0,0,240,'','','','0001-01-01',0,0,0,0,0,'','',0,
		'','',0,'','0001-01-01',0, $this->abogado)";

		if($db->query($sql)){

			$capital 		= round($saldo_capital/$this->cantidad_cuota,0);
			$interes   		= round($saldo_interes/$this->cantidad_cuota,0);
			$var_capital 	= $this->valor_total;

/*
			if(date("d")>=26){
				$vencimiento = date("Y-m-d",strtotime(date("Y-m-05")."+ 31 days"));

			}elseif (date("d")<=10) {
				$vencimiento = date("Y-m-d",strtotime(date("Y-m-05")."+ 1 days"));

			} else{
				$vencimiento = date("Y-m-d",strtotime(date("Y-m-21")."+ 1 days"));
			}
*/
		$vencimiento = date("Y-m-d"/*,strtotime(date("Y-m-d")."+ 1 days")*/);


		if(date("w",strtotime($vencimiento) == 0)) {
			$vencimiento = date("Y-m-d",strtotime($vencimiento."+ 1 days"));
		}

		$vencimiento = date("Y-m-d");
		$fecha_actual = date("Y-m-d");
		$fecha1 = new DateTime($fecha_actual);
		$fecha2 = new DateTime($vencimiento);
		$diff = $fecha1->diff($fecha2);
		$cantidad_dias  = $diff->days;

		$sql = "INSERT INTO public.fsd0171(
		beempr, besucu, beope1, beope2, becta, aacuen, bemod, betrn, 
		bemone, berubr, bevalo, berubo, befcpg, becajn, becaje, besuco, 
		behora, beplaz, beatra, be1vto, beesta, befchp, befchv, bepago, 
		baacta, becart, betelg, befono, beviat, beacue, bemora, bepuni, 
		beley, bevcta, beacta, beicta, besald, bedeve, beadev, becob, 
		beacob, beprev, besusp, becobd, bedesc, besalc, besali, betasa, 
		bemorp, bepunp, beleyp, begtop, beivap, beotrp, beiva, besalcta, 
		beotr1, beotr2, beotr3, betotpa, bebloc, belibr, bedley, bedmor, 
		bedpun, bedgto, bedant, bediva, beopecue, begtoabop)
		VALUES";

		if($this->entrega >=1){
		$sql .= "(2,1,$operacion,0,0,$this->cuenta,0,0,6900,0,'$fecha_actual',0,'0001-01-01',0,'',0,'',$cantidad_dias,0,'$vencimiento','T','0001-01-01','0001-01-01',0,0,0,0,0,0,0,0,0,0,$this->entrega,$this->entrega,0,$this->entrega,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'','',0,0,0,0,0,0,'',0),";

			if(date("d")>=26){
				$vencimiento = date("Y-m-d",strtotime(date("Y-m-05")."+ 61 days"));

			}elseif (date("d")<=10) {
				$vencimiento = date("Y-m-d",strtotime(date("Y-m-05")."+ 31 days"));

			} else{
				$vencimiento = date("Y-m-d",strtotime(date("Y-m-21")."+ 31 days"));
			}
		}


		for ($i=1; $i <=$this->cantidad_cuota; $i++) { 

			$valor_capital = ($saldo_capital >= $capital) ? $capital : $saldo_capital;
			$valor_interes = ($saldo_interes >= $interes) ? $interes : $saldo_interes;
			$var_capital  -= $cuota;
			$saldo_interes -= $interes;
			$var_capital   = ($var_capital < 0) ? 0 : $var_capital;
			$saldo_interes = ($saldo_interes < 0) ? 0 : $saldo_interes;

			$sql .= "(2,1,$operacion,0,$i,$this->cuenta,0,0,6900,0,'$fecha_actual',0,'0001-01-01',0,'',0,'',$cantidad_dias,0,'$vencimiento','T','0001-01-01','0001-01-01',0,0,0,0,0,0,0,0,0,0,$cuota,$capital,$interes,$var_capital,0,$valor_interes,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'','',0,0,0,0,0,0,'',0),";				   		


			if($i==1 && $this->entrega ==0){
				if(date("d")>=26){
					$vencimiento = date("Y-m-d",strtotime(date("Y-m-05")."+ 31 days"));

				}elseif (date("d")<=10) {
					$vencimiento = date("Y-m-d",strtotime(date("Y-m-05")."+ 0 days"));

				} else{
					$vencimiento = date("Y-m-d",strtotime(date("Y-m-21")."+ 0 days"));
				}
			}


			if($cantidad_dias == 31){
				$vencimiento = date("Y-m-d",strtotime($vencimiento."+ 30 days"));
				$cantidad_dias = 30;

			}else{
				$vencimiento = date("Y-m-d",strtotime($vencimiento."+ 31 days"));
				$cantidad_dias = 31;
			}


			if(date("w",strtotime($vencimiento)) == 0){
				$vencimiento = date("Y-m-d",strtotime($vencimiento."- 1 days"));
				$cantidad_dias -= 1;
			}

			$fecha_actual  = date("Y-m-d",strtotime($fecha_actual."+ 31 days"));
		}


		$sql .= "(2,1,$operacion,0,$i,$this->cuenta,0,0,6900,0,'$fecha_actual',0,'0001-01-01',0,'',0,'',$cantidad_dias,0,'$vencimiento','T','0001-01-01','0001-01-01',0,0,0,0,0,0,0,0,0,0,$this->cabezon,$this->cabezon,0,$this->cabezon,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'','',0,0,0,0,0,0,'',0)";

		$db -> query($sql);
		return 0;
	}else{

		return 1;
	}		
}	

public function aprobar_operacion(){
	$db = $this->conn();

# Cambia a estado vigente la operacion
	$sql = "UPDATE fsd0122 SET bfesta=7 WHERE bfempr=2 AND bfesta=6 AND bfope1=$this->operacion AND aacuen=$this->cuenta;";
	$db -> query($sql);

# Cambia a estado pendiente  las cuotas de la operacion
	$sql = "UPDATE fsd0171 SET beesta='P' WHERE beempr=2 AND beesta='T' AND beope1=$this->operacion AND aacuen=$this->cuenta;";
	$db -> query($sql);

# Cambia a estado a las otras operaciones de la cuenta
	$sql = "UPDATE fsd0122 SET bfesta=$this->tipo WHERE bfempr=2 AND bfesta=7 AND bfope1!=$this->operacion AND aacuen=$this->cuenta;";
	$db -> query($sql);	
}

public function rechazar_operacion(){
	$db = $this->conn();

# Cambia a estado rechazado la operacion
	$sql = "UPDATE fsd0122 SET bfesta=13 WHERE bfempr=2 AND bfesta=6 AND bfope1=$this->operacion AND aacuen=$this->cuenta;";
	$db -> query($sql);
}




}	



?>	