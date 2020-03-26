<!DOCTYPE html>
<html lang="en">
<?php
session_start();
if (!isset($_SESSION['authd'])) {
	header('Location: login.php');
	exit();
}
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
  </head>
  <body>
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
                <span class="menu-title">User List</span>
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
		  <li class="nav-item">
              <a class="nav-link" href="logs.php">
                <span class="menu-title">Logs</span>
                <i class="mdi mdi-contacts menu-icon"></i>
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
                  <i class="mdi mdi-contacts"></i>
                </span> Logs </h3>
            </div>
            <div class="row">
              <div class="col-12 grid-margin">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">All Previous Staff Actions</h4>
                    <div class="table-striped">
                      <table class="table" id="table" data-toggle="table" data-pagination="true" data-height="600" data-search="true">
                        <thead>
                          <tr>
                            <th> Action </th>
							<th> Description </th>
                            <th> Action By </th>
                            <th> Action On </th>
							<th> Time Since </th>
                          </tr>
                        </thead>
                        <tbody>
							<?php
							
							function getActionFlavourText($actionID) {
								switch($actionID) {
									case 0:
										return "<p class=\"text-info\">EDIT</p>";
										break;
									case 1:
										return "<p class=\"text-danger\">BAN</p>";
										break;
									case 2:
										return "<p class=\"text-success\">UNBAN</p>";
										break;
									default:
										return "<p class=\"text-primary\">ERROR TELL BLANKETS IF U SEE THIS</p>";
										break;
								}
							}
							
							function time_elapsed_string($datetime, $full = false) {
								$now = new DateTime;
 							    $ago = new DateTime($datetime);
    							$diff = $now->diff($ago);
								
    							$diff->w = floor($diff->d / 7);
    							$diff->d -= $diff->w * 7;
								
								$string = array(
									'y' => 'year',
    								'm' => 'month',
    								'w' => 'week',
    								'd' => 'day',
    								'h' => 'hour',
    								'i' => 'minute',
     								's' => 'second',
    							);
    							foreach ($string as $k => &$v) {
     								if ($diff->$k) {
     									$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
									} else {
										unset($string[$k]);
									}
    							}

								if (!$full) $string = array_slice($string, 0, 1);
								
								return $string ? implode(', ', $string) . ' ago' : 'just now';
							}
							
							$servername = "localhost";
							$username = "root";
							$password = "'YTFBf5}gK\"BmNR)";
							$dbname = "apanel";

							$conn = new mysqli($servername, $username, $password, $dbname);

							if ($conn->connect_error) {
								echo("
								<tr>
									<td>FAILED TO CONNECT TO DB</td>
									<td>TELL BLANKETS ABOUT THIS</td>
								</tr>
								");
							} else {
								$sql = "SELECT actionid, actiondetailed, actionby, actionon, timestamp FROM logs ORDER BY timestamp DESC";
								$result = $conn->query($sql);
								while($row = $result->fetch_assoc()) {
									echo("<tr>");
									echo("<td>" . getActionFlavourText($row['actionid']) . "</td>");
									echo("<td>" . $row['actiondetailed'] . "</td>");
									echo("<td>" . $row['actionby'] . "</td>");
									echo("<td>" . $row['actionon'] . "</td>");
									$timeelapsed = time_elapsed_string("@" . $row['timestamp'], true);
									
									echo("<td>" . $timeelapsed . "</td>");
									echo("</tr>");
								}
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