<?php
require('../../controlador/main.php');
require( CONTROLADOR . 'vendedores.php');
$datos = new Vendedores();
?>
<select class="custom-select custom-select-sm" id="debito_automatico" name="debito_automatico" onchange="lista_debito()">
	<option value="0">Seleccione una opción</option>
	<?php 
	foreach ($datos->lista_debito($_POST['debito']) as $valor) {
		?>
		<option value="<?= $valor['cod_empr']?>"><?= $valor['empresa']?></option>
		<?php
	}
	?>	

</select>