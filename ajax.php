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
		$u = rens($_POST['u']);
		$uID = rens($_POST['uID']);
		$p = rens($_POST['p']);
		$dd = rens($_POST['dd']);
		if(empty($u)){
			echo "Fejl: Brugernavn er tomt.";
			exit;
		}
		else{
			$sql = "UPDATE login SET u='$u' WHERE uID=$uID";
			if(!mysqli_query($db, $sql)){
				mysqli_rollback($db);
				echo "Fejl: SQL, opdatering af navn. $sql";
				exit;
			}
		}
		if(!empty($p)){
			$p = haash($p);
			$sql = "UPDATE login SET p='$p' WHERE uID=$uID";
			if(!mysqli_query($db, $sql)){
				mysqli_rollback($db);
				echo "Fejl: SQL, opdatering af kode. $sql";
				exit;
			}
		}
		$sql = "DELETE FROM con WHERE uID=$uID";
		if(!mysqli_query($db, $sql)){
			mysqli_rollback($db);
			echo "Fejl: SQL, sletning af gamle rettighedder. $sql";
			exit;
		}
		$v = "";
		$V = explode(",", $dd);
		foreach($V as $k => $val){
			if($k > 0)
				$v .= ",";
			$v .= "('$val','$uID')";
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
	elseif($action == "editMe"){
		// retter den bruger der er logget ind nu
		$u = rens($_POST['u']);
		$p = rens($_POST['p']);
		$uID = $_SESSION['user'];
		if(empty($u)){
			echo "Fejl: Brugernavn er tomt.";
			exit;
		}
		else{
			$sql = "UPDATE login SET u='$u' WHERE uID=$uID";
			if(!mysqli_query($db, $sql)){
				mysqli_rollback($db);
				echo "Fejl: SQL, opdatering af navn. $sql";
				exit;
			}
		}
		if(!empty($p)){
			$p = haash($p);
			$sql = "UPDATE login SET p='$p' WHERE uID=$uID";
			if(!mysqli_query($db, $sql)){
				mysqli_rollback($db);
				echo "Fejl: SQL, opdatering af kode. $sql";
				exit;
			}
		}
		else{
			mysqli_commit($db);
			echo "Succes";
		}
	}
	elseif($action == "login"){
		// Tjek om de givne informationer stemmer overens med noget i DB
		$u = rens($_POST['u']);
		$p = rens($_POST['p']);
		if(empty($p) || empty($u)){
			echo "Fejl: Et felt er tomt.";
			exit;
		}
		$q = mysqli_query($db, "SELECT * FROM login WHERE u='$u'");
		if(mysqli_num_rows($q) > 0){
			$r = mysqli_fetch_array($q);
			if(haash($p, $r['p']) == $r['p']){
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
	elseif($action == "newDomain"){
		// Opret et nyt domæne og giv de valgte brugere rettighed til det
		$d = rens($_POST['d']);
		$b = rens($_POST['b']);
		$bb = rens($_POST['bb']);
		if(empty($d)){
			echo "Fejl: Der er ikke oplyst et domæne.";
			exit;
		}
		// Opret domæne i DB
		$sql = "INSERT INTO domains (domain) VALUES ('$d')";
		if(!mysqli_query($db, $sql)){
			mysqli_rollback($db);
			echo "Fejl: SQL, indtastning af domæne. $sql";
			exit;
		}
		$id = mysqli_insert_id($db);
		//Giv brugere rettighedder
		$v = "";
		$V = explode(",", $bb);
		foreach($V as $k => $val){
			if($k > 0)
				$v .= ",";
			$v .= "('$id','$val')";
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
	elseif($action == "newMail"){
		// Opret ny mail eller liste, tjek først om ting er tomme, derefter om det er en mail eller en liste
	}
	elseif($action == "editMail"){
		// Ret den givne mail eller liste, slet login, hvis det er skift til liste, opret login hvis skift til mail
	}
	else{
		// Action er ikke sat, giv fejl
		echo "Fejl: Action ikke defineret eller defineret forkert.";
	}
?>