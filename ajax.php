<?php
	include "common.php";

	// Så rollback virker
	mysqli_autocommit($db, FALSE);

	$action = rens($_POST['action']);
	if($action == "newUser"){
		// Opret ny bruger
		$u = rens($_POST['u']);
		$p = rens($_POST['p']);
		$dd = rens($_POST['dd']);
		if(empty($p) || empty($u)){
			echo "Fejl: Et felt er tomt.";
			exit;
		}
		// Opret brugeren i DB
		$p = haash($p);
		$sql = "INSERT INTO login (u, p) VALUES ('$u','$p')";
		if(!mysqli_query($db, $sql)){
			mysqli_rollback($db);
			echo "Fejl: SQL, indtastning af bruger. $sql";
			exit;
		}
		$id = mysqli_insert_id($db);
		// Giv brugeren rettighedder
		$v = "";
		$V = explode(",", $dd);
		foreach($V as $k => $val){
			if($k > 0)
				$v .= ",";
			$v .= "('$val','$id')";
		}
		$sql = "INSERT INTO con (dID, uID) VALUES $v";
		if(!mysqli_query($db, $sql)){
			mysqli_rollback($db);
			echo "Fejl: SQL, rettighedder til bruger. $sql";
			exit;
		}
		else{
			mysqli_commit($db);
			echo "Succes";
		}
	}
	elseif($action == "editUser"){
		// Ret en bruger
	}
	elseif($action == "login"){
		// Tjek om de givne informationer stemmer overens med noget i DB
		$u = rens($_POST['u']);
		$p = rens($_POST['p']);
		$q = mysqli_query($db, "SELECT * FROM login WHERE u='$u'");
		if(mysqli_num_rows($q) > 0){
			$r = mysqli_fetch_array($q);
			if(hash_equals(haash($p, $r['p']), $r['p'])){
				// Passer
				$_SESSION['user'] = $r['uID'];
				echo "Succes";
			}
			else{
				echo "Fejl: Kode passer ikke.";
			}
		}
		else{
			echo "Fejl: Buger findes ikke.";
		}
	}
?>