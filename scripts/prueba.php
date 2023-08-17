<?php
function sendMail($id,$reciever){
	require("C:/xampp/vendor/phpmailer/phpmailer/src/PHPMailer.php");
  require("C:/xampp/vendor/phpmailer/phpmailer/src/SMTP.php");
  
    $mail = new PHPMailer\PHPMailer\PHPMailer();
	$mail->CharSet = 'utf-8';
	$mail->Host = "smtp.googlemail.com";
	$mail->From = "a22110098@ceti.mx";
	$mail->IsSMTP();
	$mail->SMTPAuth = true;
	$mail->Username = "a22110098@ceti.mx";
	$mail->Password = "resident evil 12357";
	$mail->SMTPSecure = "tls";
	$mail->Port = 587;
	$mail->AddAddress($reciever);
	$mail->SMTPDebug = 0;   //Muestra las trazas del mail, 0 para ocultarla
	$mail->isHTML(true);                                  // Set email format to HTML
	$mail->Subject = 'Gracias por tu compra!';
	$mail->Body = 'Muchas gracias por tu compra! <b> Te mandamos el resumen de tu compra</b>';
	$mail->AltBody = 'Muchas gracias por tu compra! Te mandamos el resumen de tu compra';

	$inMailFileName = "Compra-ComercioGlobal.pdf";
	$filePath = "../pdf/mail-$id.pdf";
	$mail->AddAttachment($filePath, $inMailFileName);

	if (!$mail->send()) {
		return;
	}
}

?>