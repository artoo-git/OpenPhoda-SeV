<?php include('views/_headerTest.php');?>

<div class="login">
	<p>
	<table>
	<form method="post" action="exp.php" name="exp">
		<tr><td><label for="Access">open dataset</label></td></tr>
		<tr><td><input id="exp_name" type="text" name="exp_name" autocomplete="off" placeholder="experiment name" required /></td></tr>
		<tr><td><input id="exp_password" type="password" name="exp_password" placeholder="password" autocomplete="off" required ></td></tr>
		<tr><td><button class="button" type="submit" name="login" value=1>login</button></td></tr>
	</form>
	<form action="register.php">
	<tr><td></td></tr>
    <tr><td><button class="button" type="submit" name="register" value="register">register</button></td></tr>
	</form>
	</table>

</div>
<?php include('views/_footer.php'); ?>
