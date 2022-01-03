<?php
$datos = array();

foreach ($_POST['data'] as $key => $value) {
	$datos[trim($key)] = trim($value);
}
//var_dump($_POST['data']);

require('/var/www/html/intranet/includes/PHPPdf/pdf_config.php');
class PDF extends PDF_CONFIG{

	// Cabecera de página
	function Header(){

		$fecha = date('d/n/Y/ h:i:s');
		//Primer Cuadro		
		$this->Cell(0,18,'',1,0,'C');
		$this->Cell(-178);
		$this->SetFont('Arial','',8);
		$this->Cell(30,5,'Fecha: '.$fecha,0,0,'L');
		$this->SetFont('Arial','',9);
		$this->Cell(0,5,utf8_decode('Pág '.$this->PageNo().'/{nb}'),0,1,'R');

		// Logo
		$this->SetFont('Arial','B',20);		
		$this->Cell(0,0,'INFORME SCORING',0,1,'C');
		$this->Ln(6);
		$this->SetFont('Arial','',10);
		$this->Cell(0,0,$_POST['data']['tipo_solicitud'],0,1,'C');
		$this->Ln(3);
		$this->SetFont('Arial','',8);
		$this->Cell(0,0,'Facilandia',0,1,'C');

		// Segundo Cuadro
		$this->Ln(10);
		$this->Cell(0,11,'',1,0,'C');		
		$this->Cell(-178);
		$this->SetFont('Arial','IB',10);
		$this->Cell(0,6,$_POST['data']['nombre_cliente'],0,1,'L');
		$this->SetFont('Arial','',8);
		$this->Cell(2);
		$this->Cell(0,1,utf8_decode('Cuenta: '.$_POST['cuenta']),0,1,'L');
		$this->Cell(0,1,utf8_decode(''),0,1,'C');
		$this->Cell(0,1,utf8_decode(''),0,1,'R');
		$this->Ln(5);
	}

// Pie de página
	function Footer()
	{   // Posición: a 1,5 cm del final
		$this->SetY(-25);
		$this->Cell(0,15,'',1,0,'C');
		$this->SetFont('Arial','',8);

		$this->Cell(-178);
		$this->Ln(2);
		$this->Cell(0,4,utf8_decode('Verificación:'.strtoupper($_POST['data']['verificador']).' Última Verificación: '.$_POST['data']['ultima_verificacion']),0,1,'L');
		$this->Cell(0,4,utf8_decode('Analisis: '.strtoupper($_POST['data']['analista']).' Último Analisis: '.$_POST['data']['ultimo_analisis']),0,1,'L');
		$this->Cell(0,4,utf8_decode('El presente Informe es de uso exclusivo y confidencial.'),0,1,'C');
	}
}
if($datos['total_puntos']>=32){
	$color = "#44B652";	
}else{
	$color = "#FF4326";
}


$tel_particular = "NO";
$tel_vecino 	= "NO";
$tel_laboral 	= "NO";
$tel_familiar 	= "NO";

for ($i=0; $i <= 4; $i++) { 
	$valor = substr($datos['datos_telefono'],$i, 1); 
	if($valor == 1){

		switch ($i) {
			case 0:
				$tel_particular = "SI";
			break;
			case 1:
				$tel_vecino 	= "SI";
			break;
			case 2:
				$tel_laboral 	= "SI";
			break;
			case 3:
				$tel_familiar 	= "SI";
			break;
		}
	} 
}

$datos['nuevo_mundo'] 	= ($datos['nuevo_mundo'] 	== 0 ) 	? "NO":"SI"; 
$datos['mas_cuenta'] 	= ($datos['mas_cuenta'] 	== 0 ) 	? "NO":"SI";
$datos['entrega'] 		= ($datos['entrega'] 		== 0 ) 	? "NO":"SI";

$expr 	= "htmlentities";
$contenido = <<<EOD
<br>
<table border="1">
<tr>
<td width="360" bgcolor="#17A2B8">Datos Básicos</td>
<td width="360" bgcolor="#17A2B8">Datos Personales</td>
</tr>
<tr>
<td>Edad</td><td width="200">{$datos['edad']}</td>
<td>Vivienda</td><td width="200">{$datos['vivienda']}</td>
</tr>
<tr>
<td>Sexo</td><td width="200">{$datos['sexo']}</td>
<td>Servicios Básicos</td><td width="200">{$datos['servicios_basicos']}</td>
</tr>
<tr>
<td>Estado Civil</td><td width="200">{$datos['estado_civil']}</td>
<td>Conyuge</td><td width="200">{$datos['conyuge']}</td>
</tr>
<tr>
<td>Cantidad de Hijos</td><td width="200">{$datos['cant_hijos']}</td>
</tr>	
</table>	
<br>

<table border="1">
<tr>
<td width="360" bgcolor="#17A2B8">Datos Laborales</td>
<td width="360" bgcolor="#17A2B8">Teléfono</td>
</tr>
<tr>
<td>Situación Laboral</td><td width="200">{$datos['situacion_laboral']}</td>
<td>PARTICULAR</td>
<td width="200">{$tel_particular}</td>
</tr>
<tr>
<td>Antiguedad Laboral</td><td width="200">{$datos['antiguedad_lab']}</td>
<td>VECINO</td>
<td width="200">{$tel_vecino}</td>
</tr>
<tr>
<td>Mercado Laboral</td><td width="200">{$datos['mercado_laboral']}</td>
<td>LABORAL</td>
<td width="200">{$tel_laboral}</td>
</tr>
<tr>
<td width="360">&nbsp;</td>
<td>FAMILIAR</td>
<td width="200">{$tel_familiar}</td>
</tr>	
</table>

<br>
<table border="1">
<tr>
<td width="360" bgcolor="#FFC107">Información Comercial</td>
<td width="360" bgcolor="#FFC107">Información de la Solicitud</td>
</tr>
<tr>
<td>Faja Informconf</td><td width="200">{$datos['faja']}</td>
<td>Producto</td><td width="200">{$datos['producto']}</td>
</tr>
<tr>
<td>Cliente</td><td width="200">{$datos['cliente']}</td>
<td>Mercado</td><td width="200">{$datos['mercado']}</td>
</tr>
<tr>
<td>Nuevo en el mundo</td><td width="200">{$datos['nuevo_mundo']}</td>
<td>Monto de Cuotas</td><td width="200">Gs.{$datos['monto_cuota']}</td>

</tr>
<tr>
<td>Cuenta Bancaria</td><td width="200">{$datos['cuenta_bancaria']}</td>
<td>Entrega Inicial</td><td width="200">{$datos['entrega']}</td>
</tr>
<tr>
<td>Mas de una cuenta</td><td width="200">{$datos['mas_cuenta']}</td>
<td>Cantidad de Cuotas</td><td width="200">{$datos['cantidad_cuota']}</td>
</tr>	
<tr>
<td>In-Situ</td>
<td width="200">{$datos['insitu']}</td>
<td>Referencia Comercial</td><td width="200">{$datos['ref_comercial']}</td>
</tr>
</table>
<br>

<table border="1">
<tr>
<td width="360" bgcolor="#FFC107">Información Financiera</td>
</tr>
<tr>
<td>Ingreso/Salario</td><td width="200">Gs.{$datos['ingreso']}</td>	
</tr>
<tr>		
<td>Mora INTERNA</td><td width="200">{$datos['mora_interna']}</td>
</tr>
<tr>		
<td>Mora EXTERNA</td><td width="200">{$datos['mora_externa']}</td>
</tr>
<tr>
<td>Deuda Mensual</td><td width="200">Gs.{$datos['deuda_mensual']}</td>		
</tr>
<tr>
<td>Total Deuda Externa</td><td width="200">Gs.{$datos['total_deuda_ex']}</td>
</tr>
</table>

<br>
<table border="1">
<tr>
<td>Total puntos acumulados:</td><td width="200" bgcolor="{$color}">{$datos['total_puntos']}</td>
</tr>
<tr>	
<td>Riesgo solicitado</td><td width="200">Gs.{$datos['riesgo_solicitado_valor']}</td>
</tr>
<tr>
<td>Capacidad de Cuota REAL</td><td width="200">Gs.{$datos['capacidad_valor']}</td>
</tr>
<tr>
<td>Limite prestable</td><td width="200">Gs.{$datos['limite_prestable_valor']}</td>
</tr>
</table>
EOD;

$pdf = new PDF('P','mm','A4');
$pdf->SetMargins(15, 10, 15);
$pdf->SetAutoPageBreak('auto',30);
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',9);
$pdf->WriteHTML(utf8_decode($contenido));

$path = "/var/www/html/intranet/prueba/";
$file_name  = $_POST['file_name'];

/*
if(file_exists($path.$file_name)) {
	unlink($path.$file_name);	
}
*/

$pdf->Output($path.$file_name,'F');

?>