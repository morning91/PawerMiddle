<?php
require_once("../pdoConnect.php");
// var_dump($_GET);
// echo "</br>";
// var_dump($_POST);


if (!isset($_POST["id"])) {
    echo "請循正常管道進入此頁";
    exit;
}

$id = $_POST["id"];
$EventStartTime = $_POST['EventStartTime'] ?? null;
$EventEndTime = $_POST['EventEndTime'] ?? null;
$EventSignStartTime = $_POST['EventSignStartTime'] ?? null;
$EventSignEndTime = $_POST['EventSignEndTime'] ?? null;
$EventTitle = $_POST['EventTitle'] ?? null;
$EventInfo = $_POST['EventInfo'] ?? null;
$EventParticipantLimit = $_POST['EventParticipantLimit'] ?? null;
$VendorID = $_POST['VendorID'] ?? null;
$EventFee = $_POST['EventFee'] ?? null;
$EventPublishStartTime = $_POST['EventPublishStartTime'] ?? null;
$EventPublishEndTime = $_POST['EventPublishEndTime'] ?? null;
$EventStatus = $_POST['EventStatus'] ?? null;
$EventRegion = $_POST['EventRegion'] ?? '';
$EventCity = $_POST['EventCity'] ?? '';
$EventLocation = $_POST['EventLocation'] ?? '';
$now = date('Y-m-d H:i:s');

// 去除千位分隔符並確保數據為浮點數，因為後後台資料庫型別為DECIMAL(10,2)
$EventFee = str_replace(',', '', $EventFee);
$EventFee = is_numeric($EventFee) ? (float)$EventFee : null;
$EventFee = $EventFee !== null ? number_format($EventFee, 2, '.', '') : null;

$sql = "UPDATE OfficialEvent SET EventStartTime=:EventStartTime,
    EventEndTime=:EventEndTime,
    EventSignStartTime=:EventSignStartTime,
    EventSignEndTime=:EventSignEndTime,
    EventEndTime=:EventEndTime, 
    EventTitle = :EventTitle,
    EventInfo=:EventInfo,
    EventParticipantLimit = :EventParticipantLimit,
    VendorID=:VendorID,
    EventFee=:EventFee,
    EventPublishStartTime=:EventPublishStartTime,
    EventPublishEndTime=:EventPublishEndTime,
    EventStatus=:EventStatus,
    EventRegion=:EventRegion,
    EventCity=:EventCity,
    EventLocation=:EventLocation,
    EventUpdateDate = :now 
WHERE EventID = :id";

$stmt = $dbHost->prepare($sql);

try {
    // 傳遞正確的參數對應
    $stmt->execute([
        ":EventStartTime" => $EventStartTime,
        ":EventEndTime" => $EventEndTime,
        ":EventSignStartTime" => $EventSignStartTime,
        ":EventSignEndTime" => $EventSignEndTime,
        ":EventTitle" => $EventTitle,
        ":EventInfo" => $EventInfo,
        ":EventParticipantLimit" => $EventParticipantLimit,
        ":VendorID" => $VendorID,
        ":EventFee" => $EventFee,
        ":EventPublishStartTime" => $EventPublishStartTime,
        ":EventPublishEndTime" => $EventPublishEndTime,
        ":EventStatus" => $EventStatus,
        ":EventRegion" => $EventRegion,
        ":EventCity" => $EventCity,
        ":EventLocation" => $EventLocation,
        ":now" => $now,
        ":id" => $id,
    ]);
    echo "<script>
    alert('活動資訊更新成功');
    window.location.href = 'OfficialEventsList.php?p=1&order=99';
  </script>";
} catch (PDOException $e) {
    echo "預處理陳述執行失敗！<br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    $dbHost = NULL;
    exit;
}
// 處理圖片上傳
if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
    $filename = $_FILES["image"]["name"];
    $tmpName = $_FILES["image"]["tmp_name"];
    $fileInfo = pathinfo($filename);
    $extension = strtolower($fileInfo["extension"]);

    // 檢查文件類型
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($extension, $allowedTypes)) {
        echo "只允許上傳 JPG, JPEG, PNG 或 GIF 文件。";
        exit;
    }
}
// 處理圖片上傳
if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
    $filename = $_FILES["image"]["name"];
    $tmpName = $_FILES["image"]["tmp_name"];
    $fileInfo = pathinfo($filename);
    $extension = strtolower($fileInfo["extension"]);

    // 檢查文件類型
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($extension, $allowedTypes)) {
        echo "只允許上傳 JPG, JPEG, PNG 或 GIF 文件。";
        exit;
    }

    // 生成唯一的文件名以避免覆蓋
    $newFilename = uniqid() . '.' . $extension;
    $uploadPath = "../upload/" . $newFilename;

    if (move_uploaded_file($tmpName, $uploadPath)) {
        $imageUploaded = true;
        $imageUrl = "./upload/" . $newFilename;

        // 插入圖片信息到數據庫
        $sql = "INSERT INTO Image (EventID, ImageName, ImageUrl, ImageUploadDate, ImageType) 
            VALUES (:EventID, :imageName, :imageUrl, :uploadDate, :imageType)";

        try {
            $stmt = $dbHost->prepare($sql);
            $stmt->execute([
                ':EventID' => $id, // 注意要使用正確的 EventID
                ':imageName' => $newFilename,
                ':imageUrl' => $imageUrl,
                ':uploadDate' => date('Y-m-d H:i:s'),
                ':imageType' => $extension
            ]);
            echo "<script>
            alert('活動創建成功');
            window.location.href = 'OfficialEventsList.php';
          </script>";
        } catch (PDOException $e) {
            echo "圖片資訊插入失敗：" . $e->getMessage() . "<br>";
            exit;
        }
    } else {
        echo "圖片上傳失敗！錯誤代碼：" . $_FILES["image"]["error"] . "<br>";
        exit;
    }
} else {
    echo "沒有圖片被上傳或上傳過程中出錯。<br>";
}
