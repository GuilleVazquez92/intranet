<?php 
	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	if(isset($_POST['orden']) && isset($_POST['codigo'])){

		require('../../controlador/main.php');
		require( CONTROLADOR . 'convenio.php');
		$convenio = new Convenios();
		$orden  = $convenio->orden 		= $_POST['orden'];
		$codigo = $convenio->codigo 	= $_POST['codigo'];
		if(isset($_POST['operacion'])){

			$convenio->codigo_1 	= $_POST['codigo_1'];
			$convenio->operacion 	= $_POST['operacion'];
			$convenio->vincular();			
		}

		$datos = $convenio->orden_vincular();
		$datos_orden  = $convenio->orden();
?>
		
			<h6>Orden : <?= $orden.' '.$datos_orden['proveedor'];?></h6>
			<h6>Factura : <?= $datos_orden['factura'];?></h6>
			<h6>Falta Vincular : <?= $datos_orden['vincular'];?></h6>

			<table class="table table-sm table-bordered table-responsive-sm">
				<thead>
					<tr class="table-warning">
						<th>OPERACION</th>
						<th class="text-center">FECHA FACTURA</th>
						<th>CLIENTE</th>
						<th>CODIGO</th>
						<th class="text-center">CANTIDAD</th>
						<th class="text-center">CUOTAS</th>
						<th class="text-center">ACCION</th>
					</tr>
				</thead>
				<tbody>
				<?php  
					for ($i=0; $i < count($datos); $i++) {
						$date=date_create($datos[$i]['fecha_factura']);
						$operacion 	= $datos[$i]['operacion'];
						$codigo_1 	= $datos[$i]['codigo'];
				?>
					<tr>
						<td><?= $operacion;?></td>
						<td class="text-center"><?= date_format($date,"d-m-Y");?></td>
						<td><?= $datos[$i]['cuenta'].' '.$datos[$i]['cliente'];?></td>
						<td><?= $codigo_1;?></td>
						<td class="text-center"><?= $datos[$i]['cantidad'];?></td>
						<td class="text-center"><?= $datos[$i]['cant_cuotas'];?></td>
						<td class="text-center">
							<?php 
								if($datos_orden['vincular']>=$datos[$i]['cantidad']){
							?>
									<button 
										class="btn btn-success btn-sm" 
										onclick="vincular_orden(<?= $orden.','.$codigo.','.$codigo_1.','.$operacion ;?>)">Vincular
									</button>
							<?php 
								}else{
							?>		
									<button class="btn btn-secondary btn-sm disabled">Vincular</button>							
							<?php
								}
							?>
						</td>
					</tr>
				<?php
					}
				?>
				</tbody>
			</table>
<?php
	}
?>

