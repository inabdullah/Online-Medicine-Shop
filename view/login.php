<?php
session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: home.php");
    exit;
}

if (isset($_COOKIE["remember_token"])) {
    require_once __DIR__ . "/../model/userModel.php";

    $parts = explode(":", $_COOKIE["remember_token"], 2);

    if (count($parts) === 2) {
        [$cookieUserId, $cookieRawToken] = $parts;
        $tokenHash = hash("sha256", $cookieRawToken);
        $user = getUserByRememberToken((int)$cookieUserId, $tokenHash);

        if ($user) {
            session_regenerate_id(true);
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["role"] = $user["role"];
            $_SESSION["email"] = $user["email"];

            $newRaw = bin2hex(random_bytes(32));
            $newHash = hash("sha256", $newRaw);
            $expiry = time() + (86400 * 7);
            saveRememberToken($user["id"], $newHash, $expiry);
            setcookie("remember_token", $user["id"] . ":" . $newRaw, [
                "expires"  => $expiry,
                "path"     => "/",
                "httponly" => true,
                "samesite" => "Lax",
            ]);

            header("Location: home.php");
            exit;
        }
    }

    setcookie("remember_token", "", [
        "expires"  => time() - 3600,
        "path"     => "/",
        "httponly" => true,
        "samesite" => "Lax",
    ]);
}

$errors = $_SESSION["login_errors"] ?? [];
$old = $_SESSION["login_old"] ?? [];
$success = $_SESSION["success"] ?? "";
$emailValue = $old["email"] ?? "";
unset($_SESSION["login_errors"], $_SESSION["login_old"], $_SESSION["success"]);

function showLoginError(array $errors, string $field): string
{
    return isset($errors[$field])
        ? '<span class="error">' . htmlspecialchars($errors[$field]) . '</span>'
        : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../asset/css/style1.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <img class="logo" src="../asset/logo.png" alt="Online Medicine Shop">

            <div class="nav">
                <h2>Online Medicine Shop</h2>
            </div>
        </div>

        <div class="content">
            <?php if ($success !== ""): ?>
                <p class="success"><?= htmlspecialchars($success) ?></p>
            <?php endif; ?>

            <?php if (isset($errors["form"])): ?>
                <p class="error"><?= htmlspecialchars($errors["form"]) ?></p>
            <?php endif; ?>

            <form method="post" action="../controller/loginCheck.php" onsubmit="return validateLogin()">
                <fieldset>
                    <legend>LOGIN</legend>

                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email"
                           value="<?= htmlspecialchars($emailValue) ?>"
                           autocomplete="email">
                    <span id="emailError" class="error"></span>
                    <?= showLoginError($errors, "email") ?>
                    <hr>

                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password"
                           autocomplete="current-password">
                    <span id="passwordError" class="error"></span>
                    <?= showLoginError($errors, "password") ?>
                    <hr>

                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" id="remember">
                        Remember Me
                    </label>
                    <br><br>

                    <input type="submit" value="Login">
                </fieldset>
            </form>

            <p class="alt-link">Don't have an account? <a href="reg.php">Register here</a></p>
        </div>

        <div class="footer">
            Copyright &copy; <?= date('Y') ?>
        </div>
    </div>
    <script src="../asset/js/loginValidation.js"></script>
</body>
</html>
