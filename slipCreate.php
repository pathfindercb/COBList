<?php
/** PAI COB Slip Create
 * package    PAI_COBList 20180511
 * @license   Copyright © 2018 Pathfinder Associates, Inc.
 *	opens the coblist db and adds to the slip table
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
	$classes = $stmt->fetchALL(PDO::FETCH_ASSOC);

	// queries the SlipMaster table and returns array of slipid For Master
	$sql = "SELECT slipid FROM SlipMaster";
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
	$slips = $stmt->fetchALL(PDO::FETCH_COLUMN);
	

	if(isset($_POST) & !empty($_POST)){
		$slipid = ($_POST['slipid']);
		$type = ($_POST['type']);
		$dock = ($_POST['dock']);
		$class = ($_POST['class']);
		$scondition = ($_POST['scondition']);
		$width = ($_POST['width']);
		$depth = ($_POST['depth']);

		if (in_array($slipid,$slips)) {
			$fmsg = "This slip already in Slip Master - please use Edit";
		} else {
			$sql = "INSERT INTO `SlipMaster` (slipid, type, dock, class, scondition, width, depth) VALUES (:slipid, :type, :dock, :class, :scondition, :width, :depth)";
			$val = array("slipid" => $slipid, "type" => $type, "dock" => $dock, "class" => $class, "scondition" => $scondition, "width" => $width, "depth" => $depth );
			$stmt = $pdo->prepare($sql);
			if($stmt->execute($val)){
				header('location: slipView.php');
			}else{
				$fmsg = "Failed to update data.";
			}
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Create Slip</title>
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
		<h2>Create Slip</h2>
			<div class="form-group">
			    <label for="slipid" class="col-sm-2 control-label">Slip</label>
			    <div class="col-sm-6">
			      <input type="text"  required name="slipid"  class="form-control" id="slipid"  placeholder="slipid" />
			    </div>
			</div>

			<div class="form-group">
			<label for="input1" class="col-sm-2 control-label">Type</label>
			<div class="col-sm-6">
				<select required name="type" class="form-control" >
					<option value=""> Select Type</option>
					<option value="Slip"  >Slip</option>
					<option value="Kayak"  >Kayak</option>
				</select>
			</div>
			</div>

			<div class="form-group">
			<label for="input1" class="col-sm-2 control-label">Dock</label>
			<div class="col-sm-6">
				<select  required name="dock" class="form-control">
					<option value="">Select Dock</option>
					<option value="MS" >MS</option>
					<option value="North Dock"  >North Dock</option>
					<option value="South Dock"  >South Dock</option>
				</select>
			</div>
			</div>

			<div class="form-group">
			<label for="input1" class="col-sm-2 control-label">Class</label>
			<div class="col-sm-6">
				<select  required name="class" class="form-control">
					<option value="">Select Rate class</option>
<?php
				// fill rate class
				foreach ($classes as $mclass) {
					echo '<option value="' . $mclass['class'] . '">' . $mclass['class'] . '</option>';
				}
?>
				</select>
			</div>
			</div>

			<div class="form-group">
			    <label for="scondition" class="col-sm-2 control-label">Condition</label>
			    <div class="col-sm-6">
			      <input type="scondition" name="scondition" value="Normal"  class="form-control" id="scondition" placeholder="Condition" />
			    </div>
			</div>

			<div class="form-group">
			    <label for="width" class="col-sm-2 control-label">Width</label>
			    <div class="col-sm-6">
			      <input type="width" name="width" value="14" class="form-control" id="width"  placeholder="Width" />
			    </div>
			</div>

			<div class="form-group">
			    <label for="depth" class="col-sm-2 control-label">Depth</label>
			    <div class="col-sm-6">
			      <input type="depth" name="depth" value="4" class="form-control" id="depth"  placeholder="Depth" />
			    </div>
			</div>
			<input type="submit" class="btn btn-primary col-sm-2 col-sm-offset-6" value="submit" />
			<input type="cancel" class="btn btn-warning col-sm-2 col-sm-offset-6" value="cancel" onClick="window.location='slipView.php';"/>
		</form>
	</div>
</div>
</body>
</html>