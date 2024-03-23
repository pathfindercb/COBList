<?php
//	Process COBList 20240229
//	package    PAI_COBList
//	@license   Copyright © 2018-2024 Pathfinder Associates, Inc.
// v4.0
 	// check if logged in 

include ('PAI_coblist.class.php');
register_shutdown_function('shutDownFunction');

$mCOB = new COBList();
$ip="";
if ($mCOB->Checkfile($msg)) {
	switch ($_POST['choice']) {
		case 1:
			$mCOB->showInfo = true;
			$mCOB->fullRun = true;
			break;
		case 2:
			$mCOB->showInfo = false;
			$mCOB->fullRun = true;
			break;
		case 3:
			$mCOB->showInfo = true;
			$mCOB->fullRun = false;
			break;
	}
	$mCOB->logging = false;
	//convert javascript time to unix time
	$mCOB->runTime = time();
	$mCOB->fileTime = time();
//	$mCOB->fileTime = intval($_POST['fileTime']/1000);
	// IE doesn't pass fileTime so just assume runTime for CSV modified time
	if ($mCOB->fileTime == 0) {$mCOB->fileTime = $mCOB->runTime;}
	$ip = $_SERVER['REMOTE_ADDR'] ;
	if ($mCOB->ProcessFile($msg)) {
	} else {
	error_log ($ip . '=' . $msg,0);
	echo $msg;
	}
} else {
	error_log ($ip . '=' . $msg,0);
	echo $msg;
}
unset($mCOB);

function shutDownFunction() { 
    $error = error_get_last();
	if($error) {
	// fatal error, E_ERROR === 1
		if ($error['type'] === E_ERROR) { 
			//do your stuff
			//error_log ($_SERVER['REMOTE_ADDR'] . '=' . $msg,0);
			echo "Program failed! Please try again using left menu Run COBList. If it keeps failing notify Chris Barlow of the errors below:";
			$filename = "error_log";
			$lines=array();
			$lines = file($filename); //get all lines into array
			if (count($lines)>10) {
				$readLines = max(0, count($lines) - 10); //n being non-zero positive integer
			} else {
				$readines = count($lines);
			}
			if($readLines > 0) {
				for ($i = $readLines; $i < count($lines); $i++) {
					echo $lines[$i];
					echo nl2br("\n");
				}
			}
		}
	}
}
?>