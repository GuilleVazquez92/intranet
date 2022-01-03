<?php 

	class Logistica extends Conexion
	{
		public $cod_chofer;	
		public $chofer;
		public $ayudante;
		public $cuenta;
		public $carro;
		public $estado;
		public $motivo;
		public $comentario;
		public $token;
		
		public function a_entregar(){
			$result = array(); 
			$i = 0;
			$sql = "SELECT 	carro,
							trim(aanom) cliente, 
							replace(trim(tcdirent),'  ','') entrega, 
							(select awtel1 telefono from fsd022 where fsd0011.aacuen=fsd022.aacuen),
							(select awcelu celular from fsd022 where fsd0011.aacuen=fsd022.aacuen),
							posicion

					from public.tef012, public.fsd0011,public.tef021, reparto.clientes 
					where 
						tef012.tccarcue=fsd0011.aacuen
					and clientes.cuenta=tef012.tccarcue
					and clientes.carro=tef012.tccarcod
					and clientes.cod_chofer=tef021.chocodi
					and tef021.clusu=upper('$this->chofer')
					and tccarest between 5 and 6
					and tcfactfec>=current_date-60
					and tctipent=2
					and tcestado1=1
					order by tcfactfec desc 
					limit 100";
			
			$db = $this->conn();
			foreach ( $db -> query($sql) as $row ) {

				$result[$i]['carro'] 		=  $row['carro'];
				$result[$i]['cliente'] 		=  $row['cliente'];
				$result[$i]['entrega'] 		=  $row['entrega'];
				$result[$i]['telefono'] 	=  $row['telefono'];
				$result[$i]['celular'] 		=  $row['celular'];
				$result[$i]['clase'] 		=  'alert alert-success';
				$result[$i]['posicion'] 	=  $row['posicion'];
				$i++;	
			}
			return $result;

		}

		public function chofer(){
			$db = $this->conn();
			$i = 0;

			$sql = "SELECT chocodi cod_chofer, clusu chofer FROM tef021 a WHERE chohab='S' order by 2;";
			foreach ( $db -> query($sql) as $row ) {
				$result[$i]['cod_chofer']	=  $row['cod_chofer'];
				$result[$i]['chofer'] 	=  $row['chofer'];
				$i++;
			}
			return $result;
		}

		public function ayudante(){
			$db = $this->conn();
			$i = 0;

			$sql = "SELECT cod_ayudante, trim(clnom) ayudante FROM reparto.ayudantes, public.fsd050	WHERE clusu=usuario	AND estado='S';";
			foreach ( $db -> query($sql) as $row ) {
				$result[$i]['cod_ayudante']	=  $row['cod_ayudante'];
				$result[$i]['ayudante'] 	=  $row['ayudante'];
				$i++;
			}
			return $result;
		}

		public function entrega(){
			$db = $this->conn();
			$sql = "INSERT INTO reparto.entrega(cuenta, carro, fecha, estado, chofer, cod_ayudante, motivo, comentario, token)
    				SELECT 	$this->cuenta, 
    						$this->carro, 
    						now(), 
    						$this->estado,
    						upper('$this->chofer'), 
    						$this->ayudante, 
    						$this->motivo, 
    						'$this->comentario',
    						'$this->token' 
    						WHERE '$this->token' NOT IN (select token from reparto.entrega where fecha::date=current_date);";

    		$db -> query($sql);	

    		if($this->estado==0){

    			$sql = "UPDATE tef012 SEt tcestado1=3 WHERE tccarcod=$this->carro AND tccarcue=$this->cuenta;";
    			$db -> query($sql);

				$sql = "UPDATE reparto.clientes SET  cod_ayudante=$this->ayudante,fecha_entrega=now() 
						 WHERE carro=$this->carro AND cuenta=$this->cuenta;";
    			$db -> query($sql);


    		}else{

				$sql = "UPDATE reparto.clientes SET  fecha_noentrega=now(), motivo=$this->motivo, cod_chofer=14 
						 WHERE carro=$this->carro AND cuenta=$this->cuenta;";
    			$db -> query($sql);

    		}
    		header('Location:'.ROOT.'contenido/reparto/index.php');
		}
	
		public function clientes(){
			$i = 0;
			$sql = "SELECT 	cuenta,
							carro,
							trim(aanom) cliente, 
							tcfact factura, 
							tcfactfec fech_factura, 
							tef021.clusu chofer,
							posicion

					from public.tef012, public.fsd0011, public.tef021, reparto.clientes 
					where 
						tef012.tccarcue=fsd0011.aacuen
					and clientes.cuenta=tef012.tccarcue
					and clientes.carro=tef012.tccarcod
					and clientes.cod_chofer=tef021.chocodi
					and tccarest between 5 and 6
					and tcfactfec>=current_date-60
					and tctipent=2
					and tcestado1=1
					order by tef021.clusu desc ";
			
			$db = $this->conn();
			foreach ( $db -> query($sql) as $row ) {

				$result[$i]['cuenta'] 		=  $row['cuenta'];
				$result[$i]['carro'] 		=  $row['carro'];
				$result[$i]['cliente'] 		=  $row['cliente'];
				$result[$i]['factura'] 		=  $row['factura'];
				$result[$i]['fech_factura'] =  $row['fech_factura'];
				$result[$i]['clase'] 		=  'alert alert-success';
				$result[$i]['chofer'] 		=  $row['chofer'];
				$result[$i]['posicion'] 	=  $row['posicion'];
				$i++;	
			}
			return $result;
		}

		function asignar_chofer(){
			$db = $this->conn();

			$sql = "UPDATE tef012 SET chocodi=$this->cod_chofer  WHERE tccarcod=$this->carro;";
			$db -> query($sql);

			$sql = "UPDATE reparto.clientes SET cod_chofer=$this->cod_chofer WHERE carro=$this->carro;";
			$db -> query($sql);

		}	
	}		
 ?>