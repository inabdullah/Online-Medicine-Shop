<?php
require_once('db.php');

function getCartItems($user_id) {
    global $con;
    $stmt = mysqli_prepare($con, "SELECT c.id, c.quantity, m.name, m.vendor_name, m.price, m.availability, m.image_path FROM cart c JOIN medicines m ON c.medicine_id = m.id WHERE c.user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $items = [];
    while($row = mysqli_fetch_assoc($result)) {
        array_push($items, $row);
    }
    return $items;
}

function addToCart($user_id, $medicine_id, $quantity) {
    global $con;
    $stmt = mysqli_prepare($con, "SELECT id, quantity FROM cart WHERE user_id = ? AND medicine_id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $medicine_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if($row = mysqli_fetch_assoc($result)) {
        $new_qty = $row['quantity'] + $quantity;
        $stmt2 = mysqli_prepare($con, "UPDATE cart SET quantity = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt2, "ii", $new_qty, $row['id']);
        return mysqli_stmt_execute($stmt2);
    } else {
        $stmt2 = mysqli_prepare($con, "INSERT INTO cart (user_id, medicine_id, quantity) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt2, "iii", $user_id, $medicine_id, $quantity);
        return mysqli_stmt_execute($stmt2);
    }
}

function updateCartQuantity($cart_id, $quantity) {
    global $con;
    $stmt = mysqli_prepare($con, "UPDATE cart SET quantity = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ii", $quantity, $cart_id);
    return mysqli_stmt_execute($stmt);
}

function removeFromCart($cart_id) {
    global $con;
    $stmt = mysqli_prepare($con, "DELETE FROM cart WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $cart_id);
    return mysqli_stmt_execute($stmt);
}

function clearCart($user_id) {
    global $con;
    $stmt = mysqli_prepare($con, "DELETE FROM cart WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    return mysqli_stmt_execute($stmt);
}

function getCartCount($user_id) {
    global $con;
    $stmt = mysqli_prepare($con, "SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['total'] ?? 0;
}
?>