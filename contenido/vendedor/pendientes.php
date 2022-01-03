<?php 
	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	require('../../header.php');
	require( CONTROLADOR . 'vendedores.php');

	$vendedor = new Vendedores();
	$vendedor->vendedor = $_COOKIE['usuario'];

	if(isset($_POST['operacion']) && isset($_POST['comentario']) && strlen($_POST['comentario'])>=10){

		$vendedor->gestor 		= $_COOKIE['usuario'];		
		$vendedor->comentario 	= $_POST['comentario'];
		$vendedor->operacion 	= $_POST['operacion'];		
		$vendedor->levantar_condicionado();
	}

	$pendientes = $vendedor->pendientes();
	unset($_SESSION);	
?>
<br>
<div class="container">
	<nav aria-label="breadcrumb">
	  <ol class="breadcrumb">
	    <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
	    <li class="breadcrumb-item active" aria-current="page">Operaciones Pendientes</li>
	  </ol>
	</nav>
	<?php
		for ($i=0; $i < count($pendientes); $i++) {
			$class_div = ($pendientes[$i]['cod_estado']==12) ? 'alert alert-warning':'alert alert-success';
			echo '<div class="'.$class_div.'">';		
	?>

			<table class="table table-sm">
				<tr>
					<td colspan="3"><span class="text-primary"><?= $pendientes[$i]['cuenta'].' - '.$pendientes[$i]['cliente'];?></span></td>
				</tr>
				<tr>
					<td><span class="text-muted"><b>operacion</b></span></td>
					<td align="left" colspan="2"><?= $pendientes[$i]['operacion'];?></td>
				</tr>							
				<tr>
					<td><span class="text-muted"><b>estado</b></span></td>
					<td align="left" colspan="2"><?= $pendientes[$i]['estado'];?></td>
				</tr>							
				<tr>
					<td><span class="text-muted"><b>total<b></span></td>
					<td align="right" colspan="2"><?= number_format($pendientes[$i]['total'],0,',','.') ?></td>
				</tr>							
				<tr>
					<td><span class="text-muted"><b>motivo</b></span></td>
					<td align="left" colspan="2"><?= $pendientes[$i]['fecha_motivo'].' '.$pendientes[$i]['motivo'];?></td>
				</tr>						
			</table>

	<?php 
		if($pendientes[$i]['cod_estado']==12){
	?>	
			<button type="button" class="btn btn-primary" data-toggle="modal" data-target="<?= '#operacion'.$pendientes[$i]['operacion'];?>">
			  Responder Condicionado
			</button>

			<!-- Modal -->
			<div class="modal fade" id="<?= 'operacion'.$pendientes[$i]['operacion'];?>" tabindex="-1" role="dialog" aria-labelledby="<?= 'operacionlabel'.$pendientes[$i]['operacion'];?>" aria-hidden="true">
			  <div class="modal-dialog modal-dialog-centered" role="document">
				<form action="" method="POST">
			    <div class="modal-content">
			      <div class="modal-header">
			        <h5 class="modal-title" id="<?= 'operacionlabel'.$pendientes[$i]['operacion'];?>">Responder Condicionado</h5>
			        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
			          <span aria-hidden="true">&times;</span>
			        </button>
			      </div>
			      <div class="modal-body">
			        <p class="text-muted"><?= $pendientes[$i]['fecha_motivo'].'<br>'.$pendientes[$i]['motivo'];?></p>
			        <br>
			        <br>
			        <p class="text-muted">Comentario:</p>
			        <textarea name="comentario" id="comentario" rows="5" style="width: 100%;"></textarea>

			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
			      </div>
			    </div>
				<input type="text" name="operacion" id="operacion" value="<?= $pendientes[$i]['operacion'];?>" hidden>
				</form>
			  </div>
			</div>
	<?php 
		}
	?>
		</div>

	<?php
		}	
	 ?>
</div>
<?php 
	require('../../footer.php'); 
?>