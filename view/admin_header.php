<?php
    require_once('admin_gate.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <script src="../public/js/admin.js"></script>
</head>
<body>
    <div class="page">
        <div class="sidebar">
            <h2>Medicine Shop</h2>
            <h3 class="welcome-text">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></h3>
            <a href="admin_dashboard.php">Dashboard</a>
            <a href="admin_categories.php">Categories</a>
            <a href="admin_medicines.php">Medicines</a>
            <a href="admin_customers.php">Customers</a>
            <a href="admin_orders.php">Purchase Requests</a>
            <a href="admin_purchase_history.php">Purchase History</a>
            <a href="../controller/adminController.php?action=logout">Logout</a>
        </div>
        <div class="content">
            <?php if(isset($_SESSION['message'])){ ?>
                <div class="message <?=$_SESSION['message_type']?>">
                    <?php
                        echo $_SESSION['message'];
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']);
                    ?>
                </div>
            <?php } ?>
