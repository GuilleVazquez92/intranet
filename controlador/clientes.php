<?php
	class Clientes extends Conexion{

		#Propiedades
		public $cuenta;

		# Metodos
		public function datos_personales(){

			$db = $this->conn();
			$resultado = array();

			$sql = "SELECT a.aacuen cuenta, aadocu documento, trim(aanom) cliente, awcalle||' CASI '||awesq||' N° '||awnume direccion, awtel1 telefono, awcelu celular, 
						trim((select apnomb from fst003 z where b.aidept=z.aidept and b.apciud=z.apciud)) ciudad,
						trim((select ajnomb from fst0051 z where b.aidept=z.aidept and b.apciud=z.apciud and b.ajbarr=z.ajbarr)) barrio
					FROM fsd0011 a, fsd022 b 
					WHERE a.aacuen = b.aacuen 
					AND a.aacuen = ".$this->cuenta;

			foreach ( $db -> query($sql) as $row ) {
				$resultado['cuenta'] 	=  $row['cuenta'];
				$resultado['documento'] =  $row['documento'];
				$resultado['cliente'] 	=  $row['cliente'];
				$resultado['direccion'] =  $row['direccion'];
				$resultado['telefono'] 	=  $row['telefono'];
				$resultado['celular'] 	=  $row['celular'];
				$resultado['ciudad'] 	=  $row['ciudad'];
				$resultado['barrio'] 	=  $row['barrio'];
			}
			return $resultado;		
		}

		public function datos_laborales(){
  		
  			$db = $this->conn();
  			$resultado = array();

			$sql = "SELECT trim(aaempr) empresa, 
						bacalle||' CASI '||baesq||' N° '||banume direccion_lab,
						batel1 telefono_lab,
						trim((select apnomb from fst003 z where b.aidept=z.aidept and b.apciud=z.apciud)) ciudad_lab,
						trim((select ajnomb from fst0051 z where b.aidept=z.aidept and b.apciud=z.apciud and b.ajbarr=z.ajbarr)) barrio_lab
  					FROM fsd023 b WHERE aacuen = $this->cuenta;";

			foreach ( $db -> query($sql) as $row ) {
				$resultado['empresa'] 	=  $row['empresa'];
				$resultado['dir_lab'] 	=  $row['direccion_lab'];
				$resultado['tel_lab'] 	=  $row['telefono_lab'];
				$resultado['ciu_lab'] 	=  $row['ciudad_lab'];
				$resultado['barr_lab'] 	=  $row['barrio_lab'];
			}
			return $resultado;		
		}
	}
?>	