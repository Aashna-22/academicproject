<?php
	require "db_connect.php";
	require "messagedisplay.php";
	require "profile_librarian.php";
?>

<html>
	<head>
		<title>Festival</title>
		<link rel="stylesheet" type="text/css" href="global_styles.css" />
		<link rel="stylesheet" type="text/css" href="form_styles.css" />
		<link rel="stylesheet" href="update_balance_style.css">
	</head>
	<body>
		<form class="cd-form" method="POST" action="#">
			<center><legend>Update Member's Total Balance</legend></center>
			
				<div class="error-message" id="error-message">
					<p id="error"></p>
				</div>
				
				<div class="icon">
					<input class="m-user" type='text' name='m_user' id="m_user" placeholder="Please Enter Member's Name" required />
				</div>
				
				<div class="icon">
					<input class="m-balance" type="number" name="m_balance" placeholder="Balance to add"  />
				</div>
				
				<input type="submit" name="m_add" value="Update Balance" />
				<input type="submit"  name="m_check" value="Check Balance" />
		</form>
	</body>
	
	<?php
		if(isset($_POST['m_add']))
		{
			$query = $con->prepare("SELECT Name FROM registered_users WHERE Name = ?;");
			$query->bind_param("s", $_POST['m_user']);
			$query->execute();
			if(mysqli_num_rows($query->get_result()) != 1)
				echo error_with_field("Invalid username", "m_user");
			else
			{
				$query = $con->prepare("UPDATE registered_users SET balance = balance + ? WHERE Name = ?;");
				$query->bind_param("ds", $_POST['m_balance'], $_POST['m_user']);
				if(!$query->execute())
					die(error_without_field("ERROR: Couldn\'t add balance"));
				echo success("Balance successfully updated");
			}
		}
		else if(isset($_POST['m_check'])) 
		{
			$query = $con->prepare("SELECT Name FROM registered_users WHERE Name = ?;");
			$query->bind_param("s", $_POST['m_user']);
			$query->execute();
			if(mysqli_num_rows($query->get_result()) != 1)
				echo error_with_field("Invalid username", "m_user");
			else
			{
				$query = $con->prepare("SELECT balance FROM registered_users WHERE Name = ?;");
				$query->bind_param("s", $_POST['m_user']);
				$query->execute();
				$balance = (int)$query->get_result()->fetch_array()[0];
				echo success("Balance: Rs.".$balance);
			}
		}
	?>
</html>