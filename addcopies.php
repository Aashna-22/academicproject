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
		<link rel="stylesheet" href="update_copies_style.css">
	</head>
	<body>
		<form class="cd-form" method="POST" action="#">
			<center><legend>Update Book Copies</legend></center>
			
				<div class="error-message" id="error-message">
					<p id="error"></p>
				</div>
				
				<div class="icon">
					<input class="b-title" type="text" name="b_title" placeholder="Book Title" required />
				</div>
					
				<div class="icon">
					<input class="b-copies" type="number" name="b_copies" placeholder="Copies to add" required />
				</div>
						
				<input type="submit" name="b_add" value="Update Book Copies" />
		</form>
	</body>
	
	<?php
		if(isset($_POST['b_add']))
		{
			$query = $con->prepare("SELECT title FROM books WHERE title = ?;");
			$query->bind_param("s", $_POST['b_title']);
			$query->execute();
			if(mysqli_num_rows($query->get_result()) != 1)
				echo error_with_field("Invalid Title", "b_title");
			else
			{
				$query = $con->prepare("UPDATE books SET copies = copies + ? WHERE title = ?;");
				$query->bind_param("ds", $_POST['b_copies'], $_POST['b_title']);
				if(!$query->execute())
					die(error_without_field("ERROR: Couldn\'t update book copies"));
				echo success("Number of book copies has been updated");
			}
		}
	?>
</html>