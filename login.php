<h2>Du har måske ikke adgang her, log lige ind</h2>
<form class="login">
	Brugernavn:<br/>
	<input type="text" name="u" placeholder="username"/><label></label><br/>
	Kodeord:<br/>
	<input type="password" name="p" placeholder="password"/><label></label><br/>
	<button class="submit">Login</button>
</form>
<script type="text/javascript">
	$(function(){
		$(".submit").click(function(e){
			e.preventDefault();
			var U = $("input[name='u']").val();
			var P = $("input[name='p']").val();
			$.post("/ajax.php", {action: "login", u: U, p: P}).done(function(r){
				if(r == "Succes"){
					setmsg("Login lykkedes, reloader siden.", "succes");
					interval = setInterval(ReLoad(), 2500);
				}
				else{
					setmsg(r, "error");
				}
			});
		});
	});
</script>