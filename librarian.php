<?php 
 require 'profile_librarian.php';
 
?>

<html>
	<head>
		<title>Festival</title>
		<link rel="stylesheet" type="text/css" href="librarian_style.css" />
	</head>
	<body>
		<div id="allTheThings">
			
			<a href="InsertBook.php">
				<input type="button" value="Insert New Book Record" />
			</a><br />

			<a href="addcopies.php">
				<input type="button" value="Update Copies of a Book" />
			</a><br />

			<a href="deleteBooks.php">
				<input type="button" value="Delete Book Records" />
			</a><br />

			<a href="availablebooks.php">
				<input type="button" value="Display Available Books" />
			</a><br />

			<a href="accept_book_request.php">
				<input type="button" value="Manage Pending Book Requests" />
			</a><br />

			<a href="memberbalance.php">
				<input type="button" value="Update Member balance Records" />
			</a><br />
			<a href="due_handle.php">
				<input type="button" value="Today's Reminder" />
			</a><br /><br />

		</div>
	</body>
</html>