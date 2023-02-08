<?php
	require "db_connect.php";
	require "messagedisplay.php";
	require "profile_librarian.php";
?>

<html>
	<head>
		<title>Festival</title>
		<link rel="stylesheet" type="text/css" href="global_styles.css">
		<link rel="stylesheet" type="text/css" href="custom_radio_button_style.css">
		<link rel="stylesheet" type="text/css" href="pending_book_requests_style.css">
	</head>
	<body>
		<?php
			$query = $con->prepare("SELECT * FROM pending_request;");
			$query->execute();
			$result = $query->get_result();;
			$rows = mysqli_num_rows($result);
			if($rows == 0)
				echo "<h2 align='center'>No requests pending</h2>";
			else
			{
				echo "<form class='cd-form' method='POST' action='#'>";
				echo "<center><legend>Pending book requests</legend></center>";
				echo "<div class='error-message' id='error-message'>
						<p id='error'></p>
					</div>";
				echo "<table width='100%' cellpadding=10 cellspacing=10>
						<tr>
							<th></th>
							<th>Username<hr></th>
							<th>Request id<hr></th>
							<th>Time<hr></th>
						</tr>";
				for($i=0; $i<$rows; $i++)
				{
					$row = mysqli_fetch_array($result);
					echo "<tr>
							<td>
								<label class='control control--radio'>
									<input type='radio' name='rd_book' value=".$row[2]." />
								<div class='control__indicator'></div>
							</td>";
					for($j=1; $j<4; $j++)
						echo "<td>".$row[$j]."</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<br /><br /><div style='float: right;'>";
				echo "<input type='submit' value='Reject Request' name='l_reject' />&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<input type='submit' value='Allow' name='l_grant'/>";
				echo "</div>";
				echo "</form>";
			}
			
			
			
			if(isset($_POST['l_grant']))
			{
				
				
					if(empty($_POST['rd_book']))
						echo error_without_field("No request selected!");
					else {	
						
						
						$query = $con->prepare("SELECT member, book_isbn FROM pending_request WHERE request_id = ?;");
						$query->bind_param("d", $_POST['rd_book']);
						$query->execute();
						$resultRow = mysqli_fetch_array($query->get_result());
						$member = $resultRow[0];
						$isbn = $resultRow[1];
						$query = $con->prepare("INSERT INTO issue_book_log(member, book_isbn) VALUES(?, ?);");
						$query->bind_param("ss", $member, $isbn);
						if(!$query->execute())
							die(error_without_field("ERROR: Couldn\'t issue book"));
						else{
							echo success("Successfully issued books");
							$query= $con->prepare("UPDATE issue_book_log SET due_date=(SELECT time from pending_request where pending_request.member=issue_book_log.member)");
							if(!$query->execute())
							die(error_without_field("ERROR: Couldn\'t issue book"));
						else{
							echo success("Successfully updated due date books");
						}
							$query=$con->prepare("DELETE FROM pending_request where request_id = ?");
							$query->bind_param("d",$_POST['rd_book']);
							if(!$query->execute())
							die(error_without_field("ERROR: Couldn\'t delete values"));
						else
							echo success("Successfully deleted requests");
						}
						
						
					}
					
			}
			
			if(isset($_POST['l_reject']))
			{
				
				
					if(isset($_POST['rd_book']))
					{
						
						$query = $con->prepare("DELETE FROM pending_request WHERE request_id = ?");
						$query->bind_param("d", $_POST['rd_book']);
						if(!$query->execute())
							die(error_without_field("ERROR: Couldn\'t delete values"));
						else
							echo success("Successfully deleted requests");
						
						
					}
					else
					echo error_without_field("No request selected");
			}
			?>