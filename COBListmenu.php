<!DOCTYPE html>

<!-- COBDelta 05/02/18 -->
<!-- This is the main load for the COBList files using a form to select run type-->
<!-- Requires COBListrun.php, COBListlogout.ph, PAI_coblist.class.php, PAI_crypt.class.php, PAI_xlsxwriter.class.php, COBconnect.php, COBkey.php  -->

<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=us-ascii">
<title>Run COBList v4.0</title>
	<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" >
 
<!-- Optional theme -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" >
 
<link rel="stylesheet" href="styles.css" >

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1">

<script type='text/javascript'>

    function showFileModified() {
        var input, file;

        // Testing for 'function' is more specific and correct, but doesn't work with Safari 6.x
        if (typeof window.FileReader !== 'function' &&
            typeof window.FileReader !== 'object') {
            write("The file API isn't supported on this browser yet.");
            return;
        }

        input = document.getElementById('fileSelect');
        if (!input) {
            write("Um, couldn't find the filename element.");
        }
        else if (!input.files) {
            write("This browser doesn't seem to support the `files` property of file inputs.");
        }
        else if (!input.files[0]) {
            write("Please select a file before clicking 'Submit'");
        }
        else {
            file = input.files[0];
 			document.getElementById('fileTime').value = file.lastModified;
        }

    }

</script>
</head>
<body>
<div class="container">
	<div class="row">
	<?php if(isset($fmsg)){ ?><div class="alert alert-danger" role="alert"> <?php echo $fmsg; ?> </div><?php } ?>
		<form name="frmReport" method="post" action="COBListrun.php" enctype="multipart/form-data" class="form-horizontal col-xs-12">
		<h2>COBList Reports</h2>
		<h4>Use this form to upload the UserExport CSV file and download the COBList Excel file</h4>
		<h6>1. Export from Manage Users -- just Select All then Export Selected</h6>
		<h6>2. Choose External report if this will sent outside office</h6>
		<h6>3. Do a partial export from Manage Users (Owner=Yes) for voter labels</h6>
			<div class="form-group">
			<label for="input1" class="col-sm-2 control-label">Report type</label>
			<div class="col-sm-6">
				<select name="choice" class="form-control">
				<option value="1">Internal - with all phone & email</option>
				<option value="2">External - showing phone & email by owner's display preference</option>
				<option value="3">Partial - with just Voter and User Sheets</option>
				</select>			
			</div>
			</div>
			<div class="form-group">
			<div class="col-sm-6">
			<input type="file" name="import" id="fileSelect" onchange="showFileModified();">
			<input type="hidden" name="fileTime" id="fileTime">
			</div>
			</div>

			<input type="submit" name="submit" class="btn btn-primary col-xs-4 " value="Run Report" />
		</form>

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

</body>
</html>

