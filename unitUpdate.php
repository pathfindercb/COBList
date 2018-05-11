<?php
/** PAI COB Unit Update
 * package    PAI_COBList 20180511
 * @license   Copyright © 2018 Pathfinder Associates, Inc.
 *	opens the coblist db and updates the unit table
 *	called by COBMastermenu.php after login
 */

 	// check if logged in 
	session_start();
	if(!isset($_SESSION["userid"])) {
		header("Location:COBMastermenu.php");
	}

	require ("COBdbopen.php");

$unit = $_GET['unit'];

$sql = "SELECT * FROM `UnitMaster` WHERE unit=:unit";
$stmt = $pdo->prepare($sql);
$stmt->execute([$unit]);
$r = $stmt->fetch(PDO::FETCH_ASSOC);
//if not found then exit
if (!$r) {header('location: unitView.php');}

if(isset($_POST) & !empty($_POST)){
	$unit = ($_POST['unit']);
	$floor = ($_POST['floor']);
	$stack = ($_POST['stack']);
	$space = ($_POST['space']);
	$bldg = ($_POST['bldg']);
	$fee = ($_POST['fee']);
	$propid = ($_POST['propid']);

	$sql = "UPDATE `UnitMaster` SET floor=:floor, stack=:stack, space=:space, bldg=:bldg, fee=:fee, propid=:propid WHERE unit=:unit";
	$val = array("unit" => $unit, "floor" => $floor, "stack" => $stack, "space" => $space, "bldg" => $bldg, "fee" => $fee, "propid" => $propid );
	$stmt = $pdo->prepare($sql);
	if($stmt->execute()){
		header('location: unitView.php');
	}else{
		$fmsg = "Failed to update data.";
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Update UnitMaster</title>
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
		<form method="post" class="form-horizontal col-md-6 col-md-offset-3">
		<h2>Update UnitMaster</h2>
			<div class="form-group">
			    <label for="unit" class="col-sm-2 control-label">Unit</label>
			    <div class="col-sm-10">
			      <input type="text" name="unit"  class="form-control" id="unit" value="<?php echo $r['unit']; ?>" readonly placeholder="unit" />
			    </div>
			</div>

			<div class="form-group">
			<label for="input1" class="col-sm-2 control-label">Floor</label>
			<div class="col-sm-10">
				<select name="type" class="form-control">
					<option value="1" <?php if($r['floor'] == '1'){ echo "selected";} ?> >1</option>
					<option value="2" <?php if($r['floor'] == '2'){ echo "selected";} ?> >2</option>
					<option value="3" <?php if($r['floor'] == '3'){ echo "selected";} ?> >3</option>
					<option value="4" <?php if($r['floor'] == '4'){ echo "selected";} ?> >4</option>
					<option value="5" <?php if($r['floor'] == '5'){ echo "selected";} ?> >5</option>
					<option value="6" <?php if($r['floor'] == '6'){ echo "selected";} ?> >6</option>
					<option value="7" <?php if($r['floor'] == '7'){ echo "selected";} ?> >7</option>
					<option value="8" <?php if($r['floor'] == '8'){ echo "selected";} ?> >8</option>
					<option value="9" <?php if($r['floor'] == '9'){ echo "selected";} ?> >9</option>
					<option value="10" <?php if($r['floor'] == '10'){ echo "selected";} ?> >10</option>
					<option value="11" <?php if($r['floor'] == '11'){ echo "selected";} ?> >11</option>
					<option value="12" <?php if($r['floor'] == '12'){ echo "selected";} ?> >12</option>
					<option value="14" <?php if($r['floor'] == '14'){ echo "selected";} ?> >14</option>
					<option value="15" <?php if($r['floor'] == '15'){ echo "selected";} ?> >15</option>
					<option value="16" <?php if($r['floor'] == '16'){ echo "selected";} ?> >16</option>
					<option value="17" <?php if($r['floor'] == '17'){ echo "selected";} ?> >17</option>
					<option value="18" <?php if($r['floor'] == '18'){ echo "selected";} ?> >18</option>
					<option value="19" <?php if($r['floor'] == '19'){ echo "selected";} ?> >19</option>
				</select>
			</div>
			</div>

			<div class="form-group">
			<label for="input1" class="col-sm-2 control-label">Stack</label>
			<div class="col-sm-10">
				<select name="Stack" class="form-control">
					<option value="01" <?php if($r['stack'] == '01'){ echo "selected";} ?> >01</option>
					<option value="02" <?php if($r['stack'] == '02'){ echo "selected";} ?> >02</option>
					<option value="03" <?php if($r['stack'] == '03'){ echo "selected";} ?> >03</option>
					<option value="04" <?php if($r['stack'] == '04'){ echo "selected";} ?> >04</option>
					<option value="05" <?php if($r['stack'] == '05'){ echo "selected";} ?> >05</option>
					<option value="06" <?php if($r['stack'] == '06'){ echo "selected";} ?> >06</option>
					<option value="07" <?php if($r['stack'] == '07'){ echo "selected";} ?> >07</option>
					<option value="08" <?php if($r['stack'] == '08'){ echo "selected";} ?> >08</option>
					<option value="09" <?php if($r['stack'] == '09'){ echo "selected";} ?> >09</option>
					<option value="10" <?php if($r['stack'] == '10'){ echo "selected";} ?> >10</option>
					<option value="11" <?php if($r['stack'] == '11'){ echo "selected";} ?> >11</option>
					<option value="12" <?php if($r['stack'] == '12'){ echo "selected";} ?> >12</option>
					<option value="14" <?php if($r['stack'] == '14'){ echo "selected";} ?> >14</option>
					<option value="15" <?php if($r['stack'] == '15'){ echo "selected";} ?> >15</option>
					<option value="16" <?php if($r['stack'] == '16'){ echo "selected";} ?> >16</option>
					<option value="17" <?php if($r['stack'] == '17'){ echo "selected";} ?> >17</option>
				</select>
			</div>
			</div>

			<div class="form-group">
			    <label for="space" class="col-sm-2 control-label">Space</label>
			    <div class="col-sm-10">
			      <input type="space" name="space"  class="form-control" id="space" value="<?php echo $r['space']; ?>" required placeholder="Space" />
			    </div>
			</div>

			<div class="form-group">
			<label for="input1" class="col-sm-2 control-label">Bldg</label>
			<div class="col-sm-10">
				<select name="bldg" class="form-control">
					<option value="Tower 1" <?php if($r['bldg'] == 'Tower 1'){ echo "selected";} ?> >Tower 1</option>
					<option value="Tower 2" <?php if($r['bldg'] == 'Tower 2'){ echo "selected";} ?> >Tower 2</option>
					<option value="Marina Suites" <?php if($r['bldg'] == 'Marina Suites'){ echo "selected";} ?> >Marina Suites</option>
				</select>
			</div>
			</div>

			<div class="form-group">
			    <label for="fee" class="col-sm-2 control-label">Fee%</label>
			    <div class="col-sm-10">
			      <input type="fee" name="fee"  class="form-control" id="fee" value="<?php echo $r['fee']; ?>" required placeholder="Fee%" />
			    </div>
			</div>

			<div class="form-group">
			    <label for="propid" class="col-sm-2 control-label">PropID</label>
			    <div class="col-sm-10">
			      <input type="propid" name="propid"  class="form-control" id="propid" value="<?php echo $r['propid']; ?>" required placeholder="PropID" />
			    </div>
			</div>

			<input type="submit" class="btn btn-primary col-md-2 col-md-offset-10" value="submit" />
			<input type="cancel" class="btn btn-warning col-md-2 col-md-offset-10" value="cancel" onClick="window.location='unitView.php';"/>
		</form>
	</div>
</div>
</body>
</html>