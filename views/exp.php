<?php include('_headerTest.php');

if ($login->isexpLoggedIn() == true) {

	// load the Experiment/Testing class
	require_once('classes/Test.php');

		//create a Test object. 
	$test = new Test();

	
}
include('_footer.php');?>