<?php
	include "common.php";
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>Folkmann mailadmin</title>
		<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
		<link rel="shortcut icon" href="/favicon.png" />
		<meta http-equiv="Pragma" content="no-cache"/>
		<link href='https://fonts.googleapis.com/css?family=Lato:400,700,400italic&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
		<!-- jQuery -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js" type="text/javascript"></script>
		<!-- Bootstrap -->
		<link href="/css/bootstrap.min.css" rel="stylesheet">
		<script src="/js/bootstrap.min.js"></script>
		<link rel="stylesheet" href="/css/theme.css" type="text/css" media="all" />
		<link rel="stylesheet" href="/css/sticky-footer-navbar.css" type="text/css" media="all" />
		<script type="text/javascript">
		// Fjerner msg og sætter en timer til at få tømt den
		function clean(){
			$(".msg").slideUp(300);
			setTimeout("empt()", 2000);
		}

		// Tømmer msg
		function empt(){
			$(".msg").html("");
		}

		// Sætter en besked "m" med classen "v"
		function setmsg(m, v){
			$(".msg").slideUp(0);
			$(".msg").html("<div class=\"alert "+v+"\" role=\"alert\">"+m+"</div>");
			$(".msg").slideDown(300);
			setTimeout("clean()", 10000);
		}

		function ReLoad(){
			window.location = '<?php echo $_SERVER['REQUEST_URI'];?>';
		}

		function Load(l){
			location.href = l;
		}
		</script>
	</head>
	<body>
		<!-- Fixed navbar -->
		<nav class="navbar navbar-inverse navbar-fixed-top">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="/">Folkmann mailadmin</a>
				</div>
				<div id="navbar" class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
						<?php if(access()):?>
						<li<?php if(!isItThisPage("user") && !isItThisPage("domain/new")){?> class="active"<?php }?>><a href="/">Domæner</a></li>
						<li<?php if(isItThisPage("user")){?> class="active"<?php }?>><a href="/user">Ret bruger</a></li>
						<?php if($u == 1):?>
						<li<?php if(isItThisPage("domain/new")){?> class="active"<?php }?>><a href="/domain/new">Opret nyt domæne</a></li>
						<?php endif;?>
						<li><a href="/logout">Log ud</a></li>
						<?php endif;?>
					</ul>
				</div><!--/.nav-collapse -->
			</div>
		</nav>
		<div class="container" role="main">
			<div class="row">
				<div class="col-md-6 col-md-offset-3">
					<div class="msg"></div>
				</div>
			</div>
<?php
	if(!access()){
		// Der er ikke logget ind, vis login
		include "login.php";
		die();
	}
?>