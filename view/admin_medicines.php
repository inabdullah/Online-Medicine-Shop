<?php
    require_once('admin_header.php');
    require_once('../model/adminModel.php');

    $categories = getAllCategories();
    $medicines = getAllMedicines();
    $editMedicine = null;

    if(isset($_GET['edit'])){
        $editMedicine = getMedicineById((int)$_GET['edit']);
    }

    function getMedicineImageSrc($image){
        if($image == ""){
            return "";
        }else if(strpos($image, "public/uploads/medicines/") === 0){
            return "../".$image;
        }else{
            return "../public/uploads/medicines/".$image;
        }
    }
?>

<h1>Medicine Management</h1>

<form method="post" action="../controller/adminController.php" enctype="multipart/form-data" onsubmit="return validateMedicine()">
    <fieldset>
        <legend><?php if($editMedicine){ echo "Edit Medicine"; }else{ echo "Add Medicine"; } ?></legend>

        <input type="hidden" name="action" value="<?php if($editMedicine){ echo "update_medicine"; }else{ echo "add_medicine"; } ?>">
        <?php if($editMedicine){ ?>
            <input type="hidden" name="id" value="<?=$editMedicine['id']?>">
            <input type="hidden" name="old_image" value="<?=htmlspecialchars($editMedicine['image_path'])?>">
        <?php } ?>

        <table class="form-table">
            <tr>
                <td>Name:</td>
                <td><input type="text" name="name" id="medicineName" value="<?php if($editMedicine){ echo htmlspecialchars($editMedicine['name']); } ?>"></td>
            </tr>
            <tr>
                <td>Category:</td>
                <td>
                    <select name="category_id" id="medicineCategory">
                        <option value="">Select Category</option>
                        <?php foreach($categories as $category){ ?>
                            <option value="<?=$category['id']?>" <?php if($editMedicine && $editMedicine['category_id'] == $category['id']){ echo "selected"; } ?>>
                                <?=htmlspecialchars($category['name'])?> (<?=htmlspecialchars($category['category_type'])?>)
                            </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Vendor Name:</td>
                <td><input type="text" name="vendor_name" id="vendorName" value="<?php if($editMedicine){ echo htmlspecialchars($editMedicine['vendor_name']); } ?>"></td>
            </tr>
            <tr>
                <td>Price:</td>
                <td><input type="number" step="0.01" name="price" id="price" value="<?php if($editMedicine){ echo htmlspecialchars($editMedicine['price']); } ?>"></td>
            </tr>
            <tr>
                <td>Availability:</td>
                <td><input type="number" name="availability" id="availability" value="<?php if($editMedicine){ echo htmlspecialchars($editMedicine['availability']); } ?>"></td>
            </tr>
            <tr>
                <td>Description:</td>
                <td><textarea name="description" id="description"><?php if($editMedicine){ echo htmlspecialchars($editMedicine['description']); } ?></textarea></td>
            </tr>
            <tr>
                <td>Image:</td>
                <td><input type="file" name="image" id="image"></td>
            </tr>
        </table>

        <?php if($editMedicine && $editMedicine['image_path'] != ""){ ?>
            <img class="thumb" src="<?=htmlspecialchars(getMedicineImageSrc($editMedicine['image_path']))?>" alt="Medicine Image">
        <?php } ?>

        <span id="medicineError" class="error-text"></span>
        <input class="form-btn" type="submit" name="submit" value="<?php if($editMedicine){ echo "Update"; }else{ echo "Add"; } ?>">
        <?php if($editMedicine){ ?>
            <a class="button-link form-btn" href="admin_medicines.php">Cancel</a>
        <?php } ?>
    </fieldset>
</form>

<h2>Medicine List</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Image</th>
        <th>Name</th>
        <th>Category</th>
        <th>Vendor</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Action</th>
    </tr>

    <?php foreach($medicines as $medicine){ ?>
        <tr>
            <td><?=$medicine['id']?></td>
            <td>
                <?php if($medicine['image_path'] != ""){ ?>
                    <img class="thumb" src="<?=htmlspecialchars(getMedicineImageSrc($medicine['image_path']))?>" alt="Medicine Image">
                <?php }else{ ?>
                    No Image
                <?php } ?>
            </td>
            <td><?=htmlspecialchars($medicine['name'])?></td>
            <td><?=htmlspecialchars($medicine['category_name'])?> (<?=htmlspecialchars($medicine['category_type'])?>)</td>
            <td><?=htmlspecialchars($medicine['vendor_name'])?></td>
            <td><?=$medicine['price']?></td>
            <td><?=$medicine['availability']?></td>
            <td>
                <a href="admin_medicines.php?edit=<?=$medicine['id']?>">EDIT</a> |
                <form class="inline-form" method="post" action="../controller/adminController.php" onsubmit="return confirm('Delete this medicine?')">
                    <input type="hidden" name="action" value="delete_medicine">
                    <input type="hidden" name="id" value="<?=$medicine['id']?>">
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
