<?php
	require $_SERVER["DOCUMENT_ROOT"]."/etc/common.php";

	$l = $Content->clean($_GET['l']);

	if(in_array($l, ['da', 'en']))
		setcookie("lang", $l, time()+60*60*24*365);

	header("Location: ".$_SERVER['HTTP_REFERER']);
?>