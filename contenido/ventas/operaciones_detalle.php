<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 

if(isset($_POST['operacion'])){

	require('../../controlador/main.php');
	require( CONTROLADOR . 'ventas.php');
	$data = new Ventas();
	$data->operacion = $_POST['operacion'];
	$datos = $data->operacion_consultar();
	//var_dump($datos);
	?>
	
	<table class="table table-sm table-borderless">
		<tr>
			<th class="table-warnig" width="14%">Cliente:</th>
			<td class="" width="85%" colspan="3"><?= $datos['cabecera'][0]['cliente'];?></td>
		</tr>
		<tr>
			<th class="align-middle" width="14%">Teléfono:</th>
			<td class="align-middle" width="36%"><?= $datos['cabecera'][0]['telefono'];?></td>
			<th class="align-middle" width="14%">Celular:</th>
			<td class="align-middle" width="36%"><?= $datos['cabecera'][0]['celular'];?></td>
		</tr>
		<tr>
			<th class="" width="14%">Operación:</th>
			<td class="" width="36%"><?= $datos['cabecera'][0]['operacion'];?></td>
			<th class="" width="14%">Tipo:</th>
			<td class="" width="36%"><?= $datos['cabecera'][0]['tipo'];?></td>
		</tr>
		<tr>
			<th class="" width="14%">1erVenc:</th>
			<td class="" width="36%"><?= date('d-m-Y', strtotime($datos['cabecera'][0]['vencimiento']));?></td>
			<th class="" width="14%">CantCuota:</th>
			<td width="36%"><?= $datos['cabecera'][0]['cuota_cant'];?></td>
		</tr>
		<tr>
			<th class="" width="14%">Cuota:</th>
			<td class="" width="36%"><?= number_format($datos['cabecera'][0]['cuota_monto'],0,',','.');?></td>
			<th class="" width="14%">Entrega:</th>
			<td class="" width="36%"><?= number_format($datos['cabecera'][0]['entrega'],0,',','.');?></td>
		</tr>
		<tr>
			<th class="" width="14%">Neto:</th>
			<td class="" width="36%"><?= number_format($datos['cabecera'][0]['neto'],0,',','.'); ?></td>
			<th class="" width="14%">Bruto:</th>
			<td class="" width="36%"><?= number_format($datos['cabecera'][0]['bruto'],0,',','.');?></td>
		</tr>		
		<tr>
			<th class="align-middle" width="14%">Vendedor:</th>
			<td class="align-middle" width="36%"><?= $datos['cabecera'][0]['vendedor'];?></td>
			<th class="align-middle" width="14%">Canal:</th>
			<td class="align-middle" width="36%"><?= $datos['cabecera'][0]['canal'];?></td>
		</tr>
		<tr>
			<th class="align-middle" width="14%">Agendado para:</th>
			<td class="align-middle" width="36%"><?= ($datos['cabecera'][0]['agendado']) ? date("d-m-Y H:i:s", strtotime($datos['cabecera'][0]['agendado'])) : "";?></td>
			<th class="align-middle" width="14%">Etiquetado:</th>
			<td class="align-middle" width="36%"><?= $datos['cabecera'][0]['etiqueta'];?></td>
		</tr>		
	</table>

	<ul class="nav nav-tabs">
		<li class="nav-item">
			<a id="tabs_detalle" class="nav-link active tabs">Detalle</a>
		</li>
		<li class="nav-item">
			<a id="tabs_estado" class="nav-link tabs">Estado</a>
		</li>
		<li class="nav-item">
			<a id="tabs_historial" class="nav-link tabs">Historial</a>
		</li>
	</ul>

	<div id="detalle" class="tabs_element">
		<table  class="table table-sm table-bordered">
			<thead class="bg-warning">
				<tr>
					<th class="">Producto</th>
					<th class="" width="5%">Cant.</th>
					<th class="" width="5%">Desc.</th>
				</tr>
			</thead>
			<tbody>
				<?php 

				for ($i=0; $i < count($datos['detalle'])+1; $i++) { 
					if(isset($datos['detalle'][$i])){
						?>
						<tr>
							<td><?= $datos['detalle'][$i]['producto'];?></td>
							<td class="text-center"><?= $datos['detalle'][$i]['cantidad'];?></td>
							<td class="text-center"><?= $datos['detalle'][$i]['descuento'];?>%</td>
						</tr>
						<?php

					}else{
						?>
						<tr>
							<td>&nbsp;</td>
							<td></td>
							<td></td>
						</tr>
						<?php
					}
				}
				?>
			</tbody>
		</table>
	</div>
	
	<div id="estado" class="d-none tabs_element">
	
		<div id="resultado"></div>
		<?php 
		#Cambiar estado
		if(($_COOKIE['cod_perfil'] == 3 && $datos['cabecera'][0]['cod_motivo']!=12 && $datos['cabecera'][0]['cod_motivo']!=1 && $datos['cabecera'][0]['cod_motivo']!=10) || $_COOKIE['cod_perfil'] == 12){
			?>
			<div id="grupo_estado">
				<div class="form-group mt-2">
					<label for="cambiar_estado">Cambiar de Estado:</label>
					<select id="cambiar_estado" class="form-control form-control-sm">
						<option></option>
						<?php
						$estado = $data->listar_motivo();
						foreach ($estado as $key) {
							if($_COOKIE['cod_perfil'] == 3 && $key['zona']=='V' && $key['id']>0){
								if($datos['cabecera'][0]['cod_motivo'] != $key['id']){
								echo "<option value='{$key['id']}'>{$key['motivo']}</option>";	
								}
							}

							if($_COOKIE['cod_perfil'] == 12 && $key['zona']=='C'){
								if($datos['cabecera'][0]['cod_motivo'] != $key['id']){
								echo "<option value='{$key['id']}'>{$key['motivo']}</option>";	
								}

							}
						}
						?>	
					</select>
				</div>
				<?php
				if($_COOKIE['cod_perfil'] == 12  && strlen($datos['cabecera'][0]['etiqueta']) == 0){
					?>
					<div class="form-group mt-2">
						<label for="etiqueta">Agendar compra en:</label>
						<select id="etiqueta" class="form-control form-control-sm" disabled="disabled">
							<option></option>
							<option value="48hs">48hs</option>
							<option value="72hs">72hs</option>
							<option value="4 días">4 días</option>
							<option value="5 días">5 días</option>
							<option value="6 días">6 días</option>
							<option value="7 días">7 días</option>
							<option value="8 días">8 días</option>
							<option value="9 días">9 días</option>
							<option value="10 días">10 días</option>
							<option value="11 días">11 días</option>
							<option value="12 días">12 días</option>
							<option value="13 días">13 días</option>
							<option value="14 días">14 días</option>
							<option value="15 días">15 días</option>
						</select>
					</div>
					<?php 
				}
				?>
				<button type="button" id="button_estado" class="btn btn-sm btn-primary mb-2" disabled="disabled" onclick="cambiar_estado(<?= $data->operacion;?>);">Cambiar</button>
			</div>
			<?php
		}


		if((($_COOKIE['cod_perfil'] == 3 && $datos['cabecera'][0]['etiqueta']=='48hs') || $_COOKIE['cod_perfil'] == 14) && strlen($datos['cabecera'][0]['etiqueta']) > 0 && $datos['cabecera'][0]['confirmado'] == 0){ 

			if(($_COOKIE['cod_perfil'] == 3 && $datos['cabecera'][0]['etiqueta']=='48hs') || $_COOKIE['cod_perfil'] == 14){

				?>
				<div id="grupo_confirmar" >
					<div class="mt-2 mb-2">
						<label for="formControlRange">Confirmación de Venta</label>	
						<div class="form-check">
							<input class="form-check-input" type="radio" value="1" name="confirma" id="confirma1">
							<label class="form-check-label" for="confirma1">
								Confirma la venta
							</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="radio" value="2" name="confirma" id="confirma2">
							<label class="form-check-label" for="confirma2">
								No confirma la venta
							</label>
						</div>
					</div>
					<?php 
				}

				if($_COOKIE['cod_perfil'] == 14){
					?>	
					<div class="form-group mt-2">
						<label for="contencion">Beneficios para contención:</label>
						<select id="contencion" class="form-control form-control-sm" disabled="disabled">
							<option></option>
							<option value="1">Sin beneficio</option>
							<option value="2">1ra Cuota de regalo</option>
							<option value="3">Obsequio con costo</option>
							<option value="4">Obsequio sin costo</option>
							<option value="5">Otros</option>
						</select>
					</div>

					<?php
				}
				?>		
				<button type="button" id="button_confirmar" class="btn btn-sm btn-primary mb-2" disabled="disabled" onclick="confirmar_venta(<?= $data->operacion;?>);">Confirmar</button>
			</div>
		<?php } 




		if($_COOKIE['cod_perfil'] == 3 || $_COOKIE['cod_perfil'] == 14){		
			?>
			<div id="grupo_desistir">
				<div class="form-group mt-2">
					<label for="desitir_operacion">Desistir Operación:</label>
					<select id="desitir_operacion" class="form-control form-control-sm">
						<option></option>
						<?php
						$motivos = $data->listar_motivo_desistir();

						foreach ($motivos as $key) {
							if($_COOKIE['cod_perfil'] == 3 && trim($key['tipo']) == 'V'){
								echo "<option value={$key['id']}>{$key['motivo']}</option>";
							}

							if($_COOKIE['cod_perfil'] == 14){
								echo "<option value={$key['id']}>{$key['motivo']}</option>";	
							}	
						}
						?>	
					</select>
				</div>
				<button type="button" id="button_desistir" class="btn btn-sm btn-danger mb-2" disabled="disabled" onclick="desistir_operacion(<?= $data->operacion;?>);">Desistir</button>		
			</div>
			<?php
		}
		?>
		<div id="grupo_comentario">
			<div class="form-group mt-2">
				<label for="agregar_comentario">Agregar comentario:</label>
				<textarea class="form-control form-control-sm" id="agregar_comentario" onkeyup="countChar(this)" rows="3"></textarea>
				<div class="text-muted">Sobran <span id="charNum">500</span> caracteres.</div>
			</div>
			<button type="button" id="button_comentario" class="btn btn-sm btn-primary mb-2" disabled="disabled" onclick="agregar_comentario(<?= $data->operacion;?>);">Agregar</button>

		</div>
	</div>

	<div id="historial" class="d-none tabs_element">
	</div>
	<?php 
}
?>
<script>
	$(document).ready(function(){
		
		$('#historial').load('operaciones_historial.php?operacion=<?= $data->operacion;?> #historial');

		$('#Modal').on('hidden.bs.modal', function (e) {
			window.location.reload();
		});

		$(".tabs").click(function(e){

			var id 		 = "#"+e.target.id;
			var elemento = "#"+id.split('_')[1];

			$(".tabs_element").hide();
			$(elemento).show(function(){
				$(this).removeClass("d-none");
			});	

			$(".tabs").removeClass("active");
			$(id).addClass("active");

		});		

		$("#cambiar_estado").change(function(){
			$('#button_estado').prop('disabled', true);
			if($(this).val()){

				if($(this).val() == 12){
					$('#etiqueta').prop('disabled', false);
				}else{
					$('#etiqueta').prop('disabled', true);
					$('#button_estado').prop('disabled', false);	
				}
			}
		});

		$("#etiqueta").change(function(){
			if($(this).val()){
				$('#button_estado').prop('disabled', false);		
			}else{
				$('#button_estado').prop('disabled', true);
			}
		});


		$("input[name=confirma]").change(function(){

			var cod_perfil = getCookie('cod_perfil');
			$('#button_confirmar').prop('disabled', true);
			$('#contencion').prop('disabled', true);

			if($(this).val() == 1 && cod_perfil == 14){
				$('#contencion').prop('disabled', false);
			}else{
				$('#button_confirmar').prop('disabled', false);
			}
		});

		$("#contencion").change(function(){
			$('#button_confirmar').prop('disabled', true);
			if($(this).val())
				$('#button_confirmar').prop('disabled', false);		
		});

		$("#desitir_operacion").change(function(){
			$('#button_desistir').prop('disabled', true);
			if($(this).val()){
				$('#button_desistir').prop('disabled', false);	
			}
		});
	});


	function cambiar_estado(operacion){

		var usuario 	= getCookie('usuario');
		var estado 		= $("#cambiar_estado").val();
		var agendado 	= $("#etiqueta").val();

		if(!agendado || estado!=12) agendado = "";

		$.ajax({
			type:'POST',
			url:"operaciones_procesar.php",
			dataType: "json",
			data:{
				usuario	  : usuario,
				operacion : operacion,
				estado 	  : estado,
				agendado  : agendado 	
			},
			success:function(resp){
				console.log(resp);

				$("#resultado").fadeIn('slow',function(){

					$("#resultado").html(resp['texto']);
					$('#historial').load('operaciones_historial.php?operacion=<?= $data->operacion;?> #historial');
					if(resp['estado']==0){
						$("#grupo_estado").fadeOut('slow');
					}
				});
			}
		});
	}

	function desistir_operacion(operacion){

		var usuario 	= getCookie('usuario');
		var desistir 	= $("#desitir_operacion").val();

		$.ajax({
			type:'POST',
			url:"operaciones_procesar.php",
			dataType: "json",
			data:{
				usuario	  : usuario,
				operacion : operacion,
				desistir  : desistir 	
			},
			success:function(resp){
				console.log(resp);

				$("#resultado").fadeIn('slow',function(){
					$("#resultado").html(resp['texto']);
					$('#historial').load('operaciones_historial.php?operacion=<?= $data->operacion;?> #historial');
					if(resp['estado'] == 0){
						$("#grupo_desistir").fadeOut('slow');
						$('#button_desistir').prop('disabled', true);
					}
				});
			}
		});
	}

	function confirmar_venta(operacion){

		var usuario 	= getCookie('usuario');
		var confirmar  	= $('input[name=confirma]:checked').val();	
		var contencion 	= $("#contencion").val();

		if(getCookie('cod_perfil') != 14) contencion = "";

		$.ajax({
			type:'POST',
			url:"operaciones_procesar.php",
			dataType: "json",
			data:{
				usuario	  : usuario,
				operacion : operacion,
				confirmar : confirmar,
				contencion: contencion
			},
			success:function(resp){
				console.log(resp);

				$("#resultado").fadeIn('slow',function(){
					$("#resultado").html(resp['texto']);
					$('#historial').load('operaciones_historial.php?operacion=<?= $data->operacion;?> #historial');
					if(resp['estado'] == 0){
						$("#grupo_confirmar").fadeOut('slow');
						$('#button_confirmar').prop('disabled', true);
					}
				});
			}
		});
	}


	function agregar_comentario(operacion){

		var usuario 	= getCookie('usuario');
		var comentario 	= $("#agregar_comentario").val();

		$.ajax({
			type:'POST',
			url:"operaciones_procesar.php",
			dataType: "json",
			data:{
				usuario	  : usuario,
				operacion : operacion,
				comentario: comentario 	
			},
			success:function(resp){
				console.log(resp);

				$("#resultado").fadeIn('slow',function(){
					$("#resultado").html(resp['texto']);
					$('#historial').load('operaciones_historial.php?operacion=<?= $data->operacion;?> #historial');
					if(resp['estado'] == 0){
						$('#button_comentario').prop('disabled', true);
						document.getElementById('charNum').innerHTML = "500";
						document.getElementById('agregar_comentario').value = "";
					}
				});
			}
		});
	}

	function countChar(val) {
		var len = val.value.length;
		$('#button_comentario').prop('disabled', true);

		if (len >= 10) {
			$('#button_comentario').prop('disabled', false);
		}

		if (len >= 500) {
			val.value = val.value.substring(0, 500);
		} else {
			$('#charNum').text(500 - len);
		}
	};

	function getCookie(cname) {
		var name = cname + "=";
		var decodedCookie = decodeURIComponent(document.cookie);
		var ca = decodedCookie.split(';');
		for(var i = 0; i <ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1);
			}
			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
			}
		}
		return "";
	}
</script>

