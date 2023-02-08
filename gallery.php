<?php
			$con = mysqli_connect('localhost', 'root', '','project');
			if (!$con) {
				die("Connection failed: " . mysqli_connect_error());
			}
			require 'profile_member.php';
?>
<?php 
function error_without_field($message)
	{
		return '<script>
					document.getElementById("error").innerHTML = "'.$message.'";
					document.getElementById("error-message").style.display = "block";
				</script>';
	}
	
	function error_with_field($message, $field)
	{
		return '<script>
					document.getElementById("error").innerHTML = "'.$message.'";
					document.getElementById("error-message").style.display = "block";
					document.getElementById("'.$field.'").className += " error-field";
				</script>';
	}
	
	function success($message)
	{
		return '<script>
					document.getElementById("error").innerHTML = "'.$message.'";
					document.getElementById("error-message").className = "success-message";
				</script>';
	}
?>
<html>
	<head>
		<title>Festival</title>
		<link rel="stylesheet" type="text/css" href="global_styles.css">
		<link rel="stylesheet" type="text/css" href="home_style.css">
		<link rel="stylesheet" type="text/css" href="custom_radio_button_style.css">
		<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<style>
		.dropdown {
			  position: relative;
			  display: inline-block;
			  float: right;
			  margin-right: 5%;
		}

		.dropdown-content {
			  display: none;
			  right: 0;
			  position: absolute;
			  background-color: #f9f9f9;
			  min-width: 160px;
			  font-size: 1.6rem;
			  box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
		}

		.dropdown-content a {
			  color: black;
			  padding: 12px 16px;
			  text-decoration: none;
			  display: block;
		}

		.dropdown-content a:hover {
			background-color: #f1f1f1;
		}

		.dropdown:hover .dropdown-content {
			display: block;
		}

		.dropdown:hover .dropbtn {
			background-color: #d34060;
		}
		</style>
	</head>
	<body>
				
	<?php
			
			
			$query = $con->prepare("SELECT * FROM books ORDER BY title");
			$query->execute();
			$result = $query->get_result();
			if(!$result)
				die("ERROR: Couldn't fetch books");
			$rows = mysqli_num_rows($result);
			if($rows == 0)
				echo "<h2 align='center'>No books available</h2>";
			else
			{
				echo "<form class='cd-form' method='POST' action='#'>";
				echo "<center><legend>List of Available Books</legend></center>";
				echo "<div class='error-message' id='error-message'>
						<p id='error'></p>
					</div>";
				echo "<center><input type='text' name='your_name' id='your_name' placeholder='Please enter your name'/></center>";
                      
				echo "<table width='100%' cellpadding=10 cellspacing=10>";
				echo "<tr>
						<th></th>
						<th>ISBN<hr></th>
						<th>Author<hr></th>
						<th>Category<hr></th>
						<th>Copies<hr></th>
						<th>Price<hr></th>
						<th>Title<hr></th>
					</tr>";
				for($i=0; $i<$rows; $i++)
				{
					$row = mysqli_fetch_array($result);
					echo "<tr>
							<td>
								<label class='control control--radio'>
									<input type='radio' name='rd_book' value=".$row[0]." />
								<div class='control__indicator'></div>
							</td>";
					for($j=0; $j<6; $j++)
						if($j == 4)
							echo "<td>Rs.".$row[$j]."</td>";
						else
							echo "<td>".$row[$j]."</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<br /><br /><input type='submit' name='m_request' value='Request Book' />";
				echo "</form>";
			}
			
			if(isset($_POST['m_request']))
			{
				if(empty($_POST['rd_book']))
					echo error_without_field("Please select a book to issue");
				else
				{
					$query = $con->prepare("SELECT copies FROM books WHERE isbn = ?;");
					$query->bind_param("s", $_POST['rd_book']);
					$query->execute();
					$copies = mysqli_fetch_array($query->get_result())[0];
					if($copies == 0)
						echo error_without_field("No copies of the selected book are available");
					else
					{
						$query = $con->prepare("SELECT request_id FROM pending_request WHERE member = ?;");
						$query->bind_param("s", $_POST['your_name']);
						$query->execute();
						if(mysqli_num_rows($query->get_result()) == 1)
							echo error_without_field("You can only request one book at a time");
						else
						{
							$query = $con->prepare("SELECT book_isbn FROM issue_book_log WHERE member = ?;");
							$query->bind_param("s", $_POST['your_name']);
							$query->execute();
							$result = $query->get_result();
							if(mysqli_num_rows($result) >= 3)
								echo error_without_field("You cannot issue more than 3 books at a time");
							else
							{
								$rows = mysqli_num_rows($result);
								for($i=0; $i<$rows; $i++)
									if(strcmp(mysqli_fetch_array($result)[0], $_POST['rd_book']) == 0)
										break;
								if($i < $rows)
									echo error_without_field("You have already issued a copy of this book");
								else
								{
									$query = $con->prepare("SELECT balance FROM registered_users WHERE Name = ?;");
									$query->bind_param("s", $_POST['your_name']);
									$query->execute();
									$memberBalance = mysqli_fetch_array($query->get_result())[0];
									
									$query = $con->prepare("SELECT price FROM books WHERE isbn = ?;");
									$query->bind_param("s", $_POST['rd_book']);
									$query->execute();
									$bookPrice = mysqli_fetch_array($query->get_result())[0];
									if($memberBalance < $bookPrice)
										echo error_without_field("You do not have sufficient balance to issue this book");
									else
									{
										$query = $con->prepare("UPDATE books SET copies = copies - 1 WHERE isbn = ?;");
										$query-> bind_param("s",$_POST['rd_book']);
										if(!$query->execute())
											echo error_without_field("ERROR: Couldn't update copies");
										else
											echo success("copies Updated");
										$query = $con->prepare("UPDATE registered_users SET balance = balance - ? WHERE Name = ?;");
										$query-> bind_param("ds",$bookPrice,$_POST['your_name']);
										if(!$query->execute())
											echo error_without_field("ERROR: Couldn't update balance");
										else
											echo success("Balance Updated");
										$query = $con->prepare("INSERT INTO pending_request(member, book_isbn) VALUES(?, ?);");
										$query->bind_param("ss", $_POST['your_name'], $_POST['rd_book']);
										if(!$query->execute())
											echo error_without_field("ERROR: Couldn\'t request book");
										else
											echo success("Selected book has been requested. Soon you'll' be notified when the book is issued to your account!");
									}
								}
							}
						}
					}
				}
			}
		?>
		</body>
</html>