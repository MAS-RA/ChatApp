<?php
session_start();
include('database_connection.php');

$message = '';

if (isset($_SESSION['user_id'])) {
	header('location:index.php');
}

if (isset($_POST['login'])) {
	$query = "
		SELECT * FROM login 
  		WHERE username = :username
	";
	$statement = $connect->prepare($query);
	$statement->execute(
		array(
			':username' => $_POST["username"]
		)
	);
	$count = $statement->rowCount();
	if ($count > 0) {
		$result = $statement->fetchAll();
		foreach ($result as $row) {
			if (password_verify($_POST["password"], $row["password"])) {
				$_SESSION['user_id'] = $row['user_id'];
				$_SESSION['username'] = $row['username'];
				$sub_query = "
				INSERT INTO login_details 
	     		(user_id) 
	     		VALUES ('" . $row['user_id'] . "')
				";
				$statement = $connect->prepare($sub_query);
				$statement->execute();
				$_SESSION['login_details_id'] = $connect->lastInsertId();
				header('location:index.php');
			} else {
				$message = '<label>Wrong Password</label>';
			}
		}
	} else {
		$message = '<label>Wrong Username</labe>';
	}
}


?>

<html>
<?php
include 'header.php';
?>

<body>
	<div class="container">
		<br />

		<h3 align="center">Chat Application | login</h3><br />
		<br />
		<div class="panel panel-default">
			<div class="panel-heading">Chat Application Login</div>
			<div class="panel-body">
				<p class="text-danger"><?php echo $message; ?></p>
				<form method="post">
					<div class="form-group">
						<label>Enter Username</label>
						<input type="text" name="username" class="form-control" required />
					</div>
					<div class="form-group">
						<label>Enter Password</label>
						<input type="password" name="password" class="form-control" required />
					</div>
					<div class="form-group">
						<input type="submit" name="login" class="btn btn-info" value="Login" />
					</div>
					<div align="center">
						<a href="register.php">Register</a>
					</div>
				</form>
				<br />
				<br />
				<br />
				<br />
			</div>
		</div>
	</div>

</body>

</html>