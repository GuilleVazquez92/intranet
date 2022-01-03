<?php 
 	//error_reporting(E_ALL);
 	//ini_set('display_errors', '1');

	if(isset($_POST['operacion']) && isset($_POST['cuenta'])){

		require('../../controlador/main.php');
		require( CONTROLADOR . 'convenio.php');

		$convenio = new Convenios();
		$convenio->operacion = $_POST['operacion'];
		$convenio->cuenta 	 = $_POST['cuenta'];
?>
		
		<h6>Operación : <?= $_POST['operacion'] ;?></h6>

		<table class="table table-sm table-bordered table-responsive-sm">
			<thead>
				<tr class="table-warning">
					<th>N°</th>
					<th class="text-center">Valor</th>
					<th>Estado</th>
					<th>Vencimiento</th>
					<th>Fecha Pago</th>
					<th class="text-center">Atraso</th>
					<!--<th class="text-center">Mora</th>
					<th class="text-center">Saldo</th>-->		
				</tr>
			</thead>

			<tbody>
				<?php 

					$total_valor = 0;
					$total_saldo = 0;
					
					$datos = $convenio->vencimiento_consultar();
					for ($i=0; $i < count($datos); $i++) {

						$venc=date_create($datos[$i]['vencimiento']);		
						$pago=date_create($datos[$i]['pagado']);
						$total_valor += $datos[$i]['valor'];
						$total_saldo += $datos[$i]['saldo'];
	
				?>
				<tr>
					<td><?= $datos[$i]['cuota']; ?></td>
					<td class="text-right"><?= number_format($datos[$i]['valor'],0,',','.')?></td>
					<td><?= $datos[$i]['estado']; ?></td>
					<td><?= date_format($venc,"d-m-Y");?></td>
					<td><?= (date_format($pago,"d-m-Y") != "01-01-0001") ? date_format($pago,"d-m-Y") : "";?></td>
					<td><?= $datos[$i]['atraso']; ?></td>
					<!--<td class="text-right"><?= number_format($datos[$i]['monto_atraso'],0,',','.')?></td>
					<td class="text-right"><?= number_format($datos[$i]['saldo'],0,',','.')?></td>	-->				
				</tr>
				<?php
					}
				?>
				<tr class="table-warning">
					<th>Total</th>
					<th class="text-right"><?= number_format($total_valor,0,',','.')?></th>
					<th colspan="5" class="text-center"></th>
			<!--		<th class="text-right"><?= number_format($total_saldo,0,',','.')?></th>-->
				</tr>
			</tbody>
		</table>
<?php
		
	}
?>

