<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('../../header.php');
require( CONTROLADOR . 'ventas.php');
$datos = new Ventas();
?>
<div class="container-fluid">
	<br>
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item">
				<a href="">Inicio</a>
			</li>
			<li class="breadcrumb-item active" aria-current="page">Canales</li>
		</ol>
	</nav>	
	<table class="table table-sm table-borderless table-hover">
		<thead class="bg-warning">
			<tr>
				<td align='center'>Canal</td>
				<td align='center'>Carpetas dia</td>
				<td align='center'>Objetivo Mes</td>
				<td align='center'>Venta Mes</td>
				<td align='center'>Venta Proyectada</td>
				<td align='center'>% Proyeccion</td>
				<td align='center'>Objetivo Dia</td>
				<td align='center'>Venta Hoy</td>
				<td align='center'>% Proyeccion</td>
				<td align='center'>% Aprobacion</td>
			</tr>
		</thead>		
		<tbody>	
			<?php
			
			$total_aprobadas 		= 0;
			$total_carpetas 		= 0;	
			$total_objetivo 		= 0;
			$total_venta_mes 		= 0;
			$total_proyectada		= 0;
			$total_objetivo_hoy 	= 0;
			$total_venta_hoy		= 0;
			$total_proyeccion_mes 	= 0;
			$total_proyeccion_dia 	= 0;
			$total_aprobacion 		= 0;

			$data = $datos->resumen_canales();
			
			foreach ($data as $key) {

				$por_aprobacion = ($key['total']!=0) ? number_format(($key['aprobadas']/$key['total'])*100,1,',','.') : 0;	
				?>
				<tr>
					<td align='left'><?= $key['codcanal']." ".$key['canal'];?></td>
					<td align='center'><?= $key['carpeta_dia'];?></td>
					<td align='right'><?= number_format($key['objetivo_mes'],0,',','.');?></td>
					<td align='right'><?= number_format($key['venta_mes'],0,',','.');?></td>
					<td align='right'><?= number_format($key['venta_proyectada'],0,',','.');?></td>
					<td align='right'><?= $key['proyeccion_mes'];?> %</td>
					<td align='right'><?= number_format($key['objetivo_hoy'],0,',','.');?></td>
					<td align='right'><?= number_format($key['venta_hoy'],0,',','.');?></td>
					<td align='right'><?= $key['proyeccion_hoy'];?> %</td>
					<td align='right'><font color=".$color."><b><?= $por_aprobacion;;?> %</b></font></td>
				</tr>
				<?php
				$total_objetivo 	+= $key['objetivo_mes'];
				$total_venta_mes 	+= $key['venta_mes'];
				$total_proyectada	+= $key['venta_proyectada'];
				$total_objetivo_hoy += $key['objetivo_hoy'];
				$total_venta_hoy	+= $key['venta_hoy'];
				$total_aprobadas 	+= $key['aprobadas'];
				$total_carpetas 	+= $key['total'];
			}

			if($total_objetivo!=0){
				$total_proyeccion_mes= $total_proyectada/$total_objetivo*100;
				$total_proyeccion_dia= $total_venta_hoy/$total_objetivo_hoy*100;
				$total_aprobacion= $total_aprobadas/$total_carpetas*100;			
			}
			?>		
		</tbody>
		<tfoot class="bg-warning">	
			<tr>
				<td align='left' colspan='2'>TOTAL</td>
				<td align='right'><?= number_format($total_objetivo,0,',','.');?></td>
				<td align='right'><?= number_format($total_venta_mes,0,',','.');?></td>
				<td align='right'><?= number_format($total_proyectada,0,',','.');?></td>
				<td align='right'><?= number_format($total_proyeccion_mes,2,',','.');?>"%</td>
				<td align='right'><?= number_format($total_objetivo_hoy,0,',','.');?></td>
				<td align='right'><?= number_format($total_venta_hoy,0,',','.');?></td>
				<td align='right'><?= number_format($total_proyeccion_dia,2,',','.');?>"%</td>
				<td align='right'><?= number_format($total_aprobacion,1,',','.');?>"%</td>
			</tr>
		</tfoot>
	</table>
</div>
<?Php

/*
if($por_aprobacion>=50){
	$color = 'green';

}elseif ($por_aprobacion>=40) {
	$color = 'orange';

}else{
	$color = 'red';
}

*//*
$habil 				= $row['habil'];  
$trascurrido 		= $row['trascurrido'];
$falta				= $row['falta'];
*/
require('../../footer.php');
?>
