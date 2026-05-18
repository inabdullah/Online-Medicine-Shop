<?php

session_start();

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../view/login.php");
    exit;
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$remember = isset($_POST["remember"]);

$errors = [];

if ($email === "") {
    $errors["email"] = "Email is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors["email"] = "Enter a valid email address.";
}

if ($password === "") {
    $errors["password"] = "Password is required.";
}

if (!empty($errors)) {
    $_SESSION["login_errors"] = $errors;
    $_SESSION["login_old"] = ["email" => $email];
    header("Location: ../view/login.php");
    exit;
}

require_once __DIR__ . "/../model/userModel.php";

$user = getUserByEmail($email);

if (!$user || !password_verify($password, $user["password_hash"])) {
    $_SESSION["login_errors"] = ["form" => "Invalid email or password."];
    $_SESSION["login_old"] = ["email" => $email];
    header("Location: ../view/login.php");
    exit;
}

session_regenerate_id(true);
$_SESSION["user_id"] = $user["id"];
$_SESSION["name"] = $user["name"];
$_SESSION["role"] = $user["role"];
$_SESSION["email"] = $user["email"];

if ($remember) {
    $rawToken = bin2hex(random_bytes(32));
    $tokenHash = hash("sha256", $rawToken);
    $expiry = time() + (86400 * 7);

    saveRememberToken($user["id"], $tokenHash, $expiry);

    setcookie("remember_token", $user["id"] . ":" . $rawToken, [
        "expires"  => $expiry,
        "path"     => "/",
        "httponly" => true,
        "samesite" => "Lax",
    ]);
} else {
    clearRememberToken($user["id"]);
    setcookie("remember_token", "", [
        "expires"  => time() - 3600,
        "path"     => "/",
        "httponly" => true,
        "samesite" => "Lax",
    ]);
}

header("Location: ../view/home.php");
exit;
