<?php 

require('../../header.php');
require( CONTROLADOR . 'vendedores.php');

$vendedor = new Vendedores();
$vendedor->vendedor = $_COOKIE['usuario'];

unset($_SESSION["detalle_carrito"]);
unset($_SESSION["tipo_venta"]);
unset($_SESSION["clase_venta"]);

$_SESSION['detalle_carrito']['filtro'] = 'T';

if(isset($_POST['cuenta'])){

	$_SESSION['cuenta']  = $_POST['cuenta']; 
	$_SESSION['cliente'] = $_POST['cliente'];	
}

$cuenta  = $_SESSION['cuenta'];  
$cliente = $_SESSION['cliente']; 

?>

<input type="text" id="cod_vendedor" value="<?= $_COOKIE['rol'];?>" hidden>
<input type="text" id="cuenta" value="<?= $cuenta;?>" hidden>
<div class="d-none" id="api_key"><?= API_KEY ?></div>

<br>

<div class="container">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
			<li class="breadcrumb-item"><a href="gestion.php">Gestión</a></li>
			<li class="breadcrumb-item active" aria-current="page">Cargar Carro</li>
		</ol>
	</nav>

	<h1 class="text-center">Muy pronto</h1>

</div>
<?php 
require('../../footer.php');
?>

