<?php
	require('../../controlador/main.php');
	require( CONTROLADOR . 'vendedores.php');
	$vendedor = new Vendedores();

	if ($_POST['accion'] =='buscar') {

		$vendedor->documento = $_POST['documento'];	
		$buscar  = $vendedor->prospecto_buscar();
	
		if(count($buscar)==0){
?>	
		<form>
		  <div class="form-group">
		    <label for="documento" class="sr-only">Documento</label>
		    <input type="text" class="form-control" id="documento" aria-describedby="cuentaHelp" value="<?= $_POST['documento'];?>" readonly="readonly">
		  </div>

		  <div class="form-group">
		    <label for="nombre" class="sr-only">Nombre del prospecto</label>
		    <input type="text" class="form-control" id="nombre" aria-describedby="nombreHelp" placeholder="Nombre del prospecto">
		  </div>

		  <div class="form-group">
		    <label for="particular" class="sr-only">Dirección particular</label>
		    <input type="text" class="form-control" id="particular" aria-describedby="particularHelp" placeholder="Dirección particular">
		  </div>

		  <div class="form-group">
		    <label for="comercial" class="sr-only">Dirección comercial</label>
		    <input type="text" class="form-control" id="comercial" aria-describedby="comercialHelp" placeholder="Dirección comercial">
		  </div>

		  <div class="form-group">
		    <label for="telefono" class="sr-only">Teléfono</label>
		    <input type="text" class="form-control" id="telefono" aria-describedby="telefonoHelp" placeholder="Teléfono">
		  </div>

		  <div class="form-group">
		    <label for="celular" class="sr-only">Celular</label>
		    <input type="text" class="form-control" id="celular" aria-describedby="celularHelp" placeholder="Celular">
		  </div>

		  <button type="button" class="btn btn-primary" onclick="crear_prospecto()">Guardar</button>
		</form>
<?php 
		}else{
?>		
		  <div class="form-group">
		    <label for="cuenta">Cuenta</label>
		    <input type="text" class="form-control" aria-describedby="cuentaHelp" value="<?= $buscar['cuenta'] ;?>" readonly="readonly">
		  </div>

		  <div class="form-group">
		    <label for="nombre">Nombre del prospecto</label>
		    <input type="text" class="form-control" aria-describedby="nombreHelp" value="<?= $buscar['cliente'];?>" readonly="readonly">
		  </div>

		  <div class="form-group">
		    <label for="particular">Dirección particular</label>
		    <input type="text" class="form-control" aria-describedby="particularHelp" value="<?= $buscar['particular'];?>" readonly="readonly">
		  </div>

		  <div class="form-group">
		    <label for="comercial">Dirección comercial</label>
		    <input type="text" class="form-control" aria-describedby="comercialHelp" value="<?= $buscar['comercial'];?>" readonly="readonly">
		  </div>

		  <div class="form-group">
		    <label for="telefono">Teléfono</label>
		    <input type="text" class="form-control" aria-describedby="telefonoHelp" value="<?= $buscar['telefono'];?>" readonly="readonly">
		  </div>

		  <div class="form-group">
		    <label for="celular">Celular</label>
		    <input type="text" class="form-control" aria-describedby="celularHelp" value="<?= $buscar['celular'];?>" readonly="readonly">
		  </div>

<?php		
		}
	}else{
		if ($_POST['accion'] == 'crear' ) {

			$vendedor->gestor 		= $_COOKIE['usuario'];	
			$vendedor->documento 	= $_POST['documento'];
			$vendedor->nombre 	 	= $_POST['nombre'];
			$vendedor->particular 	= $_POST['particular'];
			$vendedor->comercial 	= $_POST['comercial'];	
			$vendedor->telefono 	= $_POST['telefono'];
			$vendedor->celular 		= $_POST['celular'];
			$vendedor->gestor 		= $_COOKIE['usuario'];
			$crear 					= $vendedor->prospecto_crear();

			if($crear>=1){
?>
				<div class="alert alert-success" role="alert">
  					Se creó correctamente el prospecto!...
				</div>

<?php
			}else{
?>				
				<div class="alert alert-success" role="alert">
				  Se creó correctamente el prospecto!...
				</div>
<?php
			}
		}
	}

?>		
