<?php
/*
 // connection to MySQL
 $host = "localhost";
 $username = "root";
 $password = "";
 $conn = mysqli_connect($host, $username, $password) or die("Could not connect");
 // connection to database (use $conn as the link identifier)
 $db = mysqli_select_db($conn, "star_collection") or die("Could not select database");
*/
?>

<?php
    /* php &
    Oracle DB connection file */
    $user = "StarCollection"; //Oracle username
    $pass = "system"; //Oracle password
    $host = "localhost/FREEPDB1"; //server name or ip address
    $dbconn = oci_connect($user, $pass, $host);
    if(!$dbconn) {
        $e = oci_error();
        trigger_error(htmlentities($e['message'], ENT_QUOTES), 
    E_USER_ERROR);
    } else {
         echo "Oracle Connection Successful.";
    }
?>