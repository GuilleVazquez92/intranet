<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require('../../controlador/main.php');
require(CONTROLADOR.'vendedores.php');

$vendedor = new Vendedores();
$vendedor->operacion = $_POST['operacion'];
$data = $vendedor->operacion_detalles();
?>
<div class="container">
	<h4>Cuotero de la Operación</h4> 
	<div class="table-responsive-sm">
		<table class="table table-sm table-striped">
			<thead>
				<tr class="table-warning">
					<th class="text-center">Cuota</th>
					<th class="text-center">Valor</th>
					<th class="text-center">Vence.</th>
					<th class="text-center">Estado</th>
					<th class="text-center">Pagado</th>
					<th class="text-center">Atraso</th>
					<th class="text-center">Capital</th>
					<th class="text-center">Mora</th>
					<th class="text-center">Gasto</th>
					<th class="text-center">IVA</th>
					<th class="text-center">Total</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$total_operacion = 0;
				$total_saldo  	 = 0;
				$total_bruto  	 = 0;
				foreach ($data as $datos) {
					?>
					<tr>
						<?php					
						for ($i=0; $i < 11 ; $i++) { 
							$estilo1 = "text-center";		
							$valor = $datos[$i];

							if($i == 1 || $i>=5){
								
								if($i == 1){
									$total_operacion += $datos[$i];
								}
								if($i == 6){
									$total_saldo += $datos[$i];
								}

								if($i == 10){
									$total_bruto += $datos[$i];
								}

								$valor = number_format($datos[$i],0,',','.');
								$estilo1 = "text-right";	
							}

							if($i == 2 || $i==4){
								$date  = date_create($datos[$i]);
								$valor = date_format($date,"d-m-Y");
							}
							?>
							<td class="<?= $estilo1;?>" nowrap>
								<?= $valor;?>
							</td>
							<?php
						}
						?>
					</tr>
					<?php	
				}
				?>
				<tr class="table-warning">
					<th class="text-center">TOTAL</th>
					<th class="text-right"><?= number_format($total_operacion,0,',','.');?></th>
					<th colspan="4"></th>
					<th class="text-right"><?= number_format($total_saldo,0,',','.');?></th>
					<th colspan="3"></th>
					<th class="text-right"><?= number_format($total_bruto,0,',','.');?></th>
				</tr>
			</tbody>			
		</table>
	</div>
</div>
