<?php
require('../../header.php');
//require('../../controlador/main.php');
require( CONTROLADOR . 'ir.php');
$ir = new IR();

$dias = $ir-> dias_habiles();
$habiles 		= $dias[0]['habiles'];
$trascurrido 	= $dias[0]['trascurrido'];

?>
<div class="container" id="contenedor_operaciones">
	<div class="card-body">
		<?php  
		$datos = $ir->consultar_gestores();
		?>
		<h5>GESTORES</h5>
		<table class="table table-sm">
			<thead>
				<tr class="bg-warning">
					<th class="text-center">GESTOR</th>
					<th class="text-right">CANT CLIENTE</th>
					<th class="text-right">CANT OPERACION</th>
					<th class="text-right">CUOTA CAB</th>
					<th class="text-right">CARTERA</th>
					<th class="text-right">RECUPERO</th>
					<th class="text-right">META</th>
					<th class="text-right">%LOGRADO</th>
					<th class="text-right">PROYECCION</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$grupo 		=	 ""; 
				$tramo 		=	 ""; 
				$cant_cli	=	0;
				$cant_ope	=	0;
				$cuota_cab	=	0;
				$cartera	=	0;
				$recupero	=	0;
				$meta		=	0;
				$falta		=	0;


				for ($i=0; $i < $datos['cant_registros'] ; $i++){

					$cant_cli	+=	$datos[$i]['cant_cli'];
					$cant_ope	+=	$datos[$i]['cant_ope'];
					$cuota_cab	+=	$datos[$i]['cuota_cab'];
					$cartera	+=	$datos[$i]['cartera'];
					$recupero	+=	$datos[$i]['recupero'];
					$meta		+=	$datos[$i]['meta'];


					if($grupo!=$datos[$i]['grupo']){
						$ir->grupo = $grupo = $datos[$i]['grupo'];
						$grupo_control = $ir->consultar_grupo();
						?>
						<tr class="table-warning">
							<th><div data-toggle="collapse" data-target=".collapseme_<?= strtolower($grupo);?>" aria-expanded="true" aria-controls="collapseOne"><?=$datos[$i]['grupo'];?></div>
								<th class="text-right"><?= number_format($grupo_control['cant_cli'],0,',','.');?></th>
								<th class="text-right"><?= number_format($grupo_control['cant_ope'],0,',','.');?></th>
								<th class="text-right"><?= number_format($grupo_control['cuota_cab'],0,',','.');?></th>
								<th class="text-right"><?= number_format($grupo_control['cartera'],0,',','.');?></th>
								<th class="text-right"><?= number_format($grupo_control['recupero'],0,',','.');?></th>
								<th class="text-right"><?= number_format($grupo_control['meta'],0,',','.');?></th>
								<th class="text-right">
									<?php 
									if($grupo_control['recupero']==0 || $grupo_control['meta']==0){
										echo 0;
									}else{
										echo number_format($grupo_control['recupero']/$grupo_control['meta']*100,2,',','.');
									}
									?>
								%</th>
								<th class="text-right"><?= number_format($grupo_control['recupero']/$trascurrido*$habiles,0,',','.');?></th>


							</tr>
							<?php							
						}

						if($tramo!=$datos[$i]['tramo']){
							$ir->tramo = $tramo = $datos[$i]['tramo'];
							$tramo_control = $ir->consultar_tramo();
							echo '<tr class=" table-secondary">
							<td>'.$datos[$i]['descripcion'].'</td>
							<td class="text-right">'.number_format($tramo_control['cant_cli'],0,',','.').'</td>
							<td class="text-right">'.number_format($tramo_control['cant_ope'],0,',','.').'</td>
							<td class="text-right">'.number_format($tramo_control['cuota_cab'],0,',','.').'</td>
							<td class="text-right">'.number_format($tramo_control['cartera'],0,',','.').'</td>
							<td class="text-right">'.number_format($tramo_control['recupero'],0,',','.').'</td>
							<td class="text-right">'.number_format($tramo_control['meta'],0,',','.').'</td>
							<td class="text-right">';

							if($tramo_control['recupero']==0 || $tramo_control['meta']==0){
								echo 0;
							}else{
								echo number_format($tramo_control['recupero']/$tramo_control['meta']*100,2,',','.');
							}
							echo ' %</td>
								<td class="text-right">'.number_format($tramo_control['recupero']/$trascurrido*$habiles,0,',','.').'</td>
							</tr>';
						}
						?>			
						<tr  class="collapse hide collapseme_<?= strtolower($grupo);?>">		
							<td class="text-left"><?= $datos[$i]['gestor'];?></td>
							<td class="text-right"><?= number_format($datos[$i]['cant_cli'],0,',','.');?></td>
							<td class="text-right"><?= number_format($datos[$i]['cant_ope'],0,',','.');?></td>
							<td class="text-right"><?= number_format($datos[$i]['cuota_cab'],0,',','.');?></td>
							<td class="text-right"><?= number_format($datos[$i]['cartera'],0,',','.');?></td>
							<td class="text-right"><?= number_format($datos[$i]['recupero'],0,',','.');?></td>
							<td class="text-right"><?= number_format($datos[$i]['meta'],0,',','.');?></td>
							<td class="text-right">
								<?php 
								if($datos[$i]['recupero']==0 || $datos[$i]['meta']==0){
									echo 0;
								}else{
									echo number_format($datos[$i]['recupero']/$datos[$i]['meta']*100,2,',','.');
								}
								?>
							%</td>
							<td class="text-right"><?= number_format($datos[$i]['recupero']/$trascurrido*$habiles,0,',','.');?></td>
						</tr>
						<?php
					}
					?>
					<tr class="bg-warning">
						<th>TOTAL</div>
							<th class="text-right"><?= number_format($cant_cli,0,',','.');?></th>
							<th class="text-right"><?= number_format($cant_ope,0,',','.');?></th>
							<th class="text-right"><?= number_format($cuota_cab,0,',','.');?></th>
							<th class="text-right"><?= number_format($cartera,0,',','.');?></th>
							<th class="text-right"><?= number_format($recupero,0,',','.');?></th>
							<th class="text-right"><?= number_format($meta,0,',','.');?></th>
							<th class="text-right">
								<?php 
								if($recupero==0 || $meta==0){
									echo 0;
								}else{
									echo number_format($recupero/$meta*100,2,',','.');
								}
								?>	
							%</th>
							<th class="text-right"><?= number_format($recupero/$trascurrido*$habiles,0,',','.');?></th>
						</tr>
					</tbody>
				</table>
			</div>			  
		</div>
		<?php require('../../footer.php'); ?>

