<?php
//	Process COBList 2020205
//	package    PAI_COBList
//	@license        Copyright © 2018-2024 Pathfinder Associates, Inc.
// v4.1.0
// check if logged in 

// Report all PHP errors
error_reporting(E_ALL);

include ('PAI_coblist.class.php');
register_shutdown_function('shutDownFunction');
// check if logged in 
	session_start();
	if(!isset($_SESSION["userid"])) {
		header("Location:COBDeltamenu.php");
	}

$mCOB = new COBList();
$mCOB->wasDelta = $_POST["wasid"];
$mCOB->isDelta = $_POST["isid"];
$delta = $mCOB->RunDelta($msg);

// set report output type
if ($_POST["rptsel"] == "json") {
	if (headers_sent()) {
		error_log ("JSON export failed",0);
		die("Export failed. ");
	}
	error_log ("JSON export",0);
	// clean the output buffer
//	ob_start();
//	ob_clean();
	$mCOB->typeDelta = "11";
	header('Content-disposition: attachment; filename="COBDelta' . date('Ymd') .'.json'.'"');
	header("Content-Type: application/octet-stream;");
	header('Pragma: public');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

//	header('Content-disposition: attachment; filename=COBDelta' . date('Ymd') .'.json');
//	header('Content-type: application/json');

	echo(json_encode($delta));
//	$s1 = ob_get_contents(); // read ob
//	ob_end_flush();
//	error_log ($s1,0);

} else {
	error_log ("XLS export",0);

	$mCOB->typeDelta = "12";
	//Creates the XL file from all the arrays
	// Include the required Class file
	include('PAI_xlsxwriter.class.php');

	$filename = "COBDelta" . date('Ymd') . ".xlsx";

	header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	
	//setup body & heading row style
	$bstyle = array( 'font'=>'Arial','font-size'=>10,'font-style'=>'normal', 'halign'=>'center', 'border'=>'bottom');
	$hstyle = array( 'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'halign'=>'center', 'border'=>'bottom');
	$h1style = array( 'font'=>'Arial','font-size'=>12,'font-style'=>'bold', 'halign'=>'left', 'border'=>'bottom');
	$hdr = array('Title'=>'string', 'Version'=>'string','Copy'=>'string','Term'=>'string','Doc'=>'string','Author'=>'string','Delta'=>'string');
	
	//write header then sheet data and output file
	$writer = new XLSXWriter();
	$writer->setTitle('Condo on the Bay Delta Listing v4.0');
	$writer->setAuthor('Chris Barlow, Pathfinder Associates, Inc. ');
	$writer->setColWidths('Info',array(10,20,20,20,20,20,10));
	$writer->writeSheetHeader('Info',$hdr,true);
	$writer->writeSheetRow('Info',array(date('m/d/y'),'Condo on the Bay Delta Listing'),$h1style);
	$writer->writeSheetRow('Info',array_keys($delta['Response']),$hstyle);
	$writer->writeSheetRow('Info',$delta['Response'],$bstyle);
	$writer->writeSheetRow('Info',array(""),$bstyle);
	$writer->writeSheetRow('Info',array_keys($delta['Response']['Delta']['IsInfo']),$hstyle);
	$writer->writeSheetRow('Info',$delta['Response']['Delta']['IsInfo'],$bstyle);
	$writer->writeSheetRow('Info',array(""),$bstyle);
	$writer->writeSheetRow('Info',array_keys($delta['Response']['Delta']['WasInfo']),$hstyle);
	$writer->writeSheetRow('Info',$delta['Response']['Delta']['WasInfo'],$bstyle);
	
	// write Summary
	// write header first
	if (!empty($delta['Delta']['Summary'])){
		$writer->writeSheetRow('Summary',array_keys($delta['Delta']['Summary'][0]),$hstyle);
	}
	foreach ($delta['Delta']['Summary'] as $chg) {
		$writer->writeSheetRow('Summary',$chg,$bstyle);
	}
	
	//write Added
	// write header first
	if (!empty($delta['Delta']['Added'][0]['Fields']['Is'])){
		$writer->writeSheetRow('Added',array_keys($delta['Delta']['Added'][0]['Fields']['Is']),$hstyle);
	}
	foreach ($delta['Delta']['Added'] as $chg) {
		$writer->writeSheetRow('Added',$chg['Fields']['Is'],$bstyle);
	}
	
	// write changed
	$writer->setColWidths('Changed',array(20,20,20,40));
	foreach ($delta['Delta']['Changed'] as $chg) {
		$writer->writeSheetRow('Changed',$chg,$hstyle);
		foreach ($chg['Fields']['Is'] as $fld => $val) {
			$writer->writeSheetRow('Changed',array($chg['UserID'],"Is",$fld,$val),$bstyle);
		}
		foreach ($chg['Fields']['Was'] as $fld => $val) {
			$writer->writeSheetRow('Changed',array($chg['UserID'],"Was",$fld,$val),$bstyle);
		}
		$writer->writeSheetRow('Changed',array(""),$bstyle);
	}
	
	//write Deleted
	// write header first
	if (!empty($delta['Delta']['Deleted'][0]['Fields']['Was'])){
		$writer->writeSheetRow('Deleted',array_keys($delta['Delta']['Deleted'][0]['Fields']['Was']),$hstyle);
	}
	foreach ($delta['Delta']['Deleted'] as $chg) {
		$writer->writeSheetRow('Deleted',$chg['Fields']['Was'],$bstyle);
	}
	
	$writer->writeToStdOut(); 
	unset($writer);
}
unset($mCOB);
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