<?php
require_once("../pdoConnect.php");

if (!isset($_POST["ArticleID"])) {
    echo "錯誤";
    exit;
}

$ArticleID = $_POST["ArticleID"];
$title = $_POST["title"];
$start_time = $_POST["start_time"]; // 上架開始時間   
$end_time = $_POST["end_time"]; // 下架時間
$article_status = $_POST["status"];
$content = $_POST["content"];


$update_date = date("Y-m-d H:i:s"); // 更新時間
$update_user_id = 1;
$imageUrl = $_POST["update_image"];

if (!empty($_FILES['image']['name'])) {
    $image = $_FILES['image']['name'];
    $extension = pathinfo($image, PATHINFO_EXTENSION);
    $newfilename = time() . "." . $extension;
    $imageUrl = "../upload/" . $newfilename;

    // 上傳
    if (move_uploaded_file($_FILES['image']['tmp_name'], $imageUrl)) {
        $ImageUploadDate = date("Y-m-d H:i:s");
        $ImageType = $extension; // 設定圖片類型

        // 圖片是否存在
        $checkImageSql = "SELECT COUNT(*) FROM image WHERE ArticleID = ?";
        $checkImageStmt = $dbHost->prepare($checkImageSql);
        $checkImageStmt->execute([$ArticleID]);
        $imageCount = $checkImageStmt->fetchColumn();

        if ($imageCount > 0) {
            // 有圖片→更新資料庫
            $imageSql = "UPDATE image SET ImageUrl = ?, ImageUploadDate = ?, ImageType = ? WHERE ArticleID = ?";
            $imageStmt = $dbHost->prepare($imageSql);
            $imageStmt->execute([$imageUrl, $ImageUploadDate, $ImageType, $ArticleID]);
        } else {
            // 沒圖片→插入資料庫
            $imageSql = "INSERT INTO image (ArticleID, ImageName, ImageUrl, ImageUploadDate, ImageType) VALUES (?, ?, ?, ?, ?)";
            $imageStmt = $dbHost->prepare($imageSql);
            $imageStmt->execute([$ArticleID, $newfilename, $imageUrl, $ImageUploadDate, $ImageType]);
        }
    } else {
        echo "圖片上傳失敗";
        exit;
    }
}

$sql = "UPDATE article_db 
SET 
    ArticleTitle = :title,
    ArticleStartTime = :start_time,
    ArticleEndTime = :end_time,
    ArticleStatus = :article_status,
    ArticleContent = :content,
    ArticleUpdateDate = :update_date,
    ArticleUpdateUserID = :update_user_id
WHERE 
    ArticleID = :ArticleID";

try {
    $stmt = $dbHost->prepare($sql);
    $stmt->bindParam(":ArticleID", $ArticleID);
    $stmt->bindParam(":start_time", $start_time);
    $stmt->bindParam(":end_time", $end_time);
    $stmt->bindParam(":article_status", $article_status);
    $stmt->bindParam(":title", $title);
    $stmt->bindParam(":content", $content);
    $stmt->bindParam(":update_date", $update_date);
    $stmt->bindParam(":update_user_id", $update_user_id);
    
    $stmt->execute();

    echo "<script>alert('文章更新成功！'); window.location.href = 'ArticleList.php';</script>";
} catch (PDOException $e) {
    echo "更新文章失敗！ <br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
}

$dbHost = null;
?>