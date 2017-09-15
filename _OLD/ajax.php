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
		if(!mysqli_query($db, "INSERT INTO login (u, p) VALUES ('$u','$p')")){
			mysqli_rollback($db);
			echo "Fejl: SQL, indtastning af bruger.";
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
		if(!mysqli_query($db, "INSERT INTO con (d, uID) VALUES $v")){
			mysqli_rollback($db);
			echo "Fejl: SQL, rettighedder til bruger.";
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
			if(!mysqli_query($db, "UPDATE login SET u='$u' WHERE uID=$uID")){
				mysqli_rollback($db);
				echo "Fejl: SQL, opdatering af navn.";
				exit;
			}
		}
		if(!empty($p)){
			$p = haash($p);
			if(!mysqli_query($db, "UPDATE login SET p='$p' WHERE uID=$uID")){
				mysqli_rollback($db);
				echo "Fejl: SQL, opdatering af kode.";
				exit;
			}
		}
		if(!mysqli_query($db, "DELETE FROM con WHERE uID=$uID")){
			mysqli_rollback($db);
			echo "Fejl: SQL, sletning af gamle rettighedder.";
			exit;
		}
		$v = "";
		$V = explode(",", $dd);
		foreach($V as $k => $val){
			if($k > 0)
				$v .= ",";
			$v .= "('$val','$uID')";
		}
		if(!mysqli_query($db, "INSERT INTO con (d, uID) VALUES $v")){
			mysqli_rollback($db);
			echo "Fejl: SQL, rettighedder til bruger.";
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
			if(!mysqli_query($db, "UPDATE login SET u='$u' WHERE uID=$uID")){
				mysqli_rollback($db);
				echo "Fejl: SQL, opdatering af navn.";
				exit;
			}
		}
		if(!empty($p)){
			$p = haash($p);
			if(!mysqli_query($db, "UPDATE login SET p='$p' WHERE uID=$uID")){
				mysqli_rollback($db);
				echo "Fejl: SQL, opdatering af kode.";
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
			if(password_verify($p, $r['p']) == $r['p']){
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
	elseif($action == "newMail"){
		// Opret ny mail eller liste, tjek først om ting er tomme, derefter om det er en mail eller en liste
		$t = rens($_POST['t']); // "mail" eller "list"
		$b = rens($_POST['b']); // Antallet af modtagere
		$bb = rens($_POST['bb']); // Listen med modtagere, hvis det er en liste
		$m = rens($_POST['m']); // Mailadressen minus domænet
		$d = rens($_POST['d']); // Domænet
		$p = rens($_POST['p']); // Kodeordet der skal bruges hvis det er en lokal mail
		// Tjek tomme felter
		if($t == "mail"){
			if(empty($p)){
				echo "Fejl: Der er ikke skrevet et kodeord.";
				exit;
			}
			if(empty($m)){
				echo "Fejl: Der er ikke skrevet noget før @$d.";
				exit;
			}
		}
		else{
			if(empty($bb) || $bb == "undefined"){
				echo "Fejl: Der er ikke nogne modtagere.";
				exit;
			}
		}
		// Tjek om adressen allerede findes
		if(mysqli_num_rows(mysqli_query($db, "SELECT * FROM alias WHERE address='$m@$d'")) > 0){
			echo "Fejl: Den mail er allerede i databasen.";
			exit;
		}
		// Opret som liste
		if(!mysqli_query($db, "INSERT INTO alias (address, goto) VALUES ('$m@$d','$bb')")){
			mysqli_rollback($db);
			echo "Fejl: SQL, oprettelse af liste. $sql";
			exit;
		}
		else{
			mysqli_commit($db);
			echo "Succes";
		}
	}
	elseif($action == "editMail"){
		// Ret den givne mail eller liste, slet login, hvis det er skift til liste, opret login hvis skift til mail
		$b = rens($_POST['b']); // Antallet af modtagere
		$bb = rens($_POST['bb']); // Listen med modtagere, hvis det er en liste
		$m = rens($_POST['m']); // Mailadressen minus domænet
		$d = rens($_POST['d']); // Domænet
		$aID = rens($_POST['aID']); // pkid til alias, hvis det var en liste
		// Tjek tomme felter
		if(empty($bb) || $bb == "undefined"){
			echo "Fejl: Der er ikke nogne modtagere.";
			exit;
		}
		// Opdater DB
		// Var en liste, skal stadig være det
		// Opdater pkid til det nye
		if(!mysqli_query($db, "UPDATE alias SET address='$m@$d', goto='$bb' WHERE address='$aID'")){
			mysqli_rollback($db);
			echo "Fejl: SQL, opdatering af liste.";
			exit;
		}
		else{
			mysqli_commit($db);
			echo "Succes";
		}
	}
	elseif($action == "deleteMail"){
		// Sletter den givne liste eller mail
		$aID = rens($_POST['aID']);
		$u = rens($_POST['u']);
		// Slet bruger
		if(!empty($u)){
			if(!mysqli_query($db, "DELETE FROM users WHERE id='$u'")){
				mysqli_rollback($db);
				echo "Fejl: Kunne ikke slette bruger.";
				exit;
			}
		}
		if(!mysqli_query($db, "DELETE FROM alias WHERE address='$aID'")){
			mysqli_rollback($db);
			echo "Fejl: Kunne ikke slette alias";
			exit;
		}
		else{
			mysqli_commit($db);
			echo "Succes";
		}
	}
	else{
		// Action er ikke sat, giv fejl
		echo "Fejl: Action ikke defineret eller defineret forkert.";
	}
?>