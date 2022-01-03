<?php 

	class Operaciones extends Conexion{

		public $cuenta;
		public $carro;

		public function carro_cabecera(){

			$db = $this->conn();
			$resultado = array();

			$sql = "SELECT tcfact factura, tcfactfec fecha_factura FROM tef012 WHERE tef012.tccarcue=$this->cuenta AND tef012.tccarcod=$this->carro;";		
			foreach ( $db -> query($sql) as $row ) {
				$resultado['factura'] 		=  $row['factura'];
				$resultado['fecha_factura'] =  $row['fecha_factura'];
			}

			if(count($resultado)==0){
				$sql = "SELECT cucuen cuenta, cufact factura,cufech fecha_factura 
						FROM fsd015 
						WHERE cucuot=0
						AND cutipo=51
						AND cutimbrado>0
						AND cuempr=1
						AND cuope1=$this->operacion;";		
				
				foreach ( $db -> query($sql) as $row ) {
					$resultado['factura'] 		=  $row['factura'];
					$resultado['fecha_factura'] =  $row['fecha_factura'];
				}
			}

			return $resultado;
		}

		public function carro_detalle(){

			$db = $this->conn();
			$i 	 = 0;
			$resultado = array();

		 	$sql = "SELECT tccarite codigo, tccardes descripcion,tccarcan cantidad 
						FROM tef012, tef0121 
						WHERE tef012.tccarcod=tef0121.tccarcod
						AND tef012.tccarcue=$this->cuenta 
						AND tef012.tccarcod=$this->carro;";

			foreach ( $db -> query($sql) as $row ) {
				$resultado[$i]['codigo'] 		=  $row['codigo'];
				$resultado[$i]['descripcion'] 	=  $row['descripcion'];
				$resultado[$i]['cantidad'] 		=  $row['cantidad'];
				$i++;			
			}

			if(count($resultado)==0){
				$resultado[0]['codigo'] 		=  0;
				$resultado[0]['descripcion'] 	=  'Operacion en efectivo';
				$resultado[0]['cantidad'] 		=  1;
			}

			return $resultado;		
		}

		public function buscar_carro(){

			$db = $this->conn();
			$result = array();	

			$sql = "SELECT 	cuenta,carro
					from public.tef012,	public.tef021,reparto.clientes 
					where clientes.cuenta=tef012.tccarcue
					and clientes.carro=tef012.tccarcod
					and clientes.cod_chofer=tef021.chocodi
					and clientes.carro='$this->carro'
					and tccarest between 5 and 6
					and tcfactfec>=current_date-60
					and tctipent=2
					and tcestado1=1";
			
			foreach ( $db -> query($sql) as $row ) {

				$result['cuenta'] 		=  $row['cuenta'];
				$result['carro'] 		=  $row['carro'];
			}
			return $result;

		}

	}
 ?>