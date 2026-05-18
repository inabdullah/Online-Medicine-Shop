<?php
session_start();

require_once __DIR__ . "/../model/userModel.php";

// Clear remember token from DB if exists
if (isset($_SESSION["user_id"])) {
    clearRememberToken((int)$_SESSION["user_id"]);
}

// Clear the cookie
if (isset($_COOKIE["remember_token"])) {
    setcookie("remember_token", "", [
        "expires"  => time() - 3600,
        "path"     => "/",
        "httponly" => true,
        "samesite" => "Lax",
    ]);
}

// Destroy session
session_unset();
session_destroy();

header("Location: ../view/login.php");
exit;

