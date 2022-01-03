<?php 

class Capital_Humano extends Conexion{



	public function consulta_vendedores(){

		$db = $this->conn();
		$resultado = array();

		$sql = "SELECT bzclav cod_vend, trim(bznomb) vendedor
				FROM fst062, fst076
				WHERE fst062.equicodi=fst076.equicodi
				AND equicana=25
				AND bzvact='S'
				AND bzfchba is null;";		
		return $resultado;
	}
}


function inicio_fin_semana($fecha){

	$diaInicio="Monday";
	$strFecha = strtotime($fecha);
	$fechaInicio = date('Y-m-d',strtotime('last '.$diaInicio,$strFecha));

	if(date("l",$strFecha)==$diaInicio){
		$fechaInicio= date("Y-m-d",$strFecha);
	}
	return $fechaInicio;
}
?>