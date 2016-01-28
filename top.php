<?php
	include "common.php";
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Folkmann mailadmin</title>
		<meta charset="utf-8">
		<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
		<meta http-equiv="Pragma" content="no-cache"/>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js" type="text/javascript"></script>
		<link rel="stylesheet" href="style.css" type="text/css" media="all" />
	</head>
	<body>
		<div class="header">
			Folkmann mailadmin
			<?php if(!empty($d)){?>
			<span>Domæne: <?php echo $d;?></span>
			<?php }?>
		</div>
<?php
	/*if(!access()){
		// Der er ikke logget ind, vis login
		include "login.php";
		die();
	}*/
?>