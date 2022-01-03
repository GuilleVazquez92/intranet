<?php 
//	error_reporting(E_ALL);
//	ini_set('display_errors', '1');

	require('../../header.php');
	require( CONTROLADOR . 'vendedores.php');
	$vendedor = new Vendedores();
	$vendedor->vendedor = $_COOKIE['usuario'];
	
	echo '<div class="container">';

	if(isset($_GET['cuenta'])){

		$_SESSION['cuenta'] = $_GET['cuenta'];
	}
	if(isset($_GET['filtro'])){

		$_SESSION['filtro'] = $_GET['filtro'];
	}


	if(isset($_SESSION['cuenta'])){
		
		$vendedor->cuenta 	= $_SESSION['cuenta'];

		if(isset($_POST['aceptar'])){

			$vendedor->gestor 		= $_POST['gestor'];
			$vendedor->comentario 	= $_POST['comentario'];
			$vendedor->motivo 		= $_POST['motivo'];		
			$vendedor->fecha_proximo= $_POST['fecha_proximo'];
			$vendedor->guardar_gestion();
		}

		$cliente = $vendedor->cliente();
		$fecha_inicial = date("Y-m-d");
		$fecha_final = date("Y-m-d",strtotime($fecha_inicial."+ 31 days"));

?>
	<br>

	<div style="padding:10px !important">

	<h4><?= $cliente['nombre'] ;?></h4>

	<form action="gestion.php" method="POST" class="form">
		<input type="text" value="<?= $_POST['cuenta'];?>" name="cuenta" hidden>
		<input type="text" value="<?= $cliente['gestor'] ;?>" name="gestor" hidden>
		<div class="form-group">
			<label for="comentario">Agregar Comentario</label>
			<textarea name="comentario" width="100%" rows="5" class="form-control"></textarea>	
		
		</div>
		<div class="form-group">
			<label for="fecha_proximo">Fecha de próximo llamado</label>
			<input type="date" id="fecha_proximo" name="fecha_proximo"
			       value="<?= $fecha_inicial;?>"
			       min="<?= $fecha_inicial;?>" max="<?= $fecha_final;?>" required>
			
		</div>

			<div class="form-group">
				<label for="motivo">Motivo</label>
				<select name="motivo" id="motivo" class="custom-select" required>
					<option value=""></option>
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
			<button type="submit" name="aceptar" class="btn btn-warning form-control">Aceptar</button>
		</div>
	</form>

	<form action="cartera.php" class="form">	
		<div class="form-group">	
			<button type="reset" class="btn btn-secondary form-control" onclick="this.form.submit();">Cancelar</button>	
		</div>	
	</form>		
	</div>

<?php

	}else{

		echo 'No existe numero de cuenta';

	}

	echo '</div>';

	require('../../footer.php'); 
?>