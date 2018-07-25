<?php
	require_once $_SERVER["DOCUMENT_ROOT"]."/etc/header.php";
?>

<h1><?= $Content->out(10);?></h1>

<p>
	<?= $Content->out(20);?><br/>
</p>

<ul class="list-unstyled">
	<?php
		$q = $con->query("SELECT domain, description FROM domain ORDER BY domain ASC");
		foreach($q->fetchAll(PDO::FETCH_ASSOC) as $domain){
			if($Content->access($domain['domain'])){
	?>
	<li><a href="<?= HOME;?>domain/<?= $domain['domain']?>"><?= $domain['domain']?> <?= "(".$domain['description'].")";?></a></li>
	<?php
			}
		}
	?>
</ul>

<?php
	include $_SERVER["DOCUMENT_ROOT"]."/etc/footer.php";
?>