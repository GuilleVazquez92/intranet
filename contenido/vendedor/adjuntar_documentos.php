<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

require('../../header.php');
require( CONTROLADOR . 'vendedores.php');

	$vendedor 			= new Vendedores();
	$vendedor->vendedor = $_COOKIE['usuario'];
	$vendedor->cuenta 	= $_POST['cuenta'];
	$fecha_inicial	 	= date("Y-m-d");

?>
<div class="container">
	<br>
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
			<li class="breadcrumb-item"><a href="cartera.php">Cartera</a></li>
			<li class="breadcrumb-item"><a href="gestion.php">Gestión</a></li>
			<li class="breadcrumb-item active" aria-current="page">Adjuntar</li>
		</ol>
	</nav>
	<h4><?= $_POST['cuenta']." ".$_POST['cliente'] ;?></h4>
	<div class="table-responsive-sm">
		<table class="table">
			<thead>
				<tr class="table-warning">
					<th class="align-middle">Fecha</th>
					<th class="text-center align-middle">Tipo</th>
					<th class="align-middle">Documento</th>
					<th class="text-center align-middle">
					<button class="btn btn-transparent" onclick="formulario_pdf()">
							<img src="<?= IMAGE.'add-folder.png'?>" width="40px" height="40px" title="Adjuntar Documento"/>
						</button>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$ruta = 1;
				foreach ($vendedor->adjuntos() as $adjunto) { 

					if($adjunto['ruta'] != $ruta){
						?>		
						<tr class="table-warning">
							<th colspan="4">INFORMCONF</th>
						</tr>
						<?php
						$ruta = $adjunto['ruta'];
					} 
					?>
					<tr>
						<td nowrap>
							<?= $adjunto['fecha'];?>
						</td>
						<td class="text-center">
							<?= $adjunto['tipo'];?>
						</td>
						<td>
							<?= $adjunto['documento'];?>
						</td>
						<td class="text-center">
							<?php 
							$archivo  = $adjunto['archivo'];
							?>
							<i class="fa fa-paperclip mx-2 ver_pdf" aria-hidden="true" title="Archivos adjuntos" data-id="<?= $archivo;?>"  data-target="<?= $ruta;?>"></i>
						</td>	
					</tr>
					<?php 
				}
				?>
			</tbody>
		</table>
	</div>
</div>


<div class="modal fade" id="ModalAdjuntos" tabindex="-1" role="dialog" aria-labelledby="ModalCrearClienteTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="ModalCrearClienteTitle">Documentos Adjuntos</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div id="pdf-formulario" class="d-none">
					<form id="AdjuntarArchivo">
						<input type="text" name="cuenta" value="<?= $_POST['cuenta'];?>" hidden>
						<div class="form-group">
							<label for="tipo_documento">Tipo de Documento</label>
							<select name="tipo_documento" id="tipo_documento" class="form-control" onchange="verificar_form()">
								<option value="0">Selecccione una opción</option>	
								<?php 
								foreach ($vendedor->lista_documentos() as $documento) {
									?>
									<option value="<?=$documento['tipo'];?>"><?=$documento['nombre'];?></option>
									<?php
								}
								?>	
							</select>
						</div>	
						<div class="form-group">	
							<label for="fileToUpload">Imagen en (JPG, JPEG, GIF, PNG)</label>
							<input type="file" name="fileToUpload" id="fileToUpload" class="form-control" onchange="verificar_form()">
						</div>	
					</form>					
				</div>	
				<div id="pdf-resultado" class="d-none" style="height: 600px;"></div>
			</div>
			<div class="modal-footer">
				<div id="spiner" class="spinner-border text-success text-center d-none" role="status">
					<span class="sr-only">Loading...</span>
				</div>
				<button type="button" id="agregar_pdf" class="btn btn-primary d-none" onclick="agregar_pdf()">Subir Imagen</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>


<?php
require('../../footer.php');
?>
<script src="../../js/PDFObject.js"></script>
<script type="text/javascript">
	
	$(".ver_pdf").click(function(){

		var ruta 	= $(this).data('target');
		var archivo = $(this).data('id');

		if(ruta==1){
			archivo = "../../documentos/documentos/"+archivo;
		}else{
			archivo = "../../documentos/informconf/"+archivo;
		}

		$('#ModalAdjuntos').modal('toggle');
		$("#pdf-resultado").removeClass("d-none");
		$("#pdf-formulario").addClass("d-none");
		$("#agregar_pdf").addClass("d-none");
		$("#spiner").addClass("d-none");
		PDFObject.embed(archivo, "#pdf-resultado");

	});


	function verificar_form(){

		var tipo = document.getElementById('tipo_documento').value;
		var file = document.getElementById('fileToUpload').value;
		var estado= 1;

		if(tipo==0){
			estado = 0;
		}

		if(file.length==0){
			estado = 0;
		}	

		if(estado==1){
			$("#agregar_pdf").removeClass("d-none");		
			
		}else{

			$("#agregar_pdf").addClass("d-none");
		}
	}

	function formulario_pdf(){
		$('#ModalAdjuntos').modal('toggle');
		$("#pdf-resultado").addClass("d-none");
		$("#pdf-formulario").removeClass("d-none");
		$("#spiner").addClass("d-none");
	}

	function agregar_pdf(){
		event.preventDefault();
		var form = $('#AdjuntarArchivo')[0];
		var data = new FormData(form);
		$("#spiner").removeClass("d-none");
		$.ajax({
			type: "POST",
			enctype: 'multipart/form-data',
			url: "upload.php",
			data: data,
			processData: false,
			contentType: false,
			cache: false,
			timeout: 600000,
			success: function (data) {
				location.reload();
			}
		}) 
	}

</script>