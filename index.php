<?php
	include "top.php";

	if(!empty($d)){
		if(!empty($m)){
			if($m == "new"){
				// Opret en ny mail
?>
<a href="/domain/<?php echo $d;?>" class="bach">Tilbage til domæne</a>
<h2>Opret ny mail til domæne: <?php echo $d;?></h2>
<form class="mail">
	Adresse:<br/>
	<input type="text" name="m" placeholder="Det der står før @"/><label>Det der står før @<?php echo $d?>, ingen ting er en catch-all mail, kræver at det er en liste.</label><br/>
	Dette er en:<br/>
	<input type="radio" name="t" value="list" id="LIST"/><label for="LIST" class="radio">En maillingliste</label><br/>
	<input type="radio" name="t" value="mail" id="MAIL"/><label for="MAIL" class="radio">En lokal mail</label><br/>
	<div id="list">
		<h3>Mails:</h3>
		<input type="hidden" name="b" value="1"/>
		Skriv de mails (hele mailen) der skal sendes videre til.<br/>
		Et tomt felt bliver slettet.<br/>
		<br/>
		<div id="content">
			<input type="email" name="1"/><label></label><br/>
		</div>
		<button class="MOAR">En mail mere</button>
	</div>
	<div id="mail">
		<h3>Lokal mail info:</h3>
		Der skal lige sætte lidt info.<br/>
		Feltet skal fyldes.<br/>
		<br/>
		Password:<br/>
		<input type="text" name="p"/><label>Et kodeord, kan skiftes senere</label><br/>
	</div>
	<br/>
	<button class="submit">Opret mail</button>
</form>
<script type="text/javascript">
	$(function(){
		$("input[name='t']").change(function(){
			var T = $(this).val();
			$("div#"+T).slideDown(400);
			if(T == "mail")
				$("div#list").slideUp(400);
			else
				$("div#mail").slideUp(400);
		});
		$(".MOAR").click(function(e){
			e.preventDefault();
			var n = $("input[name='b']").val();
			n++;
			$(".template").clone().appendTo("#content");
			$("#content .template").attr("name", n);
			$("#content .template").slideDown(250);
			$("#content .template").removeClass("template");
			$("#content").append("<label></label><br/>");
			$("input[name='b']").val(n);
		});
		$(".submit").click(function(e){
			e.preventDefault();
			var M = $("input[name='m']").val();
			var T = $("input[name='t']").val();
			var B = $("input[name='b']").val();
			var P = $("input[name='p']").val();
			var i = 1;
			var n = 1
			var dom = "";
			while(i <= B){
				var y = $("select[name=\""+i+"\"]").val();
				if(y != ""){
					if(i > n)
						dom += ",";
					dom += y;
				}
				if(dom.length == 0)
					n++;
				i++;
			}
			$.post("/ajax.php", {action: "newMail", d: D, b: B, bb: dom, t: T, p: P}).done(function(r){
				if(r == "Succes"){
					setmsg("Oprettelse lykkedes.", "succes");
					interval = setInterval(Load("/domain/<?php echo $d;?>"), 2500);
				}
				else{
					setmsg(r, "error");
				}
			});
		});
	});
</script>
<input class="template" type="email" name=""/>
<?php
			}
			else{
				// Vis info om den givne mail på det givne domæne
				$M = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM aliases WHERE mail='$m'"));
?>
<a href="/domain/<?php echo $d;?>" class="bach">Tilbage til domæne</a>
<h2>Info om <?php if($M['mail'] == $M['destination']){echo "mail: ";}else{echo "liste: ";} echo $m;?></h2>
<form class="mail">
	<input type="hidden" name="aID" value="<?php echo $M['pkid'];?>"/>
	Adresse:<br/>
	<input type="text" name="m" placeholder="Det der står før @" value="<?php echo str_replace("@".$d, "", $M['mail']);?>"/><label>Det der står før @<?php echo $d?>, ingen ting er en catch-all mail, kræver at det er en liste.</label><br/>
	Dette er en:<br/>
	<input type="radio" name="t" value="list" id="LIST"<?php if($M['mail'] != $M['destination']){echo " checked=\"checked\"";}?>/><label for="LIST" class="radio">En maillingliste</label><br/>
	<input type="radio" name="t" value="mail" id="MAIL"<?php if($M['mail'] == $M['destination']){echo " checked=\"checked\"";}?>/><label for="MAIL" class="radio">En lokal mail</label><br/>
	<div id="list"<?php if($M['mail'] != $M['destination']){echo " style=\"display:block\"";}?>>
		<h3>Mails:</h3>
		Skriv de mails (hele mailen) der skal sendes videre til.<br/>
		Et tomt felt bliver slettet.<br/>
		<br/>
		<div id="content">
		<?php
			$i = 0;
			if($M['mail'] != $M['destination']){
				$mails = explode(",", $M['destination']);
				foreach($mails as $k => $mail){
					$i++;
					echo "<input type=\"email\" name=\"$i\" value=\"$mail\"/><label></label><br/>";
				}
			}
			else{
				$i++;
				echo "<input type=\"email\" name=\"$i\"/><label></label><br/>";
			}
		?>
		</div>
		<input type="hidden" name="b" value="<?php echo $i;?>"/>
		<button class="MOAR">En mail mere</button>
	</div>
	<div id="mail"<?php if($M['mail'] == $M['destination']){echo " style=\"display:block\"";}?>>
		<?php
			if($M['mail'] == $M['destination']){
				$x = mysqli_fetch_array(mysqli_query($db,"SELECT * FROM users WHERE id='$m'"));
				echo "<input type=\"hidden\" name=\"u\" value=\"".$x['id']."\"/>";
			}
			else{
				echo "<input type=\"hidden\" name=\"u\" value=\"\"/>";
			}
		?>
		<h3>Lokal mail info:</h3>
		Der skal lige sætte lidt info.<br/>
		Feltet skal fyldes.<br/>
		<br/>
		Password:<br/>
		<input type="text" name="p"/><label>Et kodeord, intet skrevet = intet ændres</label><br/>
	</div>
	<br/>
	<button class="submit">Opret mail</button>
</form>
<script type="text/javascript">
	$(function(){
		$("input[name='t']").change(function(){
			var T = $(this).val();
			$("div#"+T).slideDown(400);
			if(T == "mail")
				$("div#list").slideUp(400);
			else
				$("div#mail").slideUp(400);
		});
		$(".MOAR").click(function(e){
			e.preventDefault();
			var n = $("input[name='b']").val();
			n++;
			$(".template").clone().appendTo("#content");
			$("#content .template").attr("name", n);
			$("#content .template").slideDown(250);
			$("#content .template").removeClass("template");
			$("#content").append("<label></label><br/>");
			$("input[name='b']").val(n);
		});
		$(".submit").click(function(e){
			e.preventDefault();
			var M = $("input[name='m']").val();
			var T = $("input[name='t']").val();
			var B = $("input[name='b']").val();
			var P = $("input[name='p']").val();
			var aID = $("input[name='aID']").val(); // Alias ID
			var U = $("input[name='u']").val();
			var i = 1;
			var n = 1
			var dom = "";
			while(i <= B){
				var y = $("select[name=\""+i+"\"]").val();
				if(y != ""){
					if(i > n)
						dom += ",";
					dom += y;
				}
				if(dom.length == 0)
					n++;
				i++;
			}
			$.post("/ajax.php", {action: "editMail", d: D, b: B, bb: dom, t: T, p: P}).done(function(r){
				if(r == "Succes"){
					setmsg("Oprettelse lykkedes.", "succes");
					interval = setInterval(Load("/domain/<?php echo $d;?>"), 2500);
				}
				else{
					setmsg(r, "error");
				}
			});
		});
	});
</script>
<input class="template" type="email" name=""/>
<?php
			}
		}
		else{
			if($d == "new" && $u == 1){
				// Vis form til at oprette et nyt domæne og bestemme hvilke brugere der skal have rettighed til det
				$q = mysqli_query($db, "SELECT * FROM login ORDER BY u ASC");
?>
<a href="/" class="bach">Tilbage til liste</a>
<h2>Opret nyt domæne</h2>
<form class="domain">
	Domæne:<br/>
	<input type="text" name="d" placeholder="Ikke http:// og ikke æøå"/><label></label><br/>
	<h3>Brugere:</h3>
	De brugere der skal have adgang til domænet.<br/>
	Tomme felter bliver slettet.<br/>
	<br/>
	<input type="hidden" name="b" value="1"/>
	<div id="users">
		<select name="1">
			<option value="0">Vælg en bruger</option>
			<?php while($r = mysqli_fetch_array($q)){?><option value="<?php echo $r['uID'];?>"><?php echo $r['u'];?></option><?php }?>
		</select><label></label><br/>
	</div>
	<button class="MOAR">Endnu en bruger</button><br/>
	<button class="submit">Opret domæne</button>
</form>
<script type="text/javascript">
	$(function(){
		$(".MOAR").click(function(e){
			e.preventDefault();
			var n = $("input[name='b']").val();
			n++;
			$(".template").clone().appendTo("#users");
			$("#users .template").attr("name", n);
			$("#users .template").slideDown(250);
			$("#users .template").removeClass("template");
			$("#users").append("<label></label><br/>");
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
					setmsg("Oprettelse lykkedes.", "succes");
					interval = setInterval(Load("/"), 2500);
				}
				else{
					setmsg(r, "error");
				}
			});
		});
	});
</script>
<select class="template">
	<option value="0">Vælg en bruger</option>
	<?php $q = mysqli_query($db, "SELECT * FROM login ORDER BY u ASC");while($r = mysqli_fetch_array($q)){?><option value="<?php echo $r['uID'];?>"><?php echo $r['u'];?></option><?php }?>
</select>
<?php
			}
			elseif($d == "new" && $u != 1){
				// En bruger der har forvildet sig ind på noget forkert
				header("Location: /");
			}
			else{
				// Vis liste med mails der hører til det domæne
?>
<a href="/" class="bach">Tilbage til domæneoversigt</a>
<h2>Mail til domæne: <?php echo $d;?></h2>
<?php
				$q = mysqli_query($db, "SELECT * FROM aliases WHERE mail LIKE '%@$d' ORDER BY mail ASC");
				while($r = mysqli_fetch_array($q)){
					if($r['mail'] == $r['destination']){
						// Det er en lokal mail
						echo "<a href=\"/domain/$d/mail/".$r['mail']."\">Lokal mail: ".$r['mail']."</a><br/>\n";
					}
					else{
						// Det er nok en maillingsliste
						echo "<a href=\"/domain/$d/mail/".$r['mail']."\">Mailliste: ".$r['mail']."</a><br/>\n";
					}
				}
				echo "<br/>\n";
				echo "<a href=\"/domain/$d/mail/new\">Opret ny mail/liste</a><br/>\n";
			}
		}
	}
	else{
		// Vis liste med domæner brugeren er admin over
?>
<h2>Domæner du er admin over</h2>
Klik på en af dem for at blive vist informationer om mails på det domæne.<br/><br/>
<?php
		$q = mysqli_query($db, "SELECT * FROM con INNER JOIN domains ON con.dID=domains.pkid WHERE uID=$u ORDER BY domain ASC");
		while($r = mysqli_fetch_array($q)){
			echo "<a href=\"/domain/".$r['domain']."\">".$r['domain']."</a><br/>\n";
		}
		if($u == 1){
			echo "<br/>\n<a href=\"/domain/new\">Opret nyt domæne</a><br/>\n";
		}
		echo "<br/>\n<a href=\"/user\">Ret i brugeroplysninger</a><br/>\n";
	}

	include "foot.php";
?>