<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    require_once __DIR__ . "/../model/userModel.php";

    $autoLogin = false;

    if (isset($_COOKIE["remember_token"])) {
        $parts = explode(":", $_COOKIE["remember_token"], 2);

        if (count($parts) === 2) {
            [$cookieUserId, $cookieRawToken] = $parts;
            $tokenHash = hash("sha256", $cookieRawToken);
            $user = getUserByRememberToken((int)$cookieUserId, $tokenHash);

            if ($user) {
                session_regenerate_id(true);
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["name"] = $user["name"];
                $_SESSION["role"] = $user["role"];
                $_SESSION["email"] = $user["email"];

                $newRaw = bin2hex(random_bytes(32));
                $newHash = hash("sha256", $newRaw);
                $expiry = time() + (86400 * 7);
                saveRememberToken($user["id"], $newHash, $expiry);
                setcookie("remember_token", $user["id"] . ":" . $newRaw, [
                    "expires"  => $expiry,
                    "path"     => "/",
                    "httponly" => true,
                    "samesite" => "Lax",
                ]);

                $autoLogin = true;
            }
        }
    }

    if (!$autoLogin) {
        header("Location: login.php");
        exit;
    }
}

require_once __DIR__ . "/../model/medicineModel.php";

$categories = getAllCategories();
$vendors = getAllVendors();
$medicines = searchMedicines("", "", "", "");
$liquidCategories = [];
$solidCategories = [];

foreach ($categories as $category) {
    if ($category["category_type"] === "liquid") {
        $liquidCategories[] = $category;
    } else {
        $solidCategories[] = $category;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="../asset/css/style1.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <img class="logo" src="../asset/logo.png" alt="Online Medicine Shop">
            <div class="nav">
                <a href="profile.php">Profile</a>
                <a href="../controller/logout.php">Logout</a>
            </div>
        </div>

        <div class="content">
            <h1>Welcome, <?= htmlspecialchars($_SESSION["name"]) ?></h1>
            <p>Email: <?= htmlspecialchars($_SESSION["email"]) ?></p>
            <p>Role: <?= htmlspecialchars($_SESSION["role"]) ?></p>

            <hr>

            <h2>Medicine Categories</h2>

            <div class="filter-section">
                <h3>Type</h3>
                <div class="button-list">
                    <button type="button" class="type-btn active-filter" data-type="">All</button>
                    <button type="button" class="type-btn" data-type="liquid">Liquid</button>
                    <button type="button" class="type-btn" data-type="solid">Solid</button>
                </div>
            </div>

            <div class="filter-section">
                <h3>Genre</h3>
                <div class="button-list">
                    <button type="button" class="category-btn active-filter" data-category="">All Genres</button>
                </div>
            </div>

            <div class="category-area">
                <div class="category-box">
                    <h3>Liquid</h3>
                    <div class="button-list">
                        <?php foreach ($liquidCategories as $category): ?>
                            <button type="button" class="category-btn" data-category="<?= htmlspecialchars($category["id"]) ?>">
                                <?= htmlspecialchars($category["name"]) ?>
                            </button>
                        <?php endforeach; ?>
                        <?php if (empty($liquidCategories)): ?>
                            <p class="empty-small">No liquid categories.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="category-box">
                    <h3>Solid</h3>
                    <div class="button-list">
                        <?php foreach ($solidCategories as $category): ?>
                            <button type="button" class="category-btn" data-category="<?= htmlspecialchars($category["id"]) ?>">
                                <?= htmlspecialchars($category["name"]) ?>
                            </button>
                        <?php endforeach; ?>
                        <?php if (empty($solidCategories)): ?>
                            <p class="empty-small">No solid categories.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <h2>Browse Medicines</h2>

            <div class="search-area">
                <label>
                    Search Medicine:
                    <input type="text" id="searchText" placeholder="Search by medicine name">
                </label>

                <label>
                    Vendor:
                    <select id="vendorFilter">
                        <option value="">All Vendors</option>
                        <?php foreach ($vendors as $vendor): ?>
                            <option value="<?= htmlspecialchars($vendor) ?>"><?= htmlspecialchars($vendor) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    Genre:
                    <select id="genreFilter">
                        <option value="">All Genres</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars($category["id"]) ?>">
                                <?= htmlspecialchars($category["name"]) ?> (<?= htmlspecialchars($category["category_type"]) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <button type="button" id="clearFilters">Clear Filters</button>
            </div>

            <div id="medicineList" class="medicine-grid">
                <?php if (empty($medicines)): ?>
                    <p class="empty-message">No medicines found.</p>
                <?php endif; ?>

                <?php foreach ($medicines as $medicine): ?>
                    <div class="medicine-card">
                        <h3><?= htmlspecialchars($medicine["name"]) ?></h3>
                        <p><strong>Vendor:</strong> <?= htmlspecialchars($medicine["vendor_name"]) ?></p>
                        <p><strong>Genre:</strong> <?= htmlspecialchars($medicine["category_name"]) ?></p>
                        <p><strong>Type:</strong> <?= htmlspecialchars($medicine["category_type"]) ?></p>
                        <p><strong>Price:</strong> <?= htmlspecialchars($medicine["price"]) ?> Tk</p>
                        <p>
                            <strong>Availability:</strong>
                            <?= ((int)$medicine["availability"] > 0) ? "Available" : "Out of stock" ?>
                            (<?= htmlspecialchars($medicine["availability"]) ?>)
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="footer">
            Copyright &copy; <?= date('Y') ?>
        </div>
    </div>
    <script src="../asset/js/homeMedicines.js"></script>
</body>
</html>
