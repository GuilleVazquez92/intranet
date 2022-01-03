<?php
	/** INICIA FILTRO DE FECHA**/
	$fecha_actual = date('Y-m-d');

	if (isset($_POST['consultar'])) {
		
		$resultado 				= ($_POST['fecha_inicial']>$_POST['fecha_final']) ? 1 : 0;
		$convenio->fecha_inicial= $fecha_valor_minimo = $_POST['fecha_inicial'];	
		$convenio->fecha_final 	= $fecha_valor_maximo = $_POST['fecha_final'];

	}else{

		$convenio->fecha_inicial = $fecha_valor_minimo = date('Y-m-d',strtotime($fecha_actual."- 15 days"));	
		$convenio->fecha_final 	 = $fecha_valor_maximo = $fecha_actual;

	}	
?>
		<form action="" method="POST">
			<div class="form-group row">
				<label for="fecha_inicial" class="col-sm-1 col-form-label">Fecha Inicial</label>
				<div  class="col-sm-1">
					<input
						id="fecha_inicial"
						name="fecha_inicial"  
						type="date" 
						placeholder="Fecha Inicial" 
						required="required" 
						min='<?= $_COOKIE["fecha_inicio"]; ?>' 
						max='<?= $fecha_actual; ?>' 
						value='<?= $fecha_valor_minimo; ?>'
						<?= $filtro_fecha; ?>>
				</div>

				<label for="fecha_final" class="col-sm-1 col-form-label"><center>Fecha Final</center></label>	
				<div  class="col-sm-1">
					<input 
						id="fecha_final"
						name="fecha_final"			 
						type="date" 
						placeholder="Fecha Final" 
						required="required" 
						min='<?= $_COOKIE["fecha_inicio"]; ?>' 
						max='<?= $fecha_actual; ?>' 
						value='<?= $fecha_valor_maximo; ?>'
						<?= $filtro_fecha; ?>>
				</div>
				<div class="col-sm-8">
					<button type="submit" class="" name="consultar" <?= $filtro_fecha; ?>>Consultar</button>
				</div>	
			</div>
		</form>
		<?php 	
			if(isset($resultado)){
				if($resultado==0){
		 ?>	
					<div class="alert alert-success" role="alert">
					  Se realizo la consulta correctamente!
					</div> 
		<?php 
				}else{	
		 ?>				
				<div class="alert alert-danger" role="alert">
				  La fecha inicial no puede ser mayor a la fecha final. Por favor verifique las fechas sean correctas!
				</div> 
		<?php 
				}
			}

		/* FIN FILTRO DE FECHA*/
?>		