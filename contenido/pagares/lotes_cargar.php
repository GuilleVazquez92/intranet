<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');

require('../../header.php');
require( CONTROLADOR . 'pagares.php');
$data = new PAGARES();

if(isset($_POST['lote'])){
	$data->lote  = $_SESSION['lote'] = $_POST['lote'];
	$_SESSION['extra'] = $_POST['extra'];
}

if(!isset($_SESSION['extra'])){
	$_SESSION['extra'] = 0;
}

if(isset($_SESSION['lote'])){
	$data->lote  = $_SESSION['lote'];
}

$total_bruto = 0;
$total_saldo = 0;
$total_cuota = 0;

?>
<div class="container-fluid">
	<br>
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
			<li class="breadcrumb-item active" aria-current="page">Cargar Operaciones</li>
		</ol>
	</nav>

	<div class="container jumbotron">
		<h1 class="display-4">Asignar Operaciones</h1>
		<p class="lead">Para la asignación de las operaciones a un Lote específico se debera seleccionar de la lista "LOTES ABIERTOS", el Lote deberá estar abierto previamente, si no lo encuentra en la lista puede abrirlo desde la página <a href="lotes_control.php">Control de Lotes</a>.</p>
		<hr class="my-4">
		<div class="row">
			<div class="col-6 form-group">
				<label for="operacion">OPERACION</label>
				<input class="form-control"  type="numbet" id="operacion" onkeypress="cargar_operacion()">
			</div>

			<div class="col-6 form-group">
				<label for="entidad">LOTES ABIERTOS</label>
				<select class="form-control" id="lote" name="lote" required="required" onchange="seleccionar_lote();">
					<option></option>
					<?php 
					$datos_entidad = $data->consultar_abiertos();
					for ($i=0; $i < count($datos_entidad); $i++){

						if(isset($_SESSION['lote']) && $_SESSION['lote'] == $datos_entidad[$i]['lote']){
							?>
							<option value="<?= $datos_entidad[$i]['lote'];?>" selected><?= $datos_entidad[$i]['lote'].' - '.$datos_entidad[$i]['descripcion'];?></option>
							<?php
						}else{
							?>
							<option value="<?= $datos_entidad[$i]['lote'];?>"><?= $datos_entidad[$i]['lote'].' - '.$datos_entidad[$i]['descripcion'];?></option>
							<?php
						}
					}
					?>	
				</select>
			</div>
		</div>
	</div>
	<div id="resultado">	
		<?php 
		if(isset($_SESSION['lote'])){
			?>
			<div class="row justify-content-md-center">
				<div class="col-12">
					<div class="table-responsive">
						<table class="table table-sm">
							<thead>
								<tr class="table-warning">
									<th>CUENTA</th>
									<th>DOCUMENTO</th>
									<th>CLIENTE</th>
									<th>OPERACION</th>
									<th>ESTADO</th>
									<th>ATRASO</th>
									<th>PROX VENC</th>												
									<th>CANT CUOTA</th>
									<th>CUOTA</th>
									<th>BRUTO</th>
									<th>SALDO</th>

									<?php
									if($_SESSION['extra']==1){ 
										?>									
										<th>CEDULA</th>
										<th>INFORMCONF</th>
										<th>PAGARE</th>	
										<?php 
									}
									?>									
									<th class="text-center">ELIMINAR</th>
									<th>LOTE ORIGEN</th>
								</tr>
							</thead>	
							<tbody>
								<?php
								$ix = 0;
								$total_saldo = 0;

								foreach ($data->consultar_operaciones() as $key) {
									$total_cuota += $key['valor_cuota']; 
									$total_bruto += $key['valor_operacion'];
									$total_saldo += $key['saldo_capital'];

									?>
									<tr>
										<td><?= $key['cuenta']?></td>
										<td><?= $key['documento']?></td>
										<td><?= $key['cliente']?></td>
										<td class="text-center"><?= $key['operacion']?></td>
										<td class="text-center"><?= $key['estado']?></td>
										<td class="text-center"><?= number_format($key['atraso'],0,',','.');?></td>
										<?Php 
										if($key['marca']==0){
											?>	
											<td class="text-center"><?= $key['prox_venc'];?></td>
											<?Php
										}else{
											?>	
											<td class="text-center bg-warning"><?= $key['prox_venc'];?></td>
											<?Php		

										}
										?>		

										<td class="text-center"><?= number_format($key['cant_cuota'],0,',','.');?></td>
										<td class="text-right"><?= number_format($key['valor_cuota'],0,',','.');?></td>
										<td class="text-right"><?= number_format($key['valor_operacion'],0,',','.');?></td>
										<td class="text-right"><?= number_format($key['saldo_capital'],0,',','.');?></td>
										<?php
										if($_SESSION['extra']==1){ 
											?>	
											<td class="text-center">
												<?php
												if($key['file_cedula']>0){
													?>	
													<img src="<?= IMAGE;?>check_in.png" onclick="visualizar_pdf(<?= $_SESSION['lote'].','.$key['operacion'].',1';?>)">		
													<?Php
												}else{
													?>			
													<img src="<?= IMAGE;?>fail.png" onclick="actualizar_pdf(<?= $_SESSION['lote'].','.$key['operacion'];?>)";>
													<?Php
												}
												?>	
											</td>
											<td class="text-center">
												<?php
												if($key['file_informconf']>0){
													?>	
													<img src="<?= IMAGE;?>check_in.png" onclick="visualizar_pdf(<?= $_SESSION['lote'].','.$key['operacion'].',2';?>)"> 	
													<?Php
												}else{
													?>			
													<img src="<?= IMAGE;?>fail.png" onclick="actualizar_pdf(<?= $_SESSION['lote'].','.$key['operacion'];?>)";>
													<?Php
												}
												?>	
											</td>
											<td class="text-center">
												<?php
												if($key['file_pagare']>0){
													?>	
													<img src="<?= IMAGE;?>check_in.png" onclick="visualizar_pdf(<?= $_SESSION['lote'].','.$key['operacion'].',3';?>)"> 	
													<?Php
												}else{
													?>			
													<img src="<?= IMAGE;?>fail.png" onclick="actualizar_pdf(<?= $_SESSION['lote'].','.$key['operacion'];?>)";>
													<?Php
												}
												?>	
											</td>
											<?php
										} 
										?>										
										<td class="text-center"><img src="<?= IMAGE;?>trash.png" width="20" height="20" alt="" onclick="quitar_operacion(<?= $key['operacion']?>)" title="Quitar operación"></td>

											<?php
										if (empty($key['origen'])){
										?>
										<td class="text-right">--</td>
										<?php 	
										}else{
											?>
										<td class="text-right"><?= $key['origen'];?></td>
										
										<?php
										}
										?>
									</tr>
									<?php
									$ix++;
								}
								?>
								<tr>
									<th colspan="7"></th>
									<th class="text-right"><?= number_format($total_cuota,0,',','.');?></th>			
									<th class="text-right"><?= number_format($total_bruto,0,',','.');?></th>
									<th class="text-right"><?= number_format($total_saldo,0,',','.');?></th>
								
									<th colspan="4"></th>
								</tr>
							</tbody>
						</table>
					</div>
				</div>	
			</div>
		</div>	
		<?php 
	} 
	?>
</div>

<!-- Modal-->
<div class="modal fade" id="ModalCenter" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ModalCenterTitle">Agregar Operaciones</h5>
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

<div class="modal fade" id="ModalPdf" tabindex="-1" role="dialog" aria-labelledby="ModalPdfTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ModalPdfTitle">Visualizar Documento PDF</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="documento_pdf"></div>
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

	document.getElementById("operacion").focus();

	function cargar_operacion(){
		if (event.keyCode === 13) {
			event.preventDefault();
			var lote 		= document.getElementById('lote').value;
			var operacion 	= document.getElementById('operacion').value;
			$('#ModalCenter').modal('show');

			$.ajax({
				type:'POST',
				url:"operaciones_acciones.php",
				data:{
					cargar: true,
					lote: lote,
					operacion: operacion
				},
				success:function(resp){
					$("#operaciones").html(resp);
					setTimeout(function(){
						window.location.reload(1);
					}, 1500);
				}
			}); 
		}
	}

	function quitar_operacion(operacion){
		var lote = document.getElementById('lote').value;
		$('#ModalCenter').modal('show');
		$.ajax({
			type:'POST',
			url:"operaciones_acciones.php",
			data:{
				quitar: true,
				lote: lote,
				operacion: operacion
			},
			success:function(resp){
				$("#operaciones").html(resp);
				setTimeout(function(){
					window.location.reload(1);
				}, 1500);
			}
		}); 
	}	

	function seleccionar_lote(){

		var lote_sel = document.getElementById('lote'); 
		lote_sel = lote_sel.options[lote_sel.selectedIndex].text;

		var res = lote_sel.split("-");
		var lote = res[0].trim();
		var extra_campos = 0;
		if(res[1].trim()=='CPH'){
			extra_campos = 1;
		}

		$.ajax({
			type:'POST',
			url:"lotes_cargar.php",
			data:{
				seleccionar: true,
				lote: lote,
				extra: extra_campos
			},
			success:function(resp){
				window.location.reload(1);
			}
		});
	}

	function actualizar_pdf(lote,operacion){
		$.ajax({
			type:'POST',
			url:"operaciones_acciones.php",
			data:{
				actualizar_pdf: true,
				lote: lote,
				operacion: operacion
			},
			success:function(resp){
				$("#operaciones").html(resp);
				window.location.reload(1);
			}
		}); 		
	}

	function visualizar_pdf(lote,operacion,tipo){
		var tipo_file;
		$('#ModalPdf').modal('show');
		switch (tipo) {
			case 1:
			tipo_file = "file_cedula";
			break;
			
			case 2:
			tipo_file = "file_informconf";
			break;

			case 3:
			tipo_file = "file_pagare";
			break;				
		}

		$("#documento_pdf").html('<iframe src="visualizar_pdf.php?tipo_file='+tipo_file+'&lote='+lote+'&operacion='+operacion+'" width="100%" height="600"></iframe>');
	}

</script>