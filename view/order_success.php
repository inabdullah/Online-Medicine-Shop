<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header('location: login.php');
    exit();
}
require_once('../model/orderModel.php');

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$orderItems = getOrderItems($order_id);
$orders = getOrdersByUser($_SESSION['user_id']);

$currentOrder = null;
foreach($orders as $o){
    if($o['id'] == $order_id){
        $currentOrder = $o;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Order Confirmed</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 8px; }
        .success-box { background: #e8f5e9; border: 1px solid #4CAF50; padding: 20px; border-radius: 8px; margin-bottom: 20px; text-align: center; }
        h1 { color: #4CAF50; }
        h2 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #f5f5f5; }
        .total { font-size: 18px; font-weight: bold; text-align: right; margin-top: 15px; }
        .status { background: #fff3e0; color: #e65100; padding: 5px 15px; border-radius: 20px; font-weight: bold; }
        .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; margin-top: 20px; }
        .btn-primary { background: #2196F3; color: white; }
        .navbar { background: #333; padding: 10px 20px; color: white; margin-bottom: 20px; border-radius: 8px; }
        .navbar a { color: white; text-decoration: none; margin-right: 15px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .info-label { font-weight: bold; color: #666; }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="home.php">Home</a>
        <a href="cart.php">Cart</a>
        <a href="my_orders.php">My Orders</a>
        <a href="../controller/logout.php">Logout</a>
    </div>

    <div class="container">
        <div class="success-box">
            <h1>Order Confirmed!</h1>
            <p>Your order has been placed successfully and is pending admin approval.</p>
        </div>

        <?php if($currentOrder){ ?>
        <h2>Order Details</h2>
        <div class="info-row">
            <span class="info-label">Order ID:</span>
            <span>#<?=$currentOrder['id']?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Order Date:</span>
            <span><?=htmlspecialchars($currentOrder['order_date'])?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Payment Method:</span>
            <span><?=htmlspecialchars($currentOrder['payment_method'])?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Shipping Address:</span>
            <span><?=htmlspecialchars($currentOrder['shipping_address'])?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Status:</span>
            <span class="status">Pending Admin Approval</span>
        </div>

        <h2>Items Ordered</h2>
        <table>
            <tr>
                <th>Medicine</th>
                <th>Vendor</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
            </tr>
            <?php foreach($orderItems as $item){ ?>
            <tr>
                <td><?=htmlspecialchars($item['name'])?></td>
                <td><?=htmlspecialchars($item['vendor_name'])?></td>
                <td><?=$item['quantity']?></td>
                <td>Tk <?=htmlspecialchars($item['unit_price'])?></td>
                <td>Tk <?=htmlspecialchars($item['quantity'] * $item['unit_price'])?></td>
            </tr>
            <?php } ?>
        </table>
        <div class="total">Total: Tk <?=htmlspecialchars($currentOrder['total_amount'])?></div>
        <?php } ?>

        <a href="home.php" class="btn btn-primary">Continue Shopping</a>
        <a href="my_orders.php" class="btn btn-primary">View All Orders</a>
    </div>
</body>
</html>