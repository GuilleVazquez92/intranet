<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');


require('../../controlador/main.php');
require( CONTROLADOR . 'pagares.php');
$pagares = new PAGARES();
$pagares->lote = $_POST['lote'];

if(isset($_POST['aprobar'])){
	$pagares->operacion = $_POST['operacion'];
	$pagares->movimiento= $_POST['movimiento'];
	$pagares->cuota 	= $_POST['cuota'];
	$pagares->usuario 	= $_COOKIE['usuario']; 
	$pagares->aprobar_pago();
}

?>
<div class="table-responsive">
	<table class="table table-sm">
		<thead>
			<tr class="table-warning">
				<th>CUENTA</th>
				<th>CLIENTE</th>
				<th>OPERACION</th>
				<th>MOVIMIENTO</th>												
				<th>CUOTA</th>
				<th class="text-center">FECHA PAGO</th>
				<th>CAJERO</th>
				<th class="text-right">CAPITAL</th>
				<th class="text-right">MORA</th>
				<th class="text-right">TOTAL</th>				
				<th class="text-center">VERIFICADO</th>
			</tr>
		</thead>	
		<tbody>
			<?php
			$ix = 0;
			$total_capital 	= 0;
			$total_mora 	= 0;
			foreach ($pagares->consultar_pagos() as $key => $datos[]) {
				$total_capital 	+= $datos[$ix]['capital']; 
				$total_mora 	+= $datos[$ix]['mora'];
				$fecha_pago 	= date_create($datos[$ix]['fecha_pago']);
				$fecha_verifica	= date_create($datos[$ix]['fecha_verificacion']);
				$data 			= $_POST['lote']."_".strval($datos[$ix]['operacion'])."_".strval($datos[$ix]['movimiento'])."_".strval($datos[$ix]['cuota']);
				?>
				<tr>
					<td><?= $datos[$ix]['cuenta']?></td>
					<td><?= $datos[$ix]['cliente']?></td>
					<td class="text-center"><?= $datos[$ix]['operacion']?></td>
					<td class="text-center"><?= $datos[$ix]['movimiento']?></td>
					<td class="text-center"><?= $datos[$ix]['cuota']?></td>
					<td class="text-center"><?= date_format($fecha_pago, 'd-m-Y');?></td>
					<td class="text-center"><?= $datos[$ix]['cajero']?></td>
					<td class="text-right"><?= number_format($datos[$ix]['capital'],0,',','.');?></td>
					<td class="text-right"><?= number_format($datos[$ix]['mora'],0,',','.');?></td>
					<td class="text-right"><?= number_format($datos[$ix]['capital']+$datos[$ix]['mora'],0,',','.');?></td>					
					<td class="text-center">
						<?php
						if ($datos[$ix]['verificado']=='N') {
							?>		
							<div class="form-check">
								<input class="form-check-input checkbox" type="checkbox" name="checkbox" aria-label="Checkbox for following text input" onclick="verifica_estado('<?= $data;?>')">
							</div>
							<?php
						}else{
							?>
							<img src="<?= IMAGE.'check_in.png'?>" alt="" width="14px" height='14px' title="Verificado por: <?= strtoupper($datos[$ix]['usuario']);?> en la fecha: <?= date_format($fecha_verifica, 'd-m-Y');?> a las <?= date_format($fecha_verifica, 'H:m');?>hs.">			 	
							<?php
						}
						?>
					</td>
				</tr>
				<?php
				$ix++;
			}
			?>
			<tr>
				<th colspan="7"></th>
				<th class="text-right"><?= number_format($total_capital,0,',','.');?></th>			
				<th class="text-right"><?= number_format($total_mora,0,',','.');?></th>
				<th class="text-right"><?= number_format($total_capital+$total_mora,0,',','.');?></th>
				<th></th>
			</tr>
		</tbody>
	</table>
</div>
<script>
	function verifica_estado(datos){

		var res 	   = datos.split("_");
		var lote  	   = res[0];
		var operacion  = res[1];
		var movimiento = res[2];
		var cuota 	   = res[3];
		var confirmar  = confirm("Desea confirmar el pago?");
		var checkbox   = document.getElementsByName('checkbox');
		
		if (confirmar == true) {
			$.ajax({
				type:'POST',
				url:"operaciones_pagos.php",
				data:{
					aprobar: true,
					lote : lote,					
					operacion: operacion,
					movimiento: movimiento,
					cuota:cuota
				},
				success:function(resp){
					$("#operaciones").html(resp);
				}
			}); 	


		} else {
			for (var i=0;i<checkbox.length;i++){
				if ( checkbox[i].checked==true) {
					checkbox[i].checked= false;
				}
			}
		}
	}
</script>