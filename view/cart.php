<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header('location: login.php');
    exit();
}
require_once('../model/cartModel.php');

$user_id = $_SESSION['user_id'];
$cartItems = getCartItems($user_id);
$total = 0;
foreach($cartItems as $item){
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Cart</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 8px; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #4CAF50; color: white; }
        .btn { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-danger { background: #f44336; color: white; }
        .btn-primary { background: #2196F3; color: white; }
        .btn-success { background: #4CAF50; color: white; }
        .qty-btn { padding: 4px 10px; font-size: 16px; cursor: pointer; border: 1px solid #ddd; background: white; }
        .total { font-size: 20px; font-weight: bold; text-align: right; margin-top: 20px; }
        .navbar { background: #333; padding: 10px 20px; color: white; margin-bottom: 20px; border-radius: 8px; }
        .navbar a { color: white; text-decoration: none; margin-right: 15px; }
        .empty { text-align: center; padding: 40px; color: #999; }
    </style>
</head>
<body>
    <div class="navbar">
        <a href="home.php">Home</a>
        <a href="cart.php">Cart (<?=getCartCount($user_id)?>)</a>
        <a href="../controller/logout.php">Logout</a>
        <span style="float:right">Welcome, <?=$_SESSION['name']?></span>
    </div>

    <div class="container">
        <h1>My Cart</h1>

        <?php if(count($cartItems) == 0){ ?>
        <div class="empty">
            <h3>Cart is empty!</h3>
            <a href="home.php" class="btn btn-primary">Browse Medicines</a>
        </div>
        <?php } else { ?>

        <table>
            <tr>
                <th>Medicine</th>
                <th>Vendor</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th>Action</th>
            </tr>
            <?php foreach($cartItems as $item){ ?>
            <tr id="row-<?=$item['id']?>">
                <td><?=htmlspecialchars($item['name'])?></td>
                <td><?=htmlspecialchars($item['vendor_name'])?></td>
                <td>Tk <?=$item['price']?></td>
                <td>
                    <button class="qty-btn" onclick="updateQty(<?=$item['id']?>, -1)">-</button>
                    <span id="qty-<?=$item['id']?>"><?=$item['quantity']?></span>
                    <button class="qty-btn" onclick="updateQty(<?=$item['id']?>, 1, <?=$item['availability']?>)">+</button>
                </td>
                <td id="sub-<?=$item['id']?>">Tk <?=$item['price'] * $item['quantity']?></td>
                <td>
                    <button class="btn btn-danger" onclick="removeItem(<?=$item['id']?>)">Remove</button>
                </td>
            </tr>
            <?php } ?>
        </table>

        <div class="total" id="total">Total: Tk <?=$total?></div>

        <div style="text-align:right; margin-top:20px;">
            <a href="home.php" class="btn btn-primary">Continue Shopping</a>
            <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
        </div>

        <?php } ?>
    </div>

    <script>
        function updateQty(cartId, change, maxStock) {
            var qtyEl = document.getElementById('qty-' + cartId);
            var newQty = parseInt(qtyEl.innerText) + change;

            if(isNaN(newQty) || newQty < 1){
                alert('Invalid quantity!');
                return;
            }
            if(newQty < 1) {
                removeItem(cartId);
                return;
            }
            if(maxStock && newQty > maxStock) {
                alert('Stock limit reached!');
                return;
            }

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../api/cart_update.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function(){
                if(xhr.readyState == 4 && xhr.status == 200){
                    var res = JSON.parse(xhr.responseText);
                    if(res.success){
                        qtyEl.innerText = newQty;
                        document.getElementById('sub-' + cartId).innerText = 'Tk ' + res.subtotal;
                        document.getElementById('total').innerText = 'Total: Tk ' + res.total;
                    }
                }
            }
            xhr.send('cart_id=' + cartId + '&quantity=' + newQty);
        }

        function removeItem(cartId) {
            if(!confirm('Remove this item?')) return;

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../api/cart_remove.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function(){
                if(xhr.readyState == 4 && xhr.status == 200){
                    var res = JSON.parse(xhr.responseText);
                    if(res.success){
                        document.getElementById('row-' + cartId).remove();
                        document.getElementById('total').innerText = 'Total: Tk ' + res.total;
                    }
                }
            }
            xhr.send('cart_id=' + cartId);
        }
    </script>
</body>
</html>