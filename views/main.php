<?php include('_header.php');
?>
<div style="text-align:left;" class="boxcenter">
    <div class="menu">
        <div style="float: left;">
            OpenPhoda-SeV
        </div>
        
        <div style="float: right; font-size: 12px; margin-top: 5px;">
            <strong>Welcome, <?php echo htmlspecialchars($_SESSION['exp_name']); ?></strong><br>
            <a href="password_change.php">Change Password</a> | 
            <a href="exp.php?logout=logout">Logout</a>
        </div>
        
        <div style="clear: both;"></div>
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
		
