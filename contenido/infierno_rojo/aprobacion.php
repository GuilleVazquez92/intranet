<?php 
require('../../header.php');
require( CONTROLADOR . 'ir.php');
$ir = new IR();
if(isset($_POST['aprobar']) || isset($_POST['rechazar'])){

	$ir->cuenta 	= $_POST['cuenta'];
	$ir->operacion  = $_POST['operacion'];	

	switch ($_POST['tipo']) {
		case 'REFINANCIADO':
		$ir->tipo  = 16;
		break;

		case 'HOMOLOGADO':
		$ir->tipo  = 25;
		break;

		case 'PROMOCION':
		$ir->tipo  = 26;
		break;						

		default:
		$ir->tipo  = 16;
		break;
	}



	if(isset($_POST['aprobar']))
	{
		$ir->aprobar_operacion();

	} else
	{
		$ir->rechazar_operacion();
	}
}

?>
<div class="container-fluid">
	<br>
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
			<li class="breadcrumb-item active" aria-current="page">Aprobación de Refinanciación</li>
		</ol>
	</nav>	
	<div class="table-responsive-sm">
		<table class="table table-sm table-borderless">
			<thead>
				<tr class="table-warning">
					<th>Cuenta</th>
					<th>Cliente</th>
					<th>Operación</th>
					<th>Tipo</th>
					<th class="text-center">Documento</th>
					<th class="text-center">Cantidad</th>
					<th class="text-right">Cuota</th>
					<th class="text-right">MontoBruto</th>
					<th class="text-center">Op. Activas</th>
					<th>Gestor</th>
					<th class="text-center">Acción</th>
				</tr>
			</thead>

			<tbody>
				<?php  
				$datos  = $ir->operaciones_aprobar();
				if(count($datos)>0)
				{
					for ($i=0; $i < count($datos) ; $i++) 
					{ 
						?>
						<form action="" method="POST">
							<input type="text" name="cuenta" value="<?= $datos[$i]['cuenta'];?>" hidden>
							<input type="text" name="operacion" value="<?= $datos[$i]['operacion'];?>" hidden>
							<input type="text" name="tipo" value="<?= $datos[$i]['tipo'];?>" hidden>
							<tr>
								<td class="align-middle"><?= $datos[$i]['cuenta'];?></td>
								<td class="align-middle"><?= $datos[$i]['cliente'];?></td>
								<td class="align-middle"><?= $datos[$i]['operacion'];?></td>
								<td class="align-middle"><?= $datos[$i]['tipo_descripcion'];?></td>
								<td class="text-center"><a href="pagares_pdf.php?cuenta=<?= $datos[$i]['cuenta'];?>&tipo=<?= $datos[$i]['tipo'];?>" target="_blank">
									<img src="<?= IMAGE.'pdf32x32.png';?>" alt="pdf"></a>
								</td>
								<td align="center"  class="text-center align-middle"><?= number_format($datos[$i]['cantidad'],0,',','.');?></td>
								<td align="center" class="text-right align-middle"><?= number_format($datos[$i]['cuota'],0,',','.');?></td>
								<td align="center" class="text-right align-middle"><?= number_format($datos[$i]['bruto'],0,',','.');?></td>
								<td align="center" class="text-center align-middle"><?= number_format($datos[$i]['cant_oper'],0,',','.');?></td>
								<td class="align-middle"><?= $datos[$i]['gestor'];?></td>
								<td  class="text-center">

									<button type="submit" name="aprobar" class="btn btn-success btn-sm">Aceptar</button>

									<button type="submit" name="rechazar" class="btn btn-danger btn-sm">Rechazar</button></td>
								</tr>
							</form>
							<?php 
						}
					} 

					?>
				</tbody>

			</table>
		</div>
		<?php require('../../footer.php'); ?>