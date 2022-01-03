<?php
	/** INICIA FILTRO DE FECHA**/
	$fecha_actual = date('Y-m-d');

	if (isset($_POST['consultar'])) {
		 $fecha_valor = $_POST['fecha_inicial'];
		 $resultado   =  0;
	}else{
		$fecha_valor = $fecha_actual;
	}	
?>
	<form class="form-inline" method="POST">
			<label class="my-1 mr-2" for="fecha_inicial"><small class="form-text text-muted">Fecha inicial</small></label>
			<input class="form-control form-control-sm" id="fecha_inicial" name="fecha_inicial" type="date" 
					placeholder="Fecha Inicial" required="required" max='<?= $fecha_actual; ?>' value='<?= $fecha_valor; ?>'>

	  	<div class="custom-control my-1 mr-sm-2">
	  		<button type="submit" class="btn btn-primary my-1 btn-sm" name="consultar">Consultar</button>
		</div>
	</form>

	<?php 	
		if(isset($resultado)){
	 ?>	
			<div class="alert alert-success" role="alert"> Se realizo la consulta correctamente!</div> 
	<?php 
		}
	?>