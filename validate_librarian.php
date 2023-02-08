<?php
	session_start();
	require 'db_connect.php';
	
		$adminName='Ashana Chougle';
		
		$sql = "SELECT * FROM registered_users WHERE Name='$adminName'";

        $result = mysqli_query($con, $sql);

        if (mysqli_num_rows($result) === 1) {

            $row = mysqli_fetch_assoc($result);
			if ($row['Name'] === $adminName) {

                echo "Logged in!";

                $_SESSION['Name'] = $row['Name'];

                $_SESSION['email'] = $row['email'];

                

                header("Location: librarian.php");

                

            }
		}
	
?>