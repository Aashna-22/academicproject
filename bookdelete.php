<?php

session_start();

require "db_connect.php";

if(!isset($_SESSION['isbn'])){
    header('location: librarian.php');	
 }

if(isset($_GET['id'])){
    $id=$_GET['id'];

    $qry="DELETE from books where isbn=$id";
    $result=mysqli_query($con,$qry);
            
			if($result){
                header('Location:deleteBooks.php');
            }else{
                echo"ERROR!!";
            }

 }
?>