<?php
	include "top.php";
?>
<div class="row">
	<div class="col-md-10 col-md-offset-1">
<?php
	if($u == 1){
		// Superadmin, tillad at der oprettes brugerer
		if(!empty($_GET['u']) && $_GET['u'] != "new"){
			// Der skal rettes på bruger med brugernavn $_GET['u']
			$U = rens($_GET['u']);
			$r = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM login WHERE u='$U'"));
?>
		<a href="/user" class="bach btn btn-info" role="button">Tilbage til liste</a>
		<h2>Ret bruger: <?php echo $r['u'];?></h2>
		<form class="admin">
			<div class="form-group">
				<label for="InputEmail">Brugernavn:</label>
				<input type="text" class="form-control" id="InputEmail" name="u" placeholder="Hvad som helst" value="<?php echo $r['u'];?>">
			</div>
			<div class="form-group">
				<label for="InputPassword">Kodeord:</label>
				<input type="password" class="form-control" id="InputPassword" name="p"  placeholder="En kode, intet nyt, intet ændres">
			</div>
			<h3>Domæner:</h3>
			Tomme felter bliver slettet.<br/><br/>
			<?php
				$q = mysqli_query($db, "SELECT * FROM con INNER JOIN domains ON domains.pkid=con.dID WHERE uID=".$r['uID']." ORDER BY domain ASC");
				$i = 1;
			?>
			<input type="hidden" name="d" value="<?php echo mysqli_num_rows($q);?>"/>
			<div id="domains">
				<?php while($X = mysqli_fetch_array($q)){ $Q = mysqli_query($db, "SELECT * FROM domains ORDER BY domain ASC");?>
				<div class="form-group">
					<select class="form-control input-sm" name="<?php echo $i; $i++;?>">
						<option value="0">vælg en</option>
						<?php while($Y = mysqli_fetch_array($Q)){?><option value="<?php echo $Y['pkid'];?>"<?php if($Y['pkid'] == $X['dID']){echo " selected=\"selected\"";}?>><?php echo $Y['domain'];?></option><?php }?>
					</select>
				</div>
				<?php }?>
			</div>
			<button type="button" class="MOAR btn btn-primary">Endnu et domæne</button>
			<button type="button" class="submit btn btn-success">Opdater bruger</button>
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
							setmsg("Opdatering lykkedes.", "alert-success");
						}
						else{
							setmsg(r, "alert-danger");
						}
					});
				});
				// Skal håndtere at der bliver trykket for at få en boks mere frem til at vælge hvad brugeren skal være admin for
				$(".MOAR").click(function(e){
					e.preventDefault();
					var n = $("input[name='d']").val();
					n++;
					$(".TEMPLATE").clone().appendTo("#domains");
					$("#domains .template").attr("name", n);
					$("#domains .template").slideDown(250);
					$("#domains .template").removeClass("template");
					$("#domains .TEMPLATE").removeClass("TEMPLATE");
					$("input[name='d']").val(n);
				});
			});
		</script>
		<?php $q = mysqli_query($db, "SELECT * FROM domains ORDER BY domain ASC");?>
		<div class="form-group TEMPLATE">
			<select class="template form-control input-sm" name="">
				<option value="0">vælg en</option>
				<?php while($r = mysqli_fetch_array($q)){?><option value="<?php echo $r['pkid'];?>"><?php echo $r['domain'];?></option><?php }?>
			</select>
		</div>
<?php
		}
		elseif($_GET['u'] == "new"){
			// Der skal oprettes en ny bruger
			$q = mysqli_query($db, "SELECT * FROM domains ORDER BY domain ASC");
?>
		<a href="/user" class="bach btn btn-info" role="button">Tilbage til liste</a>
		<h2>Opret en bruger</h2>
		<form class="admin">
			<div class="form-group">
				<label for="InputEmail">Brugernavn:</label>
				<input type="text" class="form-control" id="InputEmail" name="u" placeholder="Hvad som helst"">
			</div>
			<div class="form-group">
				<label for="InputPassword">Kodeord:</label>
				<input type="password" class="form-control" id="InputPassword" name="p"  placeholder="En kode">
			</div>
			<h3>Domæner:</h3>
			Tomme felter bliver slettet.<br/><br/>
			<input type="hidden" name="d" value="1"/>
			<div id="domains">
				<div class="form-group">
					<select class="form-control input-sm" name="1">
						<option value="0">vælg en</option>
						<?php while($r = mysqli_fetch_array($q)){?><option value="<?php echo $r['pkid'];?>"><?php echo $r['domain'];?></option><?php }?>
					</select>
				</div>
			</div>
			<button type="button" class="MOAR btn btn-primary">Endnu et domæne</button>
			<button type="button" class="submit btn btn-success">Opret bruger</button>
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
							setmsg("Oprettelse lykkedes.", "alert-success");
							interval = setInterval(ReLoad(), 2500);
						}
						else{
							setmsg(r, "alert-danger");
						}
					});
				});
				// Skal håndtere at der bliver trykket for at få en boks mere frem til at vælge hvad brugeren skal være admin for
				$(".MOAR").click(function(e){
					e.preventDefault();
					var n = $("input[name='d']").val();
					n++;
					$(".TEMPLATE").clone().appendTo("#domains");
					$("#domains .template").attr("name", n);
					$("#domains .template").slideDown(250);
					$("#domains .template").removeClass("template");
					$("#domains .TEMPLATE").removeClass("TEMPLATE");
					$("#domains").append("<br/>");
					$("input[name='d']").val(n);
				});
			});
		</script>
		<?php $q = mysqli_query($db, "SELECT * FROM domains ORDER BY domain ASC");?>
		<div class="form-group TEMPLATE">
			<select class="template form-control input-sm" name="">
				<option value="0">vælg en</option>
				<?php while($r = mysqli_fetch_array($q)){?><option value="<?php echo $r['pkid'];?>"><?php echo $r['domain'];?></option><?php }?>
			</select>
		</div>
<?php
		}
		else{
			// Vis en liste med brugerer
?>
		<a href="/" class="bach btn btn-info" role="button">Tilbage til domæneliste</a>
		<h2>Brugerer</h2>
		<ul class="list-unstyled">
		<?php
			$q = mysqli_query($db,"SELECT * FROM login ORDER BY u ASC");
			while($r = mysqli_fetch_array($q)){
		?>
		<li><a href="/user/<?php echo $r['u'];?>"><?php echo $r['u'];?></a></li>
		<?php }?>
		<li>&nbsp;</li>
		<li><a href="/user/new">Ny bruger</a></li>
		</ul>
		<?php
				}
			}
			else{
				// Ikke superadmin, vis info om selv
				$r = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM login WHERE uID=$u"));
		?>
		<a href="/" class="bach btn btn-info" role="button">Tilbage til domæneliste</a>
		<h2>Info om dig</h2>
		<form class="admin">
			<div class="form-group">
				<label for="InputEmail">Brugernavn:</label>
				<input type="text" class="form-control" id="InputEmail" name="u" placeholder="Hvad som helst" value="<?php echo $r['u'];?>"">
			</div>
			<div class="form-group">
				<label for="InputPassword">Kodeord:</label>
				<input type="password" class="form-control" id="InputPassword" name="p"  placeholder="En kode, intet nyt, intet ændres">
			</div>
			<h3>Domæner du har adgang til:</h3>
			<?php
				$q = mysqli_query($db, "SELECT * FROM con INNER JOIN con.dID=domains.pkid WHERE uID=".$r['uID']);
			?>
			<div id="domains">
				<ul class="list-unstyled">
				<?php
					while($X = mysqli_fetch_array($q)){ $Q = mysqli_query($db, "SELECT * FROM domains ORDER BY domain ASC");
						echo "<li>".$X['domain']."</li>";
					}
				?>
				</ul>
			</div>
			<button type="button" class="submit btn btn-success">Opdater bruger</button>
		</form>
		<script type="text/javascript">
			$(function(){
				$(".submit").click(function(e){
					e.preventDefault();
					var U = $("input[name='u']").val();
					var P = $("input[name='p']").val();
					$.post("/ajax.php", {action: "editMe", u: U, p: P}).done(function(r){
						if(r == "Succes"){
							setmsg("Opdatering lykkedes.", "alert-success");
						}
						else{
							setmsg(r, "alert-danger");
						}
					});
				});
			});
		</script>
<?php
	}
?>
	</div>
</div>
<?php
	include "foot.php";
?>