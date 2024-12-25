<?php
session_start();
require_once("../pdoConnect.php");


if (!isset($_POST["id"])) {
    echo "請循正常管道進入此頁";
    exit;
}

$id = $_POST["id"];

// $sql = "DELETE FROM Discount WHERE ID = :id";
$sql = "UPDATE Discount SET IsValid = 0 WHERE ID = :id ";
$stmt = $dbHost->prepare($sql);

try {
    $stmt->execute([':id' => $id]);
    $_SESSION['SESmessage'] = '刪除成功'; //試改用存訊息至SESSION，然後到前端重讀
    echo json_encode(['status' => 1, 'message' => '刪除成功']);
} catch (PDOException $e) {
    $_SESSION['SESmessage'] = '刪除失敗'; //試改用存訊息至SESSION，然後到前端重讀
    echo json_encode(['status' => 0, 'message' => 'Database error: ' . $e->getMessage()]);
}
