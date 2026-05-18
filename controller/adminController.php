<?php
    session_start();
    require_once('../model/adminModel.php');

    function isAdmin(){
        if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'){
            return true;
        }else{
            return false;
        }
    }

    function adminGate(){
        if(!isAdmin()){
            header('location: ../view/login.php');
            exit();
        }
    }

    function setMessage($type, $message){
        $_SESSION['message_type'] = $type;
        $_SESSION['message'] = $message;
    }

    function clean($value){
        return trim($value);
    }

    function uploadMedicineImage($oldImage = ""){
        if(!isset($_FILES['image']) || $_FILES['image']['name'] == ""){
            return $oldImage;
        }

        $file = $_FILES['image'];
        $allowed = ['image/jpeg'=>'jpg', 'image/png'=>'png'];

        if($file['size'] > 2097152){
            return false;
        }

        $mime = mime_content_type($file['tmp_name']);
        if(!isset($allowed[$mime])){
            return false;
        }

        $folder = "../public/uploads/medicines/";
        if(!is_dir($folder)){
            mkdir($folder, 0777, true);
        }

        $fileName = "medicine_".time()."_".rand(1000, 9999).".".$allowed[$mime];
        $path = $folder.$fileName;

        if(move_uploaded_file($file['tmp_name'], $path)){
            $oldPath = getMedicineImagePath($oldImage);
            if($oldPath != "" && file_exists($oldPath)){
                unlink($oldPath);
            }
            return $fileName;
        }else{
            return false;
        }
    }

    function getMedicineImagePath($image){
        if($image == ""){
            return "";
        }else if(strpos($image, "public/uploads/medicines/") === 0){
            return "../".$image;
        }else{
            return "../public/uploads/medicines/".$image;
        }
    }

    if(isset($_REQUEST['action'])){
        $action = $_REQUEST['action'];

        if($action == "login"){
            $email = clean($_REQUEST['email']);
            $password = $_REQUEST['password'];

            if($email == "" || $password == ""){
                setMessage("error", "Email or password can not be empty!");
                header('location: ../view/login.php');
            }else{
                $user = login($email);

                if($user && password_verify($password, $user['password_hash'])){
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['role'] = $user['role'];

                    if($user['role'] == "admin"){
                        header('location: ../view/admin_dashboard.php');
                    }else{
                        setMessage("error", "Only admin can login here!");
                        header('location: ../view/login.php');
                    }
                }else{
                    setMessage("error", "Invalid email or password!");
                    header('location: ../view/login.php');
                }
            }
        }else if($action == "logout"){
            session_destroy();
            header('location: ../view/login.php');
        }else if($action == "add_category"){
            adminGate();
            $name = clean($_REQUEST['name']);
            $categoryType = clean($_REQUEST['category_type']);

            if($name == "" || $categoryType == ""){
                setMessage("error", "Category name and type are required!");
            }else if($categoryType != "liquid" && $categoryType != "solid"){
                setMessage("error", "Invalid category type!");
            }else if(categoryNameExists($name)){
                setMessage("error", "Category name already exists!");
            }else{
                $category = ['name'=>$name, 'category_type'=>$categoryType];
                $status = addCategory($category);

                if($status){
                    setMessage("success", "Category added successfully!");
                }else{
                    setMessage("error", "Category could not be added!");
                }
            }
            header('location: ../view/admin_categories.php');
        }else if($action == "update_category"){
            adminGate();
            $id = (int)$_REQUEST['id'];
            $name = clean($_REQUEST['name']);
            $categoryType = clean($_REQUEST['category_type']);

            if($id <= 0 || $name == "" || $categoryType == ""){
                setMessage("error", "All category fields are required!");
            }else if($categoryType != "liquid" && $categoryType != "solid"){
                setMessage("error", "Invalid category type!");
            }else if(categoryNameExists($name, $id)){
                setMessage("error", "Category name already exists!");
            }else{
                $category = ['id'=>$id, 'name'=>$name, 'category_type'=>$categoryType];
                $status = updateCategory($category);

                if($status){
                    setMessage("success", "Category updated successfully!");
                }else{
                    setMessage("error", "Category could not be updated!");
                }
            }
            header('location: ../view/admin_categories.php');
        }else if($action == "delete_category"){
            adminGate();
            $id = (int)$_REQUEST['id'];

            if($id <= 0){
                setMessage("error", "Invalid category!");
            }else if(categoryHasMedicine($id)){
                setMessage("error", "This category has medicines, so it can not be deleted!");
            }else{
                $status = deleteCategory($id);

                if($status){
                    setMessage("success", "Category deleted successfully!");
                }else{
                    setMessage("error", "Category could not be deleted!");
                }
            }
            header('location: ../view/admin_categories.php');
        }else if($action == "add_medicine"){
            adminGate();
            $name = clean($_REQUEST['name']);
            $categoryId = (int)$_REQUEST['category_id'];
            $vendorName = clean($_REQUEST['vendor_name']);
            $price = (float)$_REQUEST['price'];
            $availability = (int)$_REQUEST['availability'];
            $description = clean($_REQUEST['description']);

            if($name == "" || $categoryId <= 0 || $vendorName == "" || $price <= 0 || $availability < 0 || $description == ""){
                setMessage("error", "Please fill all medicine fields correctly!");
            }else{
                $imagePath = uploadMedicineImage();

                if($imagePath === false){
                    setMessage("error", "Medicine image must be JPEG/PNG and max 2MB!");
                }else{
                    $medicine = [
                        'name'=>$name,
                        'category_id'=>$categoryId,
                        'vendor_name'=>$vendorName,
                        'price'=>$price,
                        'availability'=>$availability,
                        'description'=>$description,
                        'image_path'=>$imagePath
                    ];
                    $status = addMedicine($medicine);

                    if($status){
                        setMessage("success", "Medicine added successfully!");
                    }else{
                        setMessage("error", "Medicine could not be added!");
                    }
                }
            }
            header('location: ../view/admin_medicines.php');
        }else if($action == "update_medicine"){
            adminGate();
            $id = (int)$_REQUEST['id'];
            $oldImage = clean($_REQUEST['old_image']);
            $name = clean($_REQUEST['name']);
            $categoryId = (int)$_REQUEST['category_id'];
            $vendorName = clean($_REQUEST['vendor_name']);
            $price = (float)$_REQUEST['price'];
            $availability = (int)$_REQUEST['availability'];
            $description = clean($_REQUEST['description']);

            if($id <= 0 || $name == "" || $categoryId <= 0 || $vendorName == "" || $price <= 0 || $availability < 0 || $description == ""){
                setMessage("error", "Please fill all medicine fields correctly!");
            }else{
                $imagePath = uploadMedicineImage($oldImage);

                if($imagePath === false){
                    setMessage("error", "Medicine image must be JPEG/PNG and max 2MB!");
                }else{
                    $medicine = [
                        'id'=>$id,
                        'name'=>$name,
                        'category_id'=>$categoryId,
                        'vendor_name'=>$vendorName,
                        'price'=>$price,
                        'availability'=>$availability,
                        'description'=>$description,
                        'image_path'=>$imagePath
                    ];
                    $status = updateMedicine($medicine);

                    if($status){
                        setMessage("success", "Medicine updated successfully!");
                    }else{
                        setMessage("error", "Medicine could not be updated!");
                    }
                }
            }
            header('location: ../view/admin_medicines.php');
        }else if($action == "delete_medicine"){
            adminGate();
            $id = (int)$_REQUEST['id'];
            $medicine = getMedicineById($id);

            if($id <= 0 || !$medicine){
                setMessage("error", "Invalid medicine!");
            }else if(medicineHasOrder($id)){
                setMessage("error", "This medicine is used in order, so it can not be deleted!");
            }else{
                $status = deleteMedicine($id);

                if($status){
                    $imagePath = getMedicineImagePath($medicine['image_path']);
                    if($imagePath != "" && file_exists($imagePath)){
                        unlink($imagePath);
                    }
                    setMessage("success", "Medicine deleted successfully!");
                }else{
                    setMessage("error", "Medicine could not be deleted!");
                }
            }
            header('location: ../view/admin_medicines.php');
        }else if($action == "delete_customer"){
            adminGate();
            $id = (int)$_REQUEST['id'];

            if($id <= 0){
                setMessage("error", "Invalid customer!");
            }else{
                $status = deleteCustomer($id);

                if($status){
                    setMessage("success", "Customer deleted successfully!");
                }else{
                    setMessage("error", "Customer could not be deleted!");
                }
            }
            header('location: ../view/admin_customers.php');
        }else if($action == "update_order_status"){
            adminGate();
            header('Content-Type: application/json');
            $id = (int)$_REQUEST['id'];
            $status = clean($_REQUEST['status']);

            if($id <= 0 || ($status != "accepted" && $status != "rejected")){
                echo json_encode(['status'=>'error', 'message'=>'Invalid order request!']);
            }else{
                $update = updateOrderStatus($id, $status);

                if($update){
                    if($status == "accepted"){
                        $message = "Order is accepted!";
                    }else{
                        $message = "Order is rejected!";
                    }
                    echo json_encode(['status'=>'success', 'message'=>$message, 'order_status'=>$status]);
                }else{
                    echo json_encode(['status'=>'error', 'message'=>'Order status already updated!']);
                }
            }
        }else{
            header('location: ../view/admin_dashboard.php');
        }
    }else{
        header('location: ../view/login.php');
    }

?>
