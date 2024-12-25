<?php

require_once("../pdoConnect.php");

// 檢查商品名稱是否存在
if (!isset($_POST["product_name"])) {
    echo "請循正常管道進入此頁";
    exit;
}

$productName = $_POST["product_name"];
if (empty($productName)) {
    echo "商品名稱不能為空";
    exit;
}

// 檢查商品是否已存在
$sqlCheck = "SELECT * FROM product WHERE product_name = :product_name";
$stmt = $dbHost->prepare($sqlCheck);
$stmt->bindParam(':product_name', $productName);
$stmt->execute();
$productCount = $stmt->rowCount();

if ($productCount > 0) {
    $msg = "此商品名稱已存在，請更改商品名稱以及確認是否重複新增";
    echo "<script>alert('$msg'); window.history.back();</script>";
    return;
}

// 加入到資料庫
$sql = "INSERT INTO product 
(product_name, 
product_brand, 
product_category_name,
product_sub_category,
product_status,
product_origin_price, 
product_sale_price, 
product_stock, 
product_start_time,
product_end_time,
product_img, 
product_info,
product_create_date,
product_update_date)
        VALUES 
(:product_name, 
:product_brand, 
:product_category_name,
:product_sub_category,
:product_status,
:product_origin_price, 
:product_sale_price, 
:product_stock, 
:product_start_time,
:product_end_time,
:product_img, 
:product_info,
:product_create_date,
:product_update_date)";

$product_brand = $_POST["product_brand"];
$product_category_name = $_POST["product_category_name"];
$product_sub_category = $_POST["product_sub_category"];
$product_status = $_POST["product_status"];
$product_origin_price = $_POST["product_origin_price"];
$product_sale_price = $_POST["product_sale_price"];
$product_stock = $_POST["product_stock"];
$product_start_time = date('Y-m-d H:i:s', strtotime($_POST["product_start_time"]));
$product_end_time = date('Y-m-d H:i:s', strtotime($_POST["product_end_time"]));
$product_img = ""; // 預設為空，待上傳後給值
$product_info = $_POST["product_info"];
$now = date('Y-m-d H:i:s');

// 圖片上傳
if (isset($_FILES["pic"]) && $_FILES["pic"]["error"] == 0) {
    $filename = $_FILES["pic"]["name"];
    $fileInfo = pathinfo($filename);
    $extension = $fileInfo["extension"];
    $newFilename = time() . ".$extension";

    if (move_uploaded_file($_FILES["pic"]["tmp_name"], "./ProductPicUpLoad/" . $newFilename)) {
        $product_img = $newFilename; // 更新圖片檔名
    } else {
        echo "上傳失敗！";
        exit;
    }
} else {
    echo "檔案上傳錯誤，錯誤代碼：" . $_FILES["pic"]["error"];
    exit;
}

// 插入資料到資料庫
$stmt = $dbHost->prepare($sql);
$stmt->bindParam(':product_name', $productName);
$stmt->bindParam(':product_brand', $product_brand);
$stmt->bindParam(':product_category_name', $product_category_name);
$stmt->bindParam(':product_sub_category', $product_sub_category);
$stmt->bindParam(':product_status', $product_status);
$stmt->bindParam(':product_origin_price', $product_origin_price);
$stmt->bindParam(':product_sale_price', $product_sale_price);
$stmt->bindParam(':product_stock', $product_stock);
$stmt->bindParam(':product_start_time', $product_start_time);
$stmt->bindParam(':product_end_time', $product_end_time);
$stmt->bindParam(':product_img', $product_img);
$stmt->bindParam(':product_info', $product_info);
$stmt->bindParam(':product_create_date', $now);
$stmt->bindParam(':product_update_date', $now);

if ($stmt->execute()) {
    $last_id = $dbHost->lastInsertId();
    $success = "新增成功, id 為 $last_id";
    echo "<script>alert('$success'); 
    setTimeout(function() { window.location.href = 'ProductList.php'; }, 0);</script>";
    exit;
} else {
    echo "Error: " . $stmt->errorInfo()[2];
}

?>
