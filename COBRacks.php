<?php
/** PAI COBSlips -  View current racks and waitlist
 * package    PAI_COBList 20240123
 * @license   Copyright © 2018-2024 Pathfinder Associates, Inc.
 *	opens the coblist db and view the slip table
 *	1. check for passcode
 *	2. opendb and get last coblist run per cobdelta
 *	3. query slip/slipmaster join? or just slip in graphic format
 *	4. query waitlist
 *	5. dupe for kayaks
 */

// check for passcode
if(isset($_GET['passcode']) & !empty($_GET['passcode']) & $_GET['passcode'] == '888'){
	
}else{
	header("Location: https://condoonthebay.com");
}

// open COBList database and set PDO object
require ("COBdbopen.php");

// get latest full COBList run
	// queries the RunLog table and returns array of logid & filetime For Delta
		$sql = "SELECT filetime FROM RunLog WHERE type = 1 OR type = 5 ORDER BY filetime desc";
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$isdate = date("d-M-Y",$result['filetime']);
	

?>
<!DOCTYPE html>
<html>
<head>
	<title>Kayak Racks & Wait List</title>
	<!-- Latest compiled and minified CSS -->
	<?php include "stdHeader.html";?>
</head>
<body>
<div class="container">
	<div class="row">
	<h2>Kayak Racks & Wait List (updated <?php echo $isdate; ?>)</h2>
	<h4> North Dock </h4>
<?php
// query all slips - leased and vacant
$sql = "SELECT b.slipid, b.class, b.scondition, a.names, a.unit
							FROM SlipMaster b
							LEFT OUTER JOIN Slips a ON a.slipid = b.slipid
							WHERE b.type = 'Kayak' 
							AND b.dock = 'North Dock'
							ORDER BY b.slipid";
$stmt = $pdo->prepare($sql);
$stmt->execute();
?>
		<table class="table table-sm"> 
		<thead> 
			<tr> 
				<th>Rack</th> 
				<th>Location</th> 
				<th>Name</th> 
				<th>Unit</th> 
			</tr> 
		</thead> 
		<tbody> 
		<?php 
		while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
		?>
			<tr class="p-0 <?php if(is_null($r['unit'])){echo ' table-warning';}?>"> 
				<th scope="row"><?php echo $r['slipid']; ?></th> 
				<td><?php echo $r['scondition']; ?></td> 
				<td> <?php echo $r['names'];?></td> 
				<td><?php echo $r['unit']; ?></td> 

			</tr> 
		<?php } ?>
		</tbody> 
		</table>
	<br>
	<h4> South Dock </h4>
<?php
// query all slips - leased and vacant
$sql = "SELECT b.slipid, b.class, b.scondition, a.names, a.unit
							FROM SlipMaster b
							LEFT OUTER JOIN Slips a ON a.slipid = b.slipid
							WHERE b.type = 'Kayak' 
							AND b.dock = 'South Dock'
							ORDER BY b.slipid";
$stmt = $pdo->prepare($sql);
$stmt->execute();
?>
		<table class="table "> 
		<thead> 
			<tr> 
				<th>Rack</th> 
				<th>Location</th> 
				<th>Name</th> 
				<th>Unit</th> 
			</tr> 
		</thead> 
		<tbody> 
		<?php 
		while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
		?>
			<tr class="p-0 <?php if(is_null($r['unit'])){echo ' table-warning';}?>"> 
				<th scope="row"><?php echo $r['slipid']; ?></th> 
				<td><?php echo $r['scondition']; ?></td> 
				<td> <?php echo $r['names'];?></td> 
				<td><?php echo $r['unit']; ?></td> 

			</tr> 
		<?php } ?>
		</tbody> 
		</table>
	<br>
	<h4> Kayak Rack Wait List </h4>
<?php
// query all slips - leased and vacant
$sql = "SELECT date,number, names, unit
							FROM WaitList
							WHERE type = 'K' 
							ORDER BY date";
$stmt = $pdo->prepare($sql);
$stmt->execute();
?>
		<div class="p-15">
		<table class="table"> 
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
		while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
		?>
			<tr> 
				<th scope="row"><?php echo $r['date']; ?></th> 
				<td><?php echo $r['names']; ?></td> 
				<td> <?php echo $r['unit'];?></td> 
				<td> <?php echo $r['number'];?></td> 

			</tr> 
		<?php } ?>
		</tbody> 
		</table>
		</div>
	</div>
</div>
!--Footer-->
<footer class="page-footer font-small blue pt-4 mt-4">
<!--Copyright-->
    <div class="footer-copyright py-3 text-center">
        <a href="License.htm"  target="_blank">Licensed for free use</a> © 2018-2024  
        <a href="http://pathfinderassociatesinc.com/">Pathfinder Associates, Inc.</a>
    </div>
<!--/.Copyright-->
</footer>
<!--/.Footer-->

</body>
</html>