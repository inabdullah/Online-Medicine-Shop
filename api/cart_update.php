<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])){
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

require_once('../model/cartModel.php');

$cart_id  = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
$user_id  = $_SESSION['user_id'];

if($cart_id <= 0 || $quantity <= 0){
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

if(updateCartQuantity($cart_id, $quantity)){
    $cartItems = getCartItems($user_id);
    $total = 0;
    $subtotal = 0;
    foreach($cartItems as $item){
        $itemSubtotal = $item['price'] * $item['quantity'];
        $total += $itemSubtotal;
        if($item['id'] == $cart_id){
            $subtotal = $itemSubtotal;
        }
    }
    echo json_encode([
        'success'  => true,
        'subtotal' => $subtotal,
        'total'    => $total
    ]);
}else{
    echo json_encode(['success' => false, 'message' => 'Update failed']);
}
?>