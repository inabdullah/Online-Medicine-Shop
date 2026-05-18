<?php
    require_once('admin_header.php');
    require_once('../model/adminModel.php');
    $orders = getPurchaseHistory();
?>

<h1>All Customers Purchase History</h1>

<?php foreach($orders as $order){ ?>
    <div class="history-card">
        <h2>Order #<?=$order['id']?></h2>
        <p>
            Customer: <?=htmlspecialchars($order['customer_name'])?> |
            Email: <?=htmlspecialchars($order['customer_email'])?> |
            Phone: <?=htmlspecialchars($order['phone'])?>
        </p>
        <p>Address: <?=htmlspecialchars($order['shipping_address'])?></p>
        <p>Total Amount: <?=$order['total_amount']?> | Payment: <?=htmlspecialchars($order['payment_method'])?> | Date: <?=$order['order_date']?></p>

        <table border="1">
            <tr>
                <th>Medicine</th>
                <th>Vendor</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Subtotal</th>
            </tr>
            <?php foreach($order['items'] as $item){ ?>
                <tr>
                    <td><?=htmlspecialchars($item['medicine_name'])?></td>
                    <td><?=htmlspecialchars($item['vendor_name'])?></td>
                    <td><?=$item['quantity']?></td>
                    <td><?=$item['unit_price']?></td>
                    <td><?=$item['quantity'] * $item['unit_price']?></td>
                </tr>
            <?php } ?>
        </table>
    </div>
<?php } ?>

        </div>
    </div>
</body>
</html>
