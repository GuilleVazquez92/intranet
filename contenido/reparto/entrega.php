<div class="container">
<!--
  <form action="" method="POST" name="fomulario" id="formulario" class="form">
    <input type="text" class="form-control" readonly name="latitud" id="latitud">
    <input type="text" class="form-control" readonly name="longitud" id="longitud">
-->
<p id="notice"></p>
<div class="form-check">
	<input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="1">
	<label class="form-check-label" for="exampleRadios2">
		Hogar
	</label>
</div>
<div class="form-check">
	<input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="2">
	<label class="form-check-label" for="exampleRadios2">
		Familiar
	</label>
</div>
<div class="form-check">
	<input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="3">
	<label class="form-check-label" for="exampleRadios2">
		Trabajo
	</label>
</div>
<div class="form-check">
	<input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="0">
	<label class="form-check-label" for="exampleRadios2">
		Otro...
	</label>
</div>
<br>

<input name="verifica" id="verifica" type="checkbox" required="required">  
<label for="verifica"><small class="text-muted">Confirmo el lugar de entrega</small></label>

<script>
	(function() {
		var x = document.getElementById("notice");
		if (navigator.geolocation) {

			navigator.geolocation.getCurrentPosition(showPosition);

		} else { 
			x.innerHTML = "Geolocation no es soportado por su navegador.";
		}

		function showPosition(position) {

			document.getElementById("latitud").value = position.coords.latitude;
			document.getElementById("longitud").value = position.coords.longitude;

		}
	})();
</script>
