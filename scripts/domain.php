<?php
	require $_SERVER["DOCUMENT_ROOT"]."/etc/common.php";

	// Definerer variabler
	$o = ["status" => "danger", "msg" => $Content->out(12)];
	$return = $_SERVER['HTTP_REFERER'];
	$medium = $_POST['medium'];
	$action = $_POST['action'];
	$e = 0;

	if($action == "update-email"){
		if(empty($_POST['u'])){
			$e++;
		} else{
			$u = $_POST['u'];
		}
		if(empty($_POST['domain'])){
			$e++;
		} else{
			$domain = $_POST['domain'];
		}
		if(empty($_POST['dom']) || !is_array($_POST['dom'])){
			$e++;
		} else{
			$input = $_POST['dom'];
		}
		if(empty($_POST['original'])){
			$e++;
		} else{
			$original = $_POST['original'];
		}

		if($e == 0){
			if($Content->access($domain)){
				$fail = false;
				$a = $u . "@" . $domain;
				try{
					$con->beginTransaction();
					$q = $con->prepare("DELETE FROM forwardings WHERE address LIKE (:a)");
					$q->bindParam(":a", $original);
					$q->execute();
					if(sizeof($input) == 1 && !empty($input[0])){
						if(!filter_var($input[0], FILTER_VALIDATE_EMAIL)){
							$o['msg'] = $Content->out(36);
							$fail = true;
						} else{
							$tmp = explode("@", $input[0]);
							if($domain == $tmp[1]){ // Hvis domænet er det samme, opret som et alias
								$alias = true;
								$q = $con->prepare("INSERT INTO forwardings (address, forwarding, domain, is_alias) VALUES (:a, :f, :d, 1)");
							} else{
								$alias = false;
								$q = $con->prepare("INSERT INTO forwardings (address, forwarding, domain, is_list) VALUES (:a, :f, :d, 1)");
							}
							$q->bindParam(":a", $a);
							$q->bindParam(":f", $input[0]);
							$q->bindParam(":d", $domain);
							$q->execute();
						}
						$counter = 1;
					} else{
						$alias = false;
						$q = $con->prepare("INSERT INTO forwardings (address, forwarding, domain, is_list) VALUES (:a, :f, :d, 1)");
						$counter = 0;
						foreach($input as $mail){
							if(!empty($mail)){
								$counter++;
								$q->bindParam(":a", $a);
								$q->bindParam(":f", $mail);
								$q->bindParam(":d", $domain);
								$q->execute();
							}
						}
					}
					if(!$alias){
						if($original != $a){ // Aliaset i "alias" skal opdateres
							$q = $con->prepare("UPDATE alias SET address = :a, modified = NOW() WHERE address LIKE (:o)");
							$q->bindParam(":a", $a);
							$q->bindParam(":o", $original);
							$q->execute();
						}
						$o['msg'] = $Content->out(42);
					} elseif($alias && !$fail){
						$o['msg'] = $Content->out(41);
					}
					if($counter == 0){
						$o['msg'] = $Content->out(48);
						$con->rollback();
					} else{
						$con->commit();
						$o['status'] = "success";
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
			$o['msg'] = $Content->out(35);
		}
	} elseif($action == "delete-email"){
		if(empty($_POST['original'])){
			$e++;
		} else{
			$original = $_POST['original'];
		}

		if($e == 0){
			if($Content->access($domain)){
				try{
					$q = $con->prepare("DELETE FROM alias WHERE address LIKE (:a)");
					$q->bindParam(":a", $original);
					$q->execute();
					$q = $con->prepare("DELETE FROM forwardings WHERE address LIKE (:a)");
					$q->bindParam(":a", $original);
					$q->execute();
					$o['msg'] = $Content->out(44);
					$o['status'] = "success";
				} catch(PDOException $e){
					error_log($e->getMessage());
					$o['msg'] = $Content->out(13);
				}
			} else{
				$o['msg'] = $Content->out(47);
			}
		} else{
			$o['msg'] = $Content->out(35);
		}
	} elseif($action == "create-email"){
		if(empty($_POST['u'])){
			$e++;
		} else{
			$u = $_POST['u'];
		}
		if(empty($_POST['domain'])){
			$e++;
		} else{
			$domain = $_POST['domain'];
		}
		if(empty($_POST['dom']) || !is_array($_POST['dom'])){
			$e++;
		} else{
			$input = $_POST['dom'];
		}

		if($e == 0){
			if($Content->access($domain)){
				$fail = false;
				$a = $u . "@" . $domain;
				try{
					$con->beginTransaction();
					$q = $con->prepare("SELECT * FROM alias WHERE address LIKE (:a)");
					$q->bindParam(":a", $a);
					$q->execute();
					$n1 = $q->rowCount();
					$q = $con->prepare("SELECT * FROM forwardings WHERE address LIKE (:a)");
					$q->bindParam(":a", $a);
					$q->execute();
					$n2 = $q->rowCount();
					$q = $con->prepare("SELECT * FROM mailbox WHERE username LIKE (:a)");
					$q->bindParam(":a", $a);
					$q->execute();
					$n3 = $q->rowCount();

					if($n1 == 0 && $n2 == 0 && $n3 == 0){
						if(sizeof($input) == 1 && !empty($input[0])){
							$tmp = explode("@", $input[0]);
							if($domain == $tmp[1]){ // Hvis domænet er det samme, opret som et alias
								$alias = true;
								$q = $con->prepare("INSERT INTO forwardings (address, forwarding, domain, is_alias) VALUES (:a, :f, :d, 1)");
							} else{
								$alias = false;
								$q = $con->prepare("INSERT INTO forwardings (address, forwarding, domain, is_list) VALUES (:a, :f, :d, 1)");
							}
							if(!filter_var($input[0], FILTER_VALIDATE_EMAIL)){
								$o['msg'] = $Content->out(36);
								$fail = true;
							} else{
								$q->bindParam(":a", $a);
								$q->bindParam(":f", $input[0]);
								$q->bindParam(":d", $domain);
								$q->execute();
							}
							$counter = 1;
						} else{
							$alias = false;
							$q = $con->prepare("INSERT INTO forwardings (address, forwarding, domain, is_list) VALUES (:a, :f, :d, 1)");
							$counter = 0;
							foreach($input as $mail){
								if(!empty($mail)){
									$q->bindParam(":a", $a);
									$q->bindParam(":f", $mail);
									$q->bindParam(":d", $domain);
									$q->execute();
									$counter++;
								}
							}
						}
						if(!$alias){
							$q = $con->prepare("INSERT INTO alias (address, domain, created) VALUES (:a, :d, NOW())");
							$q->bindParam(":a", $a);
							$q->bindParam(":d", $domain);
							$q->execute();
							$o['msg'] = $Content->out(38);
						} elseif($alias && !$fail){
							$o['msg'] = $Content->out(37);
						}
						if($counter == 0){
							$o['msg'] = $Content->out(48);
							$con->rollback();
						} else{
							$con->commit();
							$o['status'] = "success";
						}
					} else{
						$o['msg'] = $Content->out(43);
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
			$o['msg'] = $Content->out(35);
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