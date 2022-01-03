<?php
	class Geoposicion extends Conexion{
		
		public $empresa;
		public $cuenta;
		public $latitud;
		public $longitud;
		public $localizacion;
		public $usuario;
		public $departamento;
		public $zona;

		public function agregar_ubicacion(){

			$sql = "INSERT INTO intranet.clientes_geolocalizacion(empresa, cuenta, latitud, longitud, localizacion, usuario, departamento, fecha)
    				VALUES ($this->empresa, $this->cuenta,'$this->latitud','$this->longitud', '$this->localizacion', '$this->usuario', '$this->departamento', now());";

    		$db = $this->conn();
	    	$db -> query($sql);

	    	if($this->departamento=='REPARTO'){

	    		return header('Location: '. ROOT .'contenido/reparto/entrega.php?zona='.$this->zona);

	    	}else{

		    	if($this->zona == 0){

		    		return header('Location: '. ROOT .'content/cobranzas/cobrados.php');
		    	
		    	}else{

		    		return header('Location: '. ROOT .'content/cobranzas/no-cobrados.php');
		    	
		    	}
	    	}		

		}

		public function consultar(){
			$db = $this->conn();
			$i = 0;
			$sql = "SELECT id, empresa, cuenta,departamento, fecha
  					FROM intranet.clientes_geolocalizacion 
  					WHERE empresa=$this->empresa 
  						AND cuenta=$this->cuenta 
  						AND usuario='$this->usuario' 
  						AND departamento='$this->departamento' 
  						AND fecha::date=current_date;";

  			foreach ( $db -> query($sql) as $row ) {
				$resultado['cuenta'] =  $row['cuenta'];
				$i++;			
			}			
  			return $i;		
		}

		public function listar_ubicacion(){

			$sql = "";

		}		
	}
?>	