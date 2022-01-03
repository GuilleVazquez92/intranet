<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

</head>
<body>
	<style>

		.form-control {
			display: block;
			width: 100%;
			height: calc(1.5em + .75rem + 2px);
			padding: .375rem .75rem;
			font-size: 1rem;
			font-weight: 400;
			line-height: 1.5;
			color: #495057;
			/*background-color: #fff;*/
			background-clip: padding-box;
			border: 1px solid #ced4da;
			border-radius: .25rem;
			transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
		}

		button{
			color: #fff;
			background-color: #911e7e !important; 
			border-radius: 5px;
			padding: 5px 30px;
			border: none;
			text-transform: uppercase;
			display: block;
			margin: 15px auto;
			float: right;
			text-decoration: none;
			font-size: .9rem;
			height: 35px;
			line-height: 1.75;
		}

		button:hover{
			background-color: #ff5a00 !important;
		}

		label{
			font-family: 'Lato', sans-serif;
			font-size: .85rem;
			font-weight: 400;
			color: #333;
		}


	</style>
	<?php
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	$cuenta = $_GET['cuenta'];
	?>
	<div class="container-fluid">
		
		<div id="alert" class="alert" role="alert">&nbsp;</div>

		<form id="AdjuntarArchivo">
			<input type="text" id="cuenta" name="cuenta" value="<?= $cuenta;?>" hidden>
			<div class="form-group">
				<label for="tipo_documento">Tipo de Documento</label>
				<select name="tipo_documento" id="tipo_documento" class="form-control" onchange="verificar_form()">
					<option value="0">Seleccione una opción</option>
					<option value="1">Cedula de Identidad</option>
					<option value="2">Servicio Básico</option>
					<option value="3">Certificado Laboral</option>
					<option value="12">Comprobante de Ingreso</option>	
				</select>
			</div>	

			<div class="form-group">	
				<label for="fileToUpload">Imagen en (JPG, JPEG, GIF, PNG)</label>
				<input type="file" name="fileToUpload" id="fileToUpload" class="form-control" onchange="verificar_form()">
			</div>	
			<button type="button" id="agregar_pdf" class="btn">Subir Imagen</button>

		</form>					
	</div>




	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>

	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

	<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
	<script>


		function verificar_form(){
			var tipo = document.getElementById('tipo_documento').value;
			var file = document.getElementById('fileToUpload').value;
			var estado= 1;

			if(tipo==0)			estado = 0;
			if(file.length==0)	estado = 0;

			if(estado==1){
				$("#agregar_pdf").prop('disabled', false);		

			}else{

				$("#agregar_pdf").prop('disabled', true);
			}
		}

		$(document).ready(function(){
			$("#agregar_pdf").prop('disabled', true);

			$('#agregar_pdf').click(function(e){
				e.preventDefault();
				var form = $('#AdjuntarArchivo')[0];
				var data = new FormData(form);
				return $.ajax({
					type: "POST",
					enctype: 'multipart/form-data',
					url: "upload_ext.php",
					data: data,
					processData: false,
					contentType: false,
					cache: false,
					timeout: 600000,
					success: function (data) {

						$("#alert").hide();	
						
						if($.trim(data)!="Error"){
							$("#alert").html("Se envio el archivo correctamente.").addClass("alert-success").fadeIn(2000);
							
						}else{
							$("#alert").html("Ocurrio un error al subir el archivo.").addClass("alert-danger").fadeIn(1000);
							
						}
						
						setTimeout(function(){
							$("#alert").html('&nbsp;').fadeOut(3500).removeClass("alert-danger alert-success").show();
						
						}, 3500);

					}
				});
			});
		});	

	</script>
</body>
</html>