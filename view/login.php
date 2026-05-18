<?php
    session_start();

    if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'){
        header('location: admin_dashboard.php');
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
    <div class="login-box">
        <form method="post" action="../controller/adminController.php" onsubmit="return validateLogin()">
            <fieldset>
                <legend>Admin Signin</legend>

                <?php if(isset($_SESSION['message'])){ ?>
                    <div class="message <?=$_SESSION['message_type']?>">
                        <?php
                            echo $_SESSION['message'];
                            unset($_SESSION['message']);
                            unset($_SESSION['message_type']);
                        ?>
                    </div>
                <?php } ?>

                <input type="hidden" name="action" value="login">
                <table class="form-table">
                    <tr>
                        <td>Email:</td>
                        <td><input type="email" name="email" id="email" value=""></td>
                    </tr>
                    <tr>
                        <td>Password:</td>
                        <td><input type="password" name="password" id="password" value=""></td>
                    </tr>
                </table>
                <span id="loginError" class="error-text"></span>
                <input type="submit" name="submit" value="Submit">
            </fieldset>
        </form>
    </div>
    <script src="../public/js/admin.js"></script>
</body>
</html>
