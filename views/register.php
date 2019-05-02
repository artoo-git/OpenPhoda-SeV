<?php
include('views/_headerTest.php'); ?>

<div class="Login">
	<p>
	<label for="register">Create new dataset</label>
	<table><form method="post" action="register.php" name="registerform">
    <tr><td><input id="exp_name" type="text" pattern="[a-zA-Z0-9]{2,64}" name="exp_name" placeholder="experiment name" required /></td></tr>
    <tr><td><input id="password" type="password" name="password" placeholder="experiment password" pattern=".{6,}" required autocomplete="off" /></td></tr>
    <tr><td><input id="pi" type="text" name="pi" placeholder="principal investigator" required autocomplete="off" /></td></tr>
    <tr><td><input id="conditions" <input type="text" pattern="\d*" name="conditions" placeholder="number of conditions" required autocomplete="off" /></td></tr>

    
    <tr><td><button class="button" type="submit" name="register" value="register">register</button></td></tr>
	</form></table>
	</p>
	
</div>

<?php include('views/_footer.php'); ?>
