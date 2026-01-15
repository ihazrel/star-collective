<?php
    /* php &
    Oracle DB connection file */
    $user = "StarCollective"; //Oracle username
    $pass = "system"; //Oracle password
    $host = "localhost:1521/FREEPDB1"; //server name or ip address
    $conn = oci_connect($user, $pass, $host);
    if(!$conn) {
        $e = oci_error();
        trigger_error(htmlentities($e['message'], ENT_QUOTES), 
    E_USER_ERROR);
    } else {    
        // echo "Connected to Oracle!";
    }
?>