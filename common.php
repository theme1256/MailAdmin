<?php
	session_start();
	$db = mysqli_connect("localhost","mail","37WJCKaJKjE6aXXh","vmail");

	// Tjekker om en bruger er logget ind og returnerer true/false
	$u = 0;
	function access($d = NULL){
		global $u;
		if(empty($_SESSION['user'])){
			return false;
		}
		else{
			$u = $_SESSION['user'];
			if($d != NULL){
				// Tjekker adgang til de domæne
				if(mysqli_num_rows(mysqli_query($db, "SELECT * FROM con WHERE uID=$u AND $dID=$d")) > 0){
					return true;
				}
				else{
					return false;
				}
			}
			else{
				return true;
			}
		}
	}

	// Renser en variabel, så den kan sættes ind i databasen uden at den skader
	function rens($felt){
		$felt = stripslashes($felt);
		$felt = strip_tags($felt);
		$felt = addslashes($felt);
		return $felt;
	}

	// Henter variabler fra URL
	if(!empty($_GET['domain'])){
		$d = rens($_GET['domain']);
		if(!empty($_GET['mail'])){
			$m = rens($_GET['mail']);
		}
	}

	// Returnerer navnet på den nuværende fil og hvilken mappe den er i
	function pageName(){
		return str_replace(".php", "", $_SERVER['REQUEST_URI']);
	}
	// Tjekker om det givne er den side man er på lige nu
	function isItThisPage($check){
		if($check == pageName() || strpos(pageName(), $check))
			return true;
		else
			return false;
	}

	// Password-stuff
	function haash($p){
		$cost = 10;
		$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
		$salt = sprintf("$2a$%02d$", $cost) . $salt;
		return password_hash($p, PASSWORD_BCRYPT, ['cost' => $cost, 'salt' => $salt]);
	}
?>