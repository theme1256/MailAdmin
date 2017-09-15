<div class="col-md-8 col-md-offset-2">
	<h2>Du har måske ikke adgang her, log lige ind</h2>
	<form class="login" action="<?= SCRIPTS;?>login.php">
		<input type="hidden" name="method" value="regular">
		<div class="form-group">
			<label for="InputEmail">Brugernavn:</label>
			<input type="text" class="form-control" id="InputEmail" name="u" placeholder="username">
		</div>
		<div class="form-group">
			<label for="InputPassword">Kodeord:</label>
			<input type="password" class="form-control" id="InputPassword" name="p"  placeholder="Password">
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
				}, function(d){
					if(d.status === parseInt(d.status)){
						var t = "danger";
						if(d.status == 404)
							var msg = "Fejl under kommunikation med server, fejl: 404, kunne ikke finde filen på serveren.";
						else if(d.status == 500)
							var msg = "Fejl under kommunikation med server, fejl: 500, der skete en fejl på serveren.";
						else
							var msg = "Fejl under kommunikation med server, fejl: " + d.status + ", " + d.statusText;
					} else{
						var msg = d.msg;
						var t = d.status;
					}
					statusBox(".status", msg, t);
				});
			});
		});
	</script>
</div>

<?php
	require __DIR__ . "/etc/footer.php";
?>