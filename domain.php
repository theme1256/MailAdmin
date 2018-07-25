<?php
	require_once $_SERVER["DOCUMENT_ROOT"]."/etc/header.php";
?>

<?php if(empty($_GET['mail'])):?>
<!-- Oversigt over domænet -->

<a href="<?= HOME;?>" class=""><?= $Content->out(34);?></a>

<h1><?= $Content->out(21);?> <?= $d;?></h1>

<p>
	<br/>
</p>

<ul class="list-unstyled">
	<?php
		$q = $con->prepare("SELECT is_list, is_alias, is_forwarding, address, COUNT(*) AS antal FROM forwardings WHERE domain LIKE (:d) GROUP BY address ORDER BY address ASC");
		$q->bindParam(":d", $d);
		$q->execute();
		foreach($q->fetchAll(PDO::FETCH_ASSOC) as $mail){
			if($mail['is_list'] == 1){
				$type = $Content->out(27);
			} elseif($mail['is_alias'] == 1){
				$type = $Content->out(26);
			} else{
				$type = $Content->out(25);
			}
	?>
	<li>
		<?php if($mail['is_forwarding'] == 0){?>
		<a href="<?= HOME;?>domain/<?= $d.'/'.$mail['address'];?>"><?= $mail['address']?> (<?= $type;?>)</a>
		<?php } elseif($mail['is_forwarding'] == 1){?>
		<?= $mail['address']?> (<?= $type;?>) <a href="<?= HOME;?>domain/<?= $d.'/'.$mail['address'];?>">
			<?php if($mail['antal'] > 1){?>
				<?= $Content->out(51);?>
			<?php } else{?>
				<?= $Content->out(50);?>
			<?php }?>
		</a>
		<?php }?>
	</li>
	<?php
		}
	?>
	<li>&nbsp;</li>
	<li><a href="<?= HOME;?>domain/<?= $d;?>/new"><?= $Content->out(22);?></a></li>
</ul>

<?php elseif($m == "new"):?>
<!-- Opret ny -->

<a href="<?= HOME;?>domain/<?= $d;?>" class=""><?= $Content->out(33);?></a>

<h1><?= $Content->out(23);?> <?= $d;?></h1>

<p>
	<br/>
</p>

<form class="domain col-md-8 col-md-offset-2" action="<?= SCRIPTS;?>domain.php" method="post">
	<input type="hidden" name="method" value="post">
	<input type="hidden" name="action" value="create-email">
	<div class="form-group">
		<label for="InputEmail"><?= $Content->out(28);?>:</label>
		<input type="text" class="form-control" id="InputEmail" name="u" autocomplete="off">
	</div>
	<h3><?= $Content->out(29);?>:</h3>
	<?= $Content->out(30);?><br/><br/>
	<input type="hidden" name="d" value="dom[]"/>
	<div id="targets">
		<div class="form-group">
			<input type="text" class="form-control" name="dom[]">
		</div>
	</div>
	<div class="form-group text-right">
		<a type="button" class="MOAR btn btn-info"><?= $Content->out(32);?></a>
		<button type="button" class="submit btn btn-primary"><?= $Content->out(31);?></button>
	</div>
	<?= $Content->statusBox();?>
</form>

<div class="form-group TEMPLATE">
	<input type="text" class="form-control template" name="dom[]">
</div>

<script type="text/javascript">
	$(function(){
		$(".submit").click(function(e){
			e.preventDefault();
			E = 0;
			let Data = objectifyForm($("form.domain").serializeArray());
			Data.medium = "ajax";
			Data.domain = "<?= $d;?>";
			if(E == 0){
				call("<?= SCRIPTS;?>domain.php", Data, function(d){
					statusBox(".status", d.msg, d.status);
					setTimeout(function(){
						window.location = "/domain/<?= $d;?>";
					}, 1500);
				}, ".status");
			} else{
				statusBox(".status", "<?= $Content->out(35);?>", "danger");
			}
		});

		// Skal håndtere at der bliver trykket for at få en boks mere frem til at vælge hvad brugeren skal være admin for
		$(".MOAR").click(function(e){
			e.preventDefault();
			$(".TEMPLATE").clone().appendTo("#targets");
			$("#targets .template").slideDown(250);
			$("#targets .template").removeClass("template");
			$("#targets .TEMPLATE").removeClass("TEMPLATE");
			$("input[name='d']").val(n);
		});
	});
</script>

<?php else:?>
<!-- Ret emailen -->
<?php
	$q = $con->prepare("SELECT is_list, is_alias, is_forwarding, address, forwarding FROM forwardings WHERE address LIKE (:a)");
	$q->bindParam(":a", $m);
	$q->execute();
	$mails = $q->fetchAll(PDO::FETCH_ASSOC);
?>

<a href="<?= HOME;?>domain/<?= $d;?>" class=""><?= $Content->out(33);?></a>

<?php if($mails[0]['is_forwarding'] == 1):?>
<h1><?= $Content->out(52);?> <?= $d;?></h1>
<?php else:?>
<h1><?= $Content->out(24);?> <?= $d;?></h1>
<?php endif;?>

<p>
	<br/>
</p>

<form class="domain col-md-8 col-md-offset-2" action="<?= SCRIPTS;?>domain.php" method="post">
	<input type="hidden" name="method" value="post">
	<input type="hidden" name="original" id="original" value="<?= $m;?>">
	<input type="hidden" name="action" value="update-email">
	<div class="form-group">
		<label for="InputEmail"><?= $Content->out(28);?>:</label>
		<input type="text" class="form-control" id="InputEmail" name="u" autocomplete="off" value="<?= str_replace("@".$d, "", $m);?>">
	</div>
	<h3><?= $Content->out(29);?>:</h3>
	<?= $Content->out(30);?><br/><br/>
	<div id="targets">
	<?php
		foreach($mails as $line => $mail){
	?>
		<div class="form-group">
			<input type="text" class="form-control" name="dom[]" value="<?= $mail['forwarding'];?>"<?= ($mail['forwarding'] == $m ? "disabled title='".$Content->out(49)."'" : "");?>>
		</div>
	<?php
		}
	?>
	</div>
	<div class="form-group text-right">
		<a type="button" class="MOAR btn btn-info"><?= $Content->out(32);?></a>
		<button type="button" class="submit btn btn-primary"><?= $Content->out(39);?></button>
		<?php if($mails[0]['is_forwarding'] == 0):?>
		<button type="button" class="delete btn btn-danger"><?= $Content->out(40);?></button>
		<?php endif;?>
	</div>
	<?= $Content->statusBox();?>
</form>

<div class="form-group TEMPLATE">
	<input type="text" class="form-control template" name="dom[]">
</div>

<script type="text/javascript">
	$(function(){
		<?php if($mails[0]['is_forwarding'] == 0):?>
		$(".delete").click(function(e){
			e.preventDefault();
			E = 0;
			let Data = {};
			Data.medium = "ajax";
			Data.action = "delete-email";
			Data.original = validate("#original");
			Data.domain = "<?= $d;?>";
			if(E == 0){
				call("<?= SCRIPTS;?>domain.php", Data, function(d){
					statusBox(".status", d.msg, d.status);
					setTimeout(function(){
						window.location = "/domain/<?= $d;?>";
					}, 1500);
				}, ".status");
			} else{
				statusBox(".status", "<?= $Content->out(35);?>", "danger");
			}
		});
		<?php endif;?>
		$(".submit").click(function(e){
			e.preventDefault();
			E = 0;
			let Data = objectifyForm($("form.domain").serializeArray());
			Data.medium = "ajax";
			Data.domain = "<?= $d;?>";
			if(E == 0){
				call("<?= SCRIPTS;?>domain.php", Data, function(d){
					statusBox(".status", d.msg, d.status);
					setTimeout(function(){
						window.location = "/domain/<?= $d;?>";
					}, 1500);
				}, ".status");
			} else{
				statusBox(".status", "<?= $Content->out(35);?>", "danger");
			}
		});

		// Skal håndtere at der bliver trykket for at få en boks mere frem til at vælge hvad brugeren skal være admin for
		$(".MOAR").click(function(e){
			e.preventDefault();
			$(".TEMPLATE").clone().appendTo("#targets");
			$("#targets .template").slideDown(250);
			$("#targets .template").removeClass("template");
			$("#targets .TEMPLATE").removeClass("TEMPLATE");
			$("input[name='d']").val(n);
		});
	});
</script>

<?php endif;?>

<?php
	include $_SERVER["DOCUMENT_ROOT"]."/etc/footer.php";
?>