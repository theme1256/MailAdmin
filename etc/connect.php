<?php
	session_start();

	// Read configuration
	if(file_exists(__DIR__ . "/conf.php"))
		require_once(__DIR__ . "/conf.php");
	else
		die("Couldn't read configuration.");

	// Connect to the database
	try{
		$con = new PDO("mysql:host=".$db['host'].";dbname=".$db['name'].";charset=utf8mb4", $db['username'], $db['password']);
		$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$con->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
	} catch(PDOException $e){
		die("Error! " . $e->getMessage() . "<br/>");
	}
?>