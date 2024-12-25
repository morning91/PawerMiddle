<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("../pdoConnect.php");

$id = $_POST["id"];
$orderStatus = $_POST["orderStatus"];

$sql = "UPDATE `Order` 
        SET OrderDeliveryStatus = :orderStatus
        WHERE OrderID = :id";

$stmt = $dbHost->prepare($sql);

try{
    $stmt->execute([
        ':orderStatus' => $orderStatus,
        ':id' => $id
    ]);
    echo "<script>
        alert('配送資訊修改成功！');
        window.location.href = 'OrderList.php';
        </script>";
    exit;
}catch(PDOException $e){
    echo "預處理陳述式執行失敗！ <br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    $db_host = NULL;
    exit;
}

header("Location: Order.php?OrderID=$id");
$dbHost = null;
