<?php 

#https://www.facilandia.com.py/api/v1/clientes
print (2021-13);
echo hash('sha256', "elc0ch12008");




require('../../header.php');
require( CONTROLADOR . 'vendedores.php');
$vendedor = new Vendedores();
$vendedor->vendedor = $_COOKIE['usuario'];
?>
<br>
<div class="container-fluid">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
			<li class="breadcrumb-item active" aria-current="page">Contactos Web (Leads)</li>
		</ol>
	</nav>

	<table class="table table-sm">
		<thead>
			<tr class="table-warning">
				<th>Lead</th>
				<th>Cuenta</th>
				<th>Origen</th>
				<th>Cliente</th>
				<th>Teléfono</th>
				<th>Estado</th>
				<th>Mensaje corto</th>
				<th>Fecha</th>
				<th></th>
			</tr>
		</thead>
		<?php

		
		$url = 'https://www.facilandia.com.py/api/v1/leads?cod_vend=';
		$cod_vend = $_COOKIE['rol'];

		$json  = file_get_contents($url.$cod_vend);
		$data = json_decode($json, true);
		for ($i=0; $i < count($data); $i++) {

			switch ($data[$i]['estado']) {
				case 0:
				$estado = "No Concretado";
				break;
				case 1:
				$estado = "Pendiente";
				break;
				case 2:
				$estado = "Concretado";
				break;				
				case 3:
				$estado = "Esperando Documentos";
				break;				
				case 4:
				$estado = "Documentos Enviados";
				break;				
				case 5:
				$estado = "Solicitud Aprobada";
				break;				
				case 6:
				$estado = "Carpeta ingresada";
				break;				
				case 7:
				$estado = "No contestó";
				break;				
				case 8:
				$estado = "Rechazado";
				break;				
				case 9:
				$estado = "No Califica";
				break;				
				case 10:
				$estado = "En Proceso";
				break;
				case 11:
				$estado = "Duplicado";
				break;													
				default:
				$estado = "";
				break;
			}
			?>
			<tr>
				<td><?= $data[$i]['id'];?></td>
				<td><?= $data[$i]['cuenta'];?></td>
				<td>
					<img src="<?= IMAGE .'crown.png';?>" alt="" width="16px" height="16px" title="Cliente WEB">
				</td>
				<td><?= $data[$i]['nombre'];?></td>
				<td><?= $data[$i]['telefono'];?></td>
				<td><?= $estado;?></td>
				<td><?= substr($data[$i]['mensaje'], 0,30);?></td>
				<td>
					<?php 
					$fecha_creacion = str_replace('T', ' ', explode('.', $data[$i]['fecha_creacion']));
					echo $fecha_creacion[0];
					?>
				</td>
				<td>
					<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#exampleModal" onclick="ver_leads(<?= htmlspecialchars(json_encode($data[$i]));?>)">
						Ver detalle
					</button>
				</td>
			</tr>
			<?php
		}

		$datos = $vendedor->lead_linea_credito(); 
		if(count($datos)>0){
			?>
			<tr>
				<th colspan="9"  class="table-warning">Solicitud de linea de credito</th>
			</tr>
			<tr>
				<?php
			}
			foreach ($datos as $key) {
				?>
				<td><?= $key['lead'];?></td>
				<td><?= $key['cuenta'];?></td>
				<td>
					<img src="<?= IMAGE .'crown.png';?>" alt="" width="16px" height="16px" title="Cliente WEB">
				</td>
				<td><?= $key['cliente'];?></td>
				<td><?= $key['telefono'];?></td>
				<td><?= $key['estado'];?></td>
				<td><?= $key['mensaje'];?></td>
				<td><?= $key['fecha'];?></td>
				<td></td>
				<?php
			}
			;?>
		</tr>
	</table>

	<!-- Modal -->
	<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel">Contado Leads: #<span id="id"></span></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form action="">
						<div class="form-row">
							<div class="col-md-9 mb-3">
								<label for="cliente">Cliente</label>
								<input type="text" class="form-control form-control-sm" id="cliente" value="Mark" disabled="">
							</div>
							<div class="col-md-3 mb-3">
								<label for="fecha_creacion">Fecha Creado</label>
								<input type="text" class="form-control form-control-sm" id="fecha_creacion" value="Otto" disabled="">
							</div>
						</div>
						<div class="form-row">
							<div class="col-md-4 mb-3">
								<label for="telefono">Teléfono</label>
								<input type="text" class="form-control form-control-sm" id="telefono" disabled="">
							</div>
							<div class="col-md-5 mb-3">
								<label for="estado">Estado</label>
								<select class="form-control form-control-sm" id="estado" required>
									<option disabled value="">Seleccione...</option>
									<option value="1">Pendiente</option>
									<option value="2">Concretado</option>
									<option value="3">Esperando Documentos</option>
									<option value="4">Documentos Enviados</option>
									<option value="5">Solicitud Aprobada</option>
									<option value="6">Carpeta ingresada</option>
									<option value="7">No contestó</option>
									<option value="8">Rechazado</option>
									<option value="9">No Califica</option>
									<option value="10">En Proceso</option>
									<option value="11">Duplicado</option>
									<option value="0">No Concretado</option>
								</select>
							</div>
							<div class="col-md-3 mb-3">
								<label for="fecha_modificacion">Fecha Modificado</label>
								<input type="text" class="form-control form-control-sm" id="fecha_modificacion" disabled="">
							</div>
						</div>
						<div class="form-row">
							<p for="mensaje">Mensaje : <span id="mensaje" class="text-success"></span></p>
						</div>
						<div class="form-row">
							<div class="col-md-12 mb-3">
								<label for="producto">Producto</label>
								<input type="text" class="form-control form-control-sm" id="producto" disabled="">
							</div>
						</div>

						<div class="form-row">
							<label for="comentar">Comentar</label>
							<textarea class="form-control form-control-sm" id="comentar" rows="3" cols="10"></textarea>
						</div>
						<div class="form-group">
							<div class="form-check">
								<input class="form-check-input" type="checkbox" value="" id="contactado" required>
								<label class="form-check-label" for="contactado">
									Contactado!
								</label>
							</div>
						</div>
						<div id="historial_comentario">			
						</div>
					</div>
				</form>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					<button type="button" class="btn btn-primary" onclick="modificar_lead()">Guardar Cambios</button>
				</div>
			</div>
		</div>
	</div>
</div>
<?php 
require('../../footer.php');
?>
<script>
	function formato_fecha_hora(dato){

		var fecha = dato.split('T');
		var hora = fecha[1].split('.');
		return fecha[0]+' '+hora[0];
	};

	function ver_leads(data){

		var cabecera = (data.cuenta) ? data.cuenta+'-' : "";
		$("#id").html(data.id);
		$("#cliente").val(cabecera + data.nombre);
		$("#telefono").val(data.telefono);
		$("#producto").val(data.producto_cod);
		$("#mensaje").html(data.mensaje);
		$("#fecha_creacion").val(formato_fecha_hora(data.fecha_creacion));
		$("#fecha_modificacion").val(formato_fecha_hora(data.fecha_modificacion));
		$('#estado option[value="'+data.estado+'"]').prop("selected", true);
		$('#contactado').prop("checked", data.contactado);
		if(data.contactado == true)	$('#contactado').prop("disabled", data.contactado);

		$("#historial_comentario").html("<h4>Historial</h4><ul class='list-group' id='historial'>")

		$.each(data.comentarios, function(){
			$("#historial").append("<li class='list-group-item'><span class='text-success'>"+formato_fecha_hora(this.fecha_creacion)+" : </span>"+this.mensaje+"</li>");
		}); 
		$("#historial_comentario").append("</ul>")

	}

	function modificar_lead(){
		
		var id 			= $("#id").html();
		var estado 		= $("#estado").val();
		var comentario 	= $("#comentar").val();
		var contactado 	= $("#contactado").is(':checked') ? true : false;

		$.ajax({
			url: "https://www.facilandia.com.py/api/v1/leads/update_from_intranet/"+id,
			type:'POST',
			data:{
				status: estado,
				contacted: contactado,
				body:comentario
			},
			success:function(resp){
				console.log(resp);
				location.reload();
			}
		});	
	}

</script>
