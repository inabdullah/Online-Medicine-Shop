<?php

    $host = "127.0.0.1";
    $dbuser = "root";
    $dbname = "online_medicine_shop";
    $dbpass = "";

    $con = mysqli_connect($host, $dbuser, $dbpass, $dbname);

    if (!$con) {
        die("Database connection failed: " . mysqli_connect_error());
    }

?>
