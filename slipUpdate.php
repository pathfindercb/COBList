<?php
 	// check if logged in 
	session_start();
	if(!isset($_SESSION["userid"])) {
		header("Location:COBMastermenu.php");
	}

	include ("COBfolder.php");
	if (!file_exists($pfolder)) {$pfolder="";}
	require ($pfolder . 'COBconnect.php');
	$charset = 'utf8';
	$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
	$opt = [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES   => false,
	];
	$pdo = new PDO($dsn, $user, $pass, $opt);
	// queries the RateMaster table and returns array of rate class For Master
	$sql = "SELECT class FROM RateMaster ORDER BY class";
	$stmt = $pdo->prepare($sql);
	$stmt->execute();
	$rates = $stmt->fetchALL(PDO::FETCH_ASSOC);

$slipid = $_GET['slipid'];
$SelSql = "SELECT * FROM `SlipMaster` WHERE slipid=:slipid";
$res = $pdo->prepare($SelSql);
$res->execute([$slipid]);
$r = $res->fetch(PDO::FETCH_ASSOC);
if(isset($_POST) & !empty($_POST)){
	$slipid = ($_POST['slipid']);
	$type = ($_POST['type']);
	$dock = ($_POST['dock']);
	$class = ($_POST['class']);
	$scondition = ($_POST['scondition']);
	$width = ($_POST['width']);
	$depth = ($_POST['depth']);

	$UpdateSql = "UPDATE `SlipMaster` SET slipid='$slipid', type='$type', dock='$dock', class='$class', scondition='$scondition', width='$width', depth='$depth' WHERE slipid='$slipid'";
	$res = $pdo->prepare($UpdateSql);
	if($res->execute()){
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
			    <label for="input1" class="col-sm-2 control-label">Slip</label>
			    <div class="col-sm-6">
			      <input type="text" name="slipid"  class="form-control" id="input1" value="<?php echo $r['slipid']; ?>" placeholder="slipid" />
			    </div>
			</div>

			<div class="form-group">
			<label for="input1" class="col-sm-2 control-label">Type</label>
			<div class="col-sm-6">
				<select name="type" class="form-control">
					<option>Select Type</option>
					<option value="Slip" <?php if($r['type'] == 'Slip'){ echo "selected";} ?> >Slip</option>
					<option value="Kayak" <?php if($r['type'] == 'Kayak'){ echo "selected";} ?> >Kayak</option>
				</select>
			</div>
			</div>

			<div class="form-group">
			<label for="input1" class="col-sm-2 control-label">Dock</label>
			<div class="col-sm-6">
				<select name="dock" class="form-control">
					<option>Select Dock</option>
					<option value="MS" <?php if($r['dock'] == 'MS'){ echo "selected";} ?> >MS</option>
					<option value="North Dock" <?php if($r['dock'] == 'North Dock'){ echo "selected";} ?> >North Dock</option>
					<option value="South Dock" <?php if($r['dock'] == 'South Dock'){ echo "selected";} ?> >South Dock</option>
				</select>
			</div>
			</div>

			<div class="form-group">
			<label for="input1" class="col-sm-2 control-label">Class</label>
			<div class="col-sm-6">
				<select name="class" class="form-control">
<?php
				// fill Was dates skipping the latest
				foreach ($rates as $r) {
					echo '<option value="' . $r['class'] . '">' . $r['class'] . '</option>';
				}
?>
				</select>
			</div>
			</div>

			<div class="form-group">
			    <label for="input1" class="col-sm-2 control-label">Condition</label>
			    <div class="col-sm-6">
			      <input type="scondition" name="scondition"  class="form-control" id="input1" value="<?php echo $r['scondition']; ?>" placeholder="Condition" />
			    </div>
			</div>

			<div class="form-group">
			    <label for="input1" class="col-sm-2 control-label">Width</label>
			    <div class="col-sm-6">
			      <input type="width" name="width"  class="form-control" id="input1" value="<?php echo $r['width']; ?>" placeholder="Width" />
			    </div>
			</div>

			<div class="form-group">
			    <label for="input1" class="col-sm-2 control-label">Depth</label>
			    <div class="col-sm-6">
			      <input type="depth" name="depth"  class="form-control" id="input1" value="<?php echo $r['depth']; ?>" placeholder="Depth" />
			    </div>
			</div>

			<input type="submit" class="btn btn-primary col-sm-2 col-sm-offset-6" value="submit" />
			<input type="cancel" class="btn btn-warning col-sm-2 col-sm-offset-6" value="cancel" onClick="window.location='slipView.php';"/>
		</form>
	</div>
</div>
</body>
</html>