<?php
/** PAI COB Rate Create
 * package    PAI_COBList 20180511
 * @license   Copyright © 2018 Pathfinder Associates, Inc.
 *	opens the coblist db and adds to the rate table
 *	called by COBMastermenu.php after login
 */

 	// check if logged in 
	session_start();
	if(!isset($_SESSION["userid"])) {
		header("Location:COBMastermenu.php");
	}

	require ("COBdbopen.php");

	// queries the RateMaster table and returns array of rate classes For Master
	$sql = "SELECT class FROM RateMaster ORDER BY class";
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
	$classes = $stmt->fetchALL(PDO::FETCH_COLUMN);
	
	if(isset($_POST) & !empty($_POST)){
		$class = ($_POST['class']);
		$rate = ($_POST['rate']);
		$date = ($_POST['date']);

		if (in_array($class,$classes)) {
			$fmsg = "This rate class already in Rate Master - please use Edit";
		} else {
			$sql = "INSERT INTO `RateMaster` (class, rate, date) VALUES (:class, :rate, :date)";
			$val = array("class" => $class, "rate" => $rate, "date" => $date );
			$stmt = $pdo->prepare($sql);
			if($stmt->execute($val)){
				header('location: rateView.php');
			}else{
				$fmsg = "Failed to update data.";
			}
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Create Rate</title>
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
      <?php if(isset($smsg)){ ?><div class="alert alert-success" role="alert"> <?php echo $smsg; ?> </div><?php } ?>
      <?php if(isset($fmsg)){ ?><div class="alert alert-danger" role="alert"> <?php echo $fmsg; ?> </div><?php } ?>
		<form method="post" class="form-horizontal col-sm-6 col-sm-offset-3">
		<h2>Create Rate</h2>
			<div class="form-group">
			    <label for="class" class="col-sm-2 control-label">Class</label>
			    <div class="col-sm-6">
			      <input  required type="text" name="class"  class="form-control" id="class"  placeholder="Class" />
			    </div>
			</div>

			<div class="form-group">
			    <label for="rate" class="col-sm-2 control-label">Rate</label>
			    <div class="col-sm-6">
			      <input  required type="text" name="rate"  class="form-control" id="rate"  placeholder="Rate" />
			    </div>
			</div>

			<div class="form-group">
			    <label for="date" class="col-sm-2 control-label">Date</label>
			    <div class="col-sm-6">
			      <input type="date" name="date"  class="form-control" id="date" value="<?php echo date('Y-m-d'); ?>" placeholder="Date" />
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