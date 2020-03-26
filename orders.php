<!DOCTYPE html>
<html lang="en">
<?php
/*
if (!isset($_SESSION['authd'])) {
	header('Location: login.php');
	exit();
}*/
?>
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Destiny Arena Admin Panel</title>
    <!-- plugins:css -->
	<link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.15.5/dist/bootstrap-table.min.css">
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
	<link rel="stylesheet" href="/bootstrap-table/node_modules/bootstrap-table/dist/bootstrap-table.min.css" />

    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="assets/images/favicon.png" />
	
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
  <script src="popups.js"></script>
  </head>
  <body>
<?php
if ($_SESSION["actiondoneban"] == "true") {
	$_SESSION["actiondoneban"] = "false";
	if($_SESSION["failed"] == "true") {
		$_SESSION["failed"] = "false";
		echo("<script>
			Swal.fire({
			icon: 'error',
			title: 'Ban Failed',
			text: 'Contact Blankets if you see this.'
		})</script>");
	} else {
		echo("<script>
			Swal.fire(
			'Player Banned',
			'The selected player has been banned.',
			'success'
		)</script>");
	}
} elseif ($_SESSION["actiondoneunban"] == "true") {
	$_SESSION["actiondoneunban"] = "false";
	if($_SESSION["failed"] == "true") {
		$_SESSION["failed"] = "false";
		echo("<script>
			Swal.fire({
			icon: 'error',
			title: 'Unban Failed',
			text: 'Contact Blankets if you see this.'
		})</script>");
	} else {
		echo("<script>
			Swal.fire(
			'Player Unbanned',
			'The selected player has been unbanned.',
			'success'
		)</script>");
	}
} elseif ($_SESSION["actiondoneremove"] == "true") {
	$_SESSION["actiondoneremove"] = "false";
	if($_SESSION["failed"] == "true") {
		$_SESSION["failed"] = "false";
		echo("<script>
			Swal.fire({
			icon: 'error',
			title: 'Removal Failed',
			text: 'Contact Blankets if you see this.'
		})</script>");
	} else {
		echo("<script>
			Swal.fire(
			'Player Removed',
			'The selected player has been removed from the database.',
			'success'
		)</script>");
	}
}
?>
   <script src="/bootstrap-table/node_modules/bootstrap-table/dist/bootstrap-table.min.js"></script>
    <div class="container-scroller">
      <!-- partial:partials/_navbar.html -->
     
      <!-- partial -->
      <div class="container-fluid page-body-wrapper">
        <!-- partial:partials/_sidebar.html -->
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
          <ul class="nav">
            <li class="nav-item nav-profile">
              <a href="#" class="nav-link">
                <div class="nav-profile-text d-flex flex-column">
                  <span class="font-weight-bold mb-2"><?php echo($_SESSION['name'])?></span>
                  <span class="text-secondary text-small">
				  <?php 
				  switch ($_SESSION['rank']) {
						case 0:
							echo("Admin");
							break;
						case 1:
							echo("Manager");
							break;
						case 2:
							echo("Developer");
							break;
						case 3:
							echo("Owner");
							break;
						default:
							echo("IF U SEE THIS TELL BLANKETS");
							break;
				  }
				  ?>
				  </span>
                </div>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="index.php">
                <span class="menu-title">Order List</span>
                <i class="mdi mdi-format-list-bulleted menu-icon"></i>
              </a>
            </li>
			<?php
			if($_SESSION['rank'] == 2) {
				echo("<li class=\"nav-item\">
              <a class=\"nav-link\" data-toggle=\"collapse\" href=\"#ui-basic\" aria-expanded=\"false\" aria-controls=\"ui-basic\">
                <span class=\"menu-title\">Multiaccount Detection</span>
                <i class=\"menu-arrow\"></i>
                <i class=\"mdi mdi-crosshairs-gps menu-icon\"></i>
              </a>
              <div class=\"collapse\" id=\"ui-basic\">
                <ul class=\"nav flex-column sub-menu\">
                  <li class=\"nav-item\"> <a class=\"nav-link\" href=\"hubchecker.php\">HUB Checker</a></li>
                  <li class=\"nav-item\"> <a class=\"nav-link\" href=\"gamehistorychecker.php\">Game History Checker</a></li>
                </ul>
              </div>
			</li>");
			}
			?>
		  <li class="nav-item active">
              <a class="nav-link" href="orders.php">
                <span class="menu-title">Your Claimed Orders</span>
                <i class="mdi mdi-format-list-bulleted menu-icon"></i>
              </a>
		   </li>
          </ul>
        </nav>
        <!-- partial -->
        <div class="main-panel">
          <div class="content-wrapper">
            <div class="page-header">
              <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white mr-2">
                  <i class="mdi mdi-home"></i>
                </span> Fireteam Support Staff Panel </h3>
            </div>
            <div class="row">
              <div class="col-12 grid-margin">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Your Claimed Orders</h4>
                    <div class="table-striped">
                      <table class="table" id="table" data-toggle="table" data-pagination="true" data-height="600" data-search="true">
                        <thead>
                          <tr>
                            <th> ID </th>
                            <th> Username </th>
                            <th> Type </th>
                            <th> Description </th>
                            <th> Order Time </th>
                            <th> Status </th>
							<th> Account Info </th>
                          </tr>
                        </thead>
                        <tbody>
							<?php
							session_start();
							
							include('adminapi.php');
							
							$admin = new AdminAPI();
							
							$admin->edit_notification();
							
							$admin->get_claimed_orders_table(0);
							
							if (!empty($_GET["orderid"]) && !empty($_GET["status"])) {
								$admin->edit_process($_GET["status"], $_GET["orderid"]);
							}
							
							?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
			
          <!-- content-wrapper ends -->
          <!-- partial:partials/_footer.html -->
          <footer class="footer">
            
          </footer>
          <!-- partial -->
        </div>
        <!-- main-panel ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="assets/vendors/chart.js/Chart.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="assets/js/dashboard.js"></script>
    <script src="assets/js/todolist.js"></script>
    <!-- End custom js for this page -->
	
	<script src="https://unpkg.com/bootstrap-table@1.15.5/dist/bootstrap-table.min.js"></script>
	
  </body>
</html>