<?php 
require('../../header.php');
?>
<br>
<div class="container">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
			<li class="breadcrumb-item active" aria-current="page">Lista de Productos</li>
		</ol>
	</nav>
	<div id="body_productos"></div>
</div>
<?php
	require('../../footer.php'); 
?>
<script>
	$.ajax({
		type:'POST',
		url:"lista_productos_gral.php",
		data:{
			pagina : 1
		},
		success:function(resp){
			$("#body_productos").html(resp);
		}
	});
</script>

