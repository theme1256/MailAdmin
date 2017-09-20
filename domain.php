<?php
	require $_SERVER["DOCUMENT_ROOT"]."/etc/header.php";
?>

<?php if(empty($_GET['mail'])):?>
<!-- Oversigt over domænet -->

<a href="/" class=""><?= $Content->out(34);?></a>

<h1><?= $Content->out(21);?> <?= $d;?></h1>

<p>
	<br/>
</p>

<ul class="list-unstyled">
	<?php
		$q = $con->prepare("SELECT * FROM forwardings WHERE domain LIKE (:d) GROUP BY address ORDER BY address ASC");
		$q->bindParam(":d", $d);
		$q->execute();
		$mails = $q->fetchAll(PDO::FETCH_ASSOC);
		foreach($mails as $mail){
			if($mail['is_list'] == 1){
				$type = $Content->out(27);
			} elseif($mail['is_alias'] == 1){
				$type = $Content->out(26);
			} else{
				$type = $Content->out(25);
			}
	?>
	<li><?php if($mail['is_forwarding'] == 0){?><a href="/domain/<?= $d.'/'.$mail['address'];?>"><?php }?><?= $mail['address']?> (<?= $type;?>)<?php if($mail['is_forwarding'] == 0){?></a><?php }?></li>
	<?php
		}
	?>
	<li>&nbsp;</li>
	<li><a href="/domain/<?= $d;?>/new"><?= $Content->out(22);?></a></li>
</ul>

<?php elseif($m == "new"):?>
<!-- Opret ny -->

<a href="/domain/<?= $d;?>" class=""><?= $Content->out(33);?></a>

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
	<input type="hidden" name="d" value="1"/>
	<div id="targets">
		<div class="form-group">
			<input type="text" class="form-control" name="1">
		</div>
	</div>
	<div class="form-group text-right">
		<a type="button" class="MOAR btn btn-info"><?= $Content->out(32);?></a>
		<button type="button" class="submit btn btn-primary"><?= $Content->out(31);?></button>
	</div>
	<?= $Content->statusBox();?>
</form>

<div class="form-group TEMPLATE">
	<input type="text" class="form-control template" name="">
</div>

<script type="text/javascript">
	$(function(){
		$(".submit").click(function(e){
			e.preventDefault();
			E = 0;
			var D = {};
			D.medium = "ajax";
			D.action = "create-email";
			D.domain = "<?= $d;?>";
			D.u = validate("#InputEmail");
			D.d = $("input[name='d']").val();
			D.i = 1;
			D.n = 1;
			D.dom = "";
			while(D.i <= D.d){
				var y = $("input[name=\""+D.i+"\"]").val();
				if(y.length > 0){
					if(D.i > D.n)
						D.dom += ",";
					D.dom += y;
				}
				if(D.dom.length == 0)
					D.n++;
				D.i++;
			}
			if(E == 0){
				call("<?= SCRIPTS;?>domain.php", D, function(d){
					statusBox(".status", d.msg, d.status);
					setTimeout(function(){
						window.location = "/domain/<?= $d;?>";
					}, 1500);
				}, ".status");
			} else{
				statusBox(".status", "Et felt er tomt.", "danger");
			}
		});

		// Skal håndtere at der bliver trykket for at få en boks mere frem til at vælge hvad brugeren skal være admin for
		$(".MOAR").click(function(e){
			e.preventDefault();
			var n = $("input[name='d']").val();
			n++;
			$(".TEMPLATE").clone().appendTo("#targets");
			$("#targets .template").attr("name", n);
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
	$q = $con->prepare("SELECT * FROM forwardings WHERE address LIKE (:a)");
	$q->bindParam(":a", $m);
	$q->execute();
	$mails = $q->fetchAll(PDO::FETCH_ASSOC);
?>

<a href="/domain/<?= $d;?>" class=""><?= $Content->out(33);?></a>

<h1><?= $Content->out(24);?> <?= $d;?></h1>

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
	<input type="hidden" name="d" value="<?= $q->rowCount();?>"/>
	<div id="targets">
	<?php
		foreach($mails as $line => $mail){
	?>
		<div class="form-group">
			<input type="text" class="form-control" name="<?= $line+1;?>" value="<?= $mail['forwarding'];?>">
		</div>
	<?php
		}
	?>
	</div>
	<div class="form-group text-right">
		<a type="button" class="MOAR btn btn-info"><?= $Content->out(32);?></a>
		<button type="button" class="submit btn btn-primary"><?= $Content->out(39);?></button>
		<button type="button" class="delete btn btn-danger"><?= $Content->out(40);?></button>
	</div>
	<?= $Content->statusBox();?>
</form>

<div class="form-group TEMPLATE">
	<input type="text" class="form-control template" name="">
</div>

<script type="text/javascript">
	$(function(){
		$(".delete").click(function(e){
			e.preventDefault();
			E = 0;
			var D = {};
			D.medium = "ajax";
			D.action = "delete-email";
			D.original = validate("#original");
			D.domain = "<?= $d;?>";
			if(E == 0){
				call("<?= SCRIPTS;?>domain.php", D, function(d){
					statusBox(".status", d.msg, d.status);
					setTimeout(function(){
						window.location = "/domain/<?= $d;?>";
					}, 1500);
				}, ".status");
			} else{
				statusBox(".status", "Et felt er tomt.", "danger");
			}
		});
		$(".submit").click(function(e){
			e.preventDefault();
			E = 0;
			var D = {};
			D.medium = "ajax";
			D.action = "update-email";
			D.original = validate("#original");
			D.domain = "<?= $d;?>";
			D.u = validate("#InputEmail");
			D.d = $("input[name='d']").val();
			D.i = 1;
			D.n = 1;
			D.dom = "";
			while(D.i <= D.d){
				var y = $("input[name=\""+D.i+"\"]").val();
				if(y.length > 0){
					if(D.i > D.n)
						D.dom += ",";
					D.dom += y;
				}
				if(D.dom.length == 0)
					D.n++;
				D.i++;
			}
			if(E == 0){
				call("<?= SCRIPTS;?>domain.php", D, function(d){
					statusBox(".status", d.msg, d.status);
					setTimeout(function(){
						window.location = "/domain/<?= $d;?>";
					}, 1500);
				}, ".status");
			} else{
				statusBox(".status", "Et felt er tomt.", "danger");
			}
		});

		// Skal håndtere at der bliver trykket for at få en boks mere frem til at vælge hvad brugeren skal være admin for
		$(".MOAR").click(function(e){
			e.preventDefault();
			var n = $("input[name='d']").val();
			n++;
			$(".TEMPLATE").clone().appendTo("#targets");
			$("#targets .template").attr("name", n);
			$("#targets .template").slideDown(250);
			$("#targets .template").removeClass("template");
			$("#targets .TEMPLATE").removeClass("TEMPLATE");
			$("input[name='d']").val(n);
		});
	});
</script>

<?php endif;?>

<?php
	require $_SERVER["DOCUMENT_ROOT"]."/etc/footer.php";
?>