<?php
class COBRANZAS extends Conexion{

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
	public $usuario;
	public $operaciones;

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
		LEFT JOIN tfinh44 d ON a.aacuen= d.thcuen and d.thempr=1
		WHERE a.aacuen=$this->cuenta
		LIMIT 1;";
		$result = $db->query($sql)->fetchAll();
		return $result;		
	}

	
	public function operaciones_consultar(){

		$result = array();
		$db = $this->conn();			

		$sql = "SELECT cuenta,datos.operacion,cuotas_cant,cuotas_pend,monto_cuota,saldo,monto,sum(capital) saldo_capital, sum(mora+punitorio+gasto+abogado) saldo_mora, sum(iva) saldo_iva, sum(total) total from (
		SELECT 
		aacuen cuenta, 
		bfope1 operacion, 
		bfcant cuotas_cant, 
		bfpend cuotas_pend,  
		round(bfvcta) monto_cuota,
		round(bfpres+bfinte) saldo,
		round(bftcuo)monto,
		bftasa tasa
		FROM fsd0122 
		WHERE bfesta=7 
		AND bfempr= 1
		AND aacuen NOT IN (select aacuen from fsd0122 where bfempr=1 and (bfoper between 100 and 199) and bfesta=7) 
		AND aacuen = $this->cuenta) as datos,get_mora(operacion,current_date,tasa)
		GROUP BY cuenta,datos.operacion,cuotas_cant,cuotas_pend,monto_cuota,saldo,monto;";

		$result = $db->query($sql)->fetchAll();
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
		AND bfempr= 1
		AND bfoper = 205 
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

	public function crear_operacion(){

		$db 	= $this->conn();			
		$result = array();

		$sql = "UPDATE public.fst021 SET azulti = azulti+1 WHERE azcont=5000 RETURNING azulti operacion;";

		$operacion 		= $db -> query($sql) -> fetchAll();
		$operacion  	= $this->operacion = $operacion[0]['operacion'];
		$cuenta 		= $this->cuenta;
		$cod_oper 		= 205;
		$solicitado		= $this->valor_capital;
		$saldo_capital	= $this->valor_capital;
		$saldo_interes  = $this->valor_total-$this->valor_capital;		  		
		$capital 		= $this->valor_capital + ($saldo_interes*0.1);
		$cant_dias 		= $this->cantidad_cuota*31;
		$cuota 			= $this->valor_cuota;

		$sql = "INSERT INTO public.fsd0122(
		bfempr, bfsucu, bfope1, bfope2, aacuen, bfmone, bfmod, bftrn, bfrubr, bfgara, bfoper, bfultd, bfrubo, bfsoli, bfente, bfterc, 
		bfcapi, bfcons, bfefch, bfplaz, bfliqu, bfobs, bffchv, bffchd, bf1vto, bftcuo, bfcobd, bfsusp, bftint, bfdeve, bfadev, bfcant, 
		bfdevm, bfpend, bfvcta, bfcod, bfvend, bfcobr, bfesta, bfproc, bftasa, bfpres, bfinte, bfgtos, bfley, bfcomi, bfsegu, bfperi, 
		bfmed, bfcan, bfsup, bfform, bfcheq, bfcta, bfpaga, bfvist, bfcali, bfslec, bfcod1, bfcod2, bfcod3, bfreg, bfmax, bfult, bftefe, 
		bfsitu, bfclas, bftip, bfpla, bfrubc, bffin, bftipper, bfopmig, bfagen, bfcat1, bfcat2, bfcat3, bffcho, bfbanc, bfasoc, bfcodi, 
		bfprevact, bfprevant, bfporl1, bfporl2, bfporl3, bftitu, bfporl4, bfporl5, bfbloc, bffchc, bfvendan,jjabog)

		VALUES (1,1,$this->operacion,0,$this->cuenta,6900,100,1,0,1,$cod_oper,'0001-01-01',0,$solicitado,0,0,$saldo_capital+($saldo_interes*0.1),0,1,
		$cant_dias, $solicitado,'',current_date,current_date,'0001-01-01',$this->valor_total,0,0,$saldo_interes,0,$saldo_interes,
		$this->cantidad_cuota,0,$this->cantidad_cuota,$cuota,0,119,0,6,' ',$this->valor_tasa,$saldo_capital,$saldo_interes,0,
		$saldo_interes*0.1,0,0,1,10,19,96,' ', 0 ,0,1,'Vista','','',0,0,0,0,0,0,0,1,1,'A',0,0,0,0,0,240,'','','','0001-01-01',0,0,0,0,0,'','',0,
		'','',0,'','0001-01-01',0,0)";

		if($db->query($sql)){

			$capital 		= round($saldo_capital/$this->cantidad_cuota,0);
			$interes   		= round($saldo_interes/$this->cantidad_cuota,0);
			$var_capital 	= $this->valor_total;

			$vencimiento = date("Y-m-d");
			if(date("w",strtotime($vencimiento) == 0)) {
				$vencimiento = date("Y-m-d",strtotime($vencimiento."+ 1 days"));
			}


			$sql = "INSERT INTO public.fsd014(
			baempr, bcope1, assucu, aacuen, catrn, aarubc, bgaran, fatipo, bd1vto, bbcons, bcsoli, aatipo, bdcamb, bdfchd, batasa, bbcapi, bbcant, bcplaz, bbcomi, bbseg, bbgtos, bcperi, bcley, bbobs1, bbvcta, betcuo, betint, bjesta, bzclav, bcvend, bccana, bcmed, bbcta, bagtos, aacue1, aacue2, aa1, aa2, fanumt, faprod, fabrev, bcigua, bcapro, bctasa, cftran, bbgeco, bccost, bcaux1, ctcos1, ctbcp1, bbcada, bcope2, cddocu, biasoc, bjproc, refcom1, refcom2, refcom3, refper1, refper2, refper3, reftel1, reftel2, reftel3, reftel4, reftel5, reftel6, ctbanca, aomone, cdoper, bjproc1, bctaso, bborden, bbtaspor, bbporl1, bbporl2, bcsupe, crbanca, bcsolo, bcsoltip, bbfecha, bbhora, bbhorapv, bbhorapa, bbfechapa, bbfechapv, bbcobr, bbhoracon, bbfechacon, bbhoracsa, bbfechacsa, bbhoraudp)
			VALUES (1, $this->operacion, 1, $this->cuenta, 1, 0, 1, 1, '$vencimiento', 0, $solicitado, 1, 0, current_date, $this->valor_tasa, $saldo_capital+($saldo_interes*0.1), $this->cantidad_cuota, $cant_dias, 0, 0, 0, 1, $saldo_interes*0.1, ' ', $this->valor_cuota, $this->valor_total, $saldo_interes, 6, 119, 1, 19, 1, 0, 0, 0, 0, ' ', ' ', 0, ' ', ' ', ' ', ' ', $this->valor_tasa, 0, 0, $this->valor_tasa, 0, 0, $this->valor_tasa, 1, 0, ' ', 0, 'S', '', '', '', '', '', '', '', '', '', '', '', '', 0, 6900, $cod_oper, ' ', 0, '', $this->valor_tasa, 0, '', 96, 240, $solicitado, 1, current_date, '', '', '', '0001-01-01', '0001-01-01', 0, '', '0001-01-01', '', '0001-01-01', '');";
			$db->query($sql);

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
			
			for ($i=1; $i<=$this->cantidad_cuota; $i++) { 

				$valor_capital = ($saldo_capital >= $capital) ? $capital : $saldo_capital;
				$valor_interes = ($saldo_interes >= $interes) ? $interes : $saldo_interes;
				$var_capital  -= $cuota;
				$saldo_interes -= $interes;
				$var_capital   = ($var_capital < 0) ? 0 : $var_capital;
				$saldo_interes = ($saldo_interes < 0) ? 0 : $saldo_interes;

				$coma = ($i==$this->cantidad_cuota) ? "":",";
				$sql .= "(1,1,$operacion,0,$i,$this->cuenta,0,0,6900,0,'$fecha_actual',0,'0001-01-01',0,'',0,'',$cantidad_dias,0,'$vencimiento','T','0001-01-01','0001-01-01',0,0,0,0,0,0,0,0,0,0,$cuota,$capital,$interes,$var_capital,0,$valor_interes,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,'','',0,0,0,0,0,0,'',0)".$coma;				   		

				$vencimiento 	= ($cantidad_dias==31) ? date("Y-m-d",strtotime($vencimiento."+ 30 days")):date("Y-m-d",strtotime($vencimiento."+ 31 days"));
				$cantidad_dias	= ($cantidad_dias==31) ? 30:31;

				if(date("w",strtotime($vencimiento)) == 0){
					$vencimiento = date("Y-m-d",strtotime($vencimiento."- 1 days"));
					$cantidad_dias -= 1;
				}

				$fecha_actual  = date("Y-m-d",strtotime($fecha_actual."+ 31 days"));
			}
			$db->query($sql);

			$operaciones = explode(',', $this->operaciones);
			foreach ($operaciones as $key) {
				
				$sql = "UPDATE fsd0122 SET bfesta=16 WHERE aacuen=$this->cuenta and bfope1=$key";
				$db->query($sql);
			}
			return 0;
		}else{
			return 1;
		}		
	}	
}	
?>	