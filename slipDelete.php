<?php
 	// check if logged in 
	session_start();
	if(!isset($_SESSION["userid"])) {
		header("Location:COBMastermenu.php");
	}
	require_once ('COBconnect.php');
	$charset = 'utf8';
	$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
	$opt = [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES   => false,
	];
	$pdo = new PDO($dsn, $user, $pass, $opt);

	$slipid = $_GET['slipid'];
$DelSql = "DELETE FROM `SlipMaster` WHERE slipid=:slipid";
$res = $pdo->prepare($DelSql);
if($res->execute([$slipid])){
	header('location: slipView.php');
}else{
	echo "Failed to delete";
}
?>