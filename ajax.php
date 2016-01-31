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
		if(!mysqli_query($db, "INSERT INTO con (dID, uID) VALUES $v")){
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
		if(!mysqli_query($db, "INSERT INTO con (dID, uID) VALUES $v")){
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
		if(!mysqli_query($db, "INSERT INTO domains (domain) VALUES ('$d')")){
			mysqli_rollback($db);
			echo "Fejl: SQL, indtastning af domæne.";
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
		if(!mysqli_query($db, "INSERT INTO con (dID, uID) VALUES $v")){
			mysqli_rollback($db);
			echo "Fejl: SQL, rettighedder til bruger.";
			exit;
		}
		else{
			mysqli_commit($db);
			echo "Succes";
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
		if(mysqli_num_rows(mysqli_query($db, "SELECT * FROM aliases WHERE mail='$m@$d'")) > 0){
			echo "Fejl: Den mail er allerede i databasen.";
			exit;
		}
		if($t == "mail"){
			// Opret som lokal mail, husk at oprette mapper det rigtige sted
			// Mappe: /var/spool/mail/virtual/[domæne]/[mail]/
			// Opret alias
			if(!mysqli_query($db, "INSERT INTO aliases (mail, destination) VALUES ('$m@$d','$m@$d')")){
				mysqli_rollback($db);
				echo "Fejl: SQL, oprettelse af internt alias. $sql";
				exit;
			}
			// Oprettelse af bruger
			if(!mysqli_query($db, "INSERT INTO users (id, name, maildir, crypt) VALUES ('$m@$d','$m','$d/$m/',encrypt('$p', CONCAT('$5$', MD5(RAND()))))")){
				mysqli_rollback($db);
				echo "Fejl: SQL, oprettelse af internt alias. $sql";
				exit;
			}
			// Opret mappe til domæne, hvis den ikke findes
			exec('sudo /var/mail/virtual/dir.sh '.$d);
			if(!is_dir("/var/mail/virtual/$d")){
				mysqli_rollback($db);
				echo "Fejl: Kunne ikke oprette mappe til domænet.";
				exit;
			}
			// Opret mappe til bruger, hvis den ikke findes
			exec('sudo /var/mail/virtual/dir2.sh '.$d.' '.$m);
			if(!is_dir("/var/mail/virtual/$d/$m")){
				mysqli_rollback($db);
				echo "Fejl: Kunne ikke oprette mappe til mailen.";
				exit;
			}
			else{
				mysqli_commit($db);
				exec('chmod 775 -R /var/mail/virtual');
				echo "Succes";
			}
		}
		else{
			// Opret som liste
			if(!mysqli_query($db, "INSERT INTO aliases (mail, destination) VALUES ('$m@$d','$bb')")){
				mysqli_rollback($db);
				echo "Fejl: SQL, oprettelse af liste. $sql";
				exit;
			}
			else{
				mysqli_commit($db);
				echo "Succes";
			}
		}
	}
	elseif($action == "editMail"){
		// Ret den givne mail eller liste, slet login, hvis det er skift til liste, opret login hvis skift til mail
		$t = rens($_POST['t']); // "mail" eller "list"
		$b = rens($_POST['b']); // Antallet af modtagere
		$bb = rens($_POST['bb']); // Listen med modtagere, hvis det er en liste
		$m = rens($_POST['m']); // Mailadressen minus domænet
		$d = rens($_POST['d']); // Domænet
		$p = rens($_POST['p']); // Kodeordet der skal bruges hvis det er en lokal mail
		$aID = rens($_POST['aID']); // pkid til alias, hvis det var en liste
		$u = rens($_POST['u']); // Den gamle mail, hvis det var en lokal mail
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
		// Opdater DB
		if($u == "" && $t == "list"){
			// Var en liste, skal stadig være det
			// Opdater pkid til det nye
			if(!mysqli_query($db, "UPDATE aliases SET mail='$m@$d', destination='$bb' WHERE pkid=$aID")){
				mysqli_rollback($db);
				echo "Fejl: SQL, opdatering af liste.";
				exit;
			}
			else{
				mysqli_commit($db);
				echo "Succes";
			}
		}
		elseif($u == "" && $t == "mail"){
			// Var en liste, skal være en lokal mail
			// Ret alias
			if(!mysqli_query($db, "UPDATE aliases SET mail='$m@$d', destination='$m@$d' WHERE pkid=$aID")){
				mysqli_rollback($db);
				echo "Fejl i opdatering af alias.";
				exit;
			}
			// Opret bruger
			if(!mysqli_query($db, "INSERT INTO users (id, name, maildir, crypt) VALUES ('$m@$d','$m','$d/$m/',encrypt('$p', CONCAT('$5$', MD5(RAND()))))")){
				mysqli_rollback($db);
				echo "Fejl i oprettelse af bruger.";
				exit;
			}
			// Opret mappe til domæne, hvis den ikke findes
			exec('sudo /var/mail/virtual/dir.sh '.$d);
			if(!is_dir("/var/mail/virtual/$d")){
				mysqli_rollback($db);
				echo "Fejl: Kunne ikke oprette mappe til domænet.";
				exit;
			}
			// Opret mappe til bruger, hvis den ikke findes
			exec('sudo /var/mail/virtual/dir2.sh '.$d.' '.$m);
			if(!is_dir("/var/mail/virtual/$d/$m")){
				mysqli_rollback($db);
				echo "Fejl: Kunne ikke oprette mappe til mailen.";
				exit;
			}
			else{
				mysqli_commit($db);
				echo "Succes";
			}
		}
		elseif(strlen($u) > 0 && $t == "list"){
			// Var en mail, skal være en liste
			// Fjern fra users
			if(!mysqli_query($db, "DELETE FROM users WHERE id='$m@$d'")){
				mysqli_rollback($db);
				echo "Fejl i opdatering af kodeord.";
				exit;
			}
			// Opdater alias
			if(!mysqli_query($db, "UPDATE aliases SET mail='$m@$d', destination='$bb' WHERE pkid=$aID")){
				mysqli_rollback($db);
				echo "Fejl i opdatering af alias.";
				exit;
			}
			else{
				mysqli_commit($db);
				echo "Succes";
			}
		}
		else{
			// Går ud fra at det var en mail og stadig skal være en
			// Beholder den gamle mappe, da der ikke ændres mail-navn
			// Opdaterer kode
			if(!mysqli_query($db, "UPDATE users SET crypt=encrypt('$p', CONCAT('$5$', MD5(RAND()))) WHERE id='$m@$d'")){
				mysqli_rollback($db);
				echo "Fejl i opdatering af kodeord.";
				exit;
			}
			else{
				mysqli_commit($db);
				echo "Succes";
			}
		}
	}
	else{
		// Action er ikke sat, giv fejl
		echo "Fejl: Action ikke defineret eller defineret forkert.";
	}
?>