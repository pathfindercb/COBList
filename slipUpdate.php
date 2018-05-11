<?php
/** PAI COB Slip update
 * package    PAI_COBList 20180511
 * @license   Copyright © 2018 Pathfinder Associates, Inc.
 *	opens the coblist db and updates the slip table
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

$slipid = $_GET['slipid'];
$SelSql = "SELECT * FROM `SlipMaster` WHERE slipid=:slipid";
$stmt = $pdo->prepare($SelSql);
$stmt->execute([$slipid]);
$r = $stmt->fetch(PDO::FETCH_ASSOC);
//if not found then exit
if (!$r) {header('location: slipView.php');}
if(isset($_POST) & !empty($_POST)){
	$slipid = ($_POST['slipid']);
	$type = ($_POST['type']);
	$dock = ($_POST['dock']);
	$class = ($_POST['class']);
	$scondition = ($_POST['scondition']);
	$width = ($_POST['width']);
	$depth = ($_POST['depth']);

	$sql = "UPDATE `SlipMaster` SET type=:type, dock=:dock, class=:class, scondition=:scondition, width=:width, depth=:depth WHERE slipid=:slipid";
	$val = array("slipid" => $slipid, "type" => $type, "dock" => $dock, "class" => $class, "scondition" => $scondition, "width" => $width, "depth" => $depth );
	$stmt = $pdo->prepare($sql);
	if($stmt->execute($val)){
		header('location: slipView.php');
	}else{
		$fmsg = "Failed to update data.";
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Update SlipMaster</title>
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
		<h2>Update SlipMaster</h2>
			<div class="form-group">
			    <label for="slipid" class="col-sm-2 control-label">Slip</label>
			    <div class="col-sm-6">
			      <input type="text" name="slipid"  class="form-control" id="slipid" readonly value="<?php echo $r['slipid']; ?>" placeholder="slipid" />
			    </div>
			</div>

			<div class="form-group">
			<label for="input1" class="col-sm-2 control-label">Type</label>
			<div class="col-sm-6">
				<select name="type" class="form-control">
					<option value="Slip" <?php if($r['type'] == 'Slip'){ echo " selected ";} ?> >Slip</option>
					<option value="Kayak" <?php if($r['type'] == 'Kayak'){ echo " selected ";} ?> >Kayak</option>
				</select>
			</div>
			</div>

			<div class="form-group">
			<label for="input1" class="col-sm-2 control-label">Dock</label>
			<div class="col-sm-6">
				<select name="dock" class="form-control">
					<option value="MS" <?php if($r['dock'] == 'MS'){ echo "selected";} ?> >MS</option>
					<option value="North Dock" <?php if($r['dock'] == 'North Dock'){ echo " selected ";} ?> >North Dock</option>
					<option value="South Dock" <?php if($r['dock'] == 'South Dock'){ echo " selected ";} ?> >South Dock</option>
				</select>
			</div>
			</div>

			<div class="form-group">
			<label for="input1" class="col-sm-2 control-label">Class</label>
			<div class="col-sm-6">
				<select name="class" class="form-control">
<?php
				// fill rate class
				foreach ($classes as $mclass) {
					$sel = ($r['class'] == $mclass['class']) ? " selected " : "";
					echo '<option value="' . $mclass['class'] .'"' . $sel . '">' . $mclass['class'] . '</option>';
				}
?>
				</select>
			</div>
			</div>

			<div class="form-group">
			    <label for="scondition" class="col-sm-2 control-label">Condition</label>
			    <div class="col-sm-6">
			      <input type="scondition" name="scondition"  class="form-control" id="scondition" value="<?php echo $r['scondition']; ?>" placeholder="Condition" />
			    </div>
			</div>

			<div class="form-group">
			    <label for="width" class="col-sm-2 control-label">Width</label>
			    <div class="col-sm-6">
			      <input type="width" name="width"  class="form-control" id="width" value="<?php echo $r['width']; ?>" placeholder="Width" />
			    </div>
			</div>

			<div class="form-group">
			    <label for="depth" class="col-sm-2 control-label">Depth</label>
			    <div class="col-sm-6">
			      <input type="depth" name="depth"  class="form-control" id="depth" value="<?php echo $r['depth']; ?>" placeholder="Depth" />
			    </div>
			</div>

			<input type="submit" class="btn btn-primary col-sm-2 col-sm-offset-6" value="submit" />
			<input type="cancel" class="btn btn-warning col-sm-2 col-sm-offset-6" value="cancel" onClick="window.location='slipView.php';"/>
		</form>
	</div>
</div>
</body>
</html>