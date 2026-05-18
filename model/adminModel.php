<?php

    require_once('../config/db.php');

    function login($email){
        $con = getConnection();
        $sql = "select * from users where email=?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    function getDashboardCount(){
        $con = getConnection();
        $count = [];

        $tables = [
            'medicines' => "select count(*) as total from medicines",
            'categories' => "select count(*) as total from categories",
            'customers' => "select count(*) as total from users where role='customer'",
            'pending_orders' => "select count(*) as total from orders where status='pending'"
        ];

        foreach($tables as $key=>$sql){
            $result = mysqli_query($con, $sql);
            $row = mysqli_fetch_assoc($result);
            $count[$key] = $row['total'];
        }

        return $count;
    }

    function getAllCategories(){
        $con = getConnection();
        $sql = "select * from categories order by id desc";
        $result = mysqli_query($con, $sql);
        $categories = [];

        while($row = mysqli_fetch_assoc($result)){
            $categories[] = $row;
        }

        return $categories;
    }

    function getCategoryById($id){
        $con = getConnection();
        $sql = "select * from categories where id=?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    function categoryNameExists($name, $id = 0){
        $con = getConnection();
        $sql = "select id from categories where name=? and id!=?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "si", $name, $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($result) > 0){
            return true;
        }else{
            return false;
        }
    }

    function addCategory($category){
        $con = getConnection();
        $sql = "insert into categories(name, category_type) values(?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $category['name'], $category['category_type']);
        return mysqli_stmt_execute($stmt);
    }

    function updateCategory($category){
        $con = getConnection();
        $sql = "update categories set name=?, category_type=? where id=?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $category['name'], $category['category_type'], $category['id']);
        return mysqli_stmt_execute($stmt);
    }

    function categoryHasMedicine($id){
        $con = getConnection();
        $sql = "select id from medicines where category_id=? limit 1";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($result) > 0){
            return true;
        }else{
            return false;
        }
    }

    function deleteCategory($id){
        $con = getConnection();
        $sql = "delete from categories where id=?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }

    function getAllMedicines(){
        $con = getConnection();
        $sql = "select medicines.*, categories.name as category_name, categories.category_type
                from medicines
                left join categories on medicines.category_id=categories.id
                order by medicines.id desc";
        $result = mysqli_query($con, $sql);
        $medicines = [];

        while($row = mysqli_fetch_assoc($result)){
            $medicines[] = $row;
        }

        return $medicines;
    }

    function getMedicineById($id){
        $con = getConnection();
        $sql = "select * from medicines where id=?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        return mysqli_fetch_assoc($result);
    }

    function addMedicine($medicine){
        $con = getConnection();
        $sql = "insert into medicines(name, category_id, vendor_name, price, availability, description, image_path)
                values(?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            "sisdiss",
            $medicine['name'],
            $medicine['category_id'],
            $medicine['vendor_name'],
            $medicine['price'],
            $medicine['availability'],
            $medicine['description'],
            $medicine['image_path']
        );
        return mysqli_stmt_execute($stmt);
    }

    function updateMedicine($medicine){
        $con = getConnection();
        $sql = "update medicines
                set name=?, category_id=?, vendor_name=?, price=?, availability=?, description=?, image_path=?
                where id=?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            "sisdissi",
            $medicine['name'],
            $medicine['category_id'],
            $medicine['vendor_name'],
            $medicine['price'],
            $medicine['availability'],
            $medicine['description'],
            $medicine['image_path'],
            $medicine['id']
        );
        return mysqli_stmt_execute($stmt);
    }

    function medicineHasOrder($id){
        $con = getConnection();
        $sql = "select id from order_items where medicine_id=? limit 1";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($result) > 0){
            return true;
        }else{
            return false;
        }
    }

    function deleteMedicine($id){
        $con = getConnection();
        $sql = "delete from medicines where id=?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }

    function getAllCustomers(){
        $con = getConnection();
        $sql = "select * from users where role='customer' order by id desc";
        $result = mysqli_query($con, $sql);
        $customers = [];

        while($row = mysqli_fetch_assoc($result)){
            $customers[] = $row;
        }

        return $customers;
    }

    function deleteCustomer($id){
        $con = getConnection();
        mysqli_begin_transaction($con);

        try{
            $sql = "delete from cart where user_id=?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);

            $orderSql = "select id from orders where user_id=?";
            $orderStmt = mysqli_prepare($con, $orderSql);
            mysqli_stmt_bind_param($orderStmt, "i", $id);
            mysqli_stmt_execute($orderStmt);
            $orderResult = mysqli_stmt_get_result($orderStmt);

            while($order = mysqli_fetch_assoc($orderResult)){
                $orderId = $order['id'];

                $sql = "delete from payments where order_id=?";
                $stmt = mysqli_prepare($con, $sql);
                mysqli_stmt_bind_param($stmt, "i", $orderId);
                mysqli_stmt_execute($stmt);

                $sql = "delete from order_items where order_id=?";
                $stmt = mysqli_prepare($con, $sql);
                mysqli_stmt_bind_param($stmt, "i", $orderId);
                mysqli_stmt_execute($stmt);
            }

            $sql = "delete from orders where user_id=?";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);

            $sql = "delete from users where id=? and role='customer'";
            $stmt = mysqli_prepare($con, $sql);
            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);

            mysqli_commit($con);
            return true;
        }catch(Exception $e){
            mysqli_rollback($con);
            return false;
        }
    }

    function getAllOrders(){
        $con = getConnection();
        $sql = "select orders.*, users.name as customer_name, users.email as customer_email
                from orders
                inner join users on orders.user_id=users.id
                order by orders.id desc";
        $result = mysqli_query($con, $sql);
        $orders = [];

        while($row = mysqli_fetch_assoc($result)){
            $orders[] = $row;
        }

        return $orders;
    }

    function updateOrderStatus($id, $status){
        $con = getConnection();
        $sql = "update orders set status=? where id=? and status='pending'";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "si", $status, $id);
        mysqli_stmt_execute($stmt);

        if(mysqli_stmt_affected_rows($stmt) > 0){
            return true;
        }else{
            return false;
        }
    }

    function getPurchaseHistory(){
        $con = getConnection();
        $sql = "select orders.*, users.name as customer_name, users.email as customer_email,
                users.phone, users.address
                from orders
                inner join users on orders.user_id=users.id
                where orders.status='accepted'
                order by orders.id desc";
        $result = mysqli_query($con, $sql);
        $orders = [];

        while($row = mysqli_fetch_assoc($result)){
            $row['items'] = getOrderItems($row['id']);
            $orders[] = $row;
        }

        return $orders;
    }

    function getOrderItems($orderId){
        $con = getConnection();
        $sql = "select order_items.*, medicines.name as medicine_name, medicines.vendor_name
                from order_items
                inner join medicines on order_items.medicine_id=medicines.id
                where order_items.order_id=?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $orderId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $items = [];

        while($row = mysqli_fetch_assoc($result)){
            $items[] = $row;
        }

        return $items;
    }

?>
