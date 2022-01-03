<?php 
	require_once('../header.php'); 
	if(isset($_POST['mostrar'])){

		echo $mostrar = $_POST['mostrar'];

	}
?>

<h1>It's Works!!!</h1>

<form action="" method="POST">
	<input type="text" name="mostrar">
	<button type="submit">Enviar</button>
</form>





<?php require_once('../footer.php'); ?>