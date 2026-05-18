<?php
header("Content-Type: application/json");

require_once __DIR__ . "/../../../model/medicineModel.php";

$searchText = trim($_GET["q"] ?? "");
$vendor = trim($_GET["vendor"] ?? "");
$genre = trim($_GET["genre"] ?? "");
$type = trim($_GET["type"] ?? "");

if (!in_array($type, ["", "liquid", "solid"], true)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid medicine type.",
        "medicines" => [],
    ]);
    exit;
}

$medicines = searchMedicines($searchText, $vendor, $genre, $type);

echo json_encode([
    "success" => true,
    "medicines" => $medicines,
]);
