<?php include('_headerTest.php');

if ($_SESSION['exp_logged_in'] === 1) {

	// load the Experiment/Testing class
	require_once('classes/Test.php');

		//create a Test object. 
	$test = new Test();

	
}
include('_footer.php');?>