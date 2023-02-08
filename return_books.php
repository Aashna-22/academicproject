<?php
	require 'db_connect.php';
	require 'messagedisplay.php';
	require 'profile_member.php';
	
?>

<html>
	<head>
		<title>Festival</title>
		<link rel="stylesheet" type="text/css" href="global_styles.css">
		<link rel="stylesheet" type="text/css" href="custom_radio_button_style.css">
		<link rel="stylesheet" type="text/css" href="my_books_style.css">
		<link rel="stylesheet" href="Insert_book_styles.css">
		<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	</head>
	<body>
		<?php
		echo "<form class='cd-form' method='POST' action='#'>";
			echo "<div class='icon'>
					<center><input class='b-title' type='text' name='your_name' id='your_name' placeholder='Please Enter your name' /></center><br>
				</div>";
			echo "<button type='submit' style='font-size:14px; position:relative; left:455px ; color: #ffffff; background: #343642; padding: 14px 16px; font-weight: bold;' class='b-isbn'  name='open'>Enter</button>";
			echo "</form>";
			$query = $con->prepare("SELECT book_isbn FROM issue_book_log WHERE member = ?;");
			$query->bind_param("s", $_POST['your_name']);
			$query->execute();
			$result = $query->get_result();
			$rows = mysqli_num_rows($result);
			if($rows == 0)
				echo "<h2 align='center'>There are no issued books yet!</h2>";
			else
			{
				echo "<form class='cd-form' method='POST' action='#'>";
				echo "<center><legend>My Books</legend></center>";
				echo "<div class='success-message' id='success-message'>
						<p id='success'></p>
					</div>";
				echo "<div class='error-message' id='error-message'>
						<p id='error'></p>
					</div>";
				echo "<table width='100%' cellpadding='10' cellspacing='10'>
						<tr>
							<th></th>
							<th>ISBN</th>
							<th>Title</th>
							<th>Author</th>
							<th>Category</th>
							<th>Due Date</th>
						</tr>";
				for($i=0; $i<$rows; $i++)
				{
					$isbn = mysqli_fetch_array($result)[0];
					if($isbn != NULL)
					{
						$query = $con->prepare("SELECT title, author, category FROM books WHERE isbn=? ;");
						$query->bind_param("s",$isbn);
						$query->execute();
						$innerRow = mysqli_fetch_array($query->get_result());
						echo "<tr>
								<td>
									<label class='control control--radio'>
									<input type='radio' name='rd_book' value=".$isbn." />
								<div class='control__indicator'></div>
								</td>";
						echo "<td>".$isbn."</td>";
						for($j=0; $j<3; $j++)
							echo "<td>".$innerRow[$j]."</td>";
						$query = $con->prepare("SELECT due_date FROM issue_book_log WHERE member= ? AND book_isbn= ?;");
						$query->bind_param("ss", $_POST['your_name'], $isbn);
						$query->execute();
						echo "<td>".mysqli_fetch_array($query->get_result())[0]."</td>";
						echo "</tr>";
					}
				}
				echo "</table><br />";
				echo "<input type='submit' name='b_return' value='Return Selected Books' />";
				echo "</form>";
			}
			if(isset($_POST['b_return']))
			{
				if(empty($_POST['rd_book']))
					echo error_without_field("Please select a book to return");
				else
				{
					$query = $con->prepare("SELECT due_date FROM issue_book_log WHERE  book_isbn = ?;");
					$query->bind_param("s", $_POST['rd_book']);
					$query->execute();
					$due_date = mysqli_fetch_array($query->get_result())[0];
					echo "Successfully returned book at date: ".$due_date;
					echo "<br />";
					$query = $con->prepare("SELECT DATEDIFF(CURRENT_DATE, ?);");
					$query->bind_param("s", $due_date);
					$query->execute();
					$days = (int)mysqli_fetch_array($query->get_result())[0];
					echo "due date difference: ".$days;
					echo "<br />";
					$query = $con->prepare("DELETE FROM issue_book_log WHERE book_isbn = ?;");
					$query->bind_param("s", $_POST['rd_book']);
					if(!$query->execute())
							die(error_without_field("ERROR: Couldn\'t return the books"));
					
					if($days >= 0)
						{
							$penalty = (int)5+$days;
							$query = $con->prepare("SELECT price FROM books WHERE isbn = ?;");
							$query->bind_param("s", $_POST['rd_book']);
							$query->execute();
							$price = mysqli_fetch_array($query->get_result())[0];
							if($price < $penalty)
								$penalty = $price;
							echo "penalty ".$penalty." Rs. charged for keeping book after due date";
							$query = $con->prepare("UPDATE registered_users SET balance = balance - ? WHERE Name = ?;");
							$query->bind_param("ds",$penalty,$_POST['your_name']);
							if(!$query->execute())
								echo error_without_field("ERROR: Couldn't update balance");
							else
								echo success("Balance Updated");
							echo error_without_field("A penalty of Rs".$penalty."was charged on book".$_POST['rd_book']."for keeping books  ".$days."days after the due date");
							echo success("sucessfully returned books");
							
						}
					
				}
			}

			
		?>
	
	</body>
</html>