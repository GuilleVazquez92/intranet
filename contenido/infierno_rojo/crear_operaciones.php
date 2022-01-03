<?php
	//if(isset($_POST['tipo']) && isset($_POST['tramo'])){

require('../../controlador/main.php');
require( CONTROLADOR . 'ir.php');

$ir = new IR();
$ir->cuenta 		= $_POST['cuenta']; 
$ir->valor_capital 	= $_POST['valor_capital'];			
$ir->valor_tasa 	= $_POST['valor_tasa'];
$ir->cantidad_cuota = $_POST['cantidad_cuota'];
$ir->valor_cuota 	= $_POST['valor_cuota'];
$ir->valor_total 	= $_POST['valor_total'];
$ir->cabezon 		= $_POST['cabezon'];
$ir->cod_oper 		= $_POST['cod_oper'];
$ir->abogado 		= $_POST['abogado'];
$ir->entrega 		= $_POST['entrega'];

if($ir->crear_operacion() == 0){
	?>
	<div class="card" id="contenedor_operaciones">

		<div class="card-body">

			<?php  
			$datos = $ir->operaciones_pendientes();
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
							<th class="text-center">Documento</th>
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
								<td class="text-center"><a href="pagares_pdf.php?cuenta=<?= $_POST['cuenta'];?>&tipo=<?= $datos[$i]['tipo'];?>" target="_blank">
									<img src="<?= IMAGE.'pdf32x32.png';?>" alt="pdf"></a>
								</td>		
		
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

		//$datos_nuevos = $ir->recalcular_cuenta();
	//}	
		?>		