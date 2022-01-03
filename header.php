<?php 
	require('controlador/main.php');
?>
<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
		<title><?= (!isset($_COOKIE['perfil'])) ? $titulo : $_COOKIE['perfil'];?></title>
		<link rel="stylesheet" href="<?= CSS.'bootstrap.min.css';?>">
		<link rel="stylesheet" href="<?= CSS.'font-awesome.min.css';?>">
		<link rel="stylesheet" href="<?= CSS.'estilo.css?version=1.6';?>">
		<link rel="shortcut icon" href="<?= ROOT.'favicon.ico';?>">
	</head>

	<body>
	<nav class="navbar navbar-expand-lg navbar-light">

		<a class="navbar-brand" href="<?= ROOT.'index.php' ?>">
	  		<img src="<?= IMAGE .'logo100X100.png';?>" width="35" height="35" class="d-inline-block align-top" alt="" />
	  		<?= (!isset($_COOKIE['perfil'])) ? $titulo : $_COOKIE['perfil'];?>
		</a>
<?php 
	if(isset($_COOKIE['cod_perfil'])){
?>		
		<button class="navbar-toggler" 
				type="button" 
				data-toggle="collapse" 
				data-target="#navbarContent" 
				aria-controls="navbarContent" 
				aria-expanded="false" 
				aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
	  	</button>

	<!-- Menu de usuarios -->
	  	<div class="collapse navbar-collapse" id="navbarContent">
			<ul class="navbar-nav mr-auto">
		<?php
			# Menu de usuarios
			$user_menu = $user->user_menu();
			$menu  = "";

			for ($i=0; $i < count($user_menu) ; $i++){

				if($menu != $user_menu[$i]['menu']){
					if($i!=0){
				        echo	"</div>";
				      	echo "</li>";		
					}
				$menu = $user_menu[$i]['menu'];
		?>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" 
						id="navbarDropdown" 
						role="button" 
						data-toggle="dropdown" 
						aria-haspopup="true" 
						aria-expanded="false">
						<?= $menu; ?>
					</a>
	
					<div class="dropdown-menu" aria-labelledby="navbarDropdown">
	<?php
				}
				# Items de menus
	?>	
					<a class="dropdown-item" href="<?= ROOT.$user_menu[$i]['enlace'];?>"><?= $user_menu[$i]['sub_menu'];?></a>				
	<?php
			}
	?>
			</ul>
			<!-- Fin de menu de usuarios -->					
			<ul class="navbar-nav navbar-right">
				<li class="nav-item">
					<form  action="<?= ROOT.'index.php' ?>" method="POST" class="form-inline my-2 my-lg-0">
						<div class="form-group">
							<span class="my-1 mr-2"><?= $_COOKIE['nombre'];?></span>
							<button type="submit" id="logout" name="logout" class="btn btn-warning btn-sm" value="1">Salir</button>	
						</div>
					</form>
				</li>
			</ul>		
		</div>
<?php 
	}
?>
 	</nav>