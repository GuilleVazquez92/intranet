<?php

require('../../controlador/main.php');
require($_SERVER['DOCUMENT_ROOT'].'/includes/PHPMail/class.phpmailer.php');
require($_SERVER['DOCUMENT_ROOT'].'/includes/PHPMail/class.smtp.php');

$mail = new PHPMailer();
$mail->IsSMTP();
$mail->Host = "mail.facilandia.com.py";
$mail->Username = "no-reply@facilandia.com.py";
$mail->Password = "K@ramegu!8Py";
$mail->SMTPAuth = true;

$mail->From = "no-reply@facilandia.com.py";
$mail->FromName = "Mail-Automatico (NO RESPONDER)";

$mail->AltBody = "Automatico";
$mail->AddAddress('operaciones@facilandia.com.py', 'operaciones@facilandia.com.py');
$mail->AddAddress('miguel_yegros@ngosaeca.com.py', 'miguel_yegros@ngosaeca.com.py');
$mail->AddAddress('luz.gimenez@cph.com.py', 'luz.gimenez@cph.com.py');
$mail->AddAddress('rossana.benitez@cph.com.py', 'rossana.benitez@cph.com.py');
$mail->AddAddress('alcira.garcete@cph.com.py', 'alcira.garcete@cph.com.py');
$mail->AddAddress('hugo.ayala@facilandia.com.py', 'hugo.ayala@facilandia.com.py');

$mail->Subject = "FACILANDIA - Envio de LOTE:".$lote;

$body = "<meta charset='iso-8859-1'>
<b>FACILANDIA</b> envi&oacute; satisfactoriamente a la WebService de <b>CPH</b> el LOTE: $lote
<br>
<br> 
<b>Resumen:".$respuesta['Wdet']." </b>
<br>
<br>
<br>
<b>NOTA</b>: Favor no responder este mensaje que ha sido emitido autom&aacute;ticamente por el sistema de FACILANDIA";

$mail->MsgHTML($body);

if($mail->Send()) {
	echo $respuesta['Wdet'];
	echo "<br>";
	echo "Correo enviado automaticamente!";
} 
else {
	echo "Mailer Error: " . $mail->ErrorInfo;
}
?>
