<?php
	include "top.php";
	
	if($u == 1){
		// Superadmin, tillad at der oprettes brugerer
		if(!empty($_GET['u']) && $_GET['u'] != "new"){
			// Der skal rettes på bruger med brugernavn $_GET['u']
			$U = rens($_GET['u']);
			$r = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM login WHERE u='$U'"));
?>
<a href="/user" class="bach">Tilbage til liste</a>
<h2>Ret bruger: <?php echo $r['u'];?></h2>
<form class="admin">
	Brugernavn:<br/>
	<input type="text" name="u" value="<?php echo $r['u'];?>"/><label></label><br/>
	Kodeord:<br/>
	<input type="password" name="p" placeholder="En kode, intet nyt, intet ændres"/><label></label><br/>
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
		</select><label></label><br/>
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
			$("#domains").append("<label></label><br/>");
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
<a href="/user" class="bach">Tilbage til liste</a>
<h2>Opret en bruger</h2>
<form class="admin">
	Brugernavn:<br/>
	<input type="text" name="u" placeholder="Hvad som helst"/><label></label><br/>
	Kodeord:<br/>
	<input type="password" name="p" placeholder="En kode"/><label></label><br/>
	<br/>
	<h3>Domæner:</h3>
	Tomme felter bliver slettet.<br/><br/>
	<input type="hidden" name="d" value="1"/>
	<div id="domains">
		<select name="1">
			<option value="0">vælg en</option>
			<?php while($r = mysqli_fetch_array($q)){?><option value="<?php echo $r['pkid'];?>"><?php echo $r['domain'];?></option><?php }?>
		</select><label></label><br/>
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
					interval = setInterval(ReLoad(), 2500);
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
?>
<h2>Brugerer</h2>
<?php
	$q = mysqli_query($db,"SELECT * FROM login ORDER BY u ASC");
	while($r = mysqli_fetch_array($q)){
?>
<a href="/user/<?php echo $r['u'];?>"><?php echo $r['u'];?></a><br/>
<?php }?>
<br/>
<a href="/user/new">Ny bruger</a>
<?php
		}
	}
	else{
		// Ikke superadmin, vis info om selv
		$r = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM login WHERE uID=$u"));
?>
<h2>Info om dig</h2>
<form class="admin">
	Brugernavn:<br/>
	<input type="text" name="u" value="<?php echo $r['u'];?>"/><label></label><br/>
	Kodeord:<br/>
	<input type="password" name="p" placeholder="En kode, intet nyt, intet ændres"/><label>Intet nyt = intet ændres</label><br/>
	<br/>
	<h3>Domæner du har adgang til:</h3>
	<?php
		$q = mysqli_query($db, "SELECT * FROM con INNER JOIN con.dID=domains.pkid WHERE uID=".$r['uID']);
	?>
	<div id="domains">
		<?php
			while($X = mysqli_fetch_array($q)){ $Q = mysqli_query($db, "SELECT * FROM domains ORDER BY domain ASC");
				echo $X['domain']."<br/>";
			}
		?>
	</div>
	<button class="submit">Opdater bruger</button>
</form>
<script type="text/javascript">
	$(function(){
		$(".submit").click(function(e){
			e.preventDefault();
			var U = $("input[name='u']").val();
			var P = $("input[name='p']").val();
			$.post("/ajax.php", {action: "editMe", u: U, p: P}).done(function(r){
				if(r == "Succes"){
					setmsg("Opdatering lykkedes.", "succes");
				}
				else{
					setmsg(r, "error");
				}
			});
		});
	});
</script>
<?php
	}
	
	include "foot.php";
?>