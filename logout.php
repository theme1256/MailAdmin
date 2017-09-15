<?php
	require $_SERVER["DOCUMENT_ROOT"]."/etc/common.php";

	session_destroy();

	header("Location: /");
?>