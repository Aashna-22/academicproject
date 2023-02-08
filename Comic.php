<?php
	require 'profile_member.php';
?>
<html>
	<head>
		<title>LMS</title>
		<link rel="stylesheet" type="text/css" href="global_styles.css" />
		<link rel="stylesheet" type="text/css" href="form_styles.css" />
		<link rel="stylesheet" href="Insert_book_styles.css">
	</head>
	<script>
	function openUrl(){
		window.open("http://www.freepdfbook.com/x-men-god-loves-man-kills/","_blank");
	}
	</script>
	<body>
		<form class="cd-form" method="POST" action="#">
			<center><legend>Rent X-Men: God Loves, Man Kills</legend></center>
			
				<div class="error-message" id="error-message">
					<p id="error"></p>
				</div>
				<div class="icon">
					<input class="b-title" type="text" name="b_title" placeholder="Book Title" required />
				</div>
				
				<div class="icon">

					<input class="b-author" type="text" name="b_author" placeholder="Email" required />
				</div>
			
				<input class="b-isbn" type="submit" name="b_add" value="Rent book" />
				
		</form>
	<body>
	<?php
		require 'messagedisplay.php';
		require 'db_connect.php';
		
		
		if(isset($_POST['b_add']))
		{
			$query = $con->prepare("SELECT price FROM books WHERE title = ?;");
			$query->bind_param("s", $_POST['b_title']);
			$query->execute();
			$bookPrice = mysqli_fetch_array($query->get_result())[0];
			
			$query = $con->prepare("SELECT balance FROM registered_users WHERE user_name = ?;");
			$query->bind_param("s", $_POST['b_author']);
			$query->execute();
			$memberBalance = mysqli_fetch_array($query->get_result())[0];
			if($memberBalance < $bookPrice)
				echo error_without_field("You do not have sufficient balance to rent this book");
			else
			{
				echo "<button type='submit' style='font-size:14px; position:relative; left:335px; color: #ffffff; background: #343642; padding: 16px 20px; font-weight: bold;' class='b-isbn' onclick='openUrl()' name='open'>Open Pdf</button>";
						
			}
		}
	?>