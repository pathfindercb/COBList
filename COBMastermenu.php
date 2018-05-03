<!DOCTYPE html>

<!-- COBMaster 05/02/18 -->
<!--	This is the main web index for all the COBList master file maintenance using a form to select-->

<html>
<head>
<title>COBMaster User Login v4.0</title>
	<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" >
 
<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" >
 
<link rel="stylesheet" href="styles.css" >

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>
<?php
	session_start();
	include ('PAI_coblist.class.php');
	register_shutdown_function('shutDownFunction');

	$mCOB = new COBList();
	// get info from RunLog on past runs to try autologon by IP
	$results = $mCOB->GetRuns($msg);
	if ($results) {
		// check if this IP has RunCOBList and skip logon if same IP
		foreach ($results as $r) {
			if ($_SERVER['REMOTE_ADDR'] == $r['ip']) {
				$_SESSION["username"] = "Office";
				$_SESSION["userid"] = $r['ip'];
				break;
			}
		}
	} else {
		error_log ($_SERVER['REMOTE_ADDR']  . '= Auto-Login failed!',0);
		echo $msg;
	}
	
	$fmsg = "";
	$row = "";
	// first check if no Post yet 
	if(!empty($_POST)) {
		//check if post from logon form with button name = logon
		if(isset($_POST["logon"])) {
			// check if entered userid is in RunData & Admin from a prior run
			if ($mCOB->CheckUserID($_POST["userid"],$msg)) {
				$row = array($_POST["username"],$_POST["userid"]);
			}
			if(is_array($row)) {
				$_SESSION["username"] = $row[0];
				$_SESSION["userid"] = $row[1];
				} else {
					$fmsg = "Invalid Username or UserID!";
			}
		}
	}
	// now check if we're logged in and display form to choose report
	if(isset($_SESSION["userid"])) {
		// if logged in then display menu of reports
		$fmsg = $fmsg . "Welcome " . $_SESSION["username"] . ". Click here to <a href='COBMasterlogout.php'>Logout</a>";
		$tdate = date("Y-m-d");

?>
	<p>
<div class="container">
	<div class="row">
	<?php if(isset($fmsg)){ ?><div class="alert alert-danger" role="alert"> <?php echo $fmsg; ?> </div><?php } ?>
		<form name="frmReport" method="post" class="form-horizontal col-xs-12">
		<h2>COBMaster Maintenance</h2>
			<div class="col-xs-4">
			<div class="form-group">
			<input type="button" class="btn btn-primary btn-block" value="Slip Master" onClick="window.location='slipView.php';"/>
			</div>
			<div class="form-group">
			<input type="button" class="btn btn-primary btn-block" value="Rate Master" onClick="window.location='rateView.php';"/>
			</div>
			<div class="form-group">
			<input type="button" class="btn btn-primary btn-block" value="Unit Master" onClick="window.location='unitView.php';"/>
			</div>
			</div>
		</form>
	</div>
</div>
<?php
	} else {
		// if not logged in then display logon form. (empty action posts back to this form)
			$fmsg = "Please Login";

?>
	<p>
<div class="container">
	<div class="row">
	<?php if(isset($fmsg)){ ?><div class="alert alert-danger" role="alert"> <?php echo $fmsg; ?> </div><?php } ?>
		<form name="frmUser" method="post" class="form-horizontal col-xs-12">
		<h2>COB Login</h2>
			<div class="form-group">
			    <label for="input1" class="col-xs-4 control-label">UserName</label>
			    <div class="col-xs-8">
			      <input type="text" name="username"  class="form-control" id="username"/>
			    </div>
			</div>
			<div class="form-group">
			    <label for="input1" class="col-xs-4 control-label">UserID</label>
			    <div class="col-xs-8">
			      <input type="text" name="userid"  class="form-control" id="userid"/>
			    </div>
			</div>
			<input type="submit" name="logon" class="btn btn-primary col-xs-4 col-xs-offset-8" value="Login" />
		</form>
	</div>
</div>
<?php
	} 
?>
<!--Footer-->
<footer class="page-footer font-small blue pt-4 mt-4">
<!--Copyright-->
    <div class="footer-copyright py-3 text-center">
        Copyright © 2018 
        <a href="http://pathfinderassociatesinc.com/"> Pathfinder Associates, Inc.</a>
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
		echo "Program failed! Please try again using left menu Run COBDelta. If it keeps failing notify Chris Barlow.";
    } 
}
?>
</body>
</html>
