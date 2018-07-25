<?php
	require_once $_SERVER["DOCUMENT_ROOT"]."/etc/header.php";
?>

<?php if(empty($_GET['u'])):?>
<!-- Show a list of users registered -->

<h1><?= $Content->out(67);?></h1>

<ul class="list-unstyled">
	<?php
		$q = $con->prepare("SELECT user FROM ma_login ORDER BY user ASC");
		$q->execute();
		$users = $q->fetchAll(PDO::FETCH_ASSOC);
		foreach($users as $user){
	?>
	<li><a href="<?= HOME;?>admin/<?= $user['user'];?>"><?= $user['user'];?></a></li>
	<?php
		}
	?>
	<li>&nbsp;</li>
	<li><a href="<?= HOME;?>admin/new"><?= $Content->out(56);?></a></li>
</ul>

<?php elseif($_GET['u'] == "new"):?>
<!-- Show form to add a new user -->

<a href="<?= HOME;?>admin" class=""><?= $Content->out(57);?></a>

<h1><?= $Content->out(65);?></h1>

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
	<h3><?= $Content->out(58);?>:</h3>
	<?= $Content->out(59);?><br/><br/>
	<div id="domains">
		<div class="form-group">
			<select class="form-control input-sm" name="dom[]">
				<option value="0"><?= $Content->out(60);?></option>
				<?php
					$q = $con->prepare("SELECT domain FROM domain ORDER BY domain ASC");
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
		<a type="button" class="MOAR btn btn-info"><?= $Content->out(61);?></a>
		<button type="button" class="submit btn btn-primary"><?= $Content->out(64);?></button>
	</div>
	<?= $Content->statusBox();?>
</form>

<div class="form-group TEMPLATE">
	<select class="template form-control input-sm" name="dom[]">
		<option value="0"><?= $Content->out(60);?></option>
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
			let Data = objectifyForm($("form.admin").serializeArray());
			Data.medium = "ajax";
			if(E == 0){
				call("<?= SCRIPTS;?>admin.php", Data, function(d){
					statusBox(".status", d.msg, d.status);
					setTimeout(function(){
						window.location = "/admin";
					}, 1500);
				}, ".status");
			} else{
				statusBox(".status", "<?= $Content->out(45);?>", "danger");
			}
		});

		// Handles clicks on button to add another domain to user
		$(".MOAR").click(function(e){
			e.preventDefault();
			$(".TEMPLATE").clone().appendTo("#domains");
			$("#domains .template").slideDown(250);
			$("#domains .template").removeClass("template");
			$("#domains .TEMPLATE").removeClass("TEMPLATE");
		});
	});
</script>

<?php else:?>
<!-- Show form to edit user -->
<?php
	$u = $_GET['u'];
	$q = $con->prepare("SELECT userID, user FROM ma_login WHERE user LIKE (:u)");
	$q->bindParam(":u", $u);
	$q->execute();
	$U = $q->fetch(PDO::FETCH_ASSOC);
?>

<a href="<?= HOME;?>admin" class=""><?= $Content->out(57);?></a>

<h1><?= $Content->out(60);?></h1>

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
	<h3><?= $Content->out(58);?>:</h3>
	<?= $Content->out(59);?><br/><br/>
	<?php
		$q = $con->prepare("SELECT domain FROM ma_access INNER JOIN domain ON domain.domain=ma_access.domain WHERE userID LIKE (:uID) ORDER BY domain.domain ASC");
		$q->bindParam(":uID", $U['userID']);
		$q->execute();
	?>
	<div id="domains">
		<?php
			$selected = $q->fetchAll(PDO::FETCH_ASSOC);
			$q = $con->prepare("SELECT domain FROM domain ORDER BY domain ASC");
			$q->execute();
			$domains = $q->fetchAll(PDO::FETCH_ASSOC);
			foreach($selected as $line){
		?>
		<div class="form-group">
			<select class="form-control input-sm" name="dom[]">
				<option value="0"><?= $Content->out(60);?></option>
				<?php
					foreach($domains as $domain){
				?>
				<option value="<?= $domain['domain'];?>"<?php if($domain['domain'] == $line['domain']){ echo " selected=\"selected\"";}?>><?= $domain['domain'];?></option>
				<?php }?>
			</select>
		</div>
		<?php }?>
	</div>
	<div class="form-group text-right">
		<a type="button" class="MOAR btn btn-info"><?= $Content->out(61);?></a>
		<button type="button" class="submit btn btn-primary"><?= $Content->out(62);?></button>
		<button type="button" class="delete btn btn-danger"><?= $Content->out(60);?></button>
	</div>
	<?= $Content->statusBox();?>
</form>

<div class="form-group TEMPLATE">
	<select class="template form-control input-sm" name="dom[]">
		<option value="0"><?= $Content->out(60);?></option>
			<?php
				foreach($domains as $domain){
			?>
			<option value="<?= $domain['domain'];?>"><?= $domain['domain'];?></option>
			<?php }?>
	</select>
</div>

<script type="text/javascript">
	$(function(){
		$(".delete").click(function(e){
			E = 0;
			let Data = {};
			Data.medium = "ajax";
			Data.action = "delete-user";
			Data.uID = validate("#uID");
			if(E == 0){
				call("<?= SCRIPTS;?>admin.php", Data, function(d){
					statusBox(".status", d.msg, d.status);
					setTimeout(function(){
						window.location = "<?= HOME;?>admin";
					}, 1500);
				}, ".status");
			} else{
				statusBox(".status", "<?= $Content->out(46);?>", "danger");
			}
		});

		$(".submit").click(function(e){
			e.preventDefault();
			E = 0;
			let Data = objectifyForm($("form.admin").serializeArray());
			Data.medium = "ajax";
			if(E == 0){
				call("<?= SCRIPTS;?>admin.php", Data, function(d){
					statusBox(".status", d.msg, d.status);
					setTimeout(function(){
						window.location = "<?= HOME;?>admin";
					}, 1500);
				}, ".status");
			} else{
				statusBox(".status", "<?= $Content->out(45);?>", "danger");
			}
		});

		// Handles clicks on button to add another domain to user
		$(".MOAR").click(function(e){
			e.preventDefault();
			$(".TEMPLATE").clone().appendTo("#domains");
			$("#domains .template").slideDown(250);
			$("#domains .template").removeClass("template");
			$("#domains .TEMPLATE").removeClass("TEMPLATE");
			$("input[name='d']").val(n);
		});
	});
</script>

<?php endif;?>

<?php
	include $_SERVER["DOCUMENT_ROOT"]."/etc/footer.php";
?>