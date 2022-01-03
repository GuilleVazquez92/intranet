<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');
require('../../header.php');
require( CONTROLADOR . 'pagares.php');
$data = new PAGARES();

?>
<div class="container-fluid">
	<br>
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
			<li class="breadcrumb-item active" aria-current="page">Control de Lotes</li>
		</ol>
	</nav>

	<div class="table-responsive">
		<table class="table table-sm">
			<thead>
				<tr class="table-warning">
					<th>Lote</th>
					<th class='text-center'>Especial</th>
					<th>Descripción</th>
					<th>Fecha</th>
					<th>Entidad</th>
					<th class="text-right">Monto</th>
					<th class="text-right">Pago</th>
					<th class="text-right">Saldo</th>
					<th class="text-center">Plazo</th>
					<th class="text-center">Cant.Operación</th>
					<th>Modo</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$i = 0;
				
				foreach ($data->consultar_lote($_COOKIE['id']) as $key => $datos[]) {

					$check = $check_0 = $check_1 = $check_2 = $check_3 = $check_4 = $check_5 =""; 

					$date = date_create($datos[$i]['fecha_envio']);
					
					$fecha = new DateTime("2021/01/01");
					
					if ((($datos[$i]['entidad']== 2)  || ($datos[$i]['entidad']== 4))  && ($date>=$fecha)) {
						
					
					?>	
					<tr>

						<td><?= $datos[$i]['lote'];?></td>
						<td class="text-center">
							<?php 
							if($_COOKIE['id'] == 10 || $_COOKIE['id'] == 16){

								if($datos[$i]['verificado'] >= 1){
									$check = "disabled";
									switch ($datos[$i]['verificado']) {
										case 1:
										$check_1 = "selected";
										break;
										case 2:
										$check_2 = "selected";
										break;
										case 3:
										$check_3 = "selected";
										break;
										case 4:
										$check_4 = "selected";
										break;
										case 5:
										$check_5 = "selected";
										break;	
									}
								}
								?>
								<div class="form-group">
									<select class="form-control form-control-sm" id="tipo_especial" <?= $check;?> data-id="<?= $datos[$i]['lote'];?>" >
										<option value="0" <?= $check_0;?> >Normal</option>
										<option value="1" <?= $check_1;?> >Especial 5</option>
										<option value="3" <?= $check_3;?> >Especial 4</option>
										<option value="2" <?= $check_2;?> >Especial 3</option>
										<option value="4" <?= $check_4;?> >Saldos</option>
										<option value="5" <?= $check_5;?> >Especial 12</option>
									</select>
								</div>
								<?php	
							}
							?>
						</td>
						<td>
							<?php 
							if($_COOKIE['id'] == 10 || $_COOKIE['id'] == 16){
								?>	
								<div class="input-group input-group-sm">
									<input 	class="form-control" 
									type="text" 
									id="<?= $datos[$i]['lote'];?>" 
									value="<?=$datos[$i]['observacion'];?>" 
									onchange="cambio_descripcion(this.id,this.value);"
									readonly>

								</div>
								<?php
							}else{
								echo $datos[$i]['observacion'];;	
							}
							?>
						</td>
						<td><?= date_format($date, 'd-m-Y');?></td>
						<td>
							<?= $datos[$i]['descripcion'];?>
						</td>
						<td class="text-right">
							<?= number_format($datos[$i]['monto'],0,',','.');?>	
						</td>
						<td class="text-right">
							<div data-toggle="modal" data-target="#ModalCenter" onclick="consultar_pagos(<?= $datos[$i]['lote'];?>);">
								<?= number_format($datos[$i]['pago'],0,',','.');?>
								<img src="<?= IMAGE.'log.png'?>" alt="" width="14px" height='14px'>	
							</div>
						</td>
						<td class="text-right">
							<?= number_format($datos[$i]['monto']-$datos[$i]['pago'],0,',','.');?>
						</td>
						<td class="text-center"><?= $datos[$i]['plazo'];?></td>
						<td class="text-center">
							<div data-toggle="modal" data-target="#ModalCenter" onclick="consultar_operaciones(<?= $datos[$i]['lote'];?>);">
								<?= $datos[$i]['cant_operacion'];?>	
								<img src="<?= IMAGE.'log.png'?>" alt="" width="14px" height='14px'>	
							</div></td>
							<td><?= $datos[$i]['modo'];?></td>
						</tr>
						<?php 
						$i++;

					}elseif($datos[$i]['entidad']!= 2 && $datos[$i]['entidad']!= 4  ){
					?>

						<tr>

						<td><?= $datos[$i]['lote'];?></td>
						<td class="text-center">
							<?php 
							if($_COOKIE['id'] == 10 || $_COOKIE['id'] == 16){

								if($datos[$i]['verificado'] >= 1){
									$check = "disabled";
									switch ($datos[$i]['verificado']) {
										case 1:
										$check_1 = "selected";
										break;
										case 2:
										$check_2 = "selected";
										break;
										case 3:
										$check_3 = "selected";
										break;
										case 4:
										$check_4 = "selected";
										break;
										case 5:
										$check_5 = "selected";
										break;	
									}
								}
								?>
								<div class="form-group">
									<select class="form-control form-control-sm" id="tipo_especial" <?= $check;?> data-id="<?= $datos[$i]['lote'];?>" >
										<option value="0" <?= $check_0;?> >Normal</option>
										<option value="1" <?= $check_1;?> >Especial 5</option>
										<option value="3" <?= $check_3;?> >Especial 4</option>
										<option value="2" <?= $check_2;?> >Especial 3</option>
										<option value="4" <?= $check_4;?> >Saldos</option>
										<option value="5" <?= $check_5;?> >Especial 12</option>
									</select>
								</div>
								<?php	
							}
							?>
						</td>
						<td>
							<?php 
							if($_COOKIE['id'] == 10 || $_COOKIE['id'] == 16){
								?>	
								<div class="input-group input-group-sm">
									<input 	class="form-control" 
									type="text" 
									id="<?= $datos[$i]['lote'];?>" 
									value="<?=$datos[$i]['observacion'];?>" 
									onchange="cambio_descripcion(this.id,this.value);"
									readonly>
								</div>
								<?php
							}else{
								echo $datos[$i]['observacion'];;	
							}
							?>
						</td>
						<td><?= date_format($date, 'd-m-Y');?></td>
						<td>
							<?= $datos[$i]['descripcion'];?>
						</td>
						<td class="text-right">
							<?= number_format($datos[$i]['monto'],0,',','.');?>	
						</td>
						<td class="text-right">
							<div data-toggle="modal" data-target="#ModalCenter" onclick="consultar_pagos(<?= $datos[$i]['lote'];?>);">
								<?= number_format($datos[$i]['pago'],0,',','.');?>
								<img src="<?= IMAGE.'log.png'?>" alt="" width="14px" height='14px'>	
							</div>
						</td>
						<td class="text-right">
							<?= number_format($datos[$i]['monto']-$datos[$i]['pago'],0,',','.');?>
						</td>
						<td class="text-center"><?= $datos[$i]['plazo'];?></td>
						<td class="text-center">
							<div data-toggle="modal" data-target="#ModalCenter" onclick="consultar_operaciones(<?= $datos[$i]['lote'];?> ,<?= $datos[$i]['verificado'];?> );">
								<?= $datos[$i]['cant_operacion'];?>	
								<img src="<?= IMAGE.'log.png'?>" alt="" width="14px" height='14px'>	
							</div></td>
							<td><?= $datos[$i]['modo'];?></td>
						</tr>
				<?php
				$i++; 		
					}

					}
					?>						
				</tbody>
			</table>
		</div>	
	</div>

	<!-- Modal-->
	<div class="modal fade" id="ModalCenter" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="ModalCenterTitle">Operaciones Relacionadas</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body" id="operaciones"></div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>

	<?php
	require('../../footer.php');
	?>
	<script>

		$('#tipo_especial').change(function(e){

			var lote 		= $(this).data('id');
			var valor 	= $(this).val();
			$(this).prop('disabled',true);
			$.ajax({
				type:'POST',
				url:"modificaciones.php",
				data:{
					lote : lote,
					modo : valor
				},
				success:function(resp){
					location.reload();
				 }
			});
		})

		function consultar_operaciones(lote,verificado){
			
			$.ajax({
				type:'POST',
				url:"operaciones_consultar.php",
				data:{
					lote: lote,
					verificado:verificado
				},
				success:function(resp){
					$("#operaciones").html(resp);	
				}
			}); 
		}

		function consultar_pagos(lote){
			$.ajax({
				type:'POST',
				url:"operaciones_pagos.php",
				data:{
					lote: lote
				},
				success:function(resp){
					$("#operaciones").html(resp);	
				}
			}); 
		}

		function cambio_descripcion(lote,valor){

			$.ajax({
				type:'POST',
				url:"modificaciones.php",
				data:{
					lote: lote,
					descripcion : valor
				},
				success:function(resp){
				}
			}); 
		}		

	</script>