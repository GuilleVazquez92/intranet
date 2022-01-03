<?php

require('../../controlador/main.php');
require( CONTROLADOR . 'ir_regularizacion.php');

$data = new REGULARIZACION();
$data->cuenta = $_POST['cuenta']; 
$datos = $data->reg_cliente_consultar();


$data->tipo 		= $_POST['tipo_acuerdo'];
$data->monto_cuota 	= $_POST['monto_cuota'];
$data->plazo 		= $_POST['plazo'];
$data->canceladas 	= $_POST['canceladas'];
$data->fecha_acuerdo= $_POST['fecha_acuerdo'];
$data->fecha_1ervto = $_POST['fecha_1ervto'];

$fecha0 = date_create($_POST['fecha_acuerdo']);
$fecha1 = date_create($_POST['fecha_1ervto']);

?>

<div class="table-responsive">
	<table class="table">
		<tr>
			<td>Cuenta</td>
			<th><?=$_POST['cuenta'];?></th>
		</tr>
		<tr>
			<td>Cliente</td>
			<th><span id="prueba"><?=$datos[0][1];?></span></th>
		</tr>
		<tr>
			<td>Documento</td>
			<th><?=$datos[0][0];?></th>
		</tr>

		<tr>
			<td>Tipo de Acuerdo</td>
			<th><?php
			if($_POST['tipo_acuerdo']==1){
				echo "ACUERDO";	
			}else{
				echo "JUDICIAL";
			}?>
		</th>
	</tr>
	<tr>
		<td>Monto de las Cuotas</td>
		<th><?= number_format($_POST['monto_cuota'],0,',','.');?></th>
	</tr>
	<tr>
		<td>Plazo del Acuerdo</td>
		<th><?= $_POST['plazo'];?></th>
	</tr>
	<tr>
		<td>Cuotas Canceladas</td>
		<th><?= $_POST['canceladas'];?></th>
	</tr>		
	<tr>
		<td>Monto del acuerdo</td>
		<th><?= number_format($_POST['monto_cuota']*$_POST['plazo'],0,',','.');?></th>
	</tr>
	<tr>
		<td>Saldo del Acuerdo</td>
		<th><?= number_format(($_POST['monto_cuota']*$_POST['plazo'])-($_POST['monto_cuota']*$_POST['canceladas']),0,',','.');?></th>
	</tr>
	<tr>
		<td>Fecha del Acuerdo</td>
		<th><?= date_format($fecha0, 'd-m-Y');?></th>
	</tr>
	<tr>
		<td>Primer Vencimiento</td>
		<th><?= date_format($fecha1, 'd-m-Y');?></th>
	</tr>
</table>
</div>