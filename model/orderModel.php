<?php
require_once('db.php');

function createOrder($user_id, $total, $address, $payment_method) {
    global $con;
    $stmt = mysqli_prepare($con, "INSERT INTO orders (user_id, total_amount, shipping_address, status, payment_method) VALUES (?, ?, ?, 'pending', ?)");
    mysqli_stmt_bind_param($stmt, "idss", $user_id, $total, $address, $payment_method);
    mysqli_stmt_execute($stmt);
    return mysqli_insert_id($con);
}

function addOrderItems($order_id, $items) {
    global $con;
    foreach($items as $item) {
        $stmt = mysqli_prepare($con, "INSERT INTO order_items (order_id, medicine_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iiid", $order_id, $item['medicine_id'], $item['quantity'], $item['price']);
        mysqli_stmt_execute($stmt);
    }
}

function createPayment($order_id, $amount, $payment_method) {
    global $con;
    $transaction_id = 'TXN' . time() . rand(100, 999);
    $stmt = mysqli_prepare($con, "INSERT INTO payments (order_id, amount, payment_method, transaction_id) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "idss", $order_id, $amount, $payment_method, $transaction_id);
    return mysqli_stmt_execute($stmt);
}

function getOrdersByUser($user_id) {
    global $con;
    $stmt = mysqli_prepare($con, "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $orders = [];
    while($row = mysqli_fetch_assoc($result)) {
        array_push($orders, $row);
    }
    return $orders;
}

function getOrderItems($order_id) {
    global $con;
    $stmt = mysqli_prepare($con, "SELECT oi.*, m.name, m.vendor_name FROM order_items oi JOIN medicines m ON oi.medicine_id = m.id WHERE oi.order_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $items = [];
    while($row = mysqli_fetch_assoc($result)) {
        array_push($items, $row);
    }
    return $items;
}
?>