<?php 
//connection to mySQL
$host="localhost";
$username="root";
$password="";
$conn = mysqli_connect($host,$username,$password)or die("Could not connect");
//connection to database
$db = mysqli_select_db( $link,"star_collection")or die("Could not select database");
?>