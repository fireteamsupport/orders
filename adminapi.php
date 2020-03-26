<?php
class AdminAPI {
	protected $servername     = getenv("ENV_SERVERNAME");
	protected $username       = getenv("ENV_SQLUSERNAME");
	protected $password       = getenv("ENV_SQLPASSWORD");

	protected $orders_db      = getenv("ENV_ORDERSDBNAME");
	protected $orders_table   = getenv("ENV_ORDERSTABLENAME");

	protected $employee_db    = getenv("ENV_EMPLOYEESDBNAME");
	protected $employee_table = getenv("ENV_EMPLOYEESTABLENAME");
	
	protected $orders_connection;
	protected $employees_connection;

	public $error;
	
	public function __construct() {
		$this->orders_connection = new mysqli($this->servername, $this->username, $this->password, $this->orders_db);
		$this->employees_connection = new mysqli($this->servername, $this->username, $this->password, $this->employee_db);
		
		if ($this->orders_connection->connect_error || $this->employees_connection->connect_error) {
			$this->error = "Error occurred connecting to database.";
		}
	}
	
	protected function time_elapsed_string($datetime, $full = false) {
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
	
	protected function status_text($status) {
		switch ($status) {
			case 0:
				return "In queue";
				break;
			case 1:
				return "In progress";
				break;
			case 2:
				return "Complete";
				break;
			default:
				return "Incomplete";
				break;
		}
	}
	
	public function get_unclaimed_orders_table() {
		$query = "SELECT Username, OrderID, Type, Description, ClaimedBy, OrderCreation, Status, AccountName, AccountPassword FROM " . $this->orders_table;
		$result = $this->orders_connection->query($query);
		while ($row = $result->fetch_assoc()) {
			if ($row["ClaimedBy"] == "-1") {
				echo("<tr>");
				echo("<td>" . $row['OrderID'] . "</td>");
				echo("<td>" . $row['Username'] . "</td>");
				echo("<td>" . $row['Type'] . "</td>");
				echo("<td>" . $row['Description'] . "</td>");
				echo("<td>" . $this->time_elapsed_string("@" . $row['OrderCreation'], false) . "</td>");
				echo("<td><label onClick=\"popupConfirm('" . $row['OrderID'] ."')\" class=\"badge badge-gradient-info\">Claim</label></td>");
				echo("</tr>");
			}
		}
	}
	
	public function get_claimed_orders_table($claimed_by) {
		$query = "SELECT Username, OrderID, Type, Description, ClaimedBy, OrderCreation, Status, AccountName, AccountPassword FROM " . $this->orders_table 
		. " WHERE ClaimedBy='" . $claimed_by . "'";
		$result = $this->orders_connection->query($query);
		while ($row = $result->fetch_assoc()) {
			if ($row["ClaimedBy"] == $claimed_by) {
				echo("<tr>");
				echo("<td>" . $row['OrderID'] . "</td>");
				echo("<td>" . $row['Username'] . "</td>");
				echo("<td>" . $row['Type'] . "</td>");
				echo("<td>" . $row['Description'] . "</td>");
				echo("<td>" . $this->time_elapsed_string("@" . $row['OrderCreation'], false) . "</td>");
				echo("<td>" . $this->status_text($row['Status']) . " <label onClick=\"popupRadio('" . $row['OrderID'] ."')\" class=\"badge badge-gradient-info\">Edit</label></td>");
				echo("<td><label onClick=\"popupInfo('" . $row['AccountName'] ."', '" . $row["AccountPassword"] . "')\" class=\"badge badge-gradient-success\">View Info</label></td>");
				echo("</tr>");
			}
		}
	}
	
	public function add_order($username, $type, $description, $claimed_by, $order_creation, $account_name, $account_password) {
		$query = "INSERT INTO `" . $this->orders_table . "`(`Username`, `Type`, `Description`, `ClaimedBy`, `OrderCreation`, `AccountName`, `AccountPassword) VALUES ('" 
			. $username . "', '" . $type . "', '" . $description . "', '" . $claimed_by . "', '" . $order_creation . "', '" . $account_name . "', '" 
			. $account_password . "')";
		if ($this->orders_connection->query($query) === TRUE) {
			return "";
		} else {
			return "Error adding order.";
		}
	}
	
	public function claim_order($claimant, $order_id) {
		$query = "UPDATE " . $this->orders_table . " SET ClaimedBy='" . $claimant . "' WHERE OrderID='" . $order_id . "'";
		if ($this->orders_connection->query($query) === TRUE) {
			return "";
		} else {
			return "Error claiming order.";
		}
	}
	
	public function set_order_status($order_id, $status) {
		$query = "UPDATE " . $this->orders_table . " SET Status='" . $status ."' WHERE OrderID='" . $order_id . "'";
		if ($this->orders_connection->query($query) === TRUE) {
			return "";
		} else {
			return "Error setting order statuus.";
		}
	}
	
	public function get_employee_information($username) {
		$query = "SELECT UID, Password, Rank FROM " . $this->employee_table . " WHERE Username='" . $username . "'";
		if ($this->employees_connection->query($query) === TRUE) {
			return "";
		} else {
			return "Error getting employee information.";
		}
	}
	
	public function remove_employee($username) {
		$query = "DELETE FROM " . $this->employee_table . " WHERE Username='" . $username . "'";
		if ($this->employees_connection->query($query) === TRUE) {
			return "";
		} else {
			return "Error removing employee.";
		}
	}
	
	public function add_employee($username, $password, $email, $role) {
		$query = "INSERT INTO `" . $this->employee_table . "`(`Username`, `Password`, `Email`, `Role`) VALUES ('" . $username . "', '" . $password . "', '" . $email . "', '" . $role . "')";
		if ($this->employees_connection->query($query) === TRUE) {
			return "";
		} else {
			return "Error adding employee.";
		}
	}
	
	public function set_employee_role($username, $role) {
		$query = "UPDATE " . $this->employee_table . " SET Role='" . $role . "' WHERE Username='" . $username . "'";
		if ($this->employees_connection->query($query) === TRUE) {
			return "";
		} else {
			return "Error setting employee role.";
		}
	}
	
	public function claim_process($claimant, $order_id) {
		if ($this->claim_order($claimant, $order_id) == "") {
			$_SESSION["Claimed"] = "true";
			$_SESSION["Failed"] = "false";
			echo("<script>
				window.location.replace(`http://home.arturonet.com:8080/apanel/index.php`)
			</script>");
		} else {
			$_SESSION["Claimed"] = "true";
			$_SESSION["Failed"] = "true";
			echo("<script>
				window.location.replace(`http://home.arturonet.com:8080/apanel/index.php`)
			</script>");
		}
	}
	
	public function claim_notification() {
		if ($_SESSION["Claimed"] == "true") {
			$_SESSION["Claimed"] = false;
			if ($_SESSION["Failed"] == "true") {
				$_SESSION["Failed"] = false;
				echo("<script>
					Swal.fire({
					icon: 'error',
					title: 'Failed to Claim Order',
					text: 'Contact a developer if you see this.'
					})</script>");
			} else {
				echo("<script>
					Swal.fire(
					'Order Claimed',
					'The selected order has been claimed!',
					'success'
				)</script>");
			}
		}
	}
	
	public function edit_process($status, $order_id) {
		if ($this->set_order_status($order_id, $status) == "") {
			$_SESSION["Edit"] = "true";
			$_SESSION["Failed"] = "false";
			echo("<script>
				window.location.replace(`http://home.arturonet.com:8080/apanel/orders.php`)
			</script>");
		} else {
			$_SESSION["Edit"] = "true";
			$_SESSION["Failed"] = "true";
			echo("<script>
				window.location.replace(`http://home.arturonet.com:8080/apanel/orders.php`)
			</script>");
		}
	}
	
	public function edit_notification() {
		if ($_SESSION["Edit"] == "true") {
			$_SESSION["Edit"] = false;
			if ($_SESSION["Failed"] == "true") {
				$_SESSION["Failed"] = false;
				echo("<script>
					Swal.fire({
					icon: 'error',
					title: 'Failed to Edit Order Status',
					text: 'Contact a developer if you see this.'
					})</script>");
			} else {
				echo("<script>
					Swal.fire(
					'Order Status Edited',
					'The selected order status has been edited.',
					'success'
				)</script>");
			}
		}
	}
}
?>