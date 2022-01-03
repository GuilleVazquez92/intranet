<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require('../../header.php');
require( CONTROLADOR . 'pagares.php');
$data = new PAGARES();

if (isset($_POST['crear_lote']) && $_POST['crear_lote']==true) {
	$data->entidad		= $_POST['entidad'];
	$data->plazo		= $_POST['plazo'];
	$data->modo			= $_POST['modo'];
	$data->observacion	= $_POST['observacion'];
	$data->crear_lote(); 	
}
if (isset($_POST['abrir_lote']) && $_POST['abrir_lote']==true) {
	$data->lote	= $_POST['lote'];
	$data->abrir_lote(); 	
}
if (isset($_POST['cerrar_lote']) && $_POST['cerrar_lote']==true) {
	$data->lote	= $_POST['lote'];
	$data->cerrar_lote(); 	
}
if (isset($_POST['quitar_lote']) && $_POST['quitar_lote']==true) {
	$data->lote	= $_POST['lote'];
	$data->quitar_lote(); 	
}
?>
<div class="container-fluid">
	<br>
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
			<li class="breadcrumb-item active" aria-current="page">Control de Lotes</li>
		</ol>
	</nav>
	<div class="row justify-content-md-center">
		<div class="col-2 bg-tranparent">
			<button type="button" class="btn btn-success mb-2" data-toggle="modal" data-target="#ModalLote">Crear Lote</button>
		</div>
		<div class="col-10">
			<div class="table-responsive">
				<table class="table table-sm">
					<thead>
						<tr class="table-warning">
							<th>LOTE</th>
							<th>FECHA</th>
							<th>ENTIDAD</th>
							<th class="text-right">MONTO</th>
							<th class="text-center">PLAZO</th>
							<th class="text-center">CANT OPERACION</th>
							<th class="text-center">ESTADO</th>
							<th>MODO</th>
							<th class="text-center">ACCION</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						$i = 0;
						foreach ($data->consultar_lote(9999) as $key) {
							$date = date_create($key['fecha_envio']);
							?>	
							<tr>
								<td><?= $key['lote'];?></td>
								<td><?= date_format($date, 'd-m-Y');?></td>
								<td><?= $key['descripcion'];?></td>
								<td class="text-right"><?= number_format($key['monto'],0,',','.');?></td>
								<td class="text-center"><?= $key['plazo'];?></td>
								<td class="text-center"><div data-toggle="modal" data-target="#ModalCenter" onclick="consultar_operaciones(<?= $key['lote'];?>);"><?= $key['cant_operacion'];?></div></td>
								<td class="text-center">
									<?php 
									if($key['estado']=='C'){
										echo '<img src="'.IMAGE.'disable.png" alt="" onclick="abrir_lote('.$key["lote"].');">';	
									}elseif ($key['estado']=='A' && $key['cant_operacion']>0) {
										echo '<img src="'.IMAGE.'add.png" alt="" onclick="cerrar_lote('.$key["lote"].');">';
									}elseif ($key['estado']!='C' && $key['cant_operacion']>0) {
										echo '<img src="'.IMAGE.'add.png" alt="" onclick="cerrar_lote('.$key["lote"].');">';
									}
									else{
										echo '<img src="'.IMAGE.'fail.png" alt="" onclick="quitar_lote('.$key["lote"].');">';	
									}
									?>
								</td>
								<td><?= $key['modo'];?></td>
								<td>
									<a href="exportar_excel.php?lote=<?= $key['lote'];?>"><?Php echo '<img src="'.IMAGE.'excel.png" alt="">';?></a>
									<?php
									if($key['estado']=='C'){
										if($key['descripcion']=='CPH'){ ?>			
											<img src="<?Php echo IMAGE.'xml.png';?>" alt="" data-toggle="modal" data-target="#ModalSubirWS" onclick="subir_lote_WS(<?= $key['lote'];?>)">	
											<?php
										} ?>	
										<?php }
									?>
								</td>
							</tr>
							<?php 
							}
						?>						
					</tbody>
				</table>

			</div>
		</div>	
	<!--	<div class="col-1 bg-secondary">
			columna derecha
		</div>--->
	</div>	
</div>

<!-- Modal-->
<div class="modal fade" id="ModalCenter" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-dialog modal-xl" role="document">
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

<div class="modal fade" id="ModalSubirWS" tabindex="-1" role="dialog" aria-labelledby="ModalModalSubirWSTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-dialog modal" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ModalSubirWSTitle">Actualizar en el WebService</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body text-center" id="resultadoWS">
				
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="ModalLote" tabindex="-1" role="dialog" aria-labelledby="ModalCenterTitle1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ModalCenterTitle1">Crear Lote</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">

				<div class="form-group">
					<label for="entidad">Entidad</label>
					<select class="form-control" id="entidad" name="entidad" required="required">
						<option></option>
						<?php 
						$datos_entidad = $data->consultar_entidad();
						for ($i=0; $i < count($datos_entidad); $i++){ 
							?>
							<option value="<?= $datos_entidad[$i]['entidad'];?>"><?= $datos_entidad[$i]['descripcion'];?></option>
							<?php
						}
						?>	
					</select>
				</div>
				<div class="form-group">
					<label for="modo">Modo</label>
					<select class="form-control" id="modo" name="modo" required="required">
						<option></option>
						<?php 
						$datos_modo = $data->consultar_modo();
						for ($i=0; $i < count($datos_modo); $i++){ 
							?>
							<option value="<?= $datos_modo[$i]['id'];?>"><?= $datos_modo[$i]['modo_descrip'];?></option>
							<?php
						}
						?>	
					</select>
				</div>
				<div class="form-group">
					<label for="plazo">Plazo</label>
					<input class="form-control" type="number" id="plazo" name="plazo" min="1" max="12" step="1" required="required">
				</div>				  
				<div class="form-group">
					<label for="observacion">Observación</label>
					<textarea class="form-control" id="observacion" name="observacion" rows="3"></textarea>
				</div>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
				<button type="button" class="btn btn-success" onclick="crear_lote();" >Procesar</button>
			</div>
		</div>
	</div>
</div>

<?php
require('../../footer.php');
?>
<script>

	function subir_lote_WS(lote){
		document.getElementById('resultadoWS').innerHTML = 'Aguarde, estamos transmitiendo los datos...<br><div class="spinner-border text-success"></div>';
		$.ajax({
			type:'GET',
			url:"ws_carga_lote.php",
			data:{
				lote: lote
			},
			success:function(resp){
				$("#resultadoWS").html(resp);	
			}
		}); 
	}


	function consultar_operaciones(lote){
		$.ajax({
			type:'POST',
			url:"operaciones_consultar.php",
			data:{
				lote: lote
			},
			success:function(resp){
				$("#operaciones").html(resp);	
			}
		}); 
	}


	function crear_lote(){
		var entidad 	= document.getElementById('entidad').value;
		var plazo 		= document.getElementById('plazo').value;
		var modo 		= document.getElementById('modo').value;
		var observacion = document.getElementById('observacion').value;

		if(entidad.length != 0 && plazo.length != 0 && modo.length != 0){
			$.ajax({
				type:'POST',
				url:"lotes_control.php",
				data:{
					crear_lote:true,
					entidad: entidad,
					plazo: plazo,
					modo:modo, 
					observacion:observacion
				},
				success:function(resp){
					location.reload();	
				}
			});
		}else{
			alert("Verifique que los datos esten completos");
		}
	}

	function abrir_lote(lote){

		$.ajax({
			type:'POST',
			url:"lotes_control.php",
			data:{
				abrir_lote: true,
				lote: lote
			},
			success:function(resp){
				location.reload();	
			}
		}); 
	}

	function cerrar_lote(lote){

		$.ajax({
			type:'POST',
			url:"lotes_control.php",
			data:{
				cerrar_lote: true,
				lote: lote
			},
			success:function(resp){
				location.reload();	
			}
		}); 
	}

	function quitar_lote(lote){

		$.ajax({
			type:'POST',
			url:"lotes_control.php",
			data:{
				quitar_lote: true,
				lote: lote
			},
			success:function(resp){
				location.reload();	
			}
		}); 
	}
</script>