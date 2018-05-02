<?php
/** PAI COB Rate View
 * package    PAI_COBList 20180430
 * @license   Copyright © 2018 Pathfinder Associates, Inc.
 *	opens the coblist db and deletes from the rate table
 *	called by COBMastermenu.php after login
 */

 	// check if logged in 
	session_start();
	if(!isset($_SESSION["userid"])) {
		header("Location:COBMastermenu.php");
	}

	require ("COBdbopen.php");

	$class = $_GET['class'];
	$DelSql = "DELETE FROM `RateMaster` WHERE class=:class";
	$res = $pdo->prepare($DelSql);
	if($res->execute([$class])){
		header('location: rateView.php');
	}else{
		echo "Failed to delete";
	}
?>