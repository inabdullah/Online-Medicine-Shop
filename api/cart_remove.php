<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])){
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

require_once('../model/cartModel.php');

$cart_id = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;
$user_id = $_SESSION['user_id'];

if($cart_id <= 0){
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

if(removeFromCart($cart_id)){
    $cartItems = getCartItems($user_id);
    $total = 0;
    foreach($cartItems as $item){
        $total += $item['price'] * $item['quantity'];
    }
    echo json_encode([
        'success' => true,
        'total'   => $total
    ]);
}else{
    echo json_encode(['success' => false, 'message' => 'Remove failed']);
}
?>