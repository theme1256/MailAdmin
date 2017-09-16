<?php
	require $_SERVER["DOCUMENT_ROOT"]."/etc/common.php";

	// Definerer variabler
	$o = ["status" => "danger", "msg" => $Content->out(12)];
	$return = $_SERVER['HTTP_REFERER'];
	$medium = $_POST['medium'];
	$action = $_POST['action'];
	$e = 0;

	if($action == "create-user"){
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
		if(empty($_POST['dom'])){
			$e++;
		} else{
			$dom = $_POST['dom'];
		}
		if($e == 0){
			try{
				$q = $con->prepare("INSERT INTO ma_login (user, pass) VALUES (:u, :p)");
				$q->bindParam(":u", $u);
				$q->bindValue(":p", $Content->hash($p));
				$q->execute();
				$uID = $con->lastInsertId();

				$domains = explode(",", $_POST['dom']);
				$q = $con->prepare("INSERT INTO ma_access (domain, userID) VALUES (:d, :u)");
				foreach($domains as $domain){
					$q->bindParam(":d", $domain);
					$q->bindParam(":u", $uID);
					$q->execute();
				}
				$o['status'] = "success";
				$o['msg'] = "Oprettede bruger korrekt.";
			} catch(PDOException $e){
				error_log($e->getMessage());
				$o['msg'] = $Content->out(13);
			}
		} else{
			$o['msg'] = $Content->out(11);
		}
	} elseif($action == "update-user"){
		if(empty($_POST['uID'])){
			$e++;
		} else{
			$uID = $_POST['uID'];
		}
		if($e == 0){
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

				$q = $con->prepare("DELETE FROM ma_access WHERE userID LIKE (:id)");
				$q->bindParam(":id", $uID);
				$q->execute();

				$domains = explode(",", $_POST['dom']);
				$q = $con->prepare("INSERT INTO ma_access (domain, userID) VALUES (:d, :u)");
				foreach($domains as $domain){
					$q->bindParam(":d", $domain);
					$q->bindParam(":u", $uID);
					$q->execute();
				}
				$o['status'] = "success";
				$o['msg'] = "Oprettede bruger korrekt.";
			} catch(PDOException $e){
				error_log($e->getMessage());
				$o['msg'] = $Content->out(13);
			}
		} else{
			$o['msg'] = $Content->out(11);
		}
	} else{
		$o['msg'] = $Content->out(19);
	}


	// Retuner data korrekt
	if($medium == "post"){
		$_SESSION['out'] = $o;
		header("Location: " . $return);
	} else{
		echo json_encode($o);
	}
?>