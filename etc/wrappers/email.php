<?php
	require_once __DIR__ . "/../plugins/PHPMailer/PHPMailerAutoload.php";
	require_once __DIR__ . "/../plugins/Html2Text/Html2Text.php";

	function email($to, $content, $subject, $auth, $from = "Mr. Server"){
		// SMTP setup
		$mail = new PHPMailer();
		$html = new \Html2Text\Html2Text($content);
		$mail->IsSMTP();
		$mail->Host = "localhost";
		$mail->SMTPAuth = true; // enable SMTP authentication
		$mail->SMTPSecure = "tls";
		$mail->Username = $auth[0];
		$mail->Password = $auth[1];
		$mail->Port = 587;
		$mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
		);
		// $mail->SMTPDebug = 2;
		// Setup of to, from, subject, etc.
		$mail->From = $auth[0];
		$mail->FromName = $from;
		$mail->addAddress($to);
		$mail->Subject = $subject;
		$mail->AltBody = $html->getText(); // optional, comment out and test
		$mail->MsgHTML($content);
		$mail->isHTML(true);
		$mail->CharSet = 'UTF-8';
		// Tries to send and returns true/false
		if(!$mail->Send()) {
			return false;
		} else {
			return true;
		}
	}
?>