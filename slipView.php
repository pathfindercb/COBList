<?php
/** PAI COB Slip View
 * package    PAI_COBList 20240204
 * @license   Copyright © 2018-2024 Pathfinder Associates, Inc.
 *	opens the coblist db and view the slip table
 *	called by COBMastermenu.php after login
 */

 	// check if logged in 
	session_start();
	if(!isset($_SESSION["userid"])) {
		header("Location:COBMastermenu.php");
	}

	require ("COBdbopen.php");

$perpage = 18; // set this in class?
if(isset($_GET['page']) & !empty($_GET['page'])){
	$curpage = $_GET['page'];
}else{
	$curpage = 1;
}
$start = ($curpage * $perpage) - $perpage;
$PageSql = "SELECT * FROM `SlipMaster`";
$pageres = $pdo->prepare($PageSql);
$pageres->execute();
$totalres = $pageres->rowcount();

$endpage = ceil($totalres/$perpage);
$startpage = 1;
$nextpage = $curpage + 1;
$previouspage = $curpage - 1;

$sql = "SELECT * FROM `SlipMaster` ORDER BY slipid LIMIT $start, $perpage";
$stmt = $pdo->prepare($sql);
$stmt->execute();
?>
<!DOCTYPE html>
<html>
<head>
	<title>View SlipMaster</title>
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
<div class="container">
	<div class="row">
	<h2>View SlipMaster</h2>
	<input type="button" class="btn btn-info" value="Menu" onclick="location.href = 'COBMastermenu.php';">
	<input type="button" class="btn btn-info" value="Excel" onclick="location.href = 'COBToExcel.php?mtable=SlipMaster';">
	<input type="button" class="btn btn-info" value="Add Slip" onclick="location.href = 'slipCreate.php';">
	
	<nav aria-label="Page navigation">
		<ul class="pagination">
		<?php if($curpage != $startpage){ ?>
		<li class="page-item">
		  <a class="page-link" href="?page=<?php echo $startpage ?>" tabindex="-1" aria-label="Previous">
			<span aria-hidden="true">&laquo;</span>
			<span class="sr-only">First</span>
		  </a>
		</li>
		<?php } ?>
		<?php if($curpage >= 2){ ?>
		<li class="page-item"><a class="page-link" href="?page=<?php echo $previouspage ?>"><?php echo $previouspage ?></a></li>
		<?php } ?>
		<li class="page-item active"><a class="page-link" href="?page=<?php echo $curpage ?>"><?php echo $curpage ?></a></li>
		<?php if($curpage != $endpage){ ?>
		<li class="page-item"><a class="page-link" href="?page=<?php echo $nextpage ?>"><?php echo $nextpage ?></a></li>
		<li class="page-item">
		  <a class="page-link" href="?page=<?php echo $endpage ?>" aria-label="Next">
			<span aria-hidden="true">&raquo;</span>
			<span class="sr-only">Last</span>
		  </a>
		</li>
		<?php } ?>
	  </ul>
	</nav>


		<table class="table "> 
		<thead> 
			<tr> 
				<th>Slip</th> 
				<th>Type</th> 
				<th>Dock</th> 
				<th>Class</th> 
				<th>Condition</th> 
				<th>Actions</th>
			</tr> 
		</thead> 
		<tbody> 
		<?php 
		while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
		?>
			<tr> 
				<th scope="row"><?php echo $r['slipid']; ?></th> 
				<td><?php echo $r['type']; ?></td> 
				<td><?php echo $r['dock']; ?></td> 
				<td><?php echo $r['class']; ?></td> 
				<td><?php echo $r['scondition']; ?></td> 
				<td>
					<a href="slipUpdate.php?slipid=<?php echo $r['slipid']; ?>"><button type="button" class="btn btn-info btn-xs" >Edit</button></a>
					<button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#myModal<?php echo $r['slipid']; ?>">Delete</button>

					<!-- Modal -->
					  <div class="modal fade" id="myModal<?php echo $r['slipid']; ?>" role="dialog">
					    <div class="modal-dialog">
					    
					      <!-- Modal content-->
					      <div class="modal-content">
					        <div class="modal-header">
					          <button type="button" class="close" data-dismiss="modal">&times;</button>
					          <h4 class="modal-title">Delete File</h4>
					        </div>
					        <div class="modal-body">
					          <p>Are you sure?</p>
					        </div>
					        <div class="modal-footer">
					          <a href="slipDelete.php?slipid=<?php echo $r['slipid']; ?>"><button type="button" class="btn btn-danger">Delete</button></a>
					          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					        </div>
					      </div>
					      
					    </div>
				</td>
			</tr> 
		<?php } ?>
		</tbody> 
		</table>
	</div>
	
	<nav aria-label="Page navigation">
		<ul class="pagination">
		<?php if($curpage != $startpage){ ?>
		<li class="page-item">
		  <a class="page-link" href="?page=<?php echo $startpage ?>" tabindex="-1" aria-label="Previous">
			<span aria-hidden="true">&laquo;</span>
			<span class="sr-only">First</span>
		  </a>
		</li>
		<?php } ?>
		<?php if($curpage >= 2){ ?>
		<li class="page-item"><a class="page-link" href="?page=<?php echo $previouspage ?>"><?php echo $previouspage ?></a></li>
		<?php } ?>
		<li class="page-item active"><a class="page-link" href="?page=<?php echo $curpage ?>"><?php echo $curpage ?></a></li>
		<?php if($curpage != $endpage){ ?>
		<li class="page-item"><a class="page-link" href="?page=<?php echo $nextpage ?>"><?php echo $nextpage ?></a></li>
		<li class="page-item">
		  <a class="page-link" href="?page=<?php echo $endpage ?>" aria-label="Next">
			<span aria-hidden="true">&raquo;</span>
			<span class="sr-only">Last</span>
		  </a>
		</li>
		<?php } ?>
	  </ul>
	</nav>


</div>

</body>
</html>