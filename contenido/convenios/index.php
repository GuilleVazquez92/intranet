<?php 

require('../../header.php');
require( CONTROLADOR . 'convenio.php');
$convenio = new Convenios();
$variable = ""; 
$id = 0;
if(isset($_POST['sel_id'])){

	$convenio->id = $_POST['sel_id'];			
	$datos = $convenio -> consultar_convenio($_POST['sel_id']);

	for ($i=0; $i < count($datos); $i++) { 

		$id = $datos[$i]['id'];

		setcookie("id",$datos[$i]['id'],time()+86400);
		setcookie("alianza",$datos[$i]['alianza'],time()+86400);	
		setcookie("deposito",$datos[$i]['deposito'],time()+86400);
		setcookie("fecha_inicio",$datos[$i]['fecha_inicio'],time()+86400);
		$variable = '<option value="'.$datos[$i]['id'].'" selected>'.$datos[$i]['alianza'].'</option>';
	}
}
?>
<br>
<br>
<div class="container">
	<?php 

	if(!isset($_COOKIE['id']) || $_COOKIE['cod_perfil']!=9991){
	
		$datos = $convenio->consultar_convenio(9999);
		for ($i=0; $i <count($datos) ; $i++) { 

			if($id==0 || $id!=$datos[$i]['id'])
				$variable .= '<option value="'.$datos[$i]['id'].'">'.$datos[$i]['alianza'].'</option>';	
		}
		?>		
		<div class="jumbotron jumbotron-fluid">
			<div class="container">
				<h1 class="display-4">Alianzas estratégicas</h1>
				<p class="lead"></p>
				<hr class="my-4">
				<form method="POST">	
					<div class="form-group col-md-4">
						<label for="sel_id">Elige un perfil para trabajar.</label>
						<select id="sel_id" name="sel_id" class="form-control form-control-lg" onchange="this.form.submit();" required="required">
							<option>Selecciona...</option>
							<?php 
								echo $variable;
							?>	
						</select>
					</div>
				</form>
			</div>
		</div>
	<?php
	}
	?>
</div>
<?php 
	//var_dump($_COOKIE);
require('../../footer.php'); 
?>
