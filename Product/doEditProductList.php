<?php
require_once("../pdoConnect.php");

if (!isset($_POST["product_id"])) {
    echo "錯誤";
    exit;
}

$product_id = $_POST["product_id"];

// 先查詢當前商品的圖片名稱
$sqlCheck = "SELECT product_img FROM product WHERE product_id = :product_id";
$stmtCheck = $dbHost->prepare($sqlCheck);
$stmtCheck->bindParam(':product_id', $product_id, PDO::PARAM_INT);
$stmtCheck->execute();
$beforeImg = $stmtCheck->fetch(PDO::FETCH_ASSOC);

if ($beforeImg) {
    $product_img = $beforeImg['product_img']; // 獲取當前圖片名稱
} else {
    echo "找不到商品資訊";
    exit;
}

// 獲取其他商品資訊做編輯，也可不編輯
$product_status = $_POST["product_status"];
$product_name = $_POST["product_name"];
$product_brand = $_POST["product_brand"];
$product_category_name = $_POST["product_category_name"];
$product_sub_category = $_POST["product_sub_category"];
$product_origin_price = $_POST["product_origin_price"];
$product_sale_price = $_POST["product_sale_price"];
$product_stock = $_POST["product_stock"];
$product_start_time = $_POST["product_start_time"];
$product_end_time = $_POST["product_end_time"];
$product_info = $_POST["product_info"];
$product_update_date = date('Y-m-d H:i:s');


$sqlUpdate = "UPDATE product SET 
        product_status = :product_status,
        product_name = :product_name,
        product_img = :product_img,
        product_brand = :product_brand,
        product_category_name = :product_category_name,
        product_sub_category = :product_sub_category,
        product_origin_price = :product_origin_price,
        product_sale_price = :product_sale_price,
        product_stock = :product_stock,
        product_start_time = :product_start_time,
        product_end_time = :product_end_time,
        product_info = :product_info,
        product_update_date = :product_update_date
        WHERE product_id = :product_id";

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
} elseif ($_FILES["pic"]["error"] == UPLOAD_ERR_NO_FILE) {
    // 如果沒有上傳新圖片，保持原有圖片不變 UPLOAD_ERR_NO_FILE 是PHP 的錯誤代碼 代表 沒有文件被上傳
} else {
    echo "檔案上傳錯誤，錯誤代碼：" . $_FILES["pic"]["error"];
    exit;
}

try {
    $stmtUpdate = $dbHost->prepare($sqlUpdate);

    $stmtUpdate->bindParam(':product_status', $product_status);
    $stmtUpdate->bindParam(':product_name', $product_name);
    $stmtUpdate->bindParam(':product_img', $product_img);
    $stmtUpdate->bindParam(':product_brand', $product_brand);
    $stmtUpdate->bindParam(':product_category_name', $product_category_name);
    $stmtUpdate->bindParam(':product_sub_category', $product_sub_category);
    $stmtUpdate->bindParam(':product_origin_price', $product_origin_price);
    $stmtUpdate->bindParam(':product_sale_price', $product_sale_price);
    $stmtUpdate->bindParam(':product_stock', $product_stock);
    $stmtUpdate->bindParam(':product_start_time', $product_start_time);
    $stmtUpdate->bindParam(':product_end_time', $product_end_time);
    $stmtUpdate->bindParam(':product_info', $product_info);
    $stmtUpdate->bindParam(':product_update_date', $product_update_date);
    $stmtUpdate->bindParam(':product_id', $product_id, PDO::PARAM_INT);

    $stmtUpdate->execute();

    header("location: Product.php?product_id=$product_id");
} catch (PDOException $e) {
    echo "預處理陳述式執行失敗！ <br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    $dbHost = NULL;
    exit;
}

$dbHost = null;
