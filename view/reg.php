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
<html lang="en">
<head>
    <title>Registration</title>
    <link rel="stylesheet" href="../asset/style.css">
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
                    <input type="email" name="email" id="email" value="<?= oldValue($old, "email") ?>"required>
                    <span id="emailError" class="error"></span>
                    <?= showError($errors, "email") ?>
                </label>
                <hr>

                <label>
                    Password:
                    <input type="password" name="password" id="password" required>
                    <span id="passwordError" class="error"></span>
                    <?= showError($errors, "password") ?>
                </label>
                <hr>

                <label>
                    Confirm Password:
                    <input type="password" name="confirm_password" id="confirm_password" required>
                    <span id="confirmPasswordError" class="error"></span>
                    <?= showError($errors, "confirm_password") ?>
                </label>
                <hr>

                <label>
                    Address:
                    <input type="text" name="address" id="address" value="<?= oldValue($old, "address") ?>" required>
                    <span id="addressError" class="error"></span>
                    <?= showError($errors, "address") ?>
                </label>
                <hr>

                <label>
                    Phone:
                    <input type="text" name="phone" id="phone" value="<?= oldValue($old, "phone") ?>" required>
                    <span id="phoneError" class="error"></span>
                    <?= showError($errors, "phone") ?>
                </label>
                <hr>

                <label>
                    Role:
                    <select name="role" id="role" required>
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
        Copyright &copy; 2026
    </div>

</div>

 <script>
function validateRegistration() {

    let isValid = true;

    
    document.getElementById("nameError").innerHTML = "";
    document.getElementById("emailError").innerHTML = "";
    document.getElementById("passwordError").innerHTML = "";
    document.getElementById("confirmPasswordError").innerHTML = "";
    document.getElementById("addressError").innerHTML = "";
    document.getElementById("phoneError").innerHTML = "";
    document.getElementById("roleError").innerHTML = "";

    
    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm_password").value;
    const address = document.getElementById("address").value.trim();
    const phone = document.getElementById("phone").value.trim();
    const role = document.getElementById("role").value;

    
    if (name === "") {
        document.getElementById("nameError").innerHTML = "Name is required";
        isValid = false;
    }

    
    if (email === "") {
        document.getElementById("emailError").innerHTML = "Email is required";
        isValid = false;
    }

    
    if (password.length < 8) {
        document.getElementById("passwordError").innerHTML =
            "Password must be at least 8 characters";
        isValid = false;
    }

    
    if (password !== confirmPassword) {
        document.getElementById("confirmPasswordError").innerHTML =
            "Passwords do not match";
        isValid = false;
    }

    
    if (address === "") {
        document.getElementById("addressError").innerHTML =
            "Address is required";
        isValid = false;
    }

    
    if (phone === "") {
        document.getElementById("phoneError").innerHTML =
            "Phone is required";
        isValid = false;
    }

    
    if (role === "") {
        document.getElementById("roleError").innerHTML =
            "Please select a role";
        isValid = false;
    }

    return isValid;
}
</script>
</body>
</html>
