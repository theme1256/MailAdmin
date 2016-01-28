<?php
	session_start();
	$db = mysqli_connect("localhost","jb","5banden","maildb");

	// Tjekker om en bruger er logget ind og returnerer true/false
	function access($d = NULL){
		if(empty($_SESSION['user'])){
			return false;
		}
		else{
			if($d != NULL){
				// Tjekker adgang til de domæne
				$u = $_SESSION['user'];
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

	// Password-stuff
	function haash($p, $s = NULL){
		$cost = 10;
		if($s == NULL){
			$salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
			$salt = sprintf("$2a$%02d$", $cost) . $salt;
		else{
			$salt = $s;
		}
		return crypt($password, $salt);
	}
?>