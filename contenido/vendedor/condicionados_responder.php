<?php  
error_reporting(E_ALL);
ini_set('display_errors', '1');

require('../../controlador/main.php');
require( CONTROLADOR . 'vendedores.php');

$vendedor = new Vendedores();
$vendedor->vendedor = $_COOKIE['usuario'];

if(isset($_POST['operacion'])){

	$vendedor->operacion 	= $_POST['operacion'];

	if(isset($_POST['comentario']) && strlen($_POST['comentario'])>=10){

		$vendedor->gestor 		= $_COOKIE['usuario'];		
		$vendedor->comentario 	= $_POST['comentario'];
		$vendedor->levantar_condicionado();
	}
	$datos = $vendedor->condicionado();
	?>
	<div class="container">
		<table class="table table-sm table-borderless">
			<tr>
				<td colspan="3"><span class="text-primary"><?= $datos['cuenta'].' - '.$datos['cliente'];?></span></td>
			</tr>
			<tr>
				<td><span class="text-muted"><b>operacion</b></span></td>
				<td align="left" colspan="2" id="operacion"><?= $_POST['operacion'];?></td>
			</tr>
			<tr>
				<td><span class="text-muted"><b>vendedor</b></span></td>
				<td align="left" colspan="2"><?= $datos['vendedor'];?></td>
			</tr>
			<tr>
				<td><span class="text-muted"><b>canal</b></span></td>
				<td align="left" colspan="2"><?= $datos['canal'];?></td>
			</tr>							
			<tr>
				<td><span class="text-muted"><b>total<b></span></td>
					<td align="left" colspan="2"><?= number_format($datos['total'],0,',','.') ?></td>
				</tr>							
				<tr>
					<td><span class="text-muted"><b>motivo</b></span></td>
					<td align="left" colspan="2"><?= $datos['fecha_motivo'].' '.$datos['motivo'];?></td>
				</tr>						
			</table>
			<?php
		}
		?>
		<label for="comentario" class="label text-muted">Responder Condicionado:</label>
		<textarea name="comentario" id="comentario" rows="5" style="width: 100%;"></textarea>
		<button type="button" id="responder" class="btn btn-primary" onclick="condicionados_comentario()">Responder</button>
	</div>

	<script type="text/javascript">

		$( document ).ready(function() {
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

		function condicionados_comentario(){

			var operacion 	= $("#operacion").html();
			var comentario 	= $("#comentario").val();

			$.ajax({
				type:'POST',
				url:"condicionados_responder.php",
				data:{
					operacion : operacion, 
					comentario : comentario
				},
				success:function(resp){
					location.reload();
				}
			});
		}

	</script>




