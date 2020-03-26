<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "'YTFBf5}gK\"BmNR)";
$dbname = "apanel";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
	die("Something went wrong! You should wait a minute and begin this process from the start again. If there is still an issue after this, contact Blankets on discord. ER1");
}

if (!isset($_POST['username'], $_POST['password']) ) {
	$error = "Be sure to fill in both the username and password boxes";
}

$sql = "SELECT id, password FROM accounts WHERE username ='" . $_POST['username'] . "'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
	while($row = $result->fetch_assoc()) {
		if($row["password"] == hash('sha512', $_POST['password'])) {
			$_SESSION['authd'] = TRUE;
			$_SESSION['name'] = $_POST['username'];
			$_SESSION['id'] = $row["id"];
		}
	}
} else {
	$error = "Invalid login details.";
}
?>