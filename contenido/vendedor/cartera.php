<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');
require('../../header.php');
?>
<br>
<div class="container-fluid">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
			<li class="breadcrumb-item active" aria-current="page">Cartera de clientes</li>
		</ol>
	</nav>
	<div id="resultado"></div>
</div>
<?php
	require('../../footer.php'); 
?>
<script>
	$.ajax({
		type:'POST',
		url:"cartera_detalle.php",
		data:{
			pagina : 1
		},
		success:function(resp){
			$("#resultado").html(resp);
		}
	});
</script>

