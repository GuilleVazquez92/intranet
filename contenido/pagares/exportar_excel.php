<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');

require('../../controlador/main.php');
require( CONTROLADOR.'pagares.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/includes/PHPExcel/Classes/PHPExcel.php');

$data 		= new PAGARES();
$objPHPExcel= new PHPExcel();

$data->lote = $_GET['lote'];
$fecha = date('mdHis');

$cabecera = new PHPExcel_Worksheet($objPHPExcel, 'Cabecera');
$objPHPExcel->addSheet($cabecera, 0);
$objPHPExcel->setActiveSheetIndex(0);

$objPHPExcel->getActiveSheet()->SetCellValue('A1','OPERACION');
$objPHPExcel->getActiveSheet()->SetCellValue('B1','DOCUMENTO');
$objPHPExcel->getActiveSheet()->SetCellValue('C1','NOMBRE1');
$objPHPExcel->getActiveSheet()->SetCellValue('D1','NOMBRE2');
$objPHPExcel->getActiveSheet()->SetCellValue('E1','APELLIDO1');
$objPHPExcel->getActiveSheet()->SetCellValue('F1','APELLIDO2');
$objPHPExcel->getActiveSheet()->SetCellValue('G1','NOMBRE_COMPLETO');
$objPHPExcel->getActiveSheet()->SetCellValue('H1','FECHA_NAC');
$objPHPExcel->getActiveSheet()->SetCellValue('I1','SEXO');
$objPHPExcel->getActiveSheet()->SetCellValue('J1','CIUDAD_PART');
$objPHPExcel->getActiveSheet()->SetCellValue('K1','DIRECCION_PART');
$objPHPExcel->getActiveSheet()->SetCellValue('L1','CELULAR_PART');
$objPHPExcel->getActiveSheet()->SetCellValue('M1','TELEFONO1_PART');
$objPHPExcel->getActiveSheet()->SetCellValue('N1','TELEFONO2_PART');
$objPHPExcel->getActiveSheet()->SetCellValue('O1','CARGO');
$objPHPExcel->getActiveSheet()->SetCellValue('P1','LABORAL');
$objPHPExcel->getActiveSheet()->SetCellValue('Q1','FECHA_INGRESO');
$objPHPExcel->getActiveSheet()->SetCellValue('R1','SALARIO');
$objPHPExcel->getActiveSheet()->SetCellValue('S1','CIUDAD_LAB');
$objPHPExcel->getActiveSheet()->SetCellValue('T1','DIRECCION_LAB');
$objPHPExcel->getActiveSheet()->SetCellValue('U1','TELEFONO_LAB');
$objPHPExcel->getActiveSheet()->SetCellValue('V1','MONEDA');
$objPHPExcel->getActiveSheet()->SetCellValue('W1','CAPITAL');
$objPHPExcel->getActiveSheet()->SetCellValue('X1','INTERES');
$objPHPExcel->getActiveSheet()->SetCellValue('Y1','TOTAL_OPERACION');
$objPHPExcel->getActiveSheet()->SetCellValue('Z1','FECHA_OPER');
$objPHPExcel->getActiveSheet()->SetCellValue('AA1','FECHA_1VTO');
$objPHPExcel->getActiveSheet()->SetCellValue('AB1','PLAZO');
$objPHPExcel->getActiveSheet()->SetCellValue('AC1','CUOTAS_CANT');
$objPHPExcel->getActiveSheet()->SetCellValue('AD1','CUOTAS_PEND');
$objPHPExcel->getActiveSheet()->SetCellValue('AE1','ATRASO');
$objPHPExcel->getActiveSheet()->SetCellValue('AF1','MAXIMO_ATRASO');
$objPHPExcel->getActiveSheet()->SetCellValue('AG1','SALDO_CAPITAL');
$objPHPExcel->getActiveSheet()->SetCellValue('AH1','SALDO_INTERES');
$objPHPExcel->getActiveSheet()->SetCellValue('AI1','INFORMCONF');
$objPHPExcel->getActiveSheet()->SetCellValue('AJ1','MODALIDA');
$objPHPExcel->getActiveSheet()->SetCellValue('AK1','SUBMODALIDA');
$objPHPExcel->getActiveSheet()->SetCellValue('AL1','PL_ELECTRONICA');
$objPHPExcel->getActiveSheet()->SetCellValue('AM1','WS_ESTADO');
$objPHPExcel->getActiveSheet()->getStyle("A1:AM1")->getFont()->setBold(true);

$rowCount = 2;
foreach ($data->exportar_cab_lote() as $datos){
	$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, mb_strtoupper($datos['operacion'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, mb_strtoupper($datos['documento'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, mb_strtoupper($datos['nombre1'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, mb_strtoupper($datos['nombre2'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, mb_strtoupper($datos['apellido1'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, mb_strtoupper($datos['apellido2'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, mb_strtoupper($datos['nombre_completo'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, mb_strtoupper($datos['fecha_nac'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, mb_strtoupper($datos['sexo'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, mb_strtoupper($datos['ciudad_part'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, mb_strtoupper($datos['direccion_part'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, mb_strtoupper($datos['celular_part'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, mb_strtoupper($datos['telefono1_part'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, mb_strtoupper($datos['telefono2_part'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, mb_strtoupper($datos['cargo'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('P'.$rowCount, mb_strtoupper($datos['laboral'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('Q'.$rowCount, mb_strtoupper($datos['fecha_ingreso'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('R'.$rowCount, mb_strtoupper($datos['salario'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('S'.$rowCount, mb_strtoupper($datos['ciudad_lab'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('T'.$rowCount, mb_strtoupper($datos['direccion_lab'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('U'.$rowCount, mb_strtoupper($datos['telefono_lab'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('V'.$rowCount, mb_strtoupper($datos['moneda'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('W'.$rowCount, mb_strtoupper($datos['capital'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('X'.$rowCount, mb_strtoupper($datos['interes'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('Y'.$rowCount, mb_strtoupper($datos['total_operacion'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('Z'.$rowCount, mb_strtoupper($datos['fecha_oper'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('AA'.$rowCount, mb_strtoupper($datos['fecha_1vto'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('AB'.$rowCount, mb_strtoupper($datos['plazo'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('AC'.$rowCount, mb_strtoupper($datos['cuotas_cant'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('AD'.$rowCount, mb_strtoupper($datos['cuotas_pend'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('AE'.$rowCount, mb_strtoupper($datos['atraso'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('AF'.$rowCount, mb_strtoupper($datos['maximo_atraso'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('AG'.$rowCount, mb_strtoupper($datos['saldo_capital'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('AH'.$rowCount, mb_strtoupper($datos['saldo_interes'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('AI'.$rowCount, mb_strtoupper($datos['informconf'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('AJ'.$rowCount, mb_strtoupper($datos['modalida'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('AK'.$rowCount, mb_strtoupper($datos['submodalida'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('AL'.$rowCount, mb_strtoupper($datos['pl_electronica'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('AM'.$rowCount, mb_strtoupper($datos['estado'],'UTF-8'));
	$rowCount++;
}

$cuotero = new PHPExcel_Worksheet($objPHPExcel, 'Cuotero');
$objPHPExcel->addSheet($cuotero, 1);
$objPHPExcel->setActiveSheetIndex(1);

$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'OPERACION');
$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'NRO.CUOTA');
$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'VENCIMIENTO');
$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'VALOR CUOTA');
$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'CAPITAL');
$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'INTERES');
$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'ESTADO');
$objPHPExcel->getActiveSheet()->SetCellValue('H1', 'PAGO');
$objPHPExcel->getActiveSheet()->SetCellValue('I1', 'ATRASO');
$objPHPExcel->getActiveSheet()->getStyle("A1:I1")->getFont()->setBold(true);
$rowCount   =   2;
$i = 0;

foreach ($data->exportar_det_lote() as $key => $row1[]){

	$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, mb_strtoupper($row1[$i]['operacion'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, mb_strtoupper($row1[$i]['numero_cuota'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, mb_strtoupper($row1[$i]['vencimiento'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, mb_strtoupper($row1[$i]['monto_cuota'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, mb_strtoupper($row1[$i]['capital'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, mb_strtoupper($row1[$i]['interes'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, mb_strtoupper($row1[$i]['estado'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, mb_strtoupper($row1[$i]['pago'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, mb_strtoupper($row1[$i]['atraso'],'UTF-8'));
	$rowCount++;
	$i++;
}

$objPHPExcel->removeSheetByIndex(2);
$objPHPExcel->setActiveSheetIndex(0);

$objWriter  =   new PHPExcel_Writer_Excel2007($objPHPExcel);
	header('Content-Type: application/vnd.ms-excel'); //mime type
	header('Content-Disposition: attachment;filename="Lote_'.$data->lote.'_v'.$fecha.'.xlsx"'); //tell browser what's the file name
	header('Cache-Control: max-age=0'); //no cache
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');  
	$objWriter->save('php://output');

	?>