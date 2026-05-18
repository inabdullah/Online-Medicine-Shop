<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header('location: login.php');
    exit();
}
require_once('../model/orderModel.php');

$user_id = $_SESSION['user_id'];
$orders  = getOrdersByUser($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Orders</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 8px; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #2196F3; color: white; }
        .status-pending  { color: #e65100; font-weight: bold; }
        .status-accepted { color: #2e7d32; font-weight: bold; }
        .status-rejected { color: #c62828; font-weight: bold; }
        .navbar { background: #333; padding: 10px 20px; color: white; margin-bottom: 20px; border-radius: 8px; }
        .navbar a { color: white; text-decoration: none; margin-right: 15px; }
        .empty { text-align: center; padding: 40px; color: #999; }
        .btn { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; }
        .btn-primary { background: #2196F3; color: white; }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="home.php">Home</a>
        <a href="cart.php">Cart</a>
        <a href="my_orders.php">My Orders</a>
        <a href="../controller/logout.php">Logout</a>
        <span style="float:right">Welcome, <?=htmlspecialchars($_SESSION['name'])?></span>
    </div>

    <div class="container">
        <h1>My Orders</h1>

        <?php if(count($orders) == 0){ ?>
        <div class="empty">
            <h3>No orders yet!</h3>
            <a href="home.php" class="btn btn-primary">Browse Medicines</a>
        </div>
        <?php } else { ?>

        <table>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Shipping Address</th>
                <th>Status</th>
                <th>Details</th>
            </tr>
            <?php foreach($orders as $order){ ?>
            <tr>
                <td>#<?=$order['id']?></td>
                <td><?=$order['order_date']?></td>
                <td>Tk <?=$order['total_amount']?></td>
                <td><?=$order['payment_method']?></td>
                <td><?=htmlspecialchars($order['shipping_address'])?></td>
                <td>
                    <span class="status-<?=$order['status']?>">
                        <?=ucfirst($order['status'])?>
                    </span>
                </td>
                <td>
                    <a href="order_success.php?order_id=<?=$order['id']?>" class="btn btn-primary">View</a>
                </td>
            </tr>
            <?php } ?>
        </table>

        <?php } ?>
    </div>
</body>
</html>