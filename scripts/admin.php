<?php
	require $_SERVER["DOCUMENT_ROOT"]."/etc/common.php";

	// Definerer variabler
	$o = ["status" => "danger", "msg" => $Content->out(12)];
	$return = $_SERVER['HTTP_REFERER'];
	$medium = @($_POST['medium']);
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
		if($e == 0){
			if($_SESSION['userID'] == 1){
				try{
					$con->beginTransaction();
					$q = $con->prepare("INSERT INTO ma_login (user, pass) VALUES (:u, :p)");
					$q->bindParam(":u", $u);
					$q->bindValue(":p", $Content->hash($p));
					$q->execute();
					$uID = $con->lastInsertId();

					$q = $con->prepare("INSERT INTO ma_access (domain, userID) VALUES (:d, :u)");
					$ds = 0;
					$domains = $_POST['dom'];
					foreach($domains as $domain){
						if(!empty($domain)){
							$ds++;
							$q->bindParam(":d", $domain);
							$q->bindParam(":u", $uID);
							$q->execute();
						}
					}
					if($ds > 0){
						$con->commit();
						$o['status'] = "success";
						$o['msg'] = "Oprettede bruger korrekt.";
					} else{
						$con->rollback();
						$o['msg'] = "Der var ikke nogen domæner";
					}
				} catch(PDOException $e){
					error_log($e->getMessage());
					$o['msg'] = $Content->out(13);
					$con->rollback();
				}
			} else{
				$o['msg'] = $Content->out(47);
			}
		} else{
			$o['msg'] = $Content->out(45);
		}
	} elseif($action == "update-user"){
		if(empty($_POST['uID'])){
			$e++;
		} else{
			$uID = $_POST['uID'];
		}
		if($e == 0){
			if($_SESSION['userID'] == 1){
				try{
					$con->beginTransaction();
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

					$q = $con->prepare("INSERT INTO ma_access (domain, userID) VALUES (:d, :u)");
					$ds = 0;
					$domains = $_POST['dom'];
					foreach($domains as $domain){
						if(!empty($domain)){
							$q->bindParam(":d", $domain);
							$q->bindParam(":u", $uID);
							$q->execute();
							$ds++;
						}
					}
					if($ds > 0){
						$con->commit();
						$o['status'] = "success";
						$o['msg'] = "Opdaterede bruger korrekt.";
					} else{
						$con->rollback();
						$o['msg'] = "Der var ikke nogen domæner";
					}
				} catch(PDOException $e){
					$con->rollback();
					error_log($e->getMessage());
					$o['msg'] = $Content->out(13);
				}
			} else{
				$o['msg'] = $Content->out(47);
			}
		} else{
			$o['msg'] = $Content->out(11);
		}
	} elseif($action == "delete-user"){
		if(empty($_POST['uID'])){
			$e++;
		} else{
			$uID = $_POST['uID'];
		}
		if($e == 0){
			if($_SESSION['userID'] == 1){
				try{
					$q = $con->prepare("DELETE FROM ma_access WHERE userID LIKE (:id)");
					$q->bindParam(":id", $uID);
					$q->execute();
					$q = $con->prepare("DELETE FROM ma_login WHERE userID LIKE (:id)");
					$q->bindParam(":id", $uID);
					$q->execute();
					$o['status'] = "success";
					$o['msg'] = "Slettede bruger korrekt.";
				} catch(PDOException $e){
					error_log($e->getMessage());
					$o['msg'] = $Content->out(13);
				}
			} else{
				$o['msg'] = $Content->out(47);
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