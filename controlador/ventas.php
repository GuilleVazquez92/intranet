<?php 

function semaforo($fecha_hora, $valor){

}


class Ventas extends Conexion{

	public $operacion;
	public $usuario;
	public $comentario;
	public $desistir;
	public $estado;
	public $agendado;
	public $confirmar;
	public $canal;
	public $tipo;
	public $habilitado;
	public $trascurrido;
	public $falta;
	public $dia;
	// lineas agregadas por José/Lorena
	public $meta_prod;
	public $meta_salud;
	public $meta_moto;
	public $meta_total;
	public $estado_actual;

	public function dias_habiles(){

		$db 	= $this->conn();
		$sql = "SELECT habil habilitado, trasn trascurrido,habil-trasn falta, dia FROM public.fecha_empresa WHERE fecha=current_date;";
		$datos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
		$this->habilitado  	= $datos[0]['habilitado'];
		$this->trascurrido 	= $datos[0]['trascurrido'];
		$this->falta 		= $datos[0]['falta'];
		$this->dia 			= $datos[0]['dia'];	
	}



	public function resumen_monitor(){

		$result = array();
		$db 	= $this->conn();

		$this->dias_habiles();

		//print $this->canal;
		$filtro  = "";
		$filtro2 = "";
		$filtro3 = "";
		$filtro4 = "";
		
		if($this->canal!=9999){

			$filtro = " and bfcan=$this->canal";
			$filtro2 = " and canal=$this->canal";
			$filtro3 = " and equicana=$this->canal";
			$filtro4 = " and bccana=$this->canal";
		}

		$sql = "SELECT sum(neto) neto 
				FROM operaciones_mes 
				WHERE fecha=current_Date and operacion not in (select bfope1 from fsd0122 where bfempr=1 and bfoper<200 and bfoper!=205)	
				and codigo not between 80000000 and 80000500 and cuenta!=1924297 and estado!='ANULADO' $filtro2";
		$datos = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
		$result['venta_dia'] = $datos['neto'];

		$sql = "SELECT sum(neto) neto 
				FROM operaciones_mes 
				WHERE fecha=current_Date 	
				and codigo not between 80000000 and 80000500 and forma='CONTADO' and cuenta!=1924297 and estado!='ANULADO' $filtro2";
		$datos = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
		$result['contado_dia'] = $datos['neto'];		

		$sql = "SELECT sum(neto) neto 
				FROM operaciones_mes 
				WHERE fecha>=date_trunc('month',current_Date) 	
				and codigo not between 80000000 and 80000500 and forma='CONTADO' and cuenta!=1924297 and estado!='ANULADO' $filtro2";
		$datos = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
		$result['contado_mes'] = $datos['neto'];

/****/

		$sql = "SELECT sum(neto) neto 
				FROM operaciones_mes 
				WHERE fecha=current_Date 	
				and codigo in (select epcodi from tef005 where effami=52) 
				and cuenta!=1924297 and estado!='ANULADO' $filtro2";
		$datos = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
		$result['salud_dia'] = $datos['neto'];		

		$sql = "SELECT sum(neto) neto 
				FROM operaciones_mes 
				WHERE fecha>=date_trunc('month',current_Date) 	
				and codigo in (select epcodi from tef005 where effami=52)
				and cuenta!=1924297 and estado!='ANULADO' $filtro2";
		$datos = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
		$result['salud_mes'] = $datos['neto'];

/****/

		$sql = "SELECT sum(neto) neto 
		 		FROM operaciones_mes 
		 		WHERE fecha>=date_trunc('month',current_date) 
		 		and fecha!=current_Date and operacion not in (select bfope1 from fsd0122 where bfempr=1 and bfoper<200 and bfoper!=205)
		 		and codigo not between 80000000 and 80000500 and cuenta!=1924297 and estado!='ANULADO' $filtro2";
		$datos = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
		$result['venta_ayer'] = $datos['neto'];


		$sql = "SELECT sum(obmeta) meta FROM objetivo, fst062, fst076 where obbzclav=bzclav and fst062.equicodi=fst076.equicodi and date_trunc('month',current_Date)=obfechai $filtro3";
		$datos = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
		$result['meta'] = $datos['meta'];

		$sql =	"SELECT count(*) carpetas_hoy FROM tef012, fsd0122 WHERE tcoperel=bfope1 and bccana=bfcan and tccarfec=current_date	and tcoperel>0 and bfesta>=3 and bfempr=1 $filtro";
		$datos = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
		$result['carpetas_hoy'] = $datos['carpetas_hoy'];

		$sql = "SELECT round(sum(aprobado)/sum(total)*100,2) por_aprobados FROM (
					SELECT    case 
							when estado='APROBADO' then sum(total) 
							else 0	
							end aprobado,sum(total) total FROM (
						SELECT 
							case when bfesta=13 then  'RECHAZADO'
								else 'APROBADO'
							end	estado, 
							count(*) total 
						FROM public.fsd0122 fsd0122,tef012 
						WHERE tcoperel=bfope1
						AND (fsd0122.bfesta=5 or fsd0122.bfesta=6 or fsd0122.bfesta=7 or fsd0122.bfesta=50 OR fsd0122.bfesta=13)
						AND tccarfec>=date_trunc('month', current_Date)
						$filtro
						group by bfesta) as datos
					GROUP BY estado
				) as final";
		$datos = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
		$result['por_aprobados'] = $datos['por_aprobados'];

		$sql = "SELECT 0 estado,'CONTADO PENDIENTE' estado_descripcion, count(*) estado_cantidad, round(sum(tccarmon/1.1)) estado_neto,0 orden FROM tef012 WHERE tccarest=5 AND tcbandimp='S' AND tccarcre=0 AND tcoperel=0 AND tcfactfec >= ('now'::text::date - 180) $filtro4
				UNION
				SELECT bjesta estado,bjdesc estado_descripcion, estado_cantidad, estado_neto,bjver orden  
				FROM fst044 
				LEFT JOIN(SELECT bfesta,count(*) estado_cantidad, round(sum(bftcuo/1.1)) estado_neto FROM fsd0122 WHERE bfempr=1 $filtro GROUP BY 1) as carpetas on bjesta=bfesta
				WHERE bjver between 1 and  7

				union 
				SELECT bjesta estado,bjdesc estado_descripcion, estado_cantidad, estado_neto,bjver orden 
				FROM fst044 
				LEFT JOIN(SELECT bfesta,count(*) estado_cantidad, round(sum(bftcuo/1.1)) estado_neto FROM fsd0122 WHERE bfempr=1 and bffchd>=date_trunc('month',current_date) $filtro GROUP BY 1) as carpetas on bjesta=bfesta
				WHERE bjesta=21 or bjesta=13 or bjesta=15
				ORDER BY orden";
		$result['estados'] = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);


		$sql = "SELECT datos.fecha dia_mes, round(coalesce(neto,0)) dia_neto FROM (
					SELECT date_trunc('day', dd)::date fecha FROM generate_series(date_trunc('month',current_date)::timestamp, current_date::timestamp, '1 day'::interval) dd
				) as datos
				LEFT JOIN(
					SELECT fecha, sum(neto) neto FROM operaciones_mes WHERE fecha>=date_trunc('month',current_Date) and operacion not in (select bfope1 from fsd0122 where bfempr=1 and bfoper<200 and bfoper!=205)	
								and codigo not between 80000000 and 80000500 and cuenta!=1924297 and estado!='ANULADO' $filtro2 GROUP BY 1
					) AS ventas ON ventas.fecha=datos.fecha
				WHERE date_part('dow',datos.fecha)!=0";

		$result['grafico'] = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);		

			

		## CALCULOS ###
		$result['venta_mes'] 		= $result['venta_dia']+$result['venta_ayer'];
		$result['meta_dia'] 		= ($result['meta']-$result['venta_ayer'])/($this->falta+1);
		$result['proximo_objetivo'] = ($this->falta>=1) ? ($result['meta']-$result['venta_mes'])/$this->falta : $result['meta']-$result['venta_mes']; 

		$result['venta_proyectada']	= $result['venta_mes']/$this->trascurrido*$this->habilitado;
		$result['proyeccion_hoy'] 	= round($result['venta_dia']/$result['meta_dia']*100,2);
		$result['proyeccion_mes'] 	= round($result['venta_proyectada']/$result['meta']*100,2);

		$result['contado_dia_participacion'] = ($result['venta_dia'] == 0 ) ? 0 : round($result['contado_dia']/$result['venta_dia']*100,2);
		$result['contado_mes_participacion'] = round($result['contado_mes']/$result['venta_mes']*100,2);

		$result['salud_dia_participacion'] = ($result['venta_dia'] == 0 ) ? 0 : round($result['salud_dia']/$result['venta_dia']*100,2);
		$result['salud_mes_participacion'] = round($result['salud_mes']/$result['venta_mes']*100,2);

		return $result;

	}

	public function resumen_pl(){

		$result = array();
		$db 	= $this->conn();
		$sql="SELECT motivo,coalesce(cantidad,0) cantidad,coalesce(neto,0) neto 
		FROM operaciones.motivo_pl
		LEFT JOIN (SELECT 
		cod_motivo,
		count(*) cantidad,
		sum(neto) neto
		FROM operaciones.operaciones 
		WHERE (operacion in (select bfope1 from fsd0122 where bfesta=5 or bfesta=50)
		or carro in (select tccarcod from tef012 where tccarest=5 and tcbandimp!='S' and tctipent>0)
		)

		{$this->filtro_canal()}


		group by 1) AS operaciones ON id=cod_motivo
		where orden>0
		ORDER BY orden;";

		$result['motivos'] = $db->query($sql)->fetchAll();



			/*
			$db 	= $this->conn();
			$sql 	= "SELECT count(*) FROM fsd0122 WHERE bfempr=1 AND (bfesta=50 or bfesta=5)";
			$result = $db->query($sql)->fetchAll();
			*/
		//	$resumen['motivos'][1]['cantidad'];

			$result['CONTADO']['cantidad'] = 0;
			$result['CREDITO']['cantidad'] = 0;
			$result['CONTADO']['neto'] = 0;
			$result['CREDITO']['neto'] = 0;
			return $result;



		}

		public function filtro_canal(){

			if($_COOKIE['cod_canal']!=9999){
				
				$filtro = "AND cod_canal=".$_COOKIE['cod_canal'];

				if($_COOKIE['cod_canal']==14){

					$filtro = "AND (cod_canal=14 or cod_canal=15)";
				}

				if($_COOKIE['cod_canal']==18){

					$filtro = "AND (cod_canal=18 or cod_canal=16)";
				}

				if($_COOKIE['cod_canal']==22){

					$filtro = "AND (cod_canal=22 or cod_canal=13)";
				}

			}else{
				$filtro = "";

			}

			return $filtro;

		}

		public function consulta_pl(){

			$result = array();
			$db 	= $this->conn();
			$sql 	= "SELECT carro, operacion, cuenta, cliente, forma, cod_vend, vendedor, 
			cod_canal,trim(bccnom) canal,cod_motivo, motivo, confirmado, creacion, aprobacion, agendamiento agendamiento_vencimiento,agendamiento, 
			entregado, neto, solicitado, etiqueta, beneficio, origen, bfesta estado
			FROM operaciones.operaciones,operaciones.motivo_pl, fsd0122, fst025 
			where operacion=bfope1
			and id=cod_motivo
			and cod_canal=bccana
			and (bfesta=5 or bfesta=50)
			{$this->filtro_canal()}
			UNION
			
			SELECT carro, carro operacion, cuenta, cliente, forma, cod_vend, vendedor, 
			cod_canal,trim(bccnom) canal,cod_motivo, motivo, confirmado, creacion, aprobacion, agendamiento agendamiento_vencimiento,agendamiento, 
			entregado, neto, solicitado, etiqueta, beneficio, origen, 50 estado
			FROM 	operaciones.operaciones,
				operaciones.motivo_pl, 
				tef012, 
				fst025 
			where carro=tccarcod
			and id=cod_motivo
			and cod_canal=fst025.bccana
			and forma='CONTADO'
			and tccarest=5
			and tcbandimp!='S'
			and tctipent>0
			{$this->filtro_canal()}

			ORDER BY  aprobacion;";
			$result['data'] = $db->query($sql)->fetchAll();


			
			$result['agendado']['atrasado'] 	= 0;
			$result['agendado']['en_espera'] 	= 0;
			$result['agendado']['a_tiempo'] 	= 0;
			return $result;
		}

		public function lista_canales(){
			$result = array();
			$db 	= $this->conn();
			$sql 	= "SELECT bccana cod_canal, trim(bccnom) canal FROM fst025 WHERE bctipo='2';";
			$result = $db->query($sql)->fetchAll();
			return $result;
		}


		public function listar_motivo(){
			$result = array();
			$db 	= $this->conn();
			$sql 	= "SELECT id, motivo, zona FROM operaciones.motivo_pl ORDER BY orden;";
			$result = $db->query($sql)->fetchAll();
			return $result;
		}

		public function detalle_productos(){
			$result = array();
			$db 	= $this->conn();
			$sql 	= "SELECT epdesc producto_corto FROM tef012,tef0121, tef005 WHERE tef012.tccarcod=tef0121.tccarcod and tccarite=epcodi and (tcoperel=$this->operacion or tef012.tccarcod=$this->operacion);";
			$result = $db->query($sql)->fetchAll();
			return $result;	
		}

		public function operacion_consultar(){
			$result = array();
			$db 	= $this->conn();
			$sql 	= "SELECT 
			cliente, 
			carro, 
			operacion, 
			cuenta,
			forma, 
			cod_vend, 
			vendedor, 
			cod_canal, 
			trim(bccnom) canal,
			cod_motivo, 
			confirmado, creacion, aprobacion, 
			agendamiento agendado,
			entregado, 
			neto, 
			solicitado, 
			etiqueta, 
			beneficio, 
			origen,
			bfvcta cuota_monto,
			bftcuo bruto,
			entrega,
			bfcant cuota_cant, 
			bfoper tipo,
			awtel1 telefono,
			awcelu celular,
			vencimiento

			FROM operaciones.operaciones, fst025,fsd022,fsd0122
			LEFT JOIN (select beope1, min(be1vto) vencimiento from fsd0171 where beempr=1 and beope1=$this->operacion group by 1) as vencimiento on beope1=bfope1
			LEFT JOIN (select tcoperel, tccarcon entrega from tef012 where tcoperel=$this->operacion) as entrega on tcoperel=bfope1 
			WHERE cod_canal=bccana
			AND operacion=bfope1
			AND fsd0122.aacuen=fsd022.aacuen 	
			AND operacion=$this->operacion
			union 
			SELECT 
			cliente, 
			carro, 
			operacion, 
			cuenta,
			forma, 
			cod_vend, 
			vendedor, 
			cod_canal, 
			trim(bccnom) canal,
			cod_motivo, 
			confirmado, creacion, aprobacion, 
			agendamiento agendado,
			entregado, 
			neto, 
			solicitado, 
			etiqueta, 
			beneficio, 
			origen,
			bfvcta cuota_monto,
			bftcuo bruto,
			entrega,
			bfcant cuota_cant, 
			bfoper tipo,
			awtel1 telefono,
			awcelu celular,
			vencimiento

			FROM operaciones.operaciones, fst025,fsd022,fsd0122
			LEFT JOIN (select beope1, min(be1vto) vencimiento from fsd0171 where beempr=1 and beope1=$this->operacion group by 1) as vencimiento on beope1=bfope1
			LEFT JOIN (select tcoperel, tccarcon entrega from tef012 where tcoperel=$this->operacion) as entrega on tcoperel=bfope1 
			WHERE cod_canal=bccana
			AND operacion=bfope1
			AND fsd0122.aacuen=fsd022.aacuen 	
			AND carro=$this->operacion
			;";
			$result['cabecera'] = $db->query($sql)->fetchAll();

			$sql = "SELECT epcodi||' '||epdescl producto,tccarcan cantidad,round(tcpordes,2) descuento 
			FROM tef012,tef0121, tef005 
			WHERE tef012.tccarcod=tef0121.tccarcod and tccarite=epcodi 
			and (tcoperel=$this->operacion or tef012.tccarcod=$this->operacion);";
			$result['detalle'] = $db->query($sql)->fetchAll();

			return $result;	
		}


		public function operacion_historial(){

			$result = array();
			$db 	= $this->conn();
			$sql = "SELECT id, operacion, fecha||' '||hora fecha, comentario, usuario FROM public.operaciones_comentarios WHERE operacion=$this->operacion order by id desc";
			$result = $db->query($sql)->fetchAll();
			return $result;	
		}

	
		public function estado_historial(){

			$result = array();
			$db 	= $this->conn();
			$sql = "SELECT a.fecha fecha, a.hora hora, a.usuario usuario, a.operacion operacion, b.motivo motivo
			FROM operaciones.estado_historial a, operaciones.motivo_pl b
			Where b.id = a.estado_nuevo;";
			$result = $db->query($sql)->fetchAll();
			return $result;	
		}
	


		public function listar_motivo_desistir(){

			$result = array();
			$db 	= $this->conn();
			$sql = "SELECT id, motivo, responsable, tipo, zona, estado FROM operaciones.motivo_desiste;";
			$result = $db->query($sql)->fetchAll();
			return $result;	

		}

		public function agregar_comentario(){
			$db = $this->conn();
			$sql = "INSERT INTO public.operaciones_comentarios(operacion, fecha, hora, comentario, usuario)
			VALUES ($this->operacion, current_date, split_part(current_time::text,'.',1), '$this->comentario', '$this->usuario');";

			if($db->query($sql)->fetchAll()){
				$result['texto'] = "Se agrego el comentario corresctamente.";
				$result['estado'] = 0;		
			}else{
				$result['texto'] = "Error al intentar agragar el comentario.";
				$result['estado'] = 1;
			}
			return $result;
		}
		
		public function desistir_operacion(){

			$db = $this->conn();
			
			$sql = "INSERT INTO public.operaciones_comentarios(operacion, fecha, hora, comentario, usuario)
			SELECT '$this->operacion', current_date, split_part(current_time::text,'.',1), '$this->usuario'||': '||motivo||'Zona: '||responsable,'$this->usuario' 
			FROM operaciones.motivo_desiste WHERE id=$this->desistir RETURNING comentario;";
			$resultado 	= $db->query($sql)->fetchAll();
			$x = $resultado[0][0];

			$sql = "UPDATE fsd014 SET bjesta=21, aa1=substring('$x' from 1 for 60) WHERE bcope1=$this->operacion";		
			$db->query($sql);

			$sql = "UPDATE tef012 SET tccarest=7 WHERE tcoperel=$this->operacion or tccarcod=$this->operacion";		
			$db->query($sql);


			$sql = "UPDATE fsd0122 SET bfesta=21 WHERE bfope1=$this->operacion";		
			$db->query($sql);
			
			if($db->query($sql)){
				$result['texto'] = "Se agrego el comentario corresctamente.";
				$result['estado'] = 0;		
			}else{
				$result['texto'] = "Error al intentar agragar el comentario.";
				$result['estado'] = 1;
			}
			
			return $result;
		}

		public function cambiar_estado(){
		
		$db = $this->conn();

		$sql = "SELECT cod_motivo motivo
			FROM operaciones.operaciones
			where operacion =$this->operacion;";
			
		$estado_actual=$db->query($sql);
		foreach ($estado_actual as $key) {
			$motivo = $key['motivo'];
		}

		$sql = "INSERT INTO operaciones.estado_historial(estado_actual, estado_nuevo, fecha, hora, usuario, operacion)
		VALUES ($motivo,$this->estado,current_date,split_part(current_time::text,'.',1),'$this->usuario',$this->operacion);";

			 $db->query($sql);

			$sql = "UPDATE operaciones.operaciones SET cod_motivo=$this->estado, etiqueta='$this->agendado'	WHERE carro=$this->operacion or operacion=$this->operacion RETURNING 0;";

			if($db->query($sql)->fetchAll()){
				$result['texto'] = "Se agrego el comentario correctamente.";
				$result['estado'] = 0;		
			}else{
				$result['texto'] = "Error al intentar agragar el comentario.";
				$result['estado'] = 1;
			}
			
			return $result;
		}

		public function confirmar_venta(){

			$db = $this->conn();
			$sql = "UPDATE operaciones.operaciones SET beneficio=$this->contencion WHERE operacion=$this->operacion or carro=$this->operacion;";
			$db->query($sql);

			$sql = "UPDATE operaciones.operaciones SET confirmado=$this->confirmar WHERE operacion=$this->operacion or carro=$this->operacion RETURNING 0;";

			if($db->query($sql)->fetchAll()){
				$result['texto'] = "Se agrego el comentario corresctamente.";
				$result['estado'] = 0;		
			}else{
				$result['texto'] = "Error al intentar agragar el comentario.";
				$result['estado'] = 1;
			}

			return $result;
		}


		public function carpetas_detalles(){

			$filtro = "";
			if($this->canal!='9999'){
				$filtro = " and canal=$this->canal";
			}

			$result = array();
			$db = $this->conn();
			$sql 	= "SELECT fecha,operacion,cuenta,trim(aanom) cliente,vendedor codvend,bzcort vendedor,canal,forma,tipo,neto,origen, 
						(SELECT tfing11.vgusu FROM tfing11 WHERE tfing11.vgope1=operacion) verificador,	(SELECT (fsd014.bbfecha||' '||fsd014.bbhora)::text FROM fsd014 WHERE bcope1=operacion) entrada
			FROM fsd0011, fst062, web_operaciones_estado
			LEFT JOIN (select tcoperel, tccarps from tef012 where tcoperel>0) AS origen ON tcoperel=operacion
			WHERE cuenta=aacuen
			AND vendedor=bzclav
			AND estado=$this->tipo
			$filtro";
			
			$result = $db->query($sql)->fetchAll();
			return $result;

		}

		public function lista_vendedores(){

			$filtro = "";
			if($_COOKIE['cod_canal']!=9999){

				$filtro = " and fst076.equicana={$_COOKIE['cod_canal']}";

				if($_COOKIE['cod_canal']==14){
					$filtro = " and (equicana=14 or equicana=15)";
				} 

				if($_COOKIE['cod_canal']==18){
					$filtro = " and (equicana=18 or equicana=16)";
				} 

				if($_COOKIE['cod_canal']==22){
					$filtro = " and (equicana=22 or equicana=13)";
				} 

			}


			$result = array();
			$this->dias_habiles();
			$db = $this->conn();
			$sql 	= "SELECT *,
						neto/$this->trascurrido*$this->habilitado venta_proyectada,
						(meta-(neto-neto_dia)/($this->falta+1)) meta_dia,
						case when neto=0 or meta=0 then 0
							else round( (neto/$this->trascurrido*$this->habilitado)/meta*100,2) 
						end proyeccion
						FROM (
						SELECT 
						bzclav cod_vend, 
						trim(bznomb) vendedor, 
						bzcort corto, 
						bznive tramo, 
						bccana cod_canal,
						trim(bccnom) canal, 
						ceqdesc grupo,
						(SELECT count(*) carpeta_dia FROM tef012, fsd0122 WHERE tef012.bzclav=fst062.bzclav and bfvend=tef012.bzclav and tcoperel=bfope1 and bccana=bfcan and tccarfec=current_date and tcoperel>0 and bfesta>=3 and bfempr=1),

						(SELECT round(sum(aprobado)/sum(total)*100,2) aprobacion FROM (
							SELECT    case 
									when estado='APROBADO' then sum(total) 
									else 0	
									end aprobado,sum(total) total FROM (
								SELECT 
									case when bfesta=13 then  'RECHAZADO'
										else 'APROBADO'
									end	estado, 
									count(*) total 
								FROM public.fsd0122 fsd0122,tef012 
								WHERE tcoperel=bfope1
								and tef012.bzclav=fst062.bzclav
								AND (fsd0122.bfesta=5 or fsd0122.bfesta=6 or fsd0122.bfesta=7 or fsd0122.bfesta=50 OR fsd0122.bfesta=13)
								AND tccarfec>=date_trunc('month', current_Date)
								
								group by bfesta) as datos
							GROUP BY estado
						) as final),


						COALESCE(round((select obmeta from objetivo where obbzclav=bzclav and obfechai=date_trunc('month', current_date))),0) meta, 
						COALESCE(round((select sum(neto) FROM operaciones_mes where vendedor=bzclav and fecha=current_date and estado!='ANULADO')),0) neto_dia,
						COALESCE(round((select sum(neto) FROM operaciones_mes where vendedor=bzclav and fecha>=date_trunc('month',current_date) and estado!='ANULADO')),0) neto

			FROM fst062,fst076,fst025, com002 
			WHERE fst062.equicodi=fst076.equicodi 
				AND fst076.equicana=fst025.bccana 
				and cequipo=equigrup
				AND fst062.bzvact='S' 
				AND bctipo='2' 
				AND (fst062.bzfchba>= date_trunc('month', current_date) or fst062.bzfchba is null)
				$filtro	
				ORDER BY bccana,8 desc,2
		) AS DATOS
		;";
			
			$result = $db->query($sql)->fetchAll();
			return $result;
		}


		public function resumen_canales(){

			$result = array();
			$this->dias_habiles();

			$db = $this->conn();
			$sql = "SELECT codcanal,canal,objetivo_mes,venta_mes,venta_proyectada,proyeccion_mes,
			CASE
				WHEN proyeccion_mes>=90 THEN 'VERDE'
				ELSE 'ROJO'
			END color,
			objetivo_hoy,venta_hoy,proyeccion_hoy,
			habil ,trascurrido,falta,dia,
			(SELECT count(bfesta) FROM fsd0122 WHERE bfcan=codcanal 
				and (fsd0122.bfesta=5 or fsd0122.bfesta=6 or fsd0122.bfesta=7 or fsd0122.bfesta=50) 
				AND bffchd>=date_trunc('month', current_date) AND bfempr=1)::numeric aprobadas,
			(SELECT count(bfesta) FROM fsd0122 WHERE bfcan=codcanal and (fsd0122.bfesta=5 
			or fsd0122.bfesta=6 or fsd0122.bfesta=7 or fsd0122.bfesta=13 or fsd0122.bfesta=50) AND bffchd>=date_trunc('month', current_date) AND bfempr=1)::numeric total,coalesce(carpeta_dia,0) carpeta_dia
	
			from (
				select codcanal,
					canal,
					objetivo_mes,
					venta_ayer+venta_hoy venta_mes,
					round((venta_ayer+venta_hoy)/trascurrido*habil,0)venta_proyectada,
					CASE
						WHEN venta_ayer+venta_hoy=0 or objetivo_mes=0 THEN 0.00
						ELSE round(((venta_ayer+venta_hoy)/trascurrido*habil)/objetivo_mes*100,2)  
					END proyeccion_mes,
					CASE 
						WHEN (objetivo_mes = 0 AND venta_ayer = 0) or codcanal=24 THEN 0
						ELSE round((objetivo_mes-venta_ayer)/(falta+1),0)
					END objetivo_hoy,
					venta_hoy,
					CASE 
						WHEN objetivo_mes = 0 THEN 0.00
						ELSE round(venta_hoy/(objetivo_mes-venta_ayer)/(falta+1)*100,2)
					END proyeccion_hoy,habil ,trascurrido,falta,dia
					from (
						select bccana codcanal,trim(bccnom) canal,
							COALESCE((select sum(obmeta) from objetivo,fst062,fst076 
							where obfechai=date_trunc('month',current_date) and obbzclav=bzclav and fst062.equicodi=fst076.equicodi and equicana=fst025.bccana),0) objetivo_mes,
							
							COALESCE((select sum(neto) from operaciones_mes where canal=fst025.bccana and estado!='ANULADO' 
							and cuenta!=1924297 
							and operacion not in (select bfope1 from fsd0122 where bfempr=1 and bfoper<200)
							and codigo not between 80000000 and 80000500
							and fecha between date_trunc('month',current_date) and current_date-1),0) venta_ayer,
							COALESCE((select sum(neto) from operaciones_mes where canal=fst025.bccana and estado!='ANULADO' 
							and cuenta!=1924297 
							and operacion not in (select bfope1 from fsd0122 where bfempr=1 and bfoper<200)
							and codigo not between 80000000 and 80000500
							and fecha = current_date),0) venta_hoy,
							
							habil ,trasn trascurrido,habil-trasn falta,dia
						from fst025,fecha_empresa
						where bctipo='2'
						and bccana!=31
						and fecha_empresa.empresa='1'
						and fecha_empresa.fecha=current_date
				) as datos
			) as final
			LEFT JOIN (SELECT bfcan cod_canal, count(*) carpeta_dia FROM tef012, fsd0122 WHERE tcoperel=bfope1 and bccana=bfcan and tccarfec=current_date and tcoperel>0 and bfempr=1 group by 1) as carpetas on carpetas.cod_canal=final.codcanal
		order by 6 desc;";
			$result = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
			return $result;


		}

		public function consultar_vendedor_meta(){

			$result = array();

			$db = $this->conn();
			$sql = "SELECT bzclav, bznomb vendedor,bznive tramo, equinomb equipo,piso
			FROM fst062, fst076,com002,com006 
			WHERE fst062.equicodi=fst076.equicodi
			AND tramo=bznive
			AND cequipo=equigrup
			AND tipo=ceqdesc	
			AND bzclav=$this->cod_vend";
			$result = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
			return $result;
		}


		public function cargar_meta(){

			$db = $this->conn();

			$sql = "INSERT INTO public.objetivo(obcod, obbzclav, obequicodi, obmeta, obmes, obfechai, obfechaf,obdias, obdiastra, obcana)
					select 
					(select max(obcod)+1 from objetivo) obcod,
					bzclav obbzclav,
					fst062.equicodi obequicodi,
					$this->meta_total obmeta,
					date_part('month', current_date)::integer obmes,
					date_trunc('month',current_date)::date obfechai,
					date_trunc('month',current_date+31)::date-1 obfechaf,
					0 obdias,
					0 obdiastra,
					equicana obcana

					from fst062, fst076 
					where fst062.equicodi=fst076.equicodi
					and bzclav=$this->cod_vend
					RETURNING obcod";
			
			$result = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
			$obcod  = $result['obcod'];

			$sql = "INSERT INTO public.objdetal(decod, obcod, debzclav, demes, detipo, demonto, deano)
    				VALUES (1,$obcod,$this->cod_vend, date_part('month',current_Date)::smallint, 5, $this->meta_prod,date_part('year',current_Date)::smallint);";
       		$db->query($sql);

      		$sql = "INSERT INTO public.objdetal(decod, obcod, debzclav, demes, detipo, demonto, deano)
    				VALUES (2,$obcod,$this->cod_vend, date_part('month',current_Date)::smallint, 2, $this->meta_salud,date_part('year',current_Date)::smallint);";
    		$db->query($sql);

    		$sql = "INSERT INTO public.objdetal(decod, obcod, debzclav, demes, detipo, demonto, deano)
    				VALUES (3,$obcod,$this->cod_vend, date_part('month',current_Date)::smallint, 9, $this->meta_moto,date_part('year',current_Date)::smallint);";
    		$db->query($sql);		

		}

			public function resumen_400(){

			$result = array();

			$db = $this->conn();
			$sql = "SELECT sum(bevcta) neto
			from fsd0122 cab, fsd0171 det
			where
   			cab.aacuen = det.aacuen and bfoper=400 and bfesta=7 
   			and cab.bfope1 = det.beope1 and det.becta=1 
   			and bf1vto =current_date;";
			$datos = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
			$result['dia'] = $datos['neto'];


			$db = $this->conn();
			$sql = "SELECT
   					sum(bevcta) neto 
   					from fsd0122 cab, fsd0171 det
					where
					   cab.aacuen = det.aacuen and bfoper=400 and bfesta=7  
					   and cab.bfope1 = det.beope1 and det.becta=1         
					   and (bf1vto >='2021-08-01' and bf1vto <=current_date);";
			$datos = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
			$result['mes'] = $datos['neto'];

			
			$db = $this->conn();
			$sql = "SELECT 
			sum(bevcta) neto
			from 
			     fsd0122 cab, fsd0171 det
			where
			   cab.aacuen = det.aacuen and bfoper=401 and bfesta=7 
			   and cab.bfope1 = det.beope1 and det.becta=0
			   and bf1vto = current_date 
			;";
			$datos = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
			$result['dia_401'] = $datos['neto'];

			$db = $this->conn();
			$sql = "	SELECT 
     		sum(bevcta) neto
			from 
     		fsd0122 cab, fsd0171 det
			where
  			cab.aacuen = det.aacuen and bfoper=401 and bfesta=7 
   			and cab.bfope1 = det.beope1 and det.becta=0
   			and (bf1vto >='2021-08-01' and bf1vto <=current_date);";
			$datos = $db->query($sql)->fetch(PDO::FETCH_ASSOC);
			$result['mes_401'] = $datos['neto'];


			return $result;
		}
			
			

			public function consultar_operacion_motivo(){

			
			//$result = array();	
			$db = $this->conn();
			$sql = " SELECT fst074.bwobse observacion, fst064.bwdesc descripcion 
				FROM public.fst074 , public.fst064 
				where fst074.bboper =$this->operacion
				and fst074.bwrech = fst064.bwrech;";
			$result = $db->query($sql)->fetchAll();
			return $result;
		}



	}

	?>	