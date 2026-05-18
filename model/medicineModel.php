<?php
require_once "db.php";

function getAllCategories(): array
{
    global $con;
    $sql = "SELECT id, name, category_type FROM categories ORDER BY category_type, name";
    $result = mysqli_query($con, $sql);

    $categories = [];
    if (!$result) {
        return $categories;
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }

    return $categories;
}

function getAllVendors(): array
{
    global $con;
    $sql = "SELECT DISTINCT vendor_name FROM medicines ORDER BY vendor_name";
    $result = mysqli_query($con, $sql);

    $vendors = [];
    if (!$result) {
        return $vendors;
    }

    while ($row = mysqli_fetch_assoc($result)) {
        $vendors[] = $row["vendor_name"];
    }

    return $vendors;
}

function searchMedicines(string $searchText, string $vendor, string $genre, string $type): array
{
    global $con;

    $sql = "SELECT
                m.id,
                m.name,
                m.vendor_name,
                m.price,
                m.availability,
                m.description,
                m.image_path,
                c.name AS category_name,
                c.category_type
            FROM medicines m
            INNER JOIN categories c ON c.id = m.category_id
            WHERE (? = '' OR m.name LIKE CONCAT('%', ?, '%'))
              AND (? = '' OR m.vendor_name = ?)
              AND (? = '' OR CAST(c.id AS CHAR) = ? OR c.name = ?)
              AND (? = '' OR c.category_type = ?)
            ORDER BY c.category_type, c.name, m.name";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param(
        $stmt,
        "sssssssss",
        $searchText,
        $searchText,
        $vendor,
        $vendor,
        $genre,
        $genre,
        $genre,
        $type,
        $type
    );
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $medicines = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $medicines[] = $row;
    }

    mysqli_stmt_close($stmt);
    return $medicines;
}
