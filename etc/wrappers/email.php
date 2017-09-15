<?php
	function email($to, $content, $subject, $auth, $from = "Mr. Server"){
		// Sætter SMTP op
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
		// Diverse mailting
		$mail->From = $auth[0];
		$mail->FromName = $from;
		$mail->addAddress($to);
		$mail->Subject = $subject;
		$mail->AltBody = $html->getText(); // optional, comment out and test
		$mail->MsgHTML($content);
		$mail->isHTML(true);
		$mail->CharSet = 'UTF-8';
		// Sender beskeden og gemmer svaret i session
		if(!$mail->Send()) {
			return false;
		} else {
			return true;
		}
	}
?>