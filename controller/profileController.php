<?php

session_start();
require_once __DIR__ . "/../model/userModel.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../view/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../view/profile.php");
    exit;
}

if (!isset($_POST["csrf_token"], $_SESSION["csrf_token"]) || !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])) {
    $_SESSION["profile_errors"] = ["form" => "Invalid request. Please try again."];
    header("Location: ../view/profile.php");
    exit;
}

$userId = (int)$_SESSION["user_id"];
$currentUser = getUserById($userId);

if (!$currentUser) {
    session_unset();
    session_destroy();
    header("Location: ../view/login.php");
    exit;
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$address = trim($_POST["address"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$currentPassword = $_POST["current_password"] ?? "";
$newPassword = $_POST["new_password"] ?? "";
$confirmPassword = $_POST["confirm_password"] ?? "";

$errors = [];

if ($name === "") {
    $errors["name"] = "Name is required.";
}

if ($email === "") {
    $errors["email"] = "Email is required.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors["email"] = "Enter a valid email address.";
} elseif (isEmailTakenByOtherUser($email, $userId)) {
    $errors["email"] = "This email is already used by another account.";
}

if ($address === "") {
    $errors["address"] = "Address is required.";
}

if ($phone === "") {
    $errors["phone"] = "Phone is required.";
} elseif (!preg_match("/^[0-9+\\-\\s]{7,20}$/", $phone)) {
    $errors["phone"] = "Enter a valid phone number.";
}

$wantsPasswordChange = $currentPassword !== "" || $newPassword !== "" || $confirmPassword !== "";

if ($wantsPasswordChange) {
    if ($currentPassword === "") {
        $errors["current_password"] = "Current password is required.";
    } elseif (!password_verify($currentPassword, $currentUser["password_hash"])) {
        $errors["current_password"] = "Current password is incorrect.";
    }

    if ($newPassword === "") {
        $errors["new_password"] = "New password is required.";
    } elseif (strlen($newPassword) < 8) {
        $errors["new_password"] = "New password must be at least 8 characters.";
    }

    if ($confirmPassword === "") {
        $errors["confirm_password"] = "Confirm password is required.";
    } elseif ($newPassword !== $confirmPassword) {
        $errors["confirm_password"] = "Passwords do not match.";
    }
}

$profilePicturePath = null;

if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] !== UPLOAD_ERR_NO_FILE) {
    $file = $_FILES["profile_picture"];

    if ($file["error"] !== UPLOAD_ERR_OK) {
        $errors["profile_picture"] = "Profile picture upload failed.";
    } elseif ($file["size"] > 2 * 1024 * 1024) {
        $errors["profile_picture"] = "Profile picture must be 2MB or smaller.";
    } else {
        $allowedTypes = [
            "image/jpeg" => "jpg",
            "image/png" => "png",
        ];
        $mimeType = mime_content_type($file["tmp_name"]);

        if (!isset($allowedTypes[$mimeType])) {
            $errors["profile_picture"] = "Only JPG and PNG images are allowed.";
        } else {
            $uploadDir = __DIR__ . "/../public/uploads/profile_pictures";

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }

            $fileName = "profile_" . $userId . "_" . time() . "." . $allowedTypes[$mimeType];
            $destination = $uploadDir . "/" . $fileName;

            if (move_uploaded_file($file["tmp_name"], $destination)) {
                $profilePicturePath = "public/uploads/profile_pictures/" . $fileName;
            } else {
                $errors["profile_picture"] = "Could not save profile picture.";
            }
        }
    }
}

if (!empty($errors)) {
    $_SESSION["profile_errors"] = $errors;
    $_SESSION["profile_old"] = [
        "name" => $name,
        "email" => $email,
        "address" => $address,
        "phone" => $phone,
    ];
    header("Location: ../view/profile.php");
    exit;
}

$updated = updateUserProfile($userId, $name, $email, $address, $phone, $profilePicturePath);

if ($updated && $wantsPasswordChange) {
    $updated = updateUserPassword($userId, password_hash($newPassword, PASSWORD_DEFAULT));
}

if (!$updated) {
    $_SESSION["profile_errors"] = ["form" => "Profile update failed. Please try again."];
    header("Location: ../view/profile.php");
    exit;
}

$_SESSION["name"] = $name;
$_SESSION["email"] = $email;
$_SESSION["profile_success"] = "Profile updated successfully.";
unset($_SESSION["csrf_token"]);

header("Location: ../view/profile.php");
exit;
