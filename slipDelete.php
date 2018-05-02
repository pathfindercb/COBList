<?php
/** PAI COB Slip Delete
 * package    PAI_COBList 20180430
 * @license   Copyright © 2018 Pathfinder Associates, Inc.
 *	opens the coblist db and deletes from the slip table
 *	called by COBMastermenu.php after login
 */

 	// check if logged in 
	session_start();
	if(!isset($_SESSION["userid"])) {
		header("Location:COBMastermenu.php");
	}

	require ("COBdbopen.php");

	$slipid = $_GET['slipid'];
$DelSql = "DELETE FROM `SlipMaster` WHERE slipid=:slipid";
$res = $pdo->prepare($DelSql);
if($res->execute([$slipid])){
	header('location: slipView.php');
}else{
	echo "Failed to delete";
}
?>