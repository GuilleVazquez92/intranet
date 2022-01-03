<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');

require('../../controlador/main.php');
require( CONTROLADOR . 'exportar.php');
require( INCLUDES.'PHPExcel.php');
$data = new COBRANZA();
$data->id = 1;
$objPHPExcel= new PHPExcel();

/*						proveedor,
						factura,
						fecha_factura, 
						orden, 
						cuenta,
						cliente,
						operacion, 
						nro_cuota,
						movimiento,
						fecha_pago, 
						valor_cuota, 
						verificado, 
						usuario,
						fecha_verificacion
*/

//$data->lote = $_GET['lote'];
$fecha = date('mdHis');

$cabecera = new PHPExcel_Worksheet($objPHPExcel, 'Pagos');
$objPHPExcel->addSheet($cabecera, 0);
$objPHPExcel->setActiveSheetIndex(0);

$objPHPExcel->getActiveSheet()->SetCellValue('A1', 'PROVEEDOR');
$objPHPExcel->getActiveSheet()->SetCellValue('B1', 'FACTURA');
$objPHPExcel->getActiveSheet()->SetCellValue('C1', 'FECHA FACTURA');
$objPHPExcel->getActiveSheet()->SetCellValue('D1', 'ORDEN');
$objPHPExcel->getActiveSheet()->SetCellValue('E1', 'CUENTA');
$objPHPExcel->getActiveSheet()->SetCellValue('F1', 'CLIENTE');
$objPHPExcel->getActiveSheet()->SetCellValue('G1', 'OPERACION');
$objPHPExcel->getActiveSheet()->SetCellValue('H1', 'NUMERO CUOTA');
$objPHPExcel->getActiveSheet()->SetCellValue('I1', 'MOVIMIENTO');
$objPHPExcel->getActiveSheet()->SetCellValue('J1', 'CAJA');
$objPHPExcel->getActiveSheet()->SetCellValue('K1', 'FECHA PAGO');
$objPHPExcel->getActiveSheet()->SetCellValue('L1', 'VALOR CUOTA');
$objPHPExcel->getActiveSheet()->SetCellValue('M1', 'VERIFICADO');
$objPHPExcel->getActiveSheet()->SetCellValue('N1', 'USUARIO');
$objPHPExcel->getActiveSheet()->SetCellValue('O1', 'FECHA VERIFICACION');
$objPHPExcel->getActiveSheet()->getStyle("A1:O1")->getFont()->setBold(true);

$rowCount   =   2;
$i = 0;

foreach ($data->exportar_cobranza() as $key => $row[]){
//print $row[$i]['operacion'];

	$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, mb_strtoupper($row[$i]['proveedor'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('B'.$rowCount, mb_strtoupper($row[$i]['factura'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('C'.$rowCount, mb_strtoupper($row[$i]['fecha_factura'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('D'.$rowCount, mb_strtoupper($row[$i]['orden'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('E'.$rowCount, mb_strtoupper($row[$i]['cuenta'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('F'.$rowCount, mb_strtoupper($row[$i]['cliente'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('G'.$rowCount, mb_strtoupper($row[$i]['operacion'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('H'.$rowCount, mb_strtoupper($row[$i]['nro_cuota'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('I'.$rowCount, mb_strtoupper($row[$i]['movimiento'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('J'.$rowCount, mb_strtoupper($row[$i]['caja'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('K'.$rowCount, mb_strtoupper($row[$i]['fecha_pago'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('L'.$rowCount, mb_strtoupper($row[$i]['valor_cuota'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('M'.$rowCount, mb_strtoupper($row[$i]['verificado'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('N'.$rowCount, mb_strtoupper($row[$i]['usuario'],'UTF-8'));
	$objPHPExcel->getActiveSheet()->SetCellValue('O'.$rowCount, mb_strtoupper($row[$i]['fecha_verificacion'],'UTF-8'));	
	$rowCount++;
	$i++;
}

	$objPHPExcel->removeSheetByIndex(1);
	$objPHPExcel->setActiveSheetIndex(0);

	$objWriter  =   new PHPExcel_Writer_Excel2007($objPHPExcel);
	header('Content-Type: application/vnd.ms-excel'); //mime type
	header('Content-Disposition: attachment;filename="Pagos_Facilandia'.$fecha.'.xlsx"'); //tell browser what's the file name
	header('Cache-Control: max-age=0'); //no cache
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');  
	$objWriter->save('php://output');

	?>