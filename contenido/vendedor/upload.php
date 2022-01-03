
<?php 
require('../../controlador/main.php');
require( CONTROLADOR . 'vendedores.php');
$vendedor = new Vendedores();

// Compress image
function compressImage($source, $destination, $quality) {

  $info = getimagesize($source);
  
  if ($info['mime'] == 'image/jpeg')
    $image = imagecreatefromjpeg($source);

elseif ($info['mime'] == 'image/gif')
    $image = imagecreatefromgif($source);

elseif ($info['mime'] == 'image/png') 
    $image = imagecreatefrompng($source);

if(imagejpeg($image, $destination, $quality)){

    $result = 1;
}else{
    $result = 0;
}
return $result;

}

$target_dir_img = "../uploads/img/";
$target_dir_pdf = "../../archivos/";
$temp = explode(".", $_FILES["fileToUpload"]["name"]);

for ($i=0; $i < 10; $i++) { 

    $archivo = strtolower($_POST['tipo_documento'].'_'.$_POST['cuenta'].'_'.date('Ymd').'_'.$i.'.');   
   // $target_file_pdf = $target_dir_pdf.basename($archivo.'pdf');

    if(file_exists($target_dir_pdf.basename($archivo.'pdf'))) {
        $uploadOk = 0;
    }else{
        $archivo = strtolower($_POST['tipo_documento'].'_'.$_POST['cuenta'].'_'.date('Ymd').'_'.$i.'.');
        $target_file_pdf = $target_dir_pdf.basename(strtolower($archivo.'pdf'));
        $target_file_img = $target_dir_img.basename(strtolower($archivo.end($temp)));
        $uploadOk = 1;
        break;
    }
}

$imageFileType = strtolower(pathinfo($target_file_img,PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
       // echo "El archivo es una imagen - " . $check["mime"] . ".";
        $uploadOk = 1;
    } 
}

// Check file size
if ($_FILES["fileToUpload"]["size"] > 5000000) {
    //echo "Disculpe, su archivo es muy grande.";
    $uploadOk = 0;
}
// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "gif"){
    //echo "Disculpe, solo JPG son tipos permitidos.";
    $uploadOk = 0;
}



// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Error en la carga...";

} else {
    if(compressImage($_FILES["fileToUpload"]["tmp_name"],$target_file_img,75)==1){

        

        $usuario = (isset($_COOKIE['usuario'])) ? strtoupper($_COOKIE['usuario']) : "";
        

        $fecha = date("d-m-Y H:i:s");

        $vendedor->cuenta = $_POST['cuenta'];
        $vendedor->filtro = $_POST['tipo_documento'];
        $vendedor->nombre = $archivo.'pdf';
        $vendedor->gestor = $usuario; 
        $vendedor->agregar_documento(); 

        require('../../includes/PHPPdf/fpdf.php');

        class PDF extends FPDF
        {

            protected $B;
            protected $I;
            protected $U;
            protected $HREF;
            protected $fontList;
            protected $issetfont;
            protected $issetcolor;

        }

        $pdf = new PDF('P','mm','A4');
        $pdf->SetMargins(15, 10, 10,15);
        $pdf->AddPage();
        $pdf->SetFont('Arial','',10);

        $pdf->Cell(180,5,'Adjunto por: '.$usuario,0);
        $pdf->Ln();
        $pdf->Cell(180,5,'Fecha: '.$fecha,0);

        $pdf->Image($target_file_img,10,30,190);
        $pdf->Output($target_file_pdf,'F');
    };
}
?>


