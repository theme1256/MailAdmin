<?php
	require $_SERVER["DOCUMENT_ROOT"]."/etc/header.php";

	$uID = $_SESSION['userID'];
	$q = $con->prepare("SELECT * FROM ma_login WHERE userID LIKE (:uID)");
	$q->bindParam(":uID", $uID);
	$q->execute();
	$U = $q->fetch(PDO::FETCH_ASSOC);
?>

<h1><?= $Content->out(16);?></h1>

<div class="row">
	<form class="user col-md-8 col-md-offset-2" action="<?= SCRIPTS;?>user.php" method="post">
		<input type="hidden" name="method" value="post">
		<div class="form-group">
			<label for="InputEmail"><?= $Content->out(5);?>:</label>
			<input type="text" class="form-control" id="InputEmail" name="u" value="<?= $U['user'];?>" autocomplete="off">
		</div>
		<div class="form-group">
			<label for="InputPassword"><?= $Content->out(6);?>:</label>
			<input type="password" class="form-control" id="InputPassword" name="p" autocomplete="off">
		</div>
		<div class="form-group text-right">
			<button type="button" class="submit btn btn-primary"><?= $Content->out(17);?></button>
		</div>
		<?= $Content->statusBox();?>
	</form>
</div>

<script type="text/javascript">
	$(function(){
		$(".submit").click(function(e){
			e.preventDefault();
			E = 0;
			let Data = {};
			Data.method = "ajax";
			Data.u = validate("#InputEmail");
			Data.p = validate("#InputPassword");
			call("<?= SCRIPTS;?>user.php", Data, function(d){
				statusBox(".status", d.msg, d.status);
				setTimeout("ReLoad()", 1500);
			}, ".status");
		});
	});
</script>

<?php
	if($_SESSION['userID'] == 1){
?>
<br/>
<a href="/admin">Ret og opret brugere</a>
<?php
	}
?>

<?php
	require $_SERVER["DOCUMENT_ROOT"]."/etc/footer.php";
?>