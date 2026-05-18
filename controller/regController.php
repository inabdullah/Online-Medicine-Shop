<?php

session_start();
require_once __DIR__ . "/../model/userModel.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../view/reg.php");
    exit;
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirm_password"] ?? "";
$address = trim($_POST["address"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$role = $_POST["role"] ?? "";

$errors = [];

if ($name === "") {
    $errors["name"] = "Name is required.";
}

if ($email === "") {
    $errors["email"] = "Email is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors["email"] = "Enter a valid email address.";
} elseif (getUserByEmail($email)) {
    $errors["email"] = "This email is already registered.";
}

if ($password === "") {
    $errors["password"] = "Password is required.";
} elseif (strlen($password) < 8) {
    $errors["password"] = "Password must be at least 8 characters.";
}

if ($confirmPassword === "") {
    $errors["confirm_password"] = "Confirm password is required.";
} elseif ($password !== $confirmPassword) {
    $errors["confirm_password"] = "Passwords do not match.";
}

if ($address === "") {
    $errors["address"] = "Address is required.";
}

if ($phone === "") {
    $errors["phone"] = "Phone is required.";
} elseif (!preg_match("/^[0-9+\\-\\s]{7,20}$/", $phone)) {
    $errors["phone"] = "Enter a valid phone number.";
}

if (!in_array($role, ["admin", "customer"], true)) {
    $errors["role"] = "Select a valid role.";
}

if (!empty($errors)) {
    $_SESSION["errors"] = $errors;
    $_SESSION["old"] = [
        "name" => $name,
        "email" => $email,
        "address" => $address,
        "phone" => $phone,
        "role" => $role,
    ];
    header("Location: ../view/reg.php");
    exit;
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$registered = registerUser($name, $email, $passwordHash, $role, $address, $phone);

if ($registered) {
    $_SESSION["success"] = "Registration successful. You can now log in.";
    header("Location: ../view/login.php");
    exit;
}

$_SESSION["errors"] = ["form" => "Registration failed. Please try again."];
header("Location: ../view/reg.php");
exit;
