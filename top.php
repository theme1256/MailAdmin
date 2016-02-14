<?php
	include "common.php";
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Folkmann mailadmin</title>
		<meta charset="utf-8">
		<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
		<link rel="shortcut icon" href="/favicon.png" />
		<meta http-equiv="Pragma" content="no-cache"/>
		<link href='https://fonts.googleapis.com/css?family=Lato:400,700,400italic&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js" type="text/javascript"></script>
		<link rel="stylesheet" href="/style.css" type="text/css" media="all" />
		<script type="text/javascript">
		var cleaner;
		var interval

		// Fjerner msg og sætter en timer til at få tømt den
		function clean(){
			$(".msg").slideUp(300);
			cleaner = setInterval("empt()", 2000);
		}

		// Tømmer msg
		function empt(){
			$(".msg").html("");
			clearInterval(cleaner);
		}

		// Sætter en besked "m" med classen "v"
		function setmsg(m, v){
			$(".msg").html("<div class=\""+v+"\">"+m+"</div>");
			$(".msg").slideDown(300);
			cleaner = setInterval("clean()", 10000);
		}

		function ReLoad(){
			location.reload();
			clearInterval(interval);
		}

		function Load(l){
			location.href = l;
		}
		</script>
	</head>
	<body>
		<div class="header">
			Folkmann mailadmin
			<?php if(!empty($d)){?>
			<span>Domæne: <?php echo $d;?></span>
			<?php }?>
		</div>
		<div class="content">
			<div class="msg"></div>
<?php
	if(!access()){
		// Der er ikke logget ind, vis login
		include "login.php";
		die();
	}
?>