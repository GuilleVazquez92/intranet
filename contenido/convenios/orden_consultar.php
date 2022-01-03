<?php 
	//error_reporting(E_ALL);
	//ini_set('display_errors', '1');

	if(isset($_POST['orden']) && isset($_POST['codigo'])){

		require('../../controlador/main.php');
		require( CONTROLADOR . 'convenio.php');
		$convenio = new Convenios();
		$orden = $convenio->orden 	= $_POST['orden'];
		$convenio->codigo 	= $_POST['codigo'];
		$datos = $convenio->orden_consultar();
		$datos_orden  = $convenio->orden();
?>
		
			<h6>Orden : <?= $orden.' '.$datos_orden['proveedor'];?></h6>
			<h6>Factura : <?= $datos_orden['factura'];?></h6>
			<h6>Falta Vincular : <?= $datos_orden['vincular'];?></h6>

			<table class="table table-sm table-bordered table-responsive-sm">
				<thead>
					<tr class="table-warning">
						<th>OPERACION</th>
						<th>DOCUMENTO</th>
						<th>CLIENTE</th>
						<th>CODIGO</th>
						<th class="text-center">CANTIDAD</th>
						<th class="text-center">1er VENCIMINENTO</th>
						<th class="text-center">CUOTAS</th>
						<th class="text-center">ATRASO</th>
						<th class="text-center">LOTE</th>
						<th class="text-center">UBICACION</th>
					</tr>
				</thead>
				<tbody>
				<?php 

					$cantidad_total = 0; 
					for ($i=0; $i < count($datos); $i++) {
						$cantidad_total +=$datos[$i]['cantidad'];
						$venc=date_create($datos[$i]['vencimiento']);
				?>
					<tr>
						<td><?= $datos[$i]['operacion'];?></td>
						<td><?= $datos[$i]['documento'];?></td>
						<td><?= $datos[$i]['cliente'];?></td>
						<td><?= $datos[$i]['codigo'];?></td>
						<td class="text-center"><?= $datos[$i]['cantidad'];?></td>
						<td class="text-center"><?= date_format($venc,"d-m-Y");?></td>
						<td class="text-center"><?= $datos[$i]['cant_cuotas'].'/'.$datos[$i]['cuota_pend'];?></td>
						<td class="text-center"><?= $datos[$i]['atraso'];?></td>
						<td class="text-center"><?= $datos[$i]['lote'];?></td>
						<td class="text-center"><?= $datos[$i]['ubicacion'];?></td>
					</tr>
				<?php
					}
				?>
					<tr class="table-warning">
						<th colspan="4">Total</th>
						<th class="text-center"><?= $cantidad_total;?></th>
						<th colspan="5"></th>
					</tr>
				</tbody>
			</table>
<?php
		
	}
?>

