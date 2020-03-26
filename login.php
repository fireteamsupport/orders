<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Purple Admin</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css">
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
    <div class="container-scroller">
      <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth">
          <div class="row flex-grow">
            <div class="col-lg-4 mx-auto">
              <div class="auth-form-light text-left p-5">
                <form class="pt-3" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                  <div class="form-group">
                    <input type="username" class="form-control form-control-lg" name="username" placeholder="Username">
                  </div>
                  <div class="form-group">
                    <input type="password" class="form-control form-control-lg" name="password" placeholder="Password">
                  </div>
				  <!--
                  <div class="mt-3">
                    <a onclick="form.submit();" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">SIGN IN</a>
                  </div
				  -->
				  <input class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn" type="submit" value="SIGN IN" name="submitbtn">
				  <?php
				  session_start();
				  
				  if (isset($_REQUEST['submitbtn'])) {
					  $error = "";

				  $servername = "localhost";
				  $username = "root";
				  $password = "'YTFBf5}gK\"BmNR)";
				  $dbname = "apanel";

				  $conn = new mysqli($servername, $username, $password, $dbname);

				  if ($conn->connect_error) {
				  	$error = "Something went wrong! You should wait a minute and begin this process from the start again. If there is still an issue after this, contact Blankets on discord. ER1";
				  }

				  if (empty($_POST['username']) || empty($_POST['password'])) {
				  	$error = "Be sure to fill in both the username and password boxes.";
				  } else {
					$sql = "SELECT id, password, rank FROM accounts WHERE username ='" . $_POST['username'] . "'";
					$result = $conn->query($sql);

					if ($result->num_rows > 0) {
						while($row = $result->fetch_assoc()) {
							if(strcasecmp($row["password"], hash('sha512', $_POST['password'])) == 0) {
								$_SESSION['authd'] = TRUE;
								$_SESSION['name'] = $_POST['username'];
								$_SESSION['id'] = $row["id"];
								$_SESSION['rank'] = $row['rank'];
								header("Location: /apanel/index.php");
							}
						}
					} else {
				  	  $error = "Invalid login details.";
					} 
				  }

				 
				  
				  if (!empty($error)) {
						echo("<div class=\"text-center mt-4 font-weight-light\">" . $error ."</div>");
				  }
				  } 
				  
				  
				  ?>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- content-wrapper ends -->
      </div>
      <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="assets/vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="assets/js/off-canvas.js"></script>
    <script src="assets/js/hoverable-collapse.js"></script>
    <script src="assets/js/misc.js"></script>
    <!-- endinject -->
  </body>
</html>