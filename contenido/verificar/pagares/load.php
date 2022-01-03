<?php 

	/**
	 * Conexion a BD
	 */
	class Conexion
	{
		public function conn()
		{	
			try 
			{
				if(!defined('host'))
				{
					define('host',' host=localhost;');
					define('bd',' dbname=base_fc');
					define('usuario','postgres');
					define('password','postgres');
				}
				return new PDO('pgsql:'.host.bd,usuario,password);
			} 
			catch (Exception $e) 
			{
				return $e->getMessage().'<br>';
				die();
			}
		}
	}
	class Venta_Cartera extends Conexion
	{
		/* Propiedades */
		public $entidad;
		public $operacion;
		public $estado;
		public $lote;
		public $modo;


		/* Metodos */
		public function listar_lotes()
		{
			$i 	 = 0;
			$result = array();
			$db  = $this->conn();
			$sql = "SELECT lote id,b.descripcion entidad, fecha_envio, fecha_acep, plazo, estado,  modo_descrip, (select count(*) from pagares.operaciones z where a.lote=z.lote ) cant_oper  
					FROM pagares.lote a,pagares.entidad b, pagares.modo c
					WHERE a.entidad=b.entidad 
					AND a.modo=c.id
					ORDER BY 1;";
			
			foreach ( $db -> query($sql) as $row ) 
			{
				$result[$i]['id']			=  $row['id'];
				$result[$i]['entidad']		=  $row['entidad'];
				$result[$i]['fecha_envio'] 	=  $row['fecha_envio'];
				$result[$i]['fecha_acep'] 	=  $row['fecha_acep'];
				$result[$i]['plazo'] 		=  $row['plazo'];
				$result[$i]['estado'] 		=  $row['estado'];
				$result[$i]['modo'] 		=  $row['modo_descrip'];
				$result[$i]['cant_oper']	=  $row['cant_oper'];
				$i++;			
			}
			return $result;			
		}

		public function listar_entidad()
		{
			$i 	 = 0;
			$result = array();
			$db  = $this->conn();
			$sql = "SELECT entidad id, descripcion FROM pagares.entidad ORDER BY entidad;";

			foreach ( $db -> query($sql) as $row ) 
			{
				$result[$i]['id']			=  $row['id'];
				$result[$i]['descripcion'] 	=  $row['descripcion'];
				$i++;			
			}
			return $result;			
		}

		public function listar_modo()
		{
			$i 	 = 0;
			$result = array();
			$db  = $this->conn();
			$sql = "SELECT id, modo_descrip descripcion FROM pagares.modo ORDER BY id;";

			foreach ( $db -> query($sql) as $row ) 
			{
				$result[$i]['id']			=  $row['id'];
				$result[$i]['descripcion'] 	=  $row['descripcion'];
				$i++;			
			}
			return $result;			
		}
	}


	class Preventa extends Venta_Cartera
	{

		public function add()
		{
			$db = $this->conn();
			$sql = "INSERT INTO pagares.preventa(operacion, fecha) 
					SELECT bfope1,current_date 
					FROM fsd0122 
					WHERE bfempr=1
					AND bfesta=7
					AND bfope1 = $this->operacion
					AND bfope1 NOT IN (select operacion from pagares.operaciones);";
       		$db -> query($sql);	
		}

		public function delete()
		{
			$db = $this->conn();
			$sql = "DELETE FROM pagares.preventa WHERE estado=1;";
       		$db -> query($sql);
		}

		public function marcar()
		{
			$db = $this->conn();
			$sql = "UPDATE pagares.preventa SET estado=$this->estado WHERE operacion=$this->operacion;";
       		$db -> query($sql);
		}

		public function listar_preventa()
		{
			$i 	 = 0;
			$result = array();
			$db  = $this->conn();
			$sql = "SELECT 
						operacion, 
						fsd0122.aacuen cuenta,
						aadocu documento,
						trim(aanom) cliente,
						bfcant cantidad,
						bfvcta cuota, 
						bftcuo total,
						afaja faja,
						estado 

					FROM pagares.preventa, fsd0122, fsd0011
					WHERE bfope1=operacion
					AND fsd0122.aacuen=fsd0011.aacuen 
					AND bfesta=7
					AND bfempr=1
					AND operacion NOT IN (select operacion from pagares.operaciones);";

			foreach ( $db -> query($sql) as $row ) 
			{
				$result[$i]['operacion']=  $row['operacion'];
				$result[$i]['cuenta'] 	=  $row['cuenta'];
				$result[$i]['documento']=  $row['documento'];
				$result[$i]['cliente'] 	=  $row['cliente'];
				$result[$i]['cantidad'] =  $row['cantidad'];
				$result[$i]['cuota'] 	=  $row['cuota'];
				$result[$i]['total'] 	=  $row['total'];
				$result[$i]['faja'] 	=  $row['faja'];
				$result[$i]['estado'] 	=  $row['estado'];

				$i++;			
			}
			return $result;		
		}

		public function vender()
		{
			$db = $this->conn();	
			$sql = "INSERT INTO pagares.lote(lote, entidad, fecha_envio, estado, modo)
    				VALUES ($this->lote, $this->entidad, current_date, 'P', $this->modo);";
    		$db -> query($sql);
    		
    		$sql = "INSERT INTO pagares.operaciones(lote, operacion, estado)
    				SELECT $this->lote,operacion,'P' FROM pagares.preventa WHERE estado=0;";
    		$db -> query($sql);		

    		$sql = "DELETE FROM pagares.preventa WHERE estado=0;";
    		$db -> query($sql);

    		header('Location : lotes.php');
		}
	}


 ?>