<?php
    require_once('admin_header.php');
    require_once('../model/adminModel.php');

    $categories = getAllCategories();
    $editCategory = null;

    if(isset($_GET['edit'])){
        $editCategory = getCategoryById((int)$_GET['edit']);
    }
?>

<h1>Category Management</h1>

<form method="post" action="../controller/adminController.php" onsubmit="return validateCategory()">
    <fieldset>
        <legend><?php if($editCategory){ echo "Edit Category"; }else{ echo "Add Category"; } ?></legend>

        <input type="hidden" name="action" value="<?php if($editCategory){ echo "update_category"; }else{ echo "add_category"; } ?>">
        <?php if($editCategory){ ?>
            <input type="hidden" name="id" value="<?=$editCategory['id']?>">
        <?php } ?>

        <table class="form-table">
            <tr>
                <td>Name:</td>
                <td><input type="text" name="name" id="categoryName" value="<?php if($editCategory){ echo htmlspecialchars($editCategory['name']); } ?>"></td>
            </tr>
            <tr>
                <td>Type:</td>
                <td>
                    <select name="category_type" id="categoryType">
                        <option value="">Select Type</option>
                        <option value="liquid" <?php if($editCategory && $editCategory['category_type'] == 'liquid'){ echo "selected"; } ?>>Liquid</option>
                        <option value="solid" <?php if($editCategory && $editCategory['category_type'] == 'solid'){ echo "selected"; } ?>>Solid</option>
                    </select>
                </td>
            </tr>
        </table>
        <span id="categoryError" class="error-text"></span>
        <input class="form-btn" type="submit" name="submit" value="<?php if($editCategory){ echo "Update"; }else{ echo "Add"; } ?>">
        <?php if($editCategory){ ?>
            <a class="button-link form-btn" href="admin_categories.php">Cancel</a>
        <?php } ?>
    </fieldset>
</form>

<h2>Category List</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Type</th>
        <th>Created At</th>
        <th>Action</th>
    </tr>

    <?php foreach($categories as $category){ ?>
        <tr>
            <td><?=$category['id']?></td>
            <td><?=htmlspecialchars($category['name'])?></td>
            <td><?=htmlspecialchars($category['category_type'])?></td>
            <td><?=$category['created_at']?></td>
            <td>
                <a href="admin_categories.php?edit=<?=$category['id']?>">EDIT</a> |
                <form class="inline-form" method="post" action="../controller/adminController.php" onsubmit="return confirm('Delete this category?')">
                    <input type="hidden" name="action" value="delete_category">
                    <input type="hidden" name="id" value="<?=$category['id']?>">
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
