<?php include('views/_headerTest.php');?>

<div class="login">
	<p>
	<form method="post" action="exp.php" name="exp">
		<label for="Access">open dataset</label>
		<input id="exp_name" type="text" name="exp_name" autocomplete="off" placeholder="experiment name" required />
		<input id="exp_password" type="password" name="exp_password" placeholder="password" autocomplete="off" required ><br>
		<button class="button" type="submit" name="login" value=1>login</button>
	</form>
	</p><br>
	<form action="register.php">
    <button class="button" type="submit" name="register" value="register">register a new one</button>
	</form>

</div>
<?php include('views/_footer.php'); ?>
