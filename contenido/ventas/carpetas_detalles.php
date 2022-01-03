<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 

if($_POST['canal'] && $_POST['tipo']){
	require('../../controlador/main.php');
	require( CONTROLADOR . 'ventas.php');
	$data = new Ventas();

	require( CONTROLADOR . 'vendedores.php');
	$data1 = new Vendedores();

	$data->canal 	=  $_POST['canal'];
	$data->tipo 	=  $_POST['tipo'];

	//  if ($data->tipo == 0 ) {
	//  	$data->tipo = 999;
	// }


	?>
	<div class="table-responsive">
		<table class="table table-sm table-borderless">
			<thead>
				<tr class="bg-warning">
					<th colspan="2">Operación</th>
					<th>Cuenta</th>
					<th>Cliente</th>
					<th>Fecha</th>
					<th>Tipo</th>
					<th>Vendedor</th>
					<th align="center">Canal</th>
					<th align="center">Neto</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$datos = $data->carpetas_detalles();
				$i = 0;
				foreach ($datos as $key) {
					?>				
					<tr>
						<td valign="middle">
							<?php if($datos[$i]['origen'] == 'WEB'){ ?>	
								<img src="<?= IMAGE .'crown.png';?>" alt="" width="16px" height="16px">
							<?php }	?>
						</td>
						<?php 
						if($data->tipo == 15 || $data->tipo == 13 || $data->tipo == 21 || $data->tipo == 12){
							?>	
							<td class="detalle" data-toggle="collapse" data-target="#op_<?= $datos[$i]['operacion'];?>" data-estado="<?= $data->tipo;?>" id="<?= $datos[$i]['operacion'];?>">
								<?php 
									echo  $datos[$i]['operacion'];

								?>
									<img src="<?= IMAGE."comment.png"?>" alt="" width="24px" height="24px">
								<?php			
							}else{
								?>
								<td>
									<?php
										echo  $datos[$i]['operacion'];
								}
								?>
							</td>
							<td><?= $datos[$i]['cuenta'];?></td>
							<td><?= $datos[$i]['cliente'];?></td>
							<td><?= $datos[$i]['fecha'];?></td>
							<td><?= $datos[$i]['tipo'];?></td>
							<td><?= $datos[$i]['vendedor'];?></td>
							<td><?= $datos[$i]['canal'];?></td>
							<td align="right"><?= number_format($datos[$i]['neto'],0,',','.');?></td>
						</tr>
						<tr class="collapse detalle_operacion" id="op_<?= $datos[$i]['operacion'];?>">
							<td colspan="9">
								<div class="card">
									<div class="card-body" id="op_<?= $datos[$i]['operacion'];?>_det">
										
									</div>
								</div>
							</td>
						</tr>
						<?php
						$i++; 
					}
					?>
				</tbody>
			</table>	
		</div>


		<?php
		if($data->tipo == 3){
		#Verificacion

/*
		if($estado == 3){
			echo "<td align='center'>Entrada a Verificador</td>";
			echo "<td colspan='3'>Verificador</td>";
		}	

		if($estado==12){ echo 'colspan=2';}
*/

	}

}
?>

<script>
	
	$(".detalle").click(function() {

		var operacion 	= $(this).attr('id');
		var estado 		= $(this).data('estado');

		$(".detalle_operacion").collapse('hide');
		$.ajax({
			type:'POST',
			url:"operaciones_resumen.php",
			data:{
				operacion : operacion,
				estado : estado

			},
			success:function(resp){
				$("#op_"+operacion+"_det").html(resp);	
			}
		});

	});
</script>