<?php  
require('../../header.php');
require( CONTROLADOR . 'capital_humano.php');
$data = new Capital_Humano();
$data->canal = 17;
$datos = $data->consulta_vendedores();

?>
<div class="container">
	<div>
		<?php
		$fecha_inicio = inicio_fin_semana(date('d-m-Y'));



		for ($i=0; $i < 6; $i++) { 

			$fecha[$i] 	= date('dmY',strtotime($fecha_inicio.'+'.$i.' days'));
			$fecha_cab[$i] 	= date('d-m-Y',strtotime($fecha_inicio.'+'.$i.' days'));
		}


		for ($x=0; $x < count($datos); $x++) {

			?>
			<br>
			<table class="table table-sm">
				<tr>
					<th colspan="7">Vendedor : <?= $datos[$x][0].' - '.$datos[$x][1];?></th>
				</tr>
				<tr class="bg-warning">
					<td class="">Fecha</td>
					<?php 
					for ($i=0; $i < 6; $i++) { 
						echo '<td class="text-center">'.$fecha_cab[$i].'</td>';
					}
					?>
				</tr>

				<tr>
					<td class="">Contactos( F, W, e I)</td>
					<?php 
					for ($i=0; $i < 6; $i++) { 
						$id = 'u'.$datos[$x][0]."_".$fecha[$i];
						echo '<td class="text-center"><input type="number" id="'.$id.'"  onchange="cargar_dato(this.id)" style=" width: 50% !important">'.$id.'</td>';
					}
					?>
				</tr>

				<tr>
					<td class="">Plataforma</td>
					<?php 
					for ($i=0; $i < 6; $i++) { 
						$id = 'p'.$datos[$x][0]."_".$fecha[$i];
						echo '<td class="text-center"><input type="number" onchange="alert('."'$id'".')"></td>';
					}
					?>
				</tr>

				<tr>
					<td class="">Carpetas</td>
					<?php
					for ($i=0; $i < 6; $i++) { 
						$id = 'c'.$datos[$x][0]."_".$fecha[$i];			
						echo '<td class="text-center"><input type="number" onchange="alert('."'$id'".')"></td>';
					}
					?>
				</tr>
				<tr>
					<td class="">Penalización</td>
					<?php
					for ($i=0; $i < 6; $i++) { 
						$id = 'f'.$datos[$x][0]."_".$fecha[$i];					 
						echo '<td class="text-center"><input type="number" onchange="alert('."'$id'".')"></td>';
					}
					?>
				</tr>

				<tr>
					<td class="">Conversión</td>
					<?php
					for ($i=0; $i < 6; $i++) { 
						$id = 'conv_'.$datos[$x][0]."_".$fecha[$i];			
						echo '<th class="text-center">0%</th>';
					}
					?>
				</tr>
			</table>	
			<?php 
		}
		?>
	</div>	
</div>
<?Php
require('../../footer.php');
?>
<script>

	function cargar_dato(id){

		alert(id);
		var valor = document.getElementById(id).value;

		var tipo = id.substring(0,1);
		var cod_vend;
		var fecha;

		switch(tipo) {
			case 'u':
		    	tipo = 'Contactos';
		    break;

		    case 'p':
		    	tipo = 'Plataforma';
		    break;

		    case 'c':
		    	tipo = 'Carpetas';
		    break;		    

		    case 'f':
		    	tipo = 'Penalización';
		    break;		    
		}

alert(tipo);



}
</script>
