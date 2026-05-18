<?php
session_start();
require_once __DIR__ . "/../model/userModel.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user = getUserById((int)$_SESSION["user_id"]);

if (!$user) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

$errors = $_SESSION["profile_errors"] ?? [];
$old = $_SESSION["profile_old"] ?? [];
$success = $_SESSION["profile_success"] ?? "";
unset($_SESSION["profile_errors"], $_SESSION["profile_old"], $_SESSION["profile_success"]);

if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

function profileValue(array $old, array $user, string $field): string
{
    return htmlspecialchars($old[$field] ?? $user[$field] ?? "");
}

function profileError(array $errors, string $field): string
{
    return isset($errors[$field])
        ? '<span class="error">' . htmlspecialchars($errors[$field]) . '</span>'
        : '';
}

$profilePicture = $user["profile_picture"] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../asset/css/style1.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <img class="logo" src="../asset/logo.png" alt="Online Medicine Shop">
            <div class="nav">
                <a href="home.php">Home</a>
                <a href="../controller/logout.php">Logout</a>
            </div>
        </div>

        <div class="content">
            <h1>Profile</h1>

            <?php if ($success !== ""): ?>
                <p class="success"><?= htmlspecialchars($success) ?></p>
            <?php endif; ?>

            <?php if (isset($errors["form"])): ?>
                <p class="error"><?= htmlspecialchars($errors["form"]) ?></p>
            <?php endif; ?>

            <form method="post" action="../controller/profileController.php" enctype="multipart/form-data" onsubmit="return validateProfile()">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION["csrf_token"]) ?>">

                <fieldset>
                    <legend>ACCOUNT INFORMATION</legend>

                    <div class="profile-summary">
                        <?php if ($profilePicture !== ""): ?>
                            <img class="profile-picture" src="../<?= htmlspecialchars($profilePicture) ?>" alt="Profile picture">
                        <?php else: ?>
                            <div class="profile-placeholder"><?= htmlspecialchars(strtoupper(substr($user["name"], 0, 1))) ?></div>
                        <?php endif; ?>
                        <div>
                            <strong><?= htmlspecialchars($user["name"]) ?></strong>
                            <p><?= htmlspecialchars($user["role"]) ?></p>
                        </div>
                    </div>

                    <label>
                        Name:
                        <input type="text" name="name" id="name" value="<?= profileValue($old, $user, "name") ?>">
                        <span id="nameError" class="error"></span>
                        <?= profileError($errors, "name") ?>
                    </label>
                    <hr>

                    <label>
                        Email:
                        <input type="email" name="email" id="email" value="<?= profileValue($old, $user, "email") ?>">
                        <span id="emailError" class="error"></span>
                        <?= profileError($errors, "email") ?>
                    </label>
                    <hr>

                    <label>
                        Address:
                        <input type="text" name="address" id="address" value="<?= profileValue($old, $user, "address") ?>">
                        <span id="addressError" class="error"></span>
                        <?= profileError($errors, "address") ?>
                    </label>
                    <hr>

                    <label>
                        Phone:
                        <input type="text" name="phone" id="phone" value="<?= profileValue($old, $user, "phone") ?>">
                        <span id="phoneError" class="error"></span>
                        <?= profileError($errors, "phone") ?>
                    </label>
                    <hr>

                    <label>
                        Profile Picture:
                        <input type="file" name="profile_picture" id="profile_picture" accept="image/jpeg,image/png">
                        <span id="profilePictureError" class="error"></span>
                        <?= profileError($errors, "profile_picture") ?>
                    </label>
                </fieldset>

                <br>

                <fieldset>
                    <legend>CHANGE PASSWORD</legend>

                    <label>
                        Current Password:
                        <input type="password" name="current_password" id="current_password" autocomplete="current-password">
                        <span id="currentPasswordError" class="error"></span>
                        <?= profileError($errors, "current_password") ?>
                    </label>
                    <hr>

                    <label>
                        New Password:
                        <input type="password" name="new_password" id="new_password" autocomplete="new-password">
                        <span id="newPasswordError" class="error"></span>
                        <?= profileError($errors, "new_password") ?>
                    </label>
                    <hr>

                    <label>
                        Confirm New Password:
                        <input type="password" name="confirm_password" id="confirm_password" autocomplete="new-password">
                        <span id="confirmPasswordError" class="error"></span>
                        <?= profileError($errors, "confirm_password") ?>
                    </label>
                </fieldset>

                <br>
                <input type="submit" value="Update Profile">
                <input type="reset" value="Reset">
            </form>
        </div>

        <div class="footer">
            Copyright &copy; <?= date('Y') ?>
        </div>
    </div>
    <script src="../asset/js/profileValidation.js"></script>
</body>
</html>
