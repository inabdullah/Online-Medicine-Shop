<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header('location: login.php');
    exit();
}
require_once('../model/cartModel.php');
require_once('../model/orderModel.php');

$user_id = $_SESSION['user_id'];
$cartItems = getCartItems($user_id);
$total = 0;
foreach($cartItems as $item){
    $total += $item['price'] * $item['quantity'];
}

if(count($cartItems) == 0){
    header('location: cart.php');
    exit();
}

$error = '';
$step = isset($_GET['step']) ? $_GET['step'] : 'address';

// Step 2: Show invoice after address submitted
if($step == 'invoice' && isset($_SESSION['shipping_address'])){
    $address = $_SESSION['shipping_address'];
}

// Step 3: Confirm purchase
if($step == 'confirm' && $_SERVER['REQUEST_METHOD'] == 'POST'){
    $address  = $_POST['address'];
    $payment  = $_POST['payment_method'];

    // PHP Validation
    if(empty($address)){
        $error = 'Address cannot be empty!';
        $step = 'address';
    } elseif(empty($payment)){
        $error = 'Please select a payment method!';
        $step = 'payment';
    } else {
        // Create order
        $order_id = createOrder($user_id, $total, $address, $payment);

        // Add order items
        $items = [];
        foreach($cartItems as $item){
            $items[] = [
                'medicine_id' => $item['medicine_id'] ?? $item['id'],
                'quantity'    => $item['quantity'],
                'price'       => $item['price']
            ];
        }
        addOrderItems($order_id, $items);

        // Create payment record
        createPayment($order_id, $total, $payment);

        // Clear cart
        clearCart($user_id);

        // Redirect to order success
        header('location: order_success.php?order_id=' . $order_id);
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Checkout</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 700px; margin: auto; background: white; padding: 30px; border-radius: 8px; }
        h1, h2 { color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, textarea, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .btn-success { background: #4CAF50; color: white; }
        .btn-secondary { background: #9E9E9E; color: white; }
        .btn-primary { background: #2196F3; color: white; }
        .error { color: red; margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #f5f5f5; }
        .total { font-size: 18px; font-weight: bold; text-align: right; margin-top: 15px; }
        .payment-option { display: inline-block; margin: 5px; padding: 10px 20px; border: 2px solid #ddd; border-radius: 4px; cursor: pointer; }
        .payment-option input { width: auto; margin-right: 5px; }
        .navbar { background: #333; padding: 10px 20px; color: white; margin-bottom: 20px; border-radius: 8px; }
        .navbar a { color: white; text-decoration: none; margin-right: 15px; }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="home.php">Home</a>
        <a href="cart.php">Cart</a>
        <a href="../controller/logout.php">Logout</a>
    </div>

    <div class="container">
        <h1>Checkout</h1>

        <?php if($error){ ?>
        <div class="error"><?=htmlspecialchars($error)?></div>
        <?php } ?>

        <!-- Step 1: Address + Payment + Invoice + Confirm all in one form -->
        <form method="post" action="checkout.php?step=confirm" onsubmit="return validate()">

            <!-- Invoice -->
            <h2>Order Summary</h2>
            <table>
                <tr>
                    <th>Medicine</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                </tr>
                <?php foreach($cartItems as $item){ ?>
                <tr>
                    <td><?=htmlspecialchars($item['name'])?></td>
                    <td><?=$item['quantity']?></td>
                    <td>Tk <?=$item['price']?></td>
                    <td>Tk <?=$item['price'] * $item['quantity']?></td>
                </tr>
                <?php } ?>
            </table>
            <div class="total">Total: Tk <?=$total?></div>

            <hr style="margin: 20px 0;">

            <!-- Address -->
            <h2>Shipping Address</h2>
            <div class="form-group">
                <label>Address:</label>
                <textarea name="address" id="address" rows="3" placeholder="Enter your shipping address..."><?=htmlspecialchars($_SESSION['address'] ?? '')?></textarea>
            </div>

            <hr style="margin: 20px 0;">

            <!-- Payment Method -->
            <h2>Payment Method</h2>
            <div class="form-group" id="payment-group">
                <label class="payment-option"><input type="radio" name="payment_method" value="bKash"> bKash</label>
                <label class="payment-option"><input type="radio" name="payment_method" value="Nagad"> Nagad</label>
                <label class="payment-option"><input type="radio" name="payment_method" value="Credit Card"> Credit Card</label>
                <label class="payment-option"><input type="radio" name="payment_method" value="Bank Transfer"> Bank Transfer</label>
                <label class="payment-option"><input type="radio" name="payment_method" value="Cash on Delivery"> Cash on Delivery</label>
            </div>

            <div style="margin-top: 20px;">
                <a href="cart.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-success">Confirm Purchase</button>
            </div>
        </form>
    </div>

    <script>
        function validate(){
            var address = document.getElementById('address').value;
            var payment = document.querySelector('input[name="payment_method"]:checked');

            if(address == '' || address == null){
                alert('Address cannot be empty!');
                return false;
            }
            if(!payment){
                alert('Please select a payment method!');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>