<?php 

$con = mysqli_connect('localhost', 'root', '','project');
if (!$con) {
  die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['your_name']) && isset($_POST['your_pass'])) {

    function validate($data){

       $data = trim($data);

       $data = stripslashes($data);

       $data = htmlspecialchars($data);

       return $data;

    }

    $uname = validate($_POST['your_name']);

    $pass = validate($_POST['your_pass']);
	$adminName="Ashana Chougle";
	$adminPass="Aashu";

    if (empty($uname)) {

        header("Location: register.html?error=User Name is required");

        exit();

    }else if(empty($pass)){

        header("Location: register.html?error=Password is required");

        exit();

    }else{

        $sql = "SELECT * FROM registered_users WHERE Name='$uname' AND password='$pass'";

        $result = mysqli_query($con, $sql);

        if (mysqli_num_rows($result) === 1) {

            $row = mysqli_fetch_assoc($result);
			if ($row['Name'] === $adminName && $row['password'] === $adminPass) {

                echo "Logged in!";

                $_SESSION['Name'] = $row['Name'];

                $_SESSION['email'] = $row['email'];

                

                header("Location: librarian.php");

                exit();

            }

            else if ($row['Name'] === $uname && $row['password'] === $pass) {

                echo "Logged in!";

                $_SESSION['Name'] = $row['Name'];

                $_SESSION['email'] = $row['email'];

                

                header("Location: welcome.html");

                exit();

            }else{

                header("Location: register.html?error=Incorect User name or password");

                exit();

            }

        }else{

            header("Location: register.html?error=Incorect User name or password");

            exit();

        }

    }

}else{

    header("Location: index.php");

    exit();

}
?>