<?php
require_once("../pdoConnect.php");


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $imageUploaded = false;
    // $imageUrl = '';
    // // 處理事件創建
    $EventStartTime = $_POST['EventStartTime'];
    $EventEndTime = $_POST['EventEndTime'];
    $EventSignStartTime = $_POST['EventSignStartTime'];
    $EventSignEndTime = $_POST['EventSignEndTime'];
    $EventTitle = $_POST['EventTitle'];
    $EventInfo = $_POST['EventInfo'];
    $EventParticipantLimit = $_POST['EventParticipantLimit'];
    $VendorID = $_POST['VendorID'];
    $EventFee = $_POST['EventFee'];
    $EventPublishStartTime = $_POST['EventPublishStartTime'];
    $EventPublishEndTime = $_POST['EventPublishEndTime'];
    $EventStatus = $_POST['EventStatus'] ?? null;
    $EventRegion = $_POST['EventRegion'] ?? '';
    $EventCity = $_POST['EventCity'] ?? '';
    $EventLocation = $_POST['EventLocation'] ?? '';
    $now = date('Y-m-d H:i:s');

    $sql = "INSERT INTO OfficialEvent (EventStartTime, EventEndTime, EventSignStartTime, EventSignEndTime, EventTitle, EventInfo, EventParticipantLimit, VendorID, EventFee, EventPublishStartTime, EventPublishEndTime, EventStatus, EventRegion, EventCity, EventLocation, EventCreateDate, EventCreateUserID, EventUpdateDate, EventUpdateUserID)
     VALUES (:EventStartTime, :EventEndTime, :EventSignStartTime, :EventSignEndTime, :EventTitle, :EventInfo, :EventParticipantLimit, :VendorID, :EventFee, :EventPublishStartTime, :EventPublishEndTime, :EventStatus, :EventRegion, :EventCity, :EventLocation,:now, 1, :now, 1)";

    $stmt = $dbHost->prepare($sql);

    try {
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
        ]);
        echo "事件創建成功<br>";
        $newEventID = $dbHost->lastInsertId();
    } catch (PDOException $e) {
        echo "事件創建失敗：" . $e->getMessage() . "<br>";
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

        // 生成唯一的文件名以避免覆蓋
        $newFilename = uniqid() . '.' . $extension;
        $uploadPath = "../upload/" . $newFilename;

        if (move_uploaded_file($tmpName, $uploadPath)) {
            $imageUploaded = true;
            $imageUrl = "./upload/" . $newFilename;

            // 插入圖片信息到數據庫
            $sql = "INSERT INTO Image (EventID,ImageName, ImageUrl, ImageUploadDate, ImageType) 
                    VALUES (:EventID,:imageName, :imageUrl, :uploadDate, :imageType)";

            try {
                $stmt = $dbHost->prepare($sql);
                $stmt->execute([
                    ':EventID' => $newEventID,
                    ':imageName' => $newFilename,
                    ':imageUrl' => $imageUrl,
                    ':uploadDate' => date('Y-m-d H:i:s'),
                    ':imageType' => $extension
                ]);
                echo "<script>
                        alert('活動創建成功');
                            window.location.href = 'OfficialEventsList.php?p=1&order=99';
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
        // echo "沒有圖片被上傳或上傳過程中出錯。<br>";
    }
}
$dbHost = NULL;
