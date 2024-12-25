<?php
require_once("../pdoConnect.php");
if(!isset($_POST["product_id"])){
    echo "錯誤";
    exit;
    }

$page = $_POST["page"];
$product_id = $_POST["product_id"];
$product_update_date = date('Y-m-d H:i:s');
$orderArray = explode(':', $_POST['order']);
$orderID = $orderArray[0];
$orderValue = $orderArray[1];

$sql="UPDATE product SET product_valid = 0, product_status = '已上架',product_name='', product_update_date=:product_update_date WHERE product_id= :product_id";

try {
    $stmt = $dbHost->prepare($sql);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->bindParam(':product_update_date', $product_update_date);
    $stmt->execute();

} catch (PDOException $e) {
    echo "預處理陳述式執行失敗！ <br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    $dbHost = NULL;
    exit;
}



if(isset($_POST['order'])){
    header("location: ProductList.php?p=$page&order=$orderID:$orderValue");
    exit;
}else{
    header("location: ProductList.php");
}
?>