<?php include('_header.php');
?>


<div style="text-align:left" class="boxcenter">
	<div class="menu" >
		OpenPhoda-SeV
	</div>
	<div >
		
		<table align="right"><tr>
		<td><form action="exp.php" method="GET">
			<input type="hidden" name="logout" value = "logout">
			<button class="menubtn" type="submit" value="logout">logout</button></form></td>
		</tr></table>

	</div>

</div>

<div style="background:#FFFFFF">
	<div class="sections">
<?php

// load the login class
require_once('classes/Main.php');
// create a Main object. 
$main = new Main();

?>

</div>

<?php include('_footer.php');?>
		
