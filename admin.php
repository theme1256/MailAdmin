<?php
	include "top.php";
	
	if($u == 1){
		// Superadmin, tillad at der oprettes brugerer
		if(!empty($_GET['u']) && $_GET['u'] != "new"){
			// Der skal rettes på bruger med brugernavn $_GET['u']
			$U = rens($_GET['u']);
			$r = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM login WHERE u='$U'"));
?>
<h2>Ret bruger: <?php echo $r['u'];?></h2>
<form class="admin">
	Brugernavn:<br/>
	<input type="text" name="u" value="<?php echo $r['u'];?>"/><br/>
	Kodeord:<br/>
	<input type="password" name="p" placeholder="En kode, intet nyt, intet ændres"/><br/>
	<br/>
	<h3>Domæner:</h3>
	Tomme felter bliver slettet.<br/><br/>
	<?php
		$q = mysqli_query($db, "SELECT * FROM con WHERE uID=".$r['uID']);
		$i = 1;
	?>
	<input type="hidden" name="d" value="<?php echo mysqli_num_rows($q);?>"/>
	<div id="domains">
		<?php while($X = mysqli_fetch_array($q)){ $Q = mysqli_query($db, "SELECT * FROM domains ORDER BY domain ASC");?>
		<select name="<?php echo $i; $i++;?>">
			<option value="0">vælg en</option>
			<?php while($Y = mysqli_fetch_array($Q)){?><option value="<?php echo $Y['pkid'];?>"<?php if($Y['pkid'] == $X['dID']){echo " selected=\"selected\"";}?>><?php echo $Y['domain'];?></option><?php }?>
		</select><br/>
		<?php }?>
	</div>
	<button class="MOAR">Endnu et domæne</button><br/>
	<button class="submit">Opdater bruger</button>
</form>
<script type="text/javascript">
	$(function(){
		$(".submit").click(function(e){
			e.preventDefault();
			var U = $("input[name='u']").val();
			var P = $("input[name='p']").val();
			var D = $("input[name='d']").val();
			var id = <?php echo $r['uID'];?>;
			var i = 1;
			var n = 1
			var dom = "";
			while(i <= D){
				var y = $("select[name=\""+i+"\"]").val();
				if(y > 0){
					if(i > n)
						dom += ",";
					dom += y;
				}
				if(dom.length == 0)
					n++;
				i++;
			}
			$.post("/ajax.php", {action: "editUser", u: U, p: P, dd: dom, uID: id}).done(function(r){
				if(r == "Succes"){
					setmsg("Opdatering lykkedes.", "succes");
				}
				else{
					setmsg(r, "error");
				}
			});
		});
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
<?php $q = mysqli_query($db, "SELECT * FROM domains ORDER BY domain ASC");?>
<select class="template" name="">
	<option value="0">vælg en</option>
	<?php while($r = mysqli_fetch_array($q)){?><option value="<?php echo $r['pkid'];?>"><?php echo $r['domain'];?></option><?php }?>
</select>
<?php
		}
		elseif($_GET['u'] == "new"){
			// Der skal oprettes en ny bruger
			$q = mysqli_query($db, "SELECT * FROM domains ORDER BY domain ASC");
?>
<h2>Opret en bruger</h2>
<form class="admin">
	Brugernavn:<br/>
	<input type="text" name="u" placeholder="Hvad som helst"/><br/>
	Kodeord:<br/>
	<input type="password" name="p" placeholder="En kode"/><br/>
	<br/>
	<h3>Domæner:</h3>
	Tomme felter bliver slettet.<br/><br/>
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
			var n = 1
			var dom = "";
			while(i <= D){
				var y = $("select[name=\""+i+"\"]").val();
				if(y > 0){
					if(i > n)
						dom += ",";
					dom += y;
				}
				if(dom.length == 0)
					n++;
				i++;
			}
			$.post("/ajax.php", {action: "newUser", u: U, p: P, dd: dom}).done(function(r){
				if(r == "Success"){
					setmsg("Oprettelse lykkedes.", "succes");
				}
				else{
					setmsg(r, "error");
				}
			});
		});
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
<?php $q = mysqli_query($db, "SELECT * FROM domains ORDER BY domain ASC");?>
<select class="template" name="">
	<option value="0">vælg en</option>
	<?php while($r = mysqli_fetch_array($q)){?><option value="<?php echo $r['pkid'];?>"><?php echo $r['domain'];?></option><?php }?>
</select>
<?php
		}
		else{
			// Vis en liste med brugerer
		}
	}
	else{
		// Ikke superadmin, vis info om selv
?>
Info om dig
<?php
	}
	
	include "foot.php";
?>