<?php
session_start();

$errors = $_SESSION["errors"] ?? [];
$old = $_SESSION["old"] ?? [];
$success = $_SESSION["success"] ?? "";

unset($_SESSION["errors"], $_SESSION["old"], $_SESSION["success"]);

function showError($errors, $field)
{
    return isset($errors[$field]) ? "<span class=\"error\">" . htmlspecialchars($errors[$field]) . "</span>" : "";
}

function oldValue($old, $field)
{
    return htmlspecialchars($old[$field] ?? "");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="../asset/css/style1.css">
</head>
<body>
    <div class="container">
    <div class="header">
        <img class="logo" src="../asset/logo.png" alt="Online Medicine Shop">

        <div class="nav">
            <a href="login.php">Login</a>
        </div>
    </div>

    <div class="content">
        <?php if ($success !== ""): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <?php if (isset($errors["form"])): ?>
            <p class="error"><?= htmlspecialchars($errors["form"]) ?></p>
        <?php endif; ?>

        <form method="post" action="../controller/regController.php" onsubmit="return validateRegistration()">
            <fieldset>
                <legend>REGISTRATION</legend>

                <label>
                    Name:
                    <input type="text" name="name" id="name" value="<?= oldValue($old, "name") ?>">
    
                    <span id="nameError" class="error"></span>

                    <?= showError($errors, "name") ?>
                </label>
                <hr>

                <label>
                    Email:
                    <input type="email" name="email" id="email" value="<?= oldValue($old, "email") ?>">
                    <span id="emailError" class="error"></span>
                    <?= showError($errors, "email") ?>
                </label>
                <hr>

                <label>
                    Password:
                    <input type="password" name="password" id="password" >
                    <span id="passwordError" class="error"></span>
                    <?= showError($errors, "password") ?>
                </label>
                <hr>

                <label>
                    Confirm Password:
                    <input type="password" name="confirm_password" id="confirm_password" >
                    <span id="confirmPasswordError" class="error"></span>
                    <?= showError($errors, "confirm_password") ?>
                </label>
                <hr>

                <label>
                    Address:
                    <input type="text" name="address" id="address" value="<?= oldValue($old, "address") ?>" >
                    <span id="addressError" class="error"></span>
                    <?= showError($errors, "address") ?>
                </label>
                <hr>

                <label>
                    Phone:
                    <input type="text" name="phone" id="phone" value="<?= oldValue($old, "phone") ?>" >
                    <span id="phoneError" class="error"></span>
                    <?= showError($errors, "phone") ?>
                </label>
                <hr>

                <label>
                    Role:
                    <select name="role" id="role" >
                        <option value="">--Select--</option>
                        <option value="admin" <?= (($old["role"] ?? "") === "admin") ? "selected" : "" ?>>Admin</option>
                        <option value="customer" <?= (($old["role"] ?? "") === "customer") ? "selected" : "" ?>>Customer</option>
                    </select>
                    <span id="roleError" class="error"></span>
                    <?= showError($errors, "role") ?>
                </label>
                <hr>

                <input type="submit" name="submit" value="Submit">
                <input type="reset" name="reset" value="Reset">

            </fieldset>
        </form>
    </div>

    <div class="footer">
        Copyright &copy; <?= date('Y') ?>
    </div>

</div>

<script src="../asset/js/regValidation.js"></script>

</body>
</html>
