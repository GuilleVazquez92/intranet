<?php
	require('../../controlador/main.php');
	require( CONTROLADOR . 'ventas.php');
	$data = new Ventas();
	$data->operacion = $_GET['operacion'];
	$datos = $data->operacion_historial();
	$estado = $data->estado_historial();
?>
<div id="historial">
	<div class="row">
		<div class="col-12">
			<?php
			foreach ($datos as $key) {
				if($key['tipo']=='estado'){
					?>		
					<div class="card bg-info mt-2 mb-2">
						<small>
							<div class="card-body pt-1 pb-1 text-light">
								<?= date('d-m-Y H:i:s',strtotime($key['fecha']));?>: <span class="font-weight-bold"><?=$key['usuario'];?></span> cambio de estado a: <span class="font-weight-bold"><?=$key['motivo'];?></span>
							</div>
						</small>
					</div>
					<?php
				}else{
					?>		
					<div class="card mt-2 mb-2">
						<small>
							<div class="card-body pt-1 pb-1">
								<?= date('d-m-Y H:i:s',strtotime($key['fecha']));?>: <span class="font-weight-bold"><?=$key['usuario'];?></span> comento: <?=$key['comentario'];?>
							</div>
						</small>
					</div>
					<?php
				}
			}

				foreach ($estado as $key) {

					if($data->operacion == $key['operacion']){	
				
					?>		
					<div class="card bg-info mt-2 mb-2">
						<small>
							<div class="card-body pt-1 pb-1 text-light">
								<?= date('d-m-Y',strtotime($key['fecha']));?> -<?=$key['hora'];?>-<span class="font-weight-bold"><?=$key['usuario'];?></span> cambio de estado a: <span class="font-weight-bold"><?=$key['motivo'];?></span>
							</div>
						</small>
					</div>
				
					<?php
					}
				}
			?>
		</div>
	</div>
</div>	