<?php 
if(isset($_GET['zona']) && $_GET['zona']==0){
	?>
	<input type="text" value="0" name="zona" hidden="">
	<input type="text" value="100" name="opcion" hidden="">
	<div class="form-group">
		<label for="ayudante">Ayudante</label>
		<select name="ayudante" id="ayudante" class="custom-select" required="">
			<option value="0"></option>
			<?php
			$datos = $logistica->ayudante();
			for ($i=0; $i < count($datos); $i++) { 
				?>
				<option value="<?= $datos[$i]['cod_ayudante']; ?>"><?= $datos[$i]['ayudante'] ;?></option>
				<?php
			}
			?>
		</select>
	</div>

	<?php 
}
?>
<div class="container">
	<div class="form-check">
		<input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="1">
		<label class="form-check-label" for="exampleRadios2">
			Croquis no coincide
		</label>
	</div>

	<div class="form-check">
		<input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="2">
		<label class="form-check-label" for="exampleRadios2">
			Cliente no contesta
		</label>
	</div>

	<div class="form-check">
		<input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="2">
		<label class="form-check-label" for="exampleRadios2">
			Cliente no se encuentra
		</label>
	</div>

	<div class="form-check">
		<input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="4">
		<label class="form-check-label" for="exampleRadios2">
			Cliente no cuenta con documentos
		</label>
	</div>

	<div class="form-check">
		<input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="5">
		<label class="form-check-label" for="exampleRadios2">
			Cliente no cuenta con Entrega inicial
		</label>
	</div>

	<div class="form-check">
		<input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="6">
		<label class="form-check-label" for="exampleRadios2">
			Cliente no quiere la mercadería
		</label>
	</div>
	<br>
	<div class="form-group">
		<label for="comentario"><small class="text-muted">Agregar Comentario (opcional)</small></label>
		<textarea name="comentario" width="100%" rows="5" class="form-control"></textarea>	
	</div>	

</div>