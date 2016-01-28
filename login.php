<h2>Du har måske ikke adgang her, log lige ind</h2>
<form id="login">
	Brugernavn:<br/>
	<input type="text" name="u" placeholder="username"/><br/>
	Kodeord:<br/>
	<input type="password" name="p" placeholder="password"/><br/>
	<input type="submit" value="Login"/>
</form>
<script type="text/javascript">
	$(function(){
		$("form").submit(function(e){
			e.preventDefault();
			console.log("klikket");
		});
	});
</script>