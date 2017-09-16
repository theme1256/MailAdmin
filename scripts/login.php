<?php
	require $_SERVER["DOCUMENT_ROOT"]."/etc/common.php";

	// Definerer variabler
	$o = ["status" => "danger", "msg" => $Content->out(12)];
	$return = $_SERVER['HTTP_REFERER'];
	$medium = $_POST['medium'];
	$e = 0;

	if(empty($_POST['u'])){
		$e++;
	} else{
		$u = $_POST['u'];
	}
	if(empty($_POST['p'])){
		$e++;
	} else{
		$p = $_POST['p'];
	}

	if($e == 0){
		try{
			$q = $con->prepare("SELECT * FROM ma_login WHERE user LIKE (:u)");
			$q->bindParam(":u", $u);
			$q->execute();
			if($q->rowCount() > 0){
				$U = $q->fetch(PDO::FETCH_ASSOC);
				if(password_verify($p, $U['pass'])){
					$o['status'] = "success";
					$o['msg'] = $Content->out(15);
					$_SESSION['login'] = true;
					$_SESSION['userID'] = $U['userID'];
				} else{
					$o['msg'] = $Content->out(14);
				}
			} else{
				$o['msg'] = $Content->out(14);
			}
		} catch(PDOException $e){
			error_log($e->getMessage());
			$o['msg'] = $Content->out(13);
		}
	} else{
		$o['msg'] = $Content->out(11);
	}

	// Retuner data korrekt
	if($medium == "post"){
		$_SESSION['out'] = $o;
		header("Location: " . $return);
	} else{
		echo json_encode($o);
	}
?>