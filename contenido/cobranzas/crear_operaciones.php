<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require('../../controlador/main.php');
require( CONTROLADOR . 'cobranza.php');

$data = new COBRANZAS();

$data->cuenta 			= $_POST['cuenta']; 
$data->valor_cuota 		= $_POST['valor_cuota'];
$data->valor_total 		= $_POST['valor_total'];
$data->valor_capital 	= $_POST['valor_capital'];			
$data->valor_interes 	= $_POST['valor_interes'];
$data->valor_tasa 		= $_POST['valor_tasa'];
$data->cantidad_cuota 	= $_POST['cantidad_cuota'];
$data->usuario 			= $_POST['usuario'];
$data->operaciones 		= $_POST['operaciones'];

if($data->crear_operacion() == 0){
	?>
	<div class="card" id="contenedor_operaciones">
		<div class="card-body">
			<?php  
			$datos = $data->operaciones_pendientes();
			if(count($datos)>0){
				?>
				<h5>Operación Pendiente</h5>
				<table class="table table-sm">
					<thead>
						<tr class="bg-warning">
							<th class="text-center">Operación</th>
							<th class="text-center">Cantidad</th>
							<th class="text-center">Pendientes</th>
							<th class="text-right">Monto Operación</th>
							<th class="text-right">Valor Cuota</th>
							<th class="text-right">Saldo Capital</th>
						</tr>
					</thead>
					<tbody>
						<?php  
						for ($i=0; $i < count($datos) ; $i++) { 
							?>			
							<tr>
								<td class="text-center"><?= $datos[$i]['operacion'];?></td>
								<td class="text-center"><?= number_format($datos[$i]['cuotas_cant'],0,',','.');?></td>
								<td class="text-center"><?= number_format($datos[$i]['cuotas_pend'],0,',','.');?></td>
								<td class="text-right"><?= number_format($datos[$i]['monto'],0,',','.');?></td>
								<td class="text-right"><?= number_format($datos[$i]['monto_cuota'],0,',','.');?></td>
								<td class="text-right"><?= number_format($datos[$i]['saldo'],0,',','.');?></td>
							</tr>
							<?php
						}
					}
					?>		  
				</div>
			</div>
			<?Php
		}else{
			echo "error en la creacion de la operacion";
		}
		?>		