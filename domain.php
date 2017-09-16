<?php
	require $_SERVER["DOCUMENT_ROOT"]."/etc/header.php";
?>

<?php if(empty($_GET['mail'])):?>
<!-- Oversigt over domænet -->

<h1><?= $Content->out(21);?> <?= $d;?></h1>

<p>
	<br/>
</p>

<ul class="list-unstyled">
	<?php
	?>
	<?php
	?>
	<li>&nbsp;</li>
	<li><a href="/domain/<?= $d;?>/new"><?= $Content->out(22);?></a></li>
</ul>

<?php elseif($m == "new"):?>
<!-- Opret ny -->

<h1><?= $Content->out(23);?> <?= $d;?></h1>

<p>
	<br/>
</p>

<form action="">
	<!--  -->
</form>

<script type="text/javascript">
	// 
</script>

<?php else:?>
<!-- Ret emailen -->

<h1><?= $Content->out(24);?> <?= $d;?></h1>

<p>
	<br/>
</p>

<form action="">
	<!--  -->
</form>

<script type="text/javascript">
	// 
</script>

<?php endif;?>

<?php
	require $_SERVER["DOCUMENT_ROOT"]."/etc/footer.php";
?>