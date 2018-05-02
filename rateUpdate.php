<?php
/** PAI COB Rate Update
 * package    PAI_COBList 20180430
 * @license   Copyright © 2018 Pathfinder Associates, Inc.
 *	opens the coblist db and updates the rate table
 *	called by COBMastermenu.php after login
 */

 	// check if logged in 
	session_start();
	if(!isset($_SESSION["userid"])) {
		header("Location:COBMastermenu.php");
	}

	require ("COBdbopen.php");


$class = $_GET['class'];
$SelSql = "SELECT * FROM `RateMaster` WHERE class=:class";
$res = $pdo->prepare($SelSql);
$res->execute([$class]);
$r = $res->fetch(PDO::FETCH_ASSOC);
if(isset($_POST) & !empty($_POST)){
	$class = ($_POST['class']);
	$rate = ($_POST['rate']);
	$date = ($_POST['date']);

	$UpdateSql = "UPDATE `ratemaster` SET class='$class', rate='$rate', date='$date' WHERE class=$class";
	$res = $pdo->prepare($UpdateSql);
	if($res->execute()){
		header('location: viewRateMaster.php');
	}else{
		$fmsg = "Failed to update data.";
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Update RateMaster</title>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" >
 
<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" >
 
<link rel="stylesheet" href="styles.css" >
 
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<div class="container">
	<div class="row">
	<?php if(isset($fmsg)){ ?><div class="alert alert-danger" role="alert"> <?php echo $fmsg; ?> </div><?php } ?>
		<form method="post" class="form-horizontal col-sm-6 col-sm-offset-3">
		<h2>Update RateMaster</h2>
			<div class="form-group">
			    <label for="input1" class="col-sm-2 control-label">Class</label>
			    <div class="col-sm-6">
			      <input type="text" name="class"  class="form-control" id="input1" readonly value="<?php echo $r['class']; ?>" placeholder="Rate" />
			    </div>
			</div>

			<div class="form-group">
			    <label for="input1" class="col-sm-2 control-label">Rate</label>
			    <div class="col-sm-6">
			      <input type="text" name="rate"  class="form-control" id="input1" value="<?php echo $r['rate']; ?>" placeholder="Rate" />
			    </div>
			</div>

			<div class="form-group">
			    <label for="input1" class="col-sm-2 control-label">Date</label>
			    <div class="col-sm-6">
			      <input type="date" name="date"  class="form-control" id="input1" value="<?php echo $r['date']; ?>" placeholder="Date" />
			    </div>
			</div>


			<div class="form-group">
				<input type="submit" class="btn btn-primary col-sm-2 col-sm-offset-6" value="submit" />
				<input type="cancel" class="btn btn-warning col-sm-2 col-sm-offset-6" value="cancel" onClick="window.location='rateView.php';"/>
			</div>
		</form>
	</div>
</div>
</body>
</html>