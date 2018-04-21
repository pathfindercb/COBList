<?php
 	// check if logged in 
	session_start();
	if(!isset($_SESSION["userid"])) {
		header("Location:COBMastermenu.php");
	}
	require_once ('COBconnect.php');
	$charset = 'utf8';
	$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
	$opt = [
		PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
		PDO::ATTR_EMULATE_PREPARES   => false,
	];
	$pdo = new PDO($dsn, $user, $pass, $opt);

	if(isset($_POST) & !empty($_POST)){
	$class = ($_POST['class']);
	$rate = ($_POST['rate']);
	$date = ($_POST['date']);

	$Sql = "INSERT INTO `RateMaster` (class, rate, date) VALUES ('$class', '$rate', '$date')";
	$res = $pdo->prepare($Sql);
	if($res->execute()){
		header('location: rateView.php');
	}else{
		$fmsg = "Failed to update data.";
	}
}?>
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
			    <label for="input1" class="col-sm-2 control-label">Class</label>
			    <div class="col-sm-6">
			      <input type="text" name="class"  class="form-control" id="input1"  placeholder="Class" />
			    </div>
			</div>

			<div class="form-group">
			    <label for="input1" class="col-sm-2 control-label">Rate</label>
			    <div class="col-sm-6">
			      <input type="text" name="rate"  class="form-control" id="input1"  placeholder="Rate" />
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