<?php
//	Process COBList 20180430
//	package    PAI_COBList
//	@license        Copyright © 2018 Pathfinder Associates, Inc.
// v4.0

register_shutdown_function('shutDownFunction');
// check if logged in 
	session_start();
	if(!isset($_SESSION["userid"])) {
		header("Location:COBMastermenu.php");
	}

	require ("COBdbopen.php");

$mtable = $_GET['mtable'];
$q = $pdo->prepare("DESCRIBE " . $mtable);
$q->execute();
$tfields = $q->fetchAll(PDO::FETCH_COLUMN);
//header flipped set to strings
$keys = array_keys(array_flip($tfields));
$hdr = array_fill_keys($keys, "string");

//print("<pre>".print_r($tfields,true)."</pre>");		

$SelSql = "SELECT * FROM `" . $mtable . "` ORDER BY " . $tfields[0];
$res = $pdo->prepare($SelSql);
$res->execute();
$r = $res->fetchALL(PDO::FETCH_ASSOC);
//print("<pre>".print_r($r,true)."</pre>");		

	//Creates the XL file from all the array
	// Include the required Class file
	include('PAI_xlsxwriter.class.php');
	$filename = $mtable . date('Ymd') . ".xlsx";
	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
		//setup body & heading row style
	$bstyle = array( 'font'=>'Arial','font-size'=>10,'font-style'=>'normal', 'halign'=>'center', 'border'=>'bottom');
	$hstyle = array( 'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'halign'=>'center', 'border'=>'bottom');
	$h1style = array( 'font'=>'Arial','font-size'=>12,'font-style'=>'bold', 'halign'=>'left', 'border'=>'bottom');
	$hdrRes = array(date('m/d/y'),'Condo on the Bay Master Listing');
	//write header then sheet data and output file
	$writer = new XLSXWriter();
	$writer->setTitle('Condo on the Bay Master Listing v4.0');
	$writer->setAuthor('Chris Barlow, Pathfinder Associates, Inc. ');
	$writer->setColWidths($mtable,array(20,20,20,20,20,20,20,20,20,20,20));
	$writer->writeSheetHeader($mtable,$hdr,true);
	$writer->writeSheetRow($mtable,$hdrRes,$h1style);
	$writer->writeSheetRow($mtable,$tfields,$hstyle);
	$writer->writeSheet($r,$mtable,$hdr,true);
	$writer->writeToStdOut(); 
	unset($writer);
exit;


function shutDownFunction() { 
    $error = error_get_last();
    // fatal error, E_ERROR === 1
    if ($error['type'] === E_ERROR) { 
        //do your stuff
		//error_log ($_SERVER['REMOTE_ADDR'] . '=' . $msg,0);
		echo "Program failed! Please try again using left menu Run COBList. If it keeps failing notify Chris Barlow.";
    } 
}

?>