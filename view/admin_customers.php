<?php
    require_once('admin_header.php');
    require_once('../model/adminModel.php');
    $customers = getAllCustomers();
?>

<h1>Delete Customers</h1>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Address</th>
        <th>Created At</th>
        <th>Action</th>
    </tr>

    <?php foreach($customers as $customer){ ?>
        <tr>
            <td><?=$customer['id']?></td>
            <td><?=htmlspecialchars($customer['name'])?></td>
            <td><?=htmlspecialchars($customer['email'])?></td>
            <td><?=htmlspecialchars($customer['phone'])?></td>
            <td><?=htmlspecialchars($customer['address'])?></td>
            <td><?=$customer['created_at']?></td>
            <td>
                <form method="post" action="../controller/adminController.php" onsubmit="return confirm('Delete this customer and all purchase data?')">
                    <input type="hidden" name="action" value="delete_customer">
                    <input type="hidden" name="id" value="<?=$customer['id']?>">
                    <input type="submit" value="DELETE">
                </form>
            </td>
        </tr>
    <?php } ?>
</table>

        </div>
    </div>
</body>
</html>
