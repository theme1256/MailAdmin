<?php
	require $_SERVER["DOCUMENT_ROOT"]."/etc/header.php";
?>

<h1><?= $Content->out(10);?></h1>

<p>
	<?= $Content->out(20);?><br/>
</p>

<ul class="list-unstyled">
	<?php
		$q = $con->prepare("SELECT domain, description FROM domain ORDER BY domain ASC");
		$q->execute();
		$domains = $q->fetchAll();
		foreach($domains as $domain){
			if($Content->access($domain['domain'])){
	?>
	<li><a href="/domain/<?= $domain['domain']?>"><?= $domain['domain']?> <?= "(".$domain['description'].")";?></a></li>
	<?php
			}
		}
	?>
</ul>

<?php
	require $_SERVER["DOCUMENT_ROOT"]."/etc/footer.php";
?>