<?php 

error_reporting(E_ALL);
ini_set('display_errors', '1');

require('../../header.php');
require( CONTROLADOR . 'vendedores.php');
$vendedor = new Vendedores();
$vendedor->vendedor = $_COOKIE['usuario'];

if(isset($_GET['cuenta'])){

	$_SESSION['cuenta'] = $_GET['cuenta'];
}
if(isset($_POST['cuenta'])){

	$_SESSION['cuenta'] = $_POST['cuenta'];
}

if(isset($_SESSION['cuenta'])){

	$vendedor->cuenta 	= $_SESSION['cuenta'];

	if(isset($_POST['aceptar'])){

		$vendedor->gestor 		= $_POST['gestor'];
		$vendedor->comentario 	= $_POST['comentario'];
		$vendedor->motivo 		= $_POST['motivo'];		
		$vendedor->fecha_proximo= $_POST['fecha_proximo'];
		$vendedor->estado		= $_POST['estado'];	
		$vendedor->medio 		= $_POST['medio'];	

		$vendedor->guardar_gestion();
	}

	$fecha_inicial = date("Y-m-d");
	$fecha_final = date("Y-m-d",strtotime($fecha_inicial."+ 31 days"));
	$fecha_nac_inicial = date("Y-m-d",strtotime($fecha_inicial."- 25500 days"));
	$fecha_nac_final = date("Y-m-d",strtotime($fecha_inicial."- 6570 days"));

	?>
<div class="container">	
	<div class="d-none" id="api_key"><?= API_KEY ?></div>
	<br>
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
			<li class="breadcrumb-item"><a href="cartera.php">Cartera</a></li>
			<li class="breadcrumb-item active" aria-current="page">Gestión</li>
		</ol>
	</nav>

	<?php 

	$cliente = $vendedor->cliente();
	foreach ( $cliente as $datos) {
		?>	
		<h4><?= $datos['cuenta']." ".$datos['cliente'] ;?></h4>
		<small>
			Documento : <?= $datos['documento'] ;?><br>
			Dirección : <?= $datos['direccion'] ;?><br>
			Telefono : <?= $datos['telefono1'] ;?><br>
			Telefono : <?= $datos['telefono2'] ;?><br>
			Celular : <?= $datos['celular'] ;?><br>
			Estado : <?= $datos['estado'] ;?><br>
			Calificación :<?= $datos['situacion'] ;?><br>
			Tipo : <?= $datos['recurrente'] ;?><br>
			<hr>
			Linea Asignada : <span><?= number_format($datos['linea_asignada'],0,',','.') ;?></span><br>
			Linea Disponible : <span class="text-success font-weight-bold"><?= number_format($datos['linea_disponible'],0,',','.') ;?></span><br>
			Estado de Linea : <?= $datos['linea_estado'] ;?><br>
			Vencimiento de linea : <?= $datos['linea_vencimiento'] ;?><br>
			<hr>

		</small>
		<?php  
		if($datos['control']=='N'){
			?>		
			<div class="col text-right">					
				<button class="btn btn-transparent"  onclick="lista_productos()">
					<img src="<?= IMAGE.'productos.png'?>" width="45px" height="45px" title="Lista de Productos"/>
				</button>

				<button class="btn btn-transparent" data-toggle="modal" data-target="#ModalCrearCliente">
					<img src="<?= IMAGE.'nuevo_cliente.png'?>" width="45px" height="45px" title="Cliente Nuevo"/>
				</button>
			</div>
			<?php
		}else{
			?>
			<br>
			<div class="container">
				<div class="row">
					<div>					
						<button class="btn btn-transparent"  onclick="datos_clientes(<?= $_SESSION['cuenta'];?>)">
							<i class="fa fa-user mx-1" aria-hidden="true" title="Datos de Clientes"></i>
						</button>
					</div>


					<div>					
						<button class="btn btn-transparent"  onclick="lista_productos()">
							<i class="fa fa-archive mx-1" aria-hidden="true" title="Lista de Productos"></i>
							</button>
					</div>
					<div>					
						<button class="btn btn-transparent" onclick="referencias(<?= $_SESSION['cuenta'];?>)">
							 <i class="fa fa-users mx-1" aria-hidden="true" title="Referencias Personales"></i>
						</button>
					</div>
					<div>
						<form action="operaciones.php" method="POST" class="form">
							<input type="text" name="cuenta" value="<?= $datos['cuenta'] ;?>" hidden>
							<input type="text" name="cliente" value="<?= $datos['cliente'] ;?>" hidden>							
							<button class="btn btn-transparent">
								<i class="fa fa-file-text-o mx-1" aria-hidden="true" title="Operaciones"></i>
							</button>
						</form>
					</div>	
					<div>					
						<button class="btn btn-transparent"  onclick="verificaciones(<?= $_SESSION['cuenta'];?>)">
							<i class="fa fa-check-circle-o mx-1" aria-hidden="true" title="Verificaciones de Riesgos"></i>
						</button>
					</div>
					<div>					
						<form action="adjuntar_documentos.php" method="POST" class="form">
							<input type="text" name="cuenta" value="<?= $datos['cuenta'] ;?>" hidden>
							<input type="text" name="cliente" value="<?= $datos['cliente'] ;?>" hidden>
							<button class="btn btn-transparent">
								<i class="fa fa-paperclip mx-1" aria-hidden="true" title="Archivos adjuntos"></i>
							</button>
						</form>
					</div>	
					<div>
						<form action="crear_carro.php" method="POST" class="form">
							<input type="text" name="cuenta" value="<?= $datos['cuenta'] ;?>" hidden>
							<input type="text" name="cliente" value="<?= $datos['cliente'] ;?>" hidden>
							<button class="btn btn-transparent">
								<i class="fa fa-cart-plus mx-1" aria-hidden="true" title="Agregar carro"></i>
							</button>
						</form>
					</div>
				</div>
			</div>		
			<?php
		}
	}
	?>
	<br>
	<div class="table-responsive-sm">
		<table class="table">
			<thead>
				<tr class="table-warning">
					<th>Fecha</th>
					<th>Respuesta</th>
					<th>Comentario</th>
					<th>Próximo</th>
					<th>Gestor</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$gestiones = $vendedor->gestiones();
				for ($i=0; $i < count($gestiones); $i++) { 
					?>
					<tr>
						<td class="text-left" nowrap>
							<small class="form-text">	
								<?= date("d-m-Y", strtotime($gestiones[$i]['fecha']));?>
								<br>
								<?= date("H:i", strtotime($gestiones[$i]['fecha']));?>
							</small>
						</td>
						<td class="text-center">
							<small class="form-text">
								<?= $gestiones[$i]['respuesta']; ?>
							</small>
						</td>
						<td class="text-center">
							<small class="form-text">
								<?= strtoupper($gestiones[$i]['gestion']); ?>
							</small>
						</td>
						<td nowrap>
							<small class="form-text">
								<?= date("d-m-Y", strtotime($gestiones[$i]['proximo_llamado']));?>
							</small>
						</td>
						<td>
							<small class="form-text">
								<?= $gestiones[$i]['gestor']; ?>
							</small>	
						</td>
					</tr>
					<?php 
				}
				?>
			</tbody>
		</table>
	</div>
	<h4 id="nueva_gestion">NUEVA GESTION</h4>
	<form action="gestion.php" method="POST" class="form">
		<input type="text" value="<?= $datos['cuenta'] ;?>" name="cuenta" hidden>
		<input type="text" value="<?= $_COOKIE['usuario'] ;?>" name="gestor" hidden>

		<div class="form-group">
			<label for="comentario"><small>Agregar Comentario</small></label>
			<textarea name="comentario" width="100%" rows="5" class="form-control"></textarea>	

		</div>
		<div class="form-group">
			<label for="fecha_proximo"><small>Fecha de próximo llamado</small></label>
			<input type="date" id="fecha_proximo" name="fecha_proximo"
			value="<?= $fecha_inicial;?>"
			min="<?= $fecha_inicial;?>" max="<?= $fecha_final;?>" required>

		</div>

		<div class="form-group">
			<label for="motivo"><small>Motivo</small></label>
			<select name="motivo" id="motivo" class="custom-select" required>
				<option value="">Seleccione una opción</option>
				<?php
				$motivos = $vendedor->motivos();
				for ($i=0; $i < count($motivos); $i++) { 
					?>
					<option value="<?= $motivos[$i]['cod_motivo']; ?>"><?= $motivos[$i]['motivo'] ;?></option>
					<?php
				}
				?>
			</select>
		</div>

		<div class="form-group">
			<label for="medio"><small>Medio de la Gestión</small></label>
			<select name="medio" id="medio" class="custom-select" required>
				<option value="">Seleccione una opción</option>
				<option value="1">Facebook</option>
				<option value="2">WhatsApp</option>
				<option value="3">Instagram</option>
				<option value="4">Web</option>
				<option value="5">Medios Propios</option>
			</select>
		</div>

		<div class="form-group">
			<label for="estado"><small>Estado del Gestor</small></label>
			<select name="estado" id="estado" class="custom-select" required>
				<option value="">Seleccione una opción</option>
				<option value="1">EN GUARDIA</option>
				<option value="2">FUERA DE GUARDIA</option>
			</select>
		</div>

		<div class="form-group">
			<button type="submit" name="aceptar" class="btn btn-warning form-control">Aceptar</button>
		</div>
	</form>

	<form action="cartera.php" class="form">	
		<div class="form-group">	
			<button type="reset" class="btn btn-secondary form-control" onclick="this.form.submit();">Cancelar</button>	
		</div>	
	</form>		

	<?php

}else{

	echo 'No existe numero de cuenta';

}
?>
</div>

<!-- Modal-->
<div class="modal fade" id="ModalDatosClientes" tabindex="-1" role="dialog" aria-labelledby="ModalDatosClientes" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ModalDatosClientesTitle">Datos del Cliente</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div id="body_datos_clientes" class="modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>	

<div class="modal fade" id="ModalVerificaciones" tabindex="-1" role="dialog" aria-labelledby="ModalVerificaciones" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ModalVerificacionesTitle">Verificaciones</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div id="body_verificaciones" class="modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>
<!-- Modal-->
<div class="modal fade" id="ModalReferencias" tabindex="-1" role="dialog" aria-labelledby="ModalReferencias" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ModalReferenciasTitle">Referencias Personales</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div id="body_referencias" class="modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" onclick="referencias_agregar('<?= $_SESSION['cuenta'];?>')">Agregar</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>				

<!-- Modal-->
<div class="modal fade" id="ModalProductos" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="ModalProductos" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ModalProductosTitle">Lista de Productos</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div id="body_productos" class="modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>	

<!-- Modal -->
<div class="modal fade" id="ModalCrearCliente" tabindex="-1" role="dialog" aria-labelledby="ModalCrearClienteTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ModalCrearClienteTitle">Convertir Prospecto</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="resultado">

				<div class="form-group">
					<label for="pros_cuenta">Cuenta</label>
					<input type="text" class="form-control form-control-sm" aria-describedby="cuentaHelp" value="<?= $datos['cuenta'].' '.$datos['cliente'];?>" readonly="readonly">
					<input type="text" id="pros_cuenta" value="<?= $datos['cuenta'];?>" hidden>
				</div>

				<div class="form-group">
					<label for="pros_documento">Documento</label>
					<input type="text" id="pros_documento" class="form-control form-control-sm" aria-describedby="cuentaHelp" value="<?= $datos['documento'];?>">
				</div>


				<div class="form-group">
					<label for="pros_nombre">Nombres</label>
					<input type="text" id="pros_nombre" class="form-control form-control-sm" aria-describedby="nombreHelp" value="">
				</div>

				<div class="form-group">
					<label for="pros_apellido">Apellidos</label>
					<input type="text" id="pros_apellido" class="form-control form-control-sm" aria-describedby="nombreHelp" value="">
				</div>

				<div class="form-group">
					<label for="pros_fecha_nacimiento">Fecha Nacimiento</label>
					<input 
					type="date" 
					class="form-control form-control-sm"
					id="pros_fecha_nacimiento" 
					value="<?= $fecha_nac_final;?>"
					min="<?= $fecha_nac_inicial;?>" max="<?= $fecha_nac_final;?>" 
					required>
				</div>

				<div class="form-group">
					<label for="pros_celula">Teléfono/Celular</label>
					<input type="text" id="pros_celular" class="form-control form-control-sm" aria-describedby="celularHelp" value="<?= $datos['celular'];?>">
				</div>

				<div class="form-group">
					<label for="pros_direccion">Dirección particular</label>
					<input type="text" id="pros_direccion" class="form-control form-control-sm" aria-describedby="particularHelp" value="<?= $datos['direccion'];?>">
				</div>

				<div class="form-group">
					<label for="pros_laboral">Empresa Laboral</label>
					<input type="text" id="pros_laboral" class="form-control form-control-sm" aria-describedby="comercialHelp">
				</div>

				<div class="form-group">
					<label for="pros_telefono">Teléfono Laboral</label>
					<input type="text" id="pros_telefono" class="form-control form-control-sm" aria-describedby="telefonoHelp" value="">
				</div>

				<div class="form-group">
					<label for="pros_salario">Salario</label>
					<input type="number" id="pros_salario" class="form-control form-control-sm" aria-describedby="telefonoHelp" value="">
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" onclick="convertir_prospecto()">Procesar</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>
<div id="contenedor_resultado"></div>
<?php
require('../../footer.php'); 
?>
<script>

	function datos_clientes(cuenta){
		$('#ModalDatosClientes').modal('show');
		$.ajax({
			type:'POST',
			url:"datos_clientes.php",
			data:{
				cuenta : cuenta
			},
			success:function(resp){
				$("#body_datos_clientes").html(resp);
			}
		});
	}



	function lista_productos(){
		$('#ModalProductos').modal('show');
		$.ajax({
			type:'POST',
			url:"lista_productos_gral.php",
			data:{
				accion : 'consultar'
			},
			success:function(resp){
				$("#body_productos").html(resp);
			}
		});
	}

	function verificaciones(cuenta){

		$('#ModalVerificaciones').modal('show');
		$.ajax({
			type:'POST',
			url:"verificaciones.php",
			data:{
				accion : 'consultar',
				cuenta : cuenta
			},
			success:function(resp){
				$("#body_verificaciones").html(resp);
			}
		});
	}
	
	function referencias(cuenta){

		$('#ModalReferencias').modal('show');
		$.ajax({
			type:'POST',
			url:"referencias.php",
			data:{
				accion : 'consultar',
				cuenta : cuenta
			},
			success:function(resp){
				$("#body_referencias").html(resp);	
			}
		});
	}

	function referencias_agregar(cuenta){

		var verificado = 0;
		var nombre 	  = document.getElementById('refencia_nombre').value;
		var relacion  = document.getElementById('refencia_relacion').value;;
		var telefono  = document.getElementById('refencia_telefono').value;;

		if(nombre.length<=5 || telefono.length<=5){
			if(nombre.length<=5){
				alert('El valor en NOMBRE REFERENCIA es muy corto.');
			}else{
				alert('Verifique el número de teléfono.');
			}
			verificado = 1;
		}

		if(relacion.length>=16){
			alert('El valor de RELACION es muy largo.');
			verificado = 1;
		}

		if(relacion.length==0){
			relacion = "";
		}

		if(verificado==0){
			$.ajax({
				type:'POST',
				url:"referencias.php",
				data:{
					accion  : 'agregar',
					cuenta  : cuenta, 
					nombre  : nombre,
					relacion: relacion,
					telefono:telefono
				},
				success:function(resp){
					referencias(cuenta);
				}
			});
		}
	}

	function referencias_quitar(valor){

		valor = valor.split('-');
		var id = valor[0];
		var cuenta = valor[1];
		var r = confirm("Desea eliminar la referencia?");
		if (r == true) {
			$.ajax({
				type:'POST',
				url:"referencias.php",
				data:{
					accion : 'quitar',
					cuenta : cuenta, 
					id 	   : id
				},
				success:function(resp){
					referencias(cuenta);
				}
			});
		}
	}

	function convertir_prospecto(){
		var api_key 			= document.getElementById('api_key').innerHTML;
		var cuenta 				= document.getElementById('pros_cuenta').value;
		var documento 			= document.getElementById('pros_documento').value.trim();
		var nombres 			= document.getElementById('pros_nombre').value.trim();
		var apellidos 			= document.getElementById('pros_apellido').value.trim();
		var fecha_nacimiento 	= document.getElementById('pros_fecha_nacimiento').value.trim();
		var celular_particular 	= document.getElementById('pros_celular').value.trim();
		var telefono_laboral 	= document.getElementById('pros_telefono').value.trim();
		var empresa 			= document.getElementById('pros_laboral').value.trim();
		var salario 			= document.getElementById('pros_salario').value;
		var direccion 			= document.getElementById('pros_direccion').value.trim();
		var valido 				= 0;


		if(!documento || documento.length<=4){
			alert("Ingrese la CI o RUC del cliente");
			valido = 1;
		}

		if(!nombres || nombres.length<=4){
			alert("Debe ingresar los Nombres");
			valido = 1;
		}

		if(!apellidos || apellidos.length<=4){
			alert("Debe ingresar los Apellidos");
			valido = 1;
		}
		if(!fecha_nacimiento || fecha_nacimiento.length==0){
			alert("Debe ingresar la Fecha de Nacimiento");
			valido = 1;
		}

		if(!celular_particular || celular_particular.length<=4){
			alert("Debe ingresar el Celular/Teléfono");
			valido = 1;
		}

		if(!direccion || direccion.length<=8){
			alert("Debe ingresar la Direccion Particular");
			valido = 1;
		}

		if(!empresa || empresa.length<=4){
			alert("Debe ingresar el Lugar de Trabajo");
			valido = 1;
		}

		if(!telefono_laboral || telefono_laboral.length<=4){
			alert("Debe ingresar los Teléfono Laboral");
			valido = 1;
		}

		if(!salario || salario.length<=4){
			alert("Debe ingresar el Salario");
			valido = 1;
		}
		
		if(valido == 0){
			$.ajax({
				type:'POST',
				url:"https://intranet.facilandia.com.py/api/v2/index.php/clientes",
				headers: {Authorization: api_key,
					'Content-Type': 'application/x-www-form-urlencoded'},
					data:{
						accion : 'convertir',
						cuenta : cuenta,
						documento : documento,
						nombres : nombres,
						apellidos : apellidos,
						fecha_nacimiento : fecha_nacimiento,
						celular_particular : celular_particular,
						direccion : direccion,
						empresa : empresa, 
						telefono_laboral : telefono_laboral,
						salario : salario
					},
					success:function(resp){
						console.log(resp);
						$("#contenedor_resultado").html(resp);
						window.location.replace("gestion.php");

					}
				});
		}
	}	
</script>

<script>
function desbloquear(elemento) {

		//console.log(elemento);
		document.getElementById(elemento).readOnly = false;
		document.getElementById(elemento+'_editar').classList.remove("d-none");

	

/*		
		document.getElementById('nombre1').readOnly = false;
		document.getElementById('nombre2').readOnly = false;		
		document.getElementById('apellido1').readOnly = false;		
		document.getElementById('apellido2').readOnly = false;		
		document.getElementById('apellido3').readOnly = false;		
		document.getElementById('ruc').readOnly = false;
		document.getElementById('fecha_nac').readOnly = false;

		document.getElementById('calle_part').readOnly = false;
		document.getElementById('numero_part').readOnly = false;
		document.getElementById('esquina_part').readOnly = false;			
		document.getElementById('grupo_part').readOnly = false;
		document.getElementById('telefono_part').readOnly = false;
		document.getElementById('celular_part').readOnly = false;
		document.getElementById('fax_part').readOnly = false;			
		document.getElementById('email_part').readOnly = false;

		document.getElementById('ingreso_laboral').readOnly = false;
		document.getElementById('horario_laboral1').readOnly = false;
		document.getElementById('horario_laboral2').readOnly = false;
		document.getElementById('calle_laboral').readOnly = false;
		document.getElementById('numero_laboral').readOnly = false;
		document.getElementById('esquina_laboral').readOnly = false;
		document.getElementById('telefono_laboral').readOnly = false;
		document.getElementById('fax_laboral').readOnly = false;
		document.getElementById('email_laboral').readOnly = false;

		document.getElementById('nombre1_editar').removeAttribute("d-none");		
		document.getElementById('nombre2_editar').classList.add("d-none");
		document.getElementById('apellido1_editar').classList.add("d-none");		
		document.getElementById('apellido2_editar').classList.add("d-none");	
		document.getElementById('apellido3_editar').classList.add("d-none");	
		document.getElementById('ruc_editar').classList.add("d-none");

		document.getElementById('calle_part_editar').classList.add("d-none");
		document.getElementById('numero_part_editar').classList.add("d-none");
		document.getElementById('esquina_part_editar').classList.add("d-none");			
		document.getElementById('grupo_part_editar').classList.add("d-none");
		document.getElementById('telefono_part_editar').classList.add("d-none");
		document.getElementById('celular_part_editar').classList.add("d-none");
		document.getElementById('fax_part_editar').classList.add("d-none");			
		document.getElementById('email_part_editar').classList.add("d-none");

		document.getElementById('ingreso_laboral_editar').classList.add("d-none");
		document.getElementById('horario_laboral1_editar').classList.add("d-none");
		document.getElementById('horario_laboral2_editar').classList.add("d-none");
		document.getElementById('calle_laboral_editar').classList.add("d-none");
		document.getElementById('numero_laboral_editar').classList.add("d-none");
		document.getElementById('esquina_laboral_editar').classList.add("d-none");
		document.getElementById('telefono_laboral_editar').classList.add("d-none");
		document.getElementById('fax_laboral_editar').classList.add("d-none");
		document.getElementById('email_laboral_editar').classList.add("d-none");

		document.getElementById(elemento).readOnly = false;
		document.getElementById(elemento+'_editar').removeAttribute("d-none");*/
	}

	function guardar(elemento, zona){

		var campo;
		var cuenta = document.getElementById('cuenta').value;
		var valor = document.getElementById(elemento).value;

		

		switch(elemento) {
			case 'nombre1'  : campo = 'aanom1'; break;
			case 'nombre2'  : campo = 'aanom2';break;
			case 'apellido1': campo = 'aaape1'; break;
			case 'apellido2': campo = 'aaape2'; break;
			case 'apellido3': campo = 'aaape3'; break;
			case 'pais'		: campo = 'ahpais'; break;
			case 'sexo'		: campo = 'aasexo'; break;
			case 'fecha_nac': campo = 'aafech'; break;
			case 'sector'	: campo = 'ansect'; break;
			case 'actividad': campo = 'alacti'; break;
			case 'ips'		: campo = 'aavivi'; break;
			case 'ruc'		: campo = 'aaruc'; break;
			case 'estado_civil': campo = 'aaesta'; break;
			case 'reg_conyugal': campo = 'aaregi'; break;

			case 'departamento_part': campo = 'aidept'; break;
			case 'ciudad_part': campo = 'apciud'; break;
			case 'barrio_part': campo = 'ajbarr'; break;
			case 'calle_part': campo = 'awcalle'; break;
			case 'numero_part': campo = 'awnume'; break;
			case 'esquina_part': campo = 'awesq'; break;
			case 'vivienda_part': campo = 'awprop'; break;
			case 'grupo_part': campo = 'awinte'; break;
			case 'habita_part': campo = 'awdesd'; break;
			case 'telefono_part': campo = 'awtel1'; break;
			case 'celular_part': campo = 'awcelu'; break;
			case 'fax_part': campo = 'awfax'; break;
			case 'email_part': campo = 'awemai'; break;

			case 'departamento_laboral': campo = 'aidept'; break;
			case 'ciudad_laboral': campo = 'apciud'; break;
			case 'barrio_laboral': campo = 'ajbarr'; break;
			case 'cargo_laboral': campo = 'amcarg'; break;
			case 'fecha_laboral': campo = 'aaifec'; break;			
			case 'ingreso_laboral': campo = 'basala'; break;
			case 'horario_laboral1': campo = 'bahord'; break;
			case 'horario_laboral2': campo = 'bahora'; break;			
			case 'calle_laboral': campo = 'bacalle'; break;
			case 'numero_laboral': campo = 'banume'; break;
			case 'esquina_laboral': campo = 'baesq'; break;
			case 'telefono_laboral': campo = 'batel1'; break;
			case 'fax_laboral': campo = 'bafax'; break;
			case 'email_laboral': campo = 'baemai'; break;
			case 'empresa_laboral': campo = 'aaempr'; break;
		}

		//console.log(elemento,zona,cuenta,campo,valor);
		
		
		$.ajax({
			type:'POST',
			url:"modificacion_datos.php",
			data:{
				zona : zona,
				cuenta : cuenta, 
				campo : campo,
				valor : valor
			},
			success:function(resp){
			document.getElementById(elemento).readOnly = true;
				if(	elemento=='nombre1' || elemento=='nombre2' || elemento=='apellido1' || elemento=='apellido2' || elemento=='apellido3' || elemento=='ruc' || elemento=='calle_part'|| elemento=='esquina_part'|| elemento=='numero_part'|| elemento=='grupo_part'|| elemento=='telefono_part'||	elemento=='celular_part'|| elemento=='fax_part'|| elemento=='email_part' || elemento=='ingreso_laboral'||	elemento=='horario_laboral1'|| elemento=='horario_laboral2'|| elemento=='calle_laboral'||
					elemento=='numero_laboral'|| elemento=='esquina_laboral'|| elemento=='telefono_laboral'|| elemento=='fax_laboral'||	elemento=='email_laboral'){
					document.getElementById(elemento+'_editar').classList.add("d-none");	

			}

			
		}
	});

	}




</script>