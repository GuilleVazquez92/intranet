<?php
	require('header.php');
?>
	<div class="container">
		<br>
		<br>
<?php 
	if(!isset($_COOKIE['cod_perfil'])){
?>
		<div class="row">
			<div class="col-md-6  offset-md-3">
		<?php 
				if(strlen($mensaje_error)>0){ 
		?>
					<div class="alert alert-danger" role="alert"><?= $mensaje_error; ?></div>
		<?php 
				} 			

				if(strlen($mensaje_warning)>0){ 
		?>
					<div class="alert alert-warning" role="alert"><?= $mensaje_warning; ?></div>
		<?php 	
				} 
		?>
				<form action="index.php" class="form" method="POST">
					
					<div class="form-group">
						<label for="usuario" class="sr-only">Usuario</label>
						<input type="text" class="form-control" id="usuario" name="usuario" placeholder="Usuario" required="">
					</div>
					
					<div class="form-group">
						<label for="password" class="sr-only">Contraseña</label>
						<input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required="">
					</div>
					
					<div class="form-group">
						<button type="submit" name="logeo" class="btn btn-primary">Ingresar</button>		
					</div>
				</form>

			</div>
		</div>
	</div>
<?Php
	}else{
?>		
		<div class="jumbotron jumbotron-fluid">
		  <div class="container">
		    <h1 class="display-4">Bienvenido a la Intranet</h1>
		    <p class="lead">Esperamos que sea un instrumento que facilite tu trabajo diario.</p>
		  </div>
		</div>
<?php
	}
	require('footer.php');
?>
