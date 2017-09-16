<?php
	require $_SERVER["DOCUMENT_ROOT"]."/etc/header.php";
?>

<?php if(empty($_GET['u'])):?>
<!-- Vis liste med brugere -->

<h1>Brugere i systemet</h1>

<ul class="list-unstyled">
	<?php
		$q = $con->prepare("SELECT * FROM ma_login ORDER BY user ASC");
		$q->execute();
		$users = $q->fetchAll(PDO::FETCH_ASSOC);
		foreach($users as $user){
	?>
	<li><a href="/admin/<?= $user['user'];?>"><?= $user['user'];?></a></li>
	<?php
		}
	?>
	<li>&nbsp;</li>
	<li><a href="/admin/new">Opret ny bruger</a></li>
</ul>

<?php elseif($_GET['u'] == "new"):?>
<!-- Form til at oprette ny bruger -->

<h1>Opret ny bruger</h1>

<form class="admin col-md-8 col-md-offset-2" action="<?= SCRIPTS;?>admin.php" method="post">
	<input type="hidden" name="method" value="post">
	<input type="hidden" name="action" value="create-user">
	<div class="form-group">
		<label for="InputEmail"><?= $Content->out(5);?>:</label>
		<input type="text" class="form-control" id="InputEmail" name="u" autocomplete="off">
	</div>
	<div class="form-group">
		<label for="InputPassword"><?= $Content->out(6);?>:</label>
		<input type="password" class="form-control" id="InputPassword" name="p" autocomplete="off">
	</div>
	<h3>Domæner:</h3>
	Tomme felter bliver slettet.<br/><br/>
	<input type="hidden" name="d" value="1"/>
	<div id="domains">
		<div class="form-group">
			<select class="form-control input-sm" name="1">
				<option value="0">vælg en</option>
				<?php
					$q = $con->prepare("SELECT * FROM domain ORDER BY domain ASC");
					$q->execute();
					$domains = $q->fetchAll(PDO::FETCH_ASSOC);
					foreach($domains as $domain){
				?>
				<option value="<?= $domain['domain'];?>"><?= $domain['domain'];?></option>
				<?php }?>
			</select>
		</div>
	</div>
	<div class="form-group text-right">
		<a type="button" class="MOAR btn btn-info">Endnu et domæne</a>
		<button type="button" class="submit btn btn-primary">Opret bruger</button>
	</div>
	<?= $Content->statusBox();?>
</form>

<div class="form-group TEMPLATE">
	<select class="template form-control input-sm" name="">
		<option value="0">vælg en</option>
			<?php
				foreach($domains as $domain){
			?>
			<option value="<?= $domain['domain'];?>"><?= $domain['domain'];?></option>
			<?php }?>
	</select>
</div>

<script type="text/javascript">
	$(function(){
		$(".submit").click(function(e){
			e.preventDefault();
			E = 0;
			var D = {};
			D.method = "ajax";
			D.action = "create-user";
			D.u = validate("#InputEmail");
			D.p = validate("#InputPassword");
			D.d = $("input[name='d']").val();
			D.i = 1;
			D.n = 1
			D.dom = "";
			while(D.i <= D.d){
				var y = $("select[name=\""+D.i+"\"]").val();
				if(y > 0){
					if(D.i > D.n)
						D.dom += ",";
					D.dom += y;
				}
				if(D.dom.length == 0)
					D.n++;
				D.i++;
			}
			call("<?= SCRIPTS;?>admin.php", D, function(d){
				statusBox(".status", d.msg, d.status);
				setTimeout(function(){
					window.location = "/admin";
				}, 1500);
			}, ".status");
		});

		// Skal håndtere at der bliver trykket for at få en boks mere frem til at vælge hvad brugeren skal være admin for
		$(".MOAR").click(function(e){
			e.preventDefault();
			var n = $("input[name='d']").val();
			n++;
			$(".TEMPLATE").clone().appendTo("#domains");
			$("#domains .template").attr("name", n);
			$("#domains .template").slideDown(250);
			$("#domains .template").removeClass("template");
			$("#domains .TEMPLATE").removeClass("TEMPLATE");
			$("input[name='d']").val(n);
		});
	});
</script>

<?php else:?>
<?php
	$u = $_GET['u'];
	$q = $con->prepare("SELECT * FROM ma_login WHERE user LIKE (:u)");
	$q->bindParam(":u", $u);
	$q->execute();
	$U = $q->fetch(PDO::FETCH_ASSOC);
?>
<!-- Ret brugeren -->

<h1>Ret bruger</h1>

<form class="admin col-md-8 col-md-offset-2" action="<?= SCRIPTS;?>admin.php" method="post">
	<input type="hidden" name="method" value="post">
	<input type="hidden" name="action" value="update-user">
	<input type="hidden" id="uID" name="uID" value="<?= $U['userID'];?>">
	<div class="form-group">
		<label for="InputEmail"><?= $Content->out(5);?>:</label>
		<input type="text" class="form-control" id="InputEmail" name="u" value="<?= $U['user'];?>" autocomplete="off">
	</div>
	<div class="form-group">
		<label for="InputPassword"><?= $Content->out(6);?>:</label>
		<input type="password" class="form-control" id="InputPassword" name="p" autocomplete="off">
	</div>
	<h3>Domæner:</h3>
	Tomme felter bliver slettet.<br/><br/>
	<?php
		$q = $con->prepare("SELECT * FROM ma_access INNER JOIN domain ON domain.domain=ma_access.domain WHERE userID LIKE (:uID) ORDER BY domain.domain ASC");
		$q->bindParam(":uID", $U['userID']);
		$q->execute();
		$i = 1;
	?>
	<input type="hidden" name="d" value="<?= $q->rowCount();?>"/>
	<div id="domains">
		<?php
			$selected = $q->fetchAll(PDO::FETCH_ASSOC);
			$q = $con->prepare("SELECT * FROM domain ORDER BY domain ASC");
			$q->execute();
			$domains = $q->fetchAll(PDO::FETCH_ASSOC);
			foreach($selected as $line){
		?>
		<div class="form-group">
			<select class="form-control input-sm" name="<?= $i;?>">
				<option value="0">vælg en</option>
				<?php
					$i++;
					foreach($domains as $domain){
				?>
				<option value="<?= $domain['domain'];?>"<?php if($domain['domain'] == $line['domain']){ echo " selected=\"selected\"";}?>><?= $domain['domain'];?></option>
				<?php }?>
			</select>
		</div>
		<?php }?>
	</div>
	<div class="form-group text-right">
		<a type="button" class="MOAR btn btn-info">Endnu et domæne</a>
		<button type="button" class="submit btn btn-primary">Ret bruger</button>
		<button type="button" class="submit btn btn-danger">Slet bruger</button>
	</div>
	<?= $Content->statusBox();?>
</form>

<div class="form-group TEMPLATE">
	<select class="template form-control input-sm" name="">
		<option value="0">vælg en</option>
			<?php
				foreach($domains as $domain){
			?>
			<option value="<?= $domain['domain'];?>"><?= $domain['domain'];?></option>
			<?php }?>
	</select>
</div>

<script type="text/javascript">
	$(function(){
		$(".submit").click(function(e){
			e.preventDefault();
			E = 0;
			var D = {};
			D.method = "ajax";
			D.action = "update-user";
			D.uID = validate("#uID");
			D.u = validate("#InputEmail");
			D.p = validate("#InputPassword");
			D.d = $("input[name='d']").val();
			D.id = <?= $U['userID'];?>;
			D.i = 1;
			D.n = 1
			D.dom = "";
			while(D.i <= D.d){
				var y = $("select[name=\""+D.i+"\"]").val();
				if(y > 0){
					if(D.i > D.n)
						D.dom += ",";
					D.dom += y;
				}
				if(D.dom.length == 0)
					D.n++;
				D.i++;
			}
			call("<?= SCRIPTS;?>admin.php", D, function(d){
				statusBox(".status", d.msg, d.status);
				setTimeout(function(){
					window.location = "/admin";
				}, 1500);
			}, ".status");
		});

		// Skal håndtere at der bliver trykket for at få en boks mere frem til at vælge hvad brugeren skal være admin for
		$(".MOAR").click(function(e){
			e.preventDefault();
			var n = $("input[name='d']").val();
			n++;
			$(".TEMPLATE").clone().appendTo("#domains");
			$("#domains .template").attr("name", n);
			$("#domains .template").slideDown(250);
			$("#domains .template").removeClass("template");
			$("#domains .TEMPLATE").removeClass("TEMPLATE");
			$("input[name='d']").val(n);
		});
	});
</script>

<?php endif;?>

<?php
	require $_SERVER["DOCUMENT_ROOT"]."/etc/footer.php";
?>