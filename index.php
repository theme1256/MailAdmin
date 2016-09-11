<?php
	include "top.php";
?>
<div class="row">
	<div class="col-md-10 col-md-offset-1">
<?php
	if(!empty($d)){
		if(!empty($m)){
			if($m == "new"){
				// Opret en ny mail
?>
		<a href="/domain/<?php echo $d;?>" class="bach btn btn-info" role="button">Tilbage til domæne</a>
		<h2>Opret ny mail til domæne: <?php echo $d;?></h2>
		<form class="mail">
			<div class="form-group">
				<label for="InputEmail">Adresse:</label>
				<input type="text" class="form-control" id="InputEmail" name="m" placeholder="Det der står før @<?php echo $d?>, ingen ting er en catch-all mail, kræver at det er en liste.">
			</div>
			<div id="list">
				<h3>Mails:</h3>
				<input type="hidden" name="b" value="1"/>
				<p>
					Skriv de mails (hele mailen) der skal sendes videre til.<br/>
					Et tomt felt bliver slettet.<br/>
				</p>
				<div id="content">
					<div class="form-group"><input class="form-control" type="email" name=""/></div>
				</div>
				<button type="button" class="MOAR btn btn-primary">En mail mere</button>
			</div>
			<br/>
			<button type="button" class="submit btn btn-success">Opret mail</button>
		</form>
		<script type="text/javascript">
			$(function(){
				$(".MOAR").click(function(e){
					e.preventDefault();
					var n = $("input[name='b']").val();
					n++;
					$(".TEMPLATE").clone().appendTo("#content");
					$("#content .template").attr("name", n);
					$("#content .template").slideDown(250);
					$("#content .template").removeClass("template");
					$("#content .TEMPLATE").removeClass("TEMPLATE");
					$("input[name='b']").val(n);
				});
				$(".submit").click(function(e){
					e.preventDefault();
					var M = $("input[name='m']").val();
					var T = $("input[name='t']:checked").val();
					var B = $("input[name='b']").val();
					var P = $("input[name='p']").val();
					var i = 1;
					var n = 1
					var dom = "";
					while(i <= B){
						var y = $("input[name=\""+i+"\"]").val();
						if(y != ""){
							if(i > n)
								dom += ",";
							dom += y;
						}
						if(dom.length == 0)
							n++;
						i++;
					}
					console.log(T);
					$.post("/ajax.php", {action: "newMail", m: M, b: B, bb: dom, t: T, p: P, d: "<?php echo $d;?>"}).done(function(r){
						if(r == "Succes"){
							setmsg("Oprettelse lykkedes.", "alert-success");
							interval = setInterval(Load("/domain/<?php echo $d;?>"), 2500);
						}
						else{
							setmsg(r, "alert-danger");
						}
					});
				});
			});
		</script>
		<div class="form-group TEMPLATE"><input class="form-control template" type="email" name=""/></div>
<?php
			}
			else{
				// Vis info om den givne mail på det givne domæne
				$M = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM alias WHERE address='$m'"));
?>
		<a href="/domain/<?php echo $d;?>" class="bach btn btn-info" role="button">Tilbage til domæne</a>
		<h2>Info om <?php if($M['address'] == $M['goto']){echo "mail: ";}else{echo "liste: ";} echo $m;?></h2>
		<form class="mail">
			<input type="hidden" name="aID" value="<?php echo $M['address'];?>"/>
			<div class="form-group">
				<label for="InputEmail">Adresse:</label>
				<input type="text" class="form-control" id="InputEmail" name="m" placeholder="Det der står før @<?php echo $d?>, ingen ting er en catch-all mail, kræver at det er en liste." value="<?php echo str_replace("@".$d, "", $M['address']);?>">
			</div>
			<div id="list">
				<h3>Mails:</h3>
				<p>
					Skriv de mails (hele mailen) der skal sendes videre til.<br/>
					Et tomt felt bliver slettet.<br/>
				</p>
				<div id="content">
				<?php
					$i = 0;
					$mails = explode(",", $M['goto']);
					foreach($mails as $k => $mail){
						$i++;
						echo "<div class=\"form-group\"><input class=\"form-control\" type=\"email\" name=\"$i\" value=\"$mail\"/></div>";
					}
				?>
				</div>
				<input type="hidden" name="b" value="<?php echo $i;?>"/>
				<button type="button" class="MOAR btn btn-primary">En mail mere</button>
			</div>
			<br/>
			<button type="button" class="submit btn btn-success">Opdater mail</button>
			<button type="button" class="delete btn btn-danger">Slet mail</button>
		</form>
		<script type="text/javascript">
			$(function(){
				$(".MOAR").click(function(e){
					e.preventDefault();
					var n = $("input[name='b']").val();
					n++;
					$(".TEMPLATE").clone().appendTo("#content");
					$("#content .template").attr("name", n);
					$("#content .template").slideDown(250);
					$("#content .template").removeClass("template");
					$("#content .TEMPLATE").removeClass("TEMPLATE");
					$("input[name='b']").val(n);
				});
				$(".submit").click(function(e){
					e.preventDefault();
					var M = $("input[name='m']").val();
					var T = $("input[name='t']:checked").val();
					var B = $("input[name='b']").val();
					var P = $("input[name='p']").val();
					var aID = $("input[name='aID']").val(); // Alias ID
					var U = $("input[name='u']").val();
					var i = 1;
					var n = 1
					var dom = "";
					while(i <= B){
						var y = $("input[name=\""+i+"\"]").val();
						if(y != ""){
							if(i > n)
								dom += ",";
							dom += y;
						}
						if(dom.length == 0)
							n++;
						i++;
					}
					$.post("/ajax.php", {action: "editMail", m: M, b: B, bb: dom, t: T, p: P, d: "<?php echo $d;?>", aID: aID, u: U}).done(function(r){
						if(r == "Succes"){
							setmsg("Opdatering lykkedes.", "alert-success");
							interval = setInterval(Load("/domain/<?php echo $d;?>"), 2500);
						}
						else{
							setmsg(r, "alert-danger");
						}
					});
				});
				$(".delete").click(function(e){
					e.preventDefault();
					var aID = $("input[name='aID']").val(); // Alias ID
					var U = $("input[name='u']").val();
					$.post("/ajax.php", {action: "deleteMail", aID: aID, u: U}).done(function(r){
						if(r == "Succes"){
							setmsg("Sletning lykkedes.", "alert-success");
							interval = setInterval(Load("/domain/<?php echo $d;?>"), 2500);
						}
						else{
							setmsg(r, "alert-danger");
						}
					});
				});
			});
		</script>
		<div class="form-group TEMPLATE"><input class="form-control template" type="email" name=""/></div>
<?php
			}
		}
		else{
			if($d == "new" && $u == 1){
				// Vis form til at oprette et nyt domæne og bestemme hvilke brugere der skal have rettighed til det
				$q = mysqli_query($db, "SELECT * FROM login ORDER BY u ASC");
?>
		<a href="/" class="bach btn btn-info" role="button">Tilbage til liste</a>
		<h2>Opret nyt domæne</h2>
		<form class="domain">
			<div class="form-group">
				<label for="InputEmail">Domæne:</label>
				<input type="text" class="form-control" id="InputEmail" name="d" placeholder="Ikke http:// og ikke æøå">
			</div>
			<h3>Brugere:</h3>
			<p>
				De brugere der skal have adgang til domænet.<br/>
				Tomme felter bliver slettet.<br/>
			</p>
			<input type="hidden" name="b" value="1"/>
			<div id="users">
				<div class="form-group">
					<select class="form-control input-sm" name="1">
						<option value="0">Vælg en bruger</option>
						<?php while($r = mysqli_fetch_array($q)){?><option value="<?php echo $r['uID'];?>"><?php echo $r['u'];?></option><?php }?>
					</select>
				</div>
			</div>
			<button type="button" class="MOAR btn btn-primary">Endnu en bruger</button>
			<button type="button" class="submit btn btn-success">Opret domæne</button>
		</form>
		<script type="text/javascript">
			$(function(){
				$(".MOAR").click(function(e){
					e.preventDefault();
					var n = $("input[name='b']").val();
					n++;
					$(".TEMPLATE").clone().appendTo("#users");
					$("#users .template").attr("name", n);
					$("#users .template").slideDown(250);
					$("#users .template").removeClass("template");
					$("#users .TEMPLATE").removeClass("TEMPLATE");
					$("input[name='b']").val(n);
				});
				$(".submit").click(function(e){
					e.preventDefault();
					var D = $("input[name='d']").val();
					var B = $("input[name='b']").val();
					var i = 1;
					var n = 1
					var dom = "";
					while(i <= B){
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
					$.post("/ajax.php", {action: "newDomain", d: D, b: B, bb: dom}).done(function(r){
						if(r == "Succes"){
							setmsg("Oprettelse lykkedes.", "alert-success");
							interval = setInterval(Load("/"), 2500);
						}
						else{
							setmsg(r, "alert-danger");
						}
					});
				});
			});
		</script>
		<div class="form-group TEMPLATE">
			<select class="template form-control input-sm" name="">
				<option value="0">Vælg en bruger</option>
				<?php $q = mysqli_query($db, "SELECT * FROM login ORDER BY u ASC");while($r = mysqli_fetch_array($q)){?><option value="<?php echo $r['uID'];?>"><?php echo $r['u'];?></option><?php }?>
			</select>
		</div>
<?php
			}
			elseif($d == "new" && $u != 1){
				// En bruger der har forvildet sig ind på noget forkert
				header("Location: /");
			}
			else{
				// Vis liste med mails der hører til det domæne
?>
		<a href="/" class="bach btn btn-info" role="button">Tilbage til domæneoversigt</a>
		<h2>Mails til domæne: <?php echo $d;?></h2>
		<ul class="list-unstyled">
<?php
				$q = mysqli_query($db, "SELECT * FROM alias WHERE address LIKE '%@$d' ORDER BY address ASC");
				while($r = mysqli_fetch_array($q)){
					echo "<li><a href=\"/domain/$d/mail/".$r['address']."\">Mailliste: ".$r['address']."</a></li>\n";
				}
				echo "<li>&nbsp;</li>\n";
				echo "<li><a href=\"/domain/$d/mail/new\">Opret ny liste</a></li>\n";
				echo "</ul>\n";
			}
		}
	}
	else{
		// Vis liste med domæner brugeren er admin over
?>
		<h2>Domæner du er admin over</h2>
		<p>
			Klik på en af dem for at blive vist informationer om mails på det domæne.<br/>
		</p>
		<ul class="list-unstyled">
<?php
		$q = mysqli_query($db, "SELECT * FROM con INNER JOIN domain ON con.d=domain.domain WHERE uID=$u ORDER BY domain ASC");
		while($r = mysqli_fetch_array($q)){
			echo "			<li><a href=\"/domain/".$r['domain']."\">".$r['domain']."</a></li>\n";
		}
?>
		</ul>
<?php
	}
?>
	</div>
</div>
<?php
	include "foot.php";
?>