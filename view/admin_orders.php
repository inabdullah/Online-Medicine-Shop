<?php
    require_once('admin_header.php');
    require_once('../model/adminModel.php');
    $orders = getAllOrders();
?>

<h1>All Purchase Requests</h1>
<h3 id="orderMessage" class="order-message"></h3>

<table border="1">
    <tr>
        <th>Order ID</th>
        <th>Customer</th>
        <th>Total</th>
        <th>Shipping Address</th>
        <th>Payment</th>
        <th>Date</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php foreach($orders as $order){ ?>
        <tr id="orderRow<?=$order['id']?>">
            <td><?=$order['id']?></td>
            <td>
                <?=htmlspecialchars($order['customer_name'])?> <br>
                <?=htmlspecialchars($order['customer_email'])?>
            </td>
            <td><?=$order['total_amount']?></td>
            <td><?=htmlspecialchars($order['shipping_address'])?></td>
            <td><?=htmlspecialchars($order['payment_method'])?></td>
            <td><?=$order['order_date']?></td>
            <td id="status<?=$order['id']?>"><?=htmlspecialchars($order['status'])?></td>
            <td>
                <?php if($order['status'] == "pending"){ ?>
                    <button id="accept<?=$order['id']?>" type="button" onclick="updateOrderStatus(<?=$order['id']?>, 'accepted')">Accept</button>
                    <button id="reject<?=$order['id']?>" type="button" onclick="updateOrderStatus(<?=$order['id']?>, 'rejected')">Reject</button>
                <?php }else{ ?>
                    <button id="accept<?=$order['id']?>" class="disabled-btn" type="button" disabled>Accept</button>
                    <button id="reject<?=$order['id']?>" class="disabled-btn" type="button" disabled>Reject</button>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
</table>

        </div>
    </div>
</body>
</html>
