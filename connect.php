<?php
$con = mysqli_connect('localhost', 'root', '','project');
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}

$name = $_POST['name'];
$email = $_POST['email'];
$pass = $_POST['pass'];
$address=$_POST['address'];
$balance=$_POST['balance'];
$check_email = mysqli_query($con, "SELECT user_name FROM registered_users where user_name = '$email' ");
if(mysqli_num_rows($check_email) > 0){
    header("Location: register.html?error=email already exists");
}
else{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
$sql = "INSERT INTO registered_users (user_id, Name, user_name, password, Address,balance) VALUES (0, '$name', '$email', '$pass', '$address','$balance')";
$rs = mysqli_query($con, $sql);
if (mysqli_query($con, $sql)) {
  header("Location: http://localhost/project/register.html");
}
else {
  echo "Error: " . $sql . "<br>" . mysqli_error($con);
}
	}
}

?>