<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); 

if(isset($_POST['operacion']) && isset($_POST['estado'])){

	require('../../controlador/main.php');
	require( CONTROLADOR . 'ventas.php');

	$data = new Ventas();
	$data->operacion = $_POST['operacion'];
	$data->estado = $_POST['estado'];
	$datos = $data->consultar_operacion_motivo();
	//var_dump($datos);
}

?>

<input type="text" id="operacion" value="<?= $_POST['operacion'];?>" hidden>
<p class="text-secondary font-weight-bold m-0"><?= $datos[0]['observacion'];?></p>
<p class="text-primary mb-2"><?= $datos[0]['descripcion'];?></p>

<!-- <?php 
if($data->estado==12){ 
	?>
	
	<h6>Comentario:</h6>
	<textarea class="mb-2" name="comentario" id="comentario" rows="5" style="width: 100%;"></textarea>
	<button type="button" id="responder" class="btn btn-primary">Responder</button>

	<?php 
} 
?> -->

<script>

	$(document).ready(function() {
		$("#responder").prop('disabled',true);

		$('#comentario').keyup(function() {

			var min_chars = 10;
			var chars = $(this).val().length;
			if(chars>=min_chars){
				$("#responder").prop('disabled',false);
				
			}else{
				$("#responder").prop('disabled',true);
			}
		});
	});

	
	$("#responder").click(function(){

		var operacion 	= $("#operacion").val();
		var comentario 	= $("#comentario").val();

		$.ajax({
			type:'POST',
			url:"../vendedor/condicionados_responder.php",
			data:{
				operacion : operacion, 
				comentario : comentario
			},
			success:function(resp){
				location.reload();
			}
		});
	});

</script>