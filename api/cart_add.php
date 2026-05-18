<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])){
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

require_once('../model/cartModel.php');

$medicine_id = isset($_POST['medicine_id']) ? (int)$_POST['medicine_id'] : 0;
$quantity    = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
$user_id     = $_SESSION['user_id'];

if($medicine_id <= 0 || $quantity <= 0){
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit();
}

if(addToCart($user_id, $medicine_id, $quantity)){
    $cartCount = getCartCount($user_id);
    echo json_encode([
        'success'    => true,
        'message'    => 'Added to cart!',
        'cart_count' => $cartCount
    ]);
}else{
    echo json_encode(['success' => false, 'message' => 'Failed to add']);
}
?>