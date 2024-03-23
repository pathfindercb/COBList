<?php
/** PAI COBRackss -  View current racks and waitlist
 * package    PAI_COBList 20240203
 * @license   Copyright © 2018-2024 Pathfinder Associates, Inc.
 *
 *	1. check for passcode
 *	2. Get mCob class to retrieve SKData file & display
 *	5. dupe for kayaks
 */


// check for passcode
if(isset($_GET['passcode']) & !empty($_GET['passcode']) & $_GET['passcode'] == '888'){
	
}else{
	header("Location: https://condoonthebay.com");
}
	include ('PAI_coblist.class.php');
	register_shutdown_function('shutDownFunction');
	$msg = "";
	$mCOB = new COBList();
	// get SKData file from private folder decrypted
	$results = $mCOB->GetSKData($msg);
	if ($results) {
		$isdate = $results->Response->Run;
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Racks & Wait List</title>
	<!-- Latest compiled and minified CSS -->
		<?php include "stdHeader.html";?>
</head>
<body>
<div class="container">
	<div class="row">
	<h2>Kayak Racks & Wait List (updated <?php echo $isdate; ?>)</h2>
	<h4> North Dock </h4>
		<table class="table table-sm"> 
		<thead> 
			<tr> 
				<th>Slip</th> 
				<th>Location</th> 
				<th>Name</th> 
				<th>Unit</th> 
			</tr> 
		</thead> 
		<tbody> 
		<?php 
		foreach ($results->Response->Data->Racks->North as $r) {
		?>
			<tr class="p-0 <?php if(is_null($r->unit)){echo ' table-warning';}?>"> 
				<th scope="row"><?php echo $r->slipid; ?></th> 
				<td><?php echo $r->scondition; ?></td> 
				<td> <?php echo $r->names;?></td> 
				<td><?php echo $r->unit; ?></td> 
			</tr> 
		<?php } ?>
		</tbody> 
		</table>
	<br>
	<h4> South Dock </h4>
		<table class="table table-sm"> 
		<thead> 
			<tr> 
				<th>Slip</th> 
				<th>Location</th> 
				<th>Name</th> 
				<th>Unit</th> 
			</tr> 
		</thead> 
		<tbody> 
		<?php 
		foreach ($results->Response->Data->Racks->South as $r) {
		?>
			<tr class="p-0 <?php if(is_null($r->unit)){echo ' table-warning';}?>"> 
				<th scope="row"><?php echo $r->slipid; ?></th> 
				<td><?php echo $r->scondition; ?></td> 
				<td> <?php echo $r->names;?></td> 
				<td><?php echo $r->unit; ?></td> 
			</tr> 
		<?php } ?>
		</tbody> 
		</table>
	<br>
	<h4> Rack Wait List </h4>
		<table class="table-sm"> 
		<thead> 
			<tr> 
				<th>Date</th> 
				<th>Name</th> 
				<th>Unit</th> 
				<th>Number</th> 
			</tr> 
		</thead> 
		<tbody> 
		<?php 
		foreach ($results->Response->Data->Racks->Wait as $r) {
		?>
			<tr> 
				<th scope="row"><?php echo $r->date; ?></th> 
				<td><?php echo $r->names; ?></td> 
				<td> <?php echo $r->unit;?></td> 
				<td> <?php echo $r->number;?></td> 

			</tr> 
		<?php } ?>
		</tbody> 
		</table>
		</div>
	</div>
</div>
<!--Footer-->
<footer class="page-footer font-small blue pt-4 mt-4">
<!--Copyright-->
    <div class="footer-copyright py-3 text-center">
        <a href="DockLayoutGraphic.pdf" target="_blank">Dock Diagram</a><br>
        <a href="License.htm"  target="_blank">Licensed for free use</a> © 2018-2024  
        <a href="http://pathfinderassociatesinc.com/">Pathfinder Associates, Inc.</a>&emsp;&emsp;
 		    Hits = <?php file_put_contents('countr.txt',"\n",FILE_APPEND|LOCK_EX);
			echo (filesize("countr.txt")); ?> 
   </div>
<!--/.Copyright-->
</footer>
<!--/.Footer-->
<?php
unset($mCOB);

function shutDownFunction() { 
    $error = error_get_last();
    // fatal error, E_ERROR === 1
    if ($error['type'] === E_ERROR) { 
        //do your stuff
		//error_log ($_SERVER['REMOTE_ADDR'] . '=' . $msg,0);
		echo "Program failed! Please try again using left menu. If it keeps failing notify Chris Barlow.";
    } 
}
?>
</body>
</html>