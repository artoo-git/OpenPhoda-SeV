<?php include('_header.php');
?>


<div style="text-align:left" class="boxcenter">
	<div class="menu">
		Open Phoda-SeV
	</div>
	<div>
		<!-- <div><form method="GET" action="index.php"><input type="hidden" value="it" name="ln">
		<input type="image" class="flags" src="img/IT.png"  name="ln" alt="Submit" /></form> -->
		<table align="right"><tr>
		<!-- <td><form method="GET" action="index.php"><input type="hidden" value="en" name="ln">
			<input type="image" class="flags"  src="img/UK.png"  name="ln" alt="Submit" /></form></td> -->
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
		