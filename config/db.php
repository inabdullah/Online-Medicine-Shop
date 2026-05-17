<?php

    $host = "127.0.0.1";
    $dbuser = "root";
    $dbname = "online_medicine_shop";
    $dbpass = "";

    function getConnection(){
        global $host;
        global $dbuser;
        global $dbname;
        global $dbpass;
        $con = mysqli_connect($host, $dbuser, $dbpass, $dbname);
        return $con;
    }

?>
