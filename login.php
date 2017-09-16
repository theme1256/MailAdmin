<h2><?= $Content->out(4);?></h2>

<form class="login col-md-8 col-md-offset-2" action="<?= SCRIPTS;?>login.php">
	<input type="hidden" name="method" value="post">
	<div class="form-group">
		<label for="InputEmail"><?= $Content->out(5);?>:</label>
		<input type="text" class="form-control" id="InputEmail" name="u">
	</div>
	<div class="form-group">
		<label for="InputPassword"><?= $Content->out(6);?>:</label>
		<input type="password" class="form-control" id="InputPassword" name="p">
	</div>
	<div class="form-group text-right">
		<button type="button" class="submit btn btn-primary">Login</button>
	</div>
	<?= $Content->statusBox();?>
</form>

<script type="text/javascript">
	$(function(){
		$(".submit").click(function(e){
			e.preventDefault();
			E = 0;
			var D = {};
			D.method = "ajax";
			D.u = validate("#InputEmail");
			D.p = validate("#InputPassword");
			call("<?= SCRIPTS;?>login.php", D, function(d){
				statusBox(".status", d.msg, d.status);
				setTimeout("ReLoad()", 1500);
			}, ".status");
		});
	});
</script>

<?php
	require __DIR__ . "/etc/footer.php";
?>