<?php
	require $_SERVER["DOCUMENT_ROOT"]."/etc/common.php";

	// Definerer variabler
	$o = ["status" => "danger", "msg" => $Content->out(12)];
	$return = $_SERVER['HTTP_REFERER'];
	$medium = $_POST['medium'];
	$action = $_POST['action'];
	$e = 0;

	if($action == "update-email"){
		// 

		if($e == 0){
			try{
				// 
			} catch(PDOException $e){
				error_log($e->getMessage());
				$o['msg'] = $Content->out(13);
			}
		} else{
			$o['msg'] = $Content->out(11);
		}
	} elseif($action == "create-email"){
		// 

		if($e == 0){
			try{
				// 
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