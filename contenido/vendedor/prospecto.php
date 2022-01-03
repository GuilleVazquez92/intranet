<?php 
	require('../../header.php');
	require( CONTROLADOR . 'vendedores.php');

	$vendedor = new Vendedores();
	$vendedor->vendedor = $_COOKIE['usuario'];
?>

	<br>
	<div class="container-fluid">
		
		<nav aria-label="breadcrumb">
		  <ol class="breadcrumb">
		    <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
		    <li class="breadcrumb-item active" aria-current="page">Crear Prospecto</li>
		  </ol>
		</nav>
	</div>
	<div class="container" id="contenido">
		<nav class="navbar navbar-dark bg-light">
			
				<input id="documento" class="form-control mr-sm-2 my-2" type="text" placeholder="Documento" aria-label="Documento">
				<button class="btn btn-outline-success my-2 my-sm-0" type="button" onclick="buscar_prospecto()">Buscar</button>
		
		</nav>
	</div>
<?php 
	require('../../footer.php'); 
?>
<script>
	
	var input = document.getElementById("documento");
		input.addEventListener("keyup", function(event) {
		
		if (event.keyCode === 13) {
			event.preventDefault();
			buscar_prospecto();
		}

	});

	function buscar_prospecto(){

		var documento = document.getElementById("documento").value;

		$.ajax({
			type:'POST',
			url:"crear_prospecto.php",
			data:{
				documento: documento,
				accion	: "buscar"
									
			},
			success:function(resp){
				 $("#contenido").html(resp);
			}
		});
	}

		function crear_prospecto(){
				
			var documento = document.getElementById("documento").value;
			var nombre = document.getElementById("nombre").value;
			var particular = document.getElementById("particular").value;
			var comercial = document.getElementById("comercial").value;
			var telefono = document.getElementById("telefono").value;
			var celular = document.getElementById("celular").value;

			$.ajax({
				type:'POST',
				url:"crear_prospecto.php",
				data:{
					accion: 'crear',
					documento: documento,
					nombre: nombre,
					particular : particular.replace(/'/g, "''"),
					comercial : comercial.replace(/'/g, "''"),
					telefono : telefono,
					celular : celular
				},
				success:function(resp){
					 $("#contenido").html(resp);	
				}
			});
		}	
</script>