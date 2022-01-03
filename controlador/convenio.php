<?php

	if(!isset($_COOKIE['id'])){

		setcookie("id",0,time()+86400);		
		header('Location: ' . ROOT ."contenido/convenios/index.php");

		/*
				$sql = "SELECT id, descripcion, fecha_inicio, deposito, usuario FROM convenios.convenios WHERE usuario='$this->usuario';";
								$db = $this->conn();
								foreach ( $db -> query($sql) as $row ) {

									setcookie("id",$row['id'],time()+86400);
									setcookie("usuario_nombre",$row['descripcion'],time()+86400);	
									setcookie("deposito",$row['deposito'],time()+86400);
									setcookie("fecha_inicio",$row['fecha_inicio'],time()+86400);
								}
		*/
	}

	class Convenios extends Conexion{
		
		public $cuenta;
		public $documento;
		public $motivo;
		public $fecha_proximo;
		public $comentario;
		public $filtro;
		public $orden;
		public $pedido;
		public $codigo;
		public $cuota;
		public $cantidad;
		public $precio;	
		public $codigo_1;
		public $fecha_inicial;	
		public $fecha_final;
		public $id;
		public $fecha;
		public $operacion;
		public $movimiento;
		public $usuario;
		public $estado;
		public $lote;
		public $filtro_estado;
		public $nc;
		public $factura;
		public $proveedor;

		public function consultar_convenio($x){
			$i = 0;
			$result = array();
			$db = $this->conn();

			if($x==9999){
				$sql = "SELECT id, descripcion, deposito,fecha_inicio FROM convenios.convenios;";
			}else{
				$sql = "SELECT id, descripcion, deposito,fecha_inicio FROM convenios.convenios WHERE id=$this->id;";				
			}


			foreach ($db -> query($sql) as $row ) {
	
				$result[$i]['id'] 			= $row['id'];
				$result[$i]['alianza'] 	= $row['descripcion'];
				$result[$i]['deposito'] 	= $row['deposito'];
				$result[$i]['fecha_inicio'] = $row['fecha_inicio'];
				$i++;
			}
			return $result;
		}


		public function orden_consultar(){
		
			$i 	= 0;
			$result = array();
			$db = $this->conn();			

			$sql = "SELECT convenios.operaciones.operacion, 
						trim(aadocu) documento,
						trim(aanom) cliente, codigo, relacion cantidad,bfcant cant_cuotas,bfpend cuota_pend,bfult atraso, fecha_1ervto,convenios.operaciones.lote, ubicacion 
						FROM convenios.operaciones,convenios.operacion_detalle, fsd0122, fsd0011 
					WHERE convenios.operaciones.operacion=convenios.operacion_detalle.operacion 
					AND convenios.operaciones.operacion=bfope1 
					AND fsd0011.aacuen=fsd0122.aacuen  
					AND orden_relacion=$this->orden 
					AND (codigo=$this->codigo or codigo=$this->codigo+20000000)
					UNION
					SELECT control::numeric,'','NOTA DE CREDITO', nota_credito.codigo, nota_credito.cantidad,1,1,0,fecha_nc,0,'NOTA CREDITO'
					FROM convenios.nota_credito, convenios.operacion_detalle
					WHERE control::numeric=operacion
					and orden_relacion=$this->orden
					AND (nota_credito.codigo=$this->codigo or nota_credito.codigo=$this->codigo+20000000);";

			foreach ($db -> query($sql) as $row ) {
	
				$result[$i]['operacion'] 	= $row['operacion'];
				$result[$i]['documento'] 	= $row['documento'];
				$result[$i]['cliente'] 		= $row['cliente'];
				$result[$i]['codigo'] 		= $row['codigo'];
				$result[$i]['cantidad'] 	= $row['cantidad'];
				$result[$i]['cant_cuotas'] 	= $row['cant_cuotas'];
				$result[$i]['cuota_pend'] 	= $row['cuota_pend'];
				$result[$i]['atraso'] 		= $row['atraso'];
				$result[$i]['vencimiento'] 	= $row['fecha_1ervto'];
				$result[$i]['lote'] 		= $row['lote'];
				$result[$i]['ubicacion'] 	= $row['ubicacion'];	
				$i++;
			}
			return $result;
		}

		public function orden(){

			$result = array();
			$db = $this->conn();			

			$sql = "SELECT orden,trim(provdesc) proveedor, trim(factura) factura, 
							cantidad-COALESCE((
									select sum(relacion) 
									from convenios.operaciones, convenios.operacion_detalle 
									where operaciones.operacion=operacion_detalle.operacion 
									and compras.id=operaciones.id 
									and orden_relacion=orden 
									and (compras.codigo=operacion_detalle.codigo or compras.codigo+20000000=operacion_detalle.codigo)),0
							) vincular 
							 
						FROM convenios.compras,  fst079 
						WHERE proveedor=provcodi 
						AND id={$_COOKIE['id']}
						AND orden= $this->orden
						and codigo= $this->codigo;";
	
			foreach ($db -> query($sql) as $row ) {
	
				$result['orden'] 		= $row['orden'];
				$result['proveedor'] 	= $row['proveedor'];
				$result['factura'] 		= $row['factura'];
				$result['vincular'] 	= $row['vincular'];

			}
			return $result;
		}


		public function orden_vincular(){
		
			$i 	= 0;
			$result = array();
			$db = $this->conn();			

			$sql = "SELECT operaciones.operacion, fecha_factura,cuenta,trim(aanom) cliente, codigo, operacion_detalle.cantidad,bfcant cant_cuotas
					FROM convenios.operaciones,convenios.operacion_detalle, fsd0122, fsd0011
					WHERE operaciones.operacion=operacion_detalle.operacion
					AND operacion_detalle.operacion=bfope1
					AND fsd0011.aacuen=fsd0122.aacuen
					AND aaso != 19
					AND operaciones.estado = 'VIGENTE'
					--AND operaciones.cantidad = 12
					AND (orden_relacion = 0 or orden_relacion is null) 
					AND (codigo=$this->codigo or codigo=$this->codigo+20000000)
					order by 2;";

			foreach ($db -> query($sql) as $row ) {
	
				$result[$i]['operacion'] 	= $row['operacion'];
				$result[$i]['fecha_factura']= $row['fecha_factura'];
				$result[$i]['cuenta'] 		= $row['cuenta'];
				$result[$i]['cliente'] 		= $row['cliente'];
				$result[$i]['codigo'] 		= $row['codigo'];
				$result[$i]['cantidad'] 	= $row['cantidad'];
				$result[$i]['cant_cuotas'] 	= $row['cant_cuotas'];
				$i++;
			}
			return $result;
		}		

		public function vincular(){
		
			$result = array();
			$db = $this->conn();			

		 	$sql = "UPDATE convenios.operacion_detalle
					   SET relacion=cantidad, orden_relacion=$this->orden
					 WHERE codigo=$this->codigo_1 AND operacion=$this->operacion;";

			$db -> query($sql);
			return $result;
		}

		public function compras($nc){

			$i 	= 0;
			$result = array();
			$db = $this->conn();			

		 	$sql = "SELECT id, fecha_factura, trim(provdesc) proveedor, orden, trim(factura) factura, codigo, trim(epdescl) producto,
		 			(select sum(precio_total) from convenios.compras b where compras.orden=b.orden) total_factura,
					COALESCE((select sum(valor_cuota) from convenios.cobranza b where compras.orden=b.orden and verificado='S'),0) cobrado,
		 			COALESCE((select sum(relacion) 
					from convenios.operaciones, convenios.operacion_detalle 
					where operaciones.operacion=operacion_detalle.operacion
					and compras.id=operaciones.id 
					and orden_relacion=orden 
					and (compras.codigo=operacion_detalle.codigo or compras.codigo+20000000=operacion_detalle.codigo)),0) relacion, cantidad, precio_unitario, precio_total, cant_cuotas, valor_cuotas 
				FROM convenios.compras, fst079, tef005
				WHERE proveedor=provcodi
				AND tef005.epcodi=codigo
				AND id={$_COOKIE['id']}
				AND nc='{$nc}' 
				AND fecha_factura between '$this->fecha_inicial' and '$this->fecha_final'
				ORDER BY 1,2,5;";
	
			foreach ($db -> query($sql) as $row ) {
	
				$result[$i]['fecha_factura'] 	= $row['fecha_factura'];
				$result[$i]['orden'] 			= $row['orden'];
				$result[$i]['proveedor'] 		= $row['proveedor'];
				$result[$i]['factura'] 			= $row['factura'];
				$result[$i]['codigo'] 			= $row['codigo'];
				$result[$i]['producto'] 		= $row['producto'];
				$result[$i]['relacion'] 		= $row['relacion'];
				$result[$i]['cantidad'] 		= $row['cantidad'];	
				$result[$i]['precio_unitario'] 	= $row['precio_unitario'];
				$result[$i]['precio_total'] 	= $row['precio_total'];
				$result[$i]['cant_cuotas'] 		= $row['cant_cuotas'];
				$result[$i]['valor_cuotas'] 	= $row['valor_cuotas'];
				$result[$i]['total_factura'] 	= $row['total_factura'];
				$result[$i]['cobrado'] 			= $row['cobrado'];
				$result[$i]['saldo'] 			= $row['total_factura']-$row['cobrado'];
				$i++;
	
			}
		
			return $result;
		}

		public function pendientes(){

			$i 	= 0;
			$result = array();
			$db = $this->conn();

			$sql = "SELECT 	fsd0122.aacuen cuenta,
						trim(aadocu) documento,
						trim(aanom) cliente, 
						bfope1 operacion,
						bjdesc estado,
						bfplaz plazo,
						bfcant cantidad,
						bfpend pendiente,
						round(bftcuo) total, 
						round(bfvcta) cuota, 
						round(bfpres+bfinte) saldo, 
						bffchv vigencia, 
						bfult atraso

					FROM FSD0122, TEF012, FSD0011, FST044 
					WHERE bfope1=tcoperel 
					AND fsd0011.aacuen=fsd0122.aacuen
					AND bjesta=bfesta
					and tcoperel>1 
					AND (bfesta between 3 and 5 or bfesta=50) 
					AND tccarcod in (select tccarcod from tef0121,tef006 where tccarite=epcodi and dpcodi={$_COOKIE['deposito']})
					AND bfempr=1";

			foreach ($db -> query($sql) as $row ) {
				$result[$i]['documento'] 	= $row['documento'];
				$result[$i]['cuenta'] 		= $row['cuenta'];
				$result[$i]['cliente'] 		= $row['cliente'];
				$result[$i]['operacion'] 	= $row['operacion'];
				$result[$i]['estado'] 		= $row['estado'];	
				$result[$i]['cantidad'] 	= $row['cantidad'];
				$result[$i]['cuota'] 		= $row['cuota'];
				$result[$i]['total'] 		= $row['total'];
				$i++;
			}
		
			return $result;
		}

		public function facturadas(){

			if(isset($this->filtro_estado) && $this->filtro_estado>0){

				switch ($this->filtro_estado) {
					case '1':
						$filtro_estado = "AND ubicacion='EN FACILANDIA' ";
						break;
					case '2':
						$filtro_estado = "AND ubicacion='ENVIO AL ALIADO' ";
						break;
					case '3':
						$filtro_estado = "AND ubicacion='EN EL ALIADO' ";
						break;
					case '4':
						$filtro_estado = "AND ubicacion='ENVIO A FACILANDIA' ";
						break;
				}
			}else{

				$filtro_estado = "";
			}	

			$i 	= 0;
			$result = array();
			$db = $this->conn();

			$sql = "SELECT 	
						cuenta,
						trim(aadocu) documento,
						trim(aanom) cliente, 
						operacion,
						estado,
						bfplaz plazo,
						cantidad,
						pendiente,
						bruto total, 
						valor_cuota cuota, 
						saldo_capital saldo, 
						fecha_factura vigencia,
						fecha_1ervto vencimiento, 
						bfult atraso,
						COALESCE(operaciones.lote,0) lote, 
						ubicacion

					FROM FSD0122, FSD0011, convenios.operaciones 
					WHERE bfope1=operacion 
					AND fsd0011.aacuen=fsd0122.aacuen
					and fsd0122.aacuen=cuenta
					AND id={$_COOKIE['id']} 
					AND bfesta = 7
					AND operacion IN (select operacion FROM convenios.operacion_detalle WHERE orden_relacion is not NULL)
					AND fecha_factura between '$this->fecha_inicial' and '$this->fecha_final'
					AND bfempr = 1 
					{$filtro_estado}
					ORDER BY operaciones.lote,fecha_factura;"; 
		
			foreach ($db -> query($sql) as $row ) {
				$result[$i]['documento'] 	= $row['documento'];
				$result[$i]['cuenta'] 		= $row['cuenta'];
				$result[$i]['cliente'] 		= $row['cliente'];
				$result[$i]['operacion'] 	= $row['operacion'];
				$result[$i]['vigencia'] 	= $row['vigencia'];
				$result[$i]['vencimiento'] 	= $row['vencimiento'];	
				$result[$i]['cantidad'] 	= $row['cantidad'];
				$result[$i]['pendiente'] 	= $row['pendiente'];
				$result[$i]['atraso'] 		= $row['atraso'];
				$result[$i]['cuota'] 		= $row['cuota'];
				$result[$i]['total'] 		= $row['total'];
				$result[$i]['lote'] 		= $row['lote'];
				$result[$i]['ubicacion'] 	= $row['ubicacion'];
				$i++;
			}  
		
			return $result;
		}
		
		public function atrasadas(){

			$i 	= 0;
			$result = array();
			$db = $this->conn();

			$sql = "SELECT 	
						cuenta,
						trim(aadocu) documento,
						trim(aanom) cliente, 
						operacion,
						estado,
						bfplaz plazo,
						cantidad,
						pendiente,
						bruto total, 
						valor_cuota cuota, 
						saldo_capital saldo, 
						fecha_factura vigencia, 
						bfult atraso

					FROM FSD0122, FSD0011, convenios.operaciones 
					WHERE bfope1=operacion 
					AND fsd0011.aacuen=fsd0122.aacuen
					and fsd0122.aacuen=cuenta
					AND id={$_COOKIE['id']} 
					AND fsd0122.bfult > 0
					AND bfesta=7
					AND operacion IN (select operacion FROM convenios.operacion_detalle WHERE orden_relacion is not NULL)
				--	AND fecha_factura between '$this->fecha_inicial' and '$this->fecha_final'
					AND bfempr=1
					ORDER BY fecha_factura;";
					  

			foreach ($db -> query($sql) as $row ) {
				$result[$i]['documento'] 	= $row['documento'];
				$result[$i]['cuenta'] 		= $row['cuenta'];
				$result[$i]['cliente'] 		= $row['cliente'];
				$result[$i]['operacion'] 	= $row['operacion'];
				$result[$i]['estado'] 		= $row['estado'];
				$result[$i]['vigencia'] 	= $row['vigencia'];	
				$result[$i]['cantidad'] 	= $row['cantidad'];
				$result[$i]['pendiente'] 	= $row['pendiente'];
				$result[$i]['atraso'] 		= $row['atraso'];
				$result[$i]['cuota'] 		= $row['cuota'];
				$result[$i]['total'] 		= $row['total'];
				$i++;
			}
		
			return $result;
		}

		public function cobranza(){

			$i 	= 0;
			$result = array();
			$db = $this->conn();

			$sql = "SELECT operacion, movimiento, fecha_pago, nro_cuota,cuenta,trim(aanom) cliente, cajero, orden, pago, valor_capital, valor_moratorio, valor_total, valor_cuota
  					FROM convenios.cobranza, fsd0011
  					WHERE cuenta=aacuen
  					AND fecha_pago between '$this->fecha_inicial' and '$this->fecha_final'
  					AND orden in (SELECT orden FROM convenios.compras WHERE id=$this->id)
  					AND (verificado is null or verificado='' or verificado='N')
  				ORDER BY movimiento;";

			foreach ($db -> query($sql) as $row ) {
				$result[$i]['operacion'] 		= $row['operacion'];
				$result[$i]['cuenta'] 			= $row['cuenta'];
				$result[$i]['cliente'] 			= $row['cliente'];
				$result[$i]['fecha_pago'] 		= $row['fecha_pago'];
				$result[$i]['cajero'] 			= $row['cajero'];
				$result[$i]['movimiento'] 		= $row['movimiento'];	
				$result[$i]['nro_cuota'] 		= $row['nro_cuota'];
				$result[$i]['pago'] 			= $row['pago'];
				$result[$i]['valor_cuota_conv']	= $row['valor_cuota'];
				$result[$i]['valor_capital']	= $row['valor_capital'];
				$result[$i]['valor_moratorio'] 	= $row['valor_moratorio'];
				$result[$i]['valor_total'] 		= $row['valor_total'];
				$i++;
			}
			return $result;
		}


		public function verificar_pago(){
		
			//$result = array();
			$db = $this->conn();			

		 	$sql = "UPDATE convenios.cobranza
					   SET verificado='$this->estado', usuario='$this->usuario', fecha_verificacion=current_date
					 WHERE movimiento=$this->movimiento AND nro_cuota=$this->orden;";

			$db -> query($sql);
			//return $result;
		}



		public function vencimiento_consultar(){

			$i = 0;
			$result = array();
			$db = $this->conn();

			$sql = "SELECT b.operacion, bftasa tasa, sum(valor_cuot_ind*b.cantidad) cuota
					FROM convenios.compras a, convenios.operacion_detalle b, fsd0122 c
					WHERE a.orden=b.orden_relacion
					AND b.operacion=bfope1
					AND b.operacion=$this->operacion 
					AND aacuen=$this->cuenta
					AND bfempr=1
					AND (a.codigo=b.codigo or a.codigo+20000000=b.codigo)
					GROUP BY 1,2;";

			foreach ($db -> query($sql) as $row ) {

				$tasa  = $row['tasa'];
				$cuota = $row['cuota'];

			}

			$sql = "SELECT cuota,capital valor, estado, vencimiento, pagado, atraso, (mora+punitorio+gasto+iva+abogado)monto_atraso,total saldo 
					FROM get_mora($this->operacion,current_date,$tasa) where cuota>0";

			foreach ($db -> query($sql) as $row ) {

				$result[$i]['cuota'] 			= $row['cuota'];
				$result[$i]['valor'] 			= $cuota;
				$result[$i]['estado'] 			= $row['estado'];
				$result[$i]['vencimiento'] 		= $row['vencimiento'];
				$result[$i]['pagado'] 			= $row['pagado'];
				$result[$i]['atraso'] 			= $row['atraso'];
				$result[$i]['monto_atraso'] 	= $row['monto_atraso'];
				$result[$i]['saldo'] 			= $row['saldo'];
				$i++;
		
			}		
			return $result;
		}

		public function deposito_productos(){
			
			$i = 0;
			$result = array();
			$db = $this->conn();

			$sql = "SELECT tef005.epcodi codigo, trim(epdescl) nombre_producto,epstact cantidad, epstsol precio_lista,
					COALESCE((SELECT sum(cantidad)  FROM convenios.productos_solicitados WHERE codigo=tef005.epcodi AND estado=0),0) solicitado,
					COALESCE((SELECT sum(cantidad_aprob) FROM convenios.productos_solicitados WHERE codigo=tef005.epcodi AND estado=1 AND fecha_aprob>=current_date-3),0) aprobado 
					FROM tef005, tef006
					WHERE tef005.epcodi=tef006.epcodi 
						AND dpcodi= {$_COOKIE['deposito']} 
						AND tef005.epcodi<3000000 ORDER BY tef005.epcodi;";

			foreach ($db -> query($sql) as $row ) {

				$result[$i]['codigo'] 			= $row['codigo'];
				$result[$i]['nombre_producto'] 	= $row['nombre_producto'];
				$result[$i]['cantidad'] 		= $row['cantidad'];
				$result[$i]['precio_lista'] 	= $row['precio_lista'];
				$result[$i]['solicitado'] 		= $row['solicitado'];
				$result[$i]['aprobado'] 		= $row['aprobado'];
				$i++;
		
			}		
			return $result;
		}

		public function productos_pedidos(){
			$i = 0;
			$result = array();
			$db = $this->conn();

			$sql = "SELECT pedido,fecha, usuario, cuota, cantidad, cantidad_aprob, 
					case
						when estado = 0 then 'Pendiente'
						when estado = 1 then 'Aprobado'
						else 'Rechazado'
					end	estado,
					fecha_aprob,
					usuario_aprob

  					FROM convenios.productos_solicitados WHERE codigo=$this->codigo ORDER BY fecha DESC LIMIT 15;";

			foreach ($db -> query($sql) as $row ) {

				$result[$i]['pedido'] 			= $row['pedido'];
				$result[$i]['fecha'] 			= $row['fecha'];
				$result[$i]['usuario'] 			= $row['usuario'];
				$result[$i]['cuota'] 			= $row['cuota'];
				$result[$i]['cantidad'] 		= $row['cantidad'];
				$result[$i]['cantidad_aprob'] 	= $row['cantidad_aprob'];
				$result[$i]['estado'] 			= $row['estado'];
				$result[$i]['fecha_aprob'] 		= $row['fecha_aprob'];
				$result[$i]['usuario_aprob'] 	= $row['usuario_aprob'];
				$i++;
		
			}		
			return $result;
		}


		public function producto_agregar(){

			$db = $this->conn();
			$sql = "INSERT INTO convenios.productos_solicitados(codigo, cuota, cantidad, usuario)
    				VALUES ($this->codigo, $this->cuota, $this->cantidad, '$this->usuario');";
			$db -> query($sql);
		}

		public function producto_quitar(){

			$db = $this->conn();
			$sql = "DELETE FROM convenios.productos_solicitados WHERE estado=0  AND pedido=$this->pedido;";
			$db -> query($sql);
			
		}

		public function producto_aprobar(){

			$db = $this->conn();
			$sql = "UPDATE convenios.productos_solicitados SET estado=1, cantidad_aprob=$this->cantidad,usuario_aprob='$this->usuario', fecha_aprob=now() WHERE estado=0  AND pedido=$this->pedido;";
			$db -> query($sql);
			
		}

		public function producto_rechazar(){
		
			$db = $this->conn();
			$sql = "UPDATE convenios.productos_solicitados SET estado=2, usuario_aprob='$this->usuario', fecha_aprob=now() WHERE estado=0  AND pedido=$this->pedido;";
			$db -> query($sql);
			
		}



		public function stock_producto($codigo,$cantidad){

			$sql = "UPDATE tef006 SET epstact=$cantidad WHERE epcodi=$codigo AND dpcodi={$_COOKIE['deposito']}";
			$db = $this->conn();
			$db -> query($sql);
		}

		public function pagares_ubicacion(){

			$db  = $this->conn();
			$sql = "INSERT INTO convenios.pagares_historial(id, fecha, operacion, lote, usuario, estado)
    				VALUES ($this->id, now(), $this->operacion, $this->lote, '$this->usuario', '$this->estado');";
			$db -> query($sql);

			$sql = "UPDATE convenios.operaciones SET lote=$this->lote, ubicacion='$this->estado' WHERE id=$this->id AND operacion=$this->operacion;";
			$db -> query($sql);
			
		}

		public function pagares_historial(){
			$i = 0;
			$result = array();
			$db = $this->conn();

			$sql = "SELECT id, fecha, operacion, lote, usuario, estado FROM convenios.pagares_historial WHERE operacion=$this->operacion ORDER BY 2 ASC;";
			foreach ($db -> query($sql) as $row ) {

				$result[$i]['id'] 		= $row['id'];
				$result[$i]['fecha'] 	= $row['fecha'];
				$result[$i]['operacion']= $row['operacion'];
				$result[$i]['lote'] 	= $row['lote'];
				$result[$i]['usuario'] 	= $row['usuario'];
				$result[$i]['estado'] 	= $row['estado'];
				$i++;
			}		
			return $result;
		}

		public function lotes(){

			$i = 0;
			$result = array();
			$db = $this->conn();
			$sql = "SELECT lote FROM convenios.lotes WHERE estado=0 ORDER BY 1 asc;";

			foreach ($db -> query($sql) as $row ) {
				$result[$i]['lote'] 	= $row['lote'];
				$i++;
			}		
			return $result;

		}

		public function consulta_proveedor(){

			$i = 0;
			$result = array();
			$db = $this->conn();
			$sql = "SELECT proveedor, trim(provdesc) descripcion FROM convenios.compras, fst079 WHERE provcodi=proveedor AND id=$this->id GROUP BY 1,2;";

			foreach ($db -> query($sql) as $row ) {
				$result[$i]['proveedor'] 	= $row['proveedor'];
				$result[$i]['descripcion'] 	= $row['descripcion'];
				$i++;
			}		
			return $result;
		}

		public function consulta_factura(){

			$i = 0;
			$result = array();
			$db = $this->conn();
			$sql = "SELECT codigo,trim(epdescl) producto, cantidad, precio_unitario precio,precio_total 
					FROM convenios.compras, tef005 WHERE codigo=epcodi and factura='$this->factura' and proveedor=$this->proveedor AND id=$this->id;";

			foreach ($db -> query($sql) as $row ) {
				$result[$i]['codigo'] 	= $row['codigo'];
				$result[$i]['producto'] = $row['producto'];
				$result[$i]['cantidad'] = $row['cantidad'];
				$result[$i]['precio'] 	= $row['precio'];

				$i++;
			}		
			return $result;
		}




		public function nc(){

			$i 	= 0;
			$result = array();
			$db = $this->conn();			

		 	$sql = "SELECT fecha_nc, proveedor, nc, factura, codigo, trim(epdescl) producto, cantidad, precio_unitario,precio_total, control
 					FROM convenios.nota_credito, tef005
					WHERE epcodi=codigo
					AND id={$_COOKIE['id']}
					ORDER BY 1,2,3,4,5 asc;";
	
			foreach ($db -> query($sql) as $row ) {
	
				$result[$i]['fecha'] 			= $row['fecha_nc'];
				$result[$i]['proveedor'] 		= $row['proveedor'];
				$result[$i]['nc'] 				= $row['nc'];
				$result[$i]['factura'] 			= $row['factura'];
				$result[$i]['codigo'] 			= $row['codigo'];
				$result[$i]['producto'] 		= $row['producto'];
				$result[$i]['cantidad'] 		= $row['cantidad'];	
				$result[$i]['precio_unitario'] 	= $row['precio_unitario'];
				$result[$i]['precio_total'] 	= $row['precio_total'];
				$result[$i]['control'] 			= $row['control'];				
				$i++;
	
			}
		
			return $result;
		}


		public function guardar_nc_procesar(){

			$id_datos =  trim(str_replace(array('0','-'), '', $this->proveedor.$this->factura.$this->fecha));
			$id_datos = $id_datos+0;

			$db = $this->conn();
			$sql = "INSERT INTO convenios.nota_credito(id, fecha_nc, proveedor, nc, factura, codigo, cantidad, precio_unitario, precio_total, usuario, fecha,control)
    				VALUES ($this->id,'$this->fecha',$this->proveedor,'$this->nc','$this->factura', $this->codigo, $this->cantidad, $this->precio, round($this->precio*$this->cantidad), '$this->usuario',now(),'$id_datos');";
    		$db -> query($sql);	

    		$sql = "INSERT INTO convenios.operacion_detalle(operacion, codigo, cantidad, relacion, orden_relacion)
    				SELECT a.control::numeric, a.codigo, a.cantidad,a.cantidad, b.orden
  					FROM convenios.nota_credito a,convenios.compras b
					WHERE a.factura=b.factura 
					AND a.codigo=b.codigo 
					AND a.proveedor=b.proveedor
					AND a.factura='$this->factura' 
					AND a.control='$id_datos' 
					AND a.codigo=$this->codigo;";	
    		$db -> query($sql);

    		$sql = "INSERT INTO convenios.operaciones(id, operacion, cuenta, estado, cantidad, pendiente, bruto, valor_cuota, saldo_capital, factura, fecha_factura, fecha_1ervto, lote, ubicacion)
    				VALUES ($this->id, $id_datos, 1, 'NOTA CREDITO', 1, 0, round($this->precio*$this->cantidad), 0, 0, '$this->nc', '$this->fecha', '$this->fecha', 0, 'NOTA CREDITO');";
    		$db -> query($sql);


    		$sql = "INSERT INTO convenios.cobranza(operacion, movimiento, fecha_pago, nro_cuota, cuenta, cajero, orden, pago, valor_capital, valor_moratorio, valor_total, valor_cuota, verificado, usuario, fecha_verificacion)
					SELECT a.control::numeric operacion,1 movimiento,current_date fecha_pago,1 nro_cuota,1 cuenta,'NOTA_CRED' cajero,b.orden,1 pago, sum(a.precio_total) valor_capital,0 valor_moratorio,sum(a.precio_total) valor_total,sum(a.precio_total) valor_cuota,'S' verificado,'NOTA CREDITO' usuario,now() fecha_verificacion 
					FROM convenios.nota_credito a,convenios.compras b
					WHERE a.factura=b.factura 
					AND a.codigo=b.codigo 
					AND a.proveedor=b.proveedor
					AND a.factura='$this->factura' 
					AND a.control='$id_datos'
					group by 1,orden
					ON CONFLICT(operacion, movimiento, fecha_pago, nro_cuota) DO NOTHING;";
    		$db -> query($sql);

		}
	}	
?>	