<?php
require_once("../pdoConnect.php");

// 檢查是否有 EventID 傳入
if (!isset($_GET["id"])) {
    echo "請循正常管道進入此頁";
    exit;
}

// 獲取 EventID
$id = $_GET["id"];
$now = date('Y-m-d H:i:s');

// 更新活動狀態的 SQL 語句
$sql = "UPDATE OfficialEvent SET EventValid = 0, EventUpdateDate = :now WHERE EventID = :id";
$stmt = $dbHost->prepare($sql);

try {
    // 執行更新操作
    $stmt->execute([
        ":now" => $now,
        ":id" => $id,
    ]);

    // 頁面重定向至 OfficialEventsList.php
    echo "<script>
                        alert('刪除成功');
                            window.location.href = 'OfficialEventsList.php';
                      </script>";

    // header("Location: OfficialEventsList.php");
    exit; // 確保腳本執行結束

} catch (PDOException $e) {
    echo "更新失敗！<br/>";
    echo "錯誤信息: " . $e->getMessage() . "<br/>";
    exit;
}

// 結束連線
$dbHost = NULL;
