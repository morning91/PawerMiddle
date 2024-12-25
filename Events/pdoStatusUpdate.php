<?php
require_once("../pdoConnect.php");

$updateStatus = "";
$now = date('Y-m-d H:i:s'); // 取得當前時間

if (!isset($_POST["event_id"]) || !isset($_POST["status"])) {
    echo "請循正常管道進入此頁";
    exit;
}

$id = $_POST["event_id"];
$updateStatus = $_POST['status'];

$sql = "UPDATE OfficialEvent SET EventStatus = :updateStatus, EventUpdateDate = :now WHERE EventID = :id";
$stmt = $dbHost->prepare($sql);

try {
    $stmt->execute([
        ":updateStatus" => $updateStatus,
        ":now" => $now,
        ":id" => $id,
    ]);
    echo "操作成功"; // 這裡用於 AJAX 的回應
} catch (PDOException $e) {
    echo "預處理陳述執行失敗！<br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    $dbHost = NULL;
    exit;
}

$dbHost = NULL;
