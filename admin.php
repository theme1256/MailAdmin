<?php
	include "top.php";
	
	if($u == 1){
		// Superadmin, tillad at der oprettes brugerer
		if(!empty($_GET['u']) && $_GET['u'] != "new"){
			// Der skal rettes på bruger med brugernavn $_GET['u']
		}
		else{
			// Der skal oprettes en ny bruger
			$q = mysqli_query($db, "SELECT * FROM domains ORDER BY domain ASC");
?>
<form class="admin">
	Brugernavn:<br/>
	<input type="text" name="u" placeholder="Hvad som helst"/><br/>
	Kodeord:<br/>
	<input type="password" name="p" placeholder="En kode"/><br/>
	<br/>
	Domæner:<br/>
	<input type="hidden" name="d" value="1"/>
	<div id="domains">
		<select name="1">
			<option value="0">vælg en</option>
			<?php while($r = mysqli_fetch_array($q)){?><option value="<?php echo $r['pkid'];?>"><?php echo $r['domain'];?></option><?php }?>
		</select><br/>
	</div>
	<button class="MOAR">Endnu et domæne</button><br/>
	<button class="submit">Opret bruger</button>
</form>
<script type="text/javascript">
	$(function(){
		$(".submit").click(function(e){
			e.preventDefault();
			var U = $("input[name='u']").val();
			var P = $("input[name='p']").val();
			var D = $("input[name='d']").val();
			var i = 1;
			var dom = "";
			while(i <= D){
				if(i > 1)
					dom += ",";
				dom += $("select[name=\""+i+"\"]").val();
				i++;
			}
			$.post("/ajax.php", {action: "newUser", u: U, p: P, dd: dom}).done(function(r){
				if(r == "Success"){
					console.log("Oprettet");
				}
				else{
					console.log(r);
				}
			});
		});
	});
</script>
<?php
		}
		$q = mysqli_query($db, "SELECT * FROM domains ORDER BY domain ASC");
?>
<select class="template" name="">
	<option value="0">vælg en</option>
	<?php while($r = mysqli_fetch_array($q)){?><option value="<?php echo $r['pkid'];?>"><?php echo $r['domain'];?></option><?php }?>
</select>
<script type="text/javascript">
	$(function(){
		// Skal håndtere at der bliver trykket for at få en boks mere frem til at vælge hvad brugeren skal være admin for
		$(".MOAR").click(function(e){
			e.preventDefault();
			var n = $("input[name='d']").val();
			n++;
			$(".template").clone().appendTo("#domains");
			$("#domains .template").attr("name", n);
			$("#domains .template").slideDown(250);
			$("#domains .template").removeClass("template");
			$("#domains").append("<br/>");
			$("input[name='d']").val(n);
		});
	});
</script>
<?php
	}
	else{
		// Ikke superadmin, vis info om selv
	}
	
	include "foot.php";
?>