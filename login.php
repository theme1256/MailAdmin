<div class="row">
	<div class="col-md-8 col-md-offset-2">
		<h2>Du har måske ikke adgang her, log lige ind</h2>
		<form class="login">
			<div class="form-group">
				<label for="InputEmail">Brugernavn:</label>
				<input type="text" class="form-control" id="InputEmail" name="u" placeholder="username">
			</div>
			<div class="form-group">
				<label for="InputPassword">Kodeord:</label>
				<input type="password" class="form-control" id="InputPassword" name="p"  placeholder="Password">
			</div>
			<button type="button" class="submit btn btn-primary">Login</button>
		</form>
		<script type="text/javascript">
			$(function(){
				$(".submit").click(function(e){
					e.preventDefault();
					var U = $("input[name='u']").val();
					var P = $("input[name='p']").val();
					$.post("/ajax.php", {action: "login", u: U, p: P}).done(function(r){
						if(r == "Succes"){
							setmsg("Login lykkedes, reloader siden.", "alert-success");
							setTimeout("ReLoad()", 1500);
						}
						else{
							setmsg(r, "alert-danger");
						}
					});
				});
			});
		</script>
	</div>
</div>
<?php
	include "foot.php";
?>