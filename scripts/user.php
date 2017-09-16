<?php
	require $_SERVER["DOCUMENT_ROOT"]."/etc/common.php";

	// Definerer variabler
	$o = ["status" => "danger", "msg" => $Content->out(12)];
	$return = $_SERVER['HTTP_REFERER'];
	$medium = $_POST['medium'];
	$e = 0;

	if($e == 0){
		$uID = $_SESSION['userID'];
		try{
			if(!empty($_POST['u'])){
				$q = $con->prepare("UPDATE ma_login SET user = :u WHERE userID LIKE (:id)");
				$q->bindParam(":u", $_POST['u']);
				$q->bindParam(":id", $uID);
				$q->execute();
			}
			if(!empty($_POST['p'])){
				$q = $con->prepare("UPDATE ma_login SET pass = :p WHERE userID LIKE (:id)");
				$q->bindValue(":p", $Content->hash($_POST['p']));
				$q->bindParam(":id", $uID);
				$q->execute();
			}
			$o['msg'] = $Content->out(18);
			$o['status'] = "success";
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