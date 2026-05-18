<?php
    require_once('admin_header.php');
    require_once('../model/adminModel.php');
    $count = getDashboardCount();
?>

<h1>Admin Dashboard</h1>

<div class="dashboard">
    <div class="box">
        <h3>Total Medicines</h3>
        <p><?=$count['medicines']?></p>
    </div>
    <div class="box">
        <h3>Total Categories</h3>
        <p><?=$count['categories']?></p>
    </div>
    <div class="box">
        <h3>Total Customers</h3>
        <p><?=$count['customers']?></p>
    </div>
    <div class="box">
        <h3>Pending Orders</h3>
        <p><?=$count['pending_orders']?></p>
    </div>
</div>

        </div>
    </div>
</body>
</html>
