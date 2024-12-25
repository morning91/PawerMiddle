<?php
require_once("../pdoConnect.php"); 
$imagePath = ""; 
$uploadDir = "../upload/";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    $title = $_POST["title"];
    $start_time = $_POST["start_time"];//上架開始時間   
    $end_time = $_POST["end_time"];//下架時間
    $article_status=$_POST["flexRadioDefault"];
    $content = $_POST["content"];
    $create_date = date("Y-m-d H:i:s"); //建檔時間
    $create_user_id = 1; 
    $update_date = date("Y-m-d H:i:s"); // 更新時間
    $update_user_id = 1;
    }
        // 送出其他資料
        $sql = "INSERT INTO article_db 
        (ArticleStartTime, ArticleEndTime,ArticleStatus, ArticleTitle, ArticleContent, ArticleCreateDate, ArticleCreateUserID, ArticleUpdateDate, ArticleUpdateUserID) 
    VALUES 
        (:start_time, :end_time, :article_status ,:title, :content, :create_date, :create_user_id, :update_date, :update_user_id)";


try {
$stmt = $dbHost->prepare($sql);
$stmt->bindParam(":start_time", $start_time);
$stmt->bindParam(":end_time", $end_time);
$stmt ->bindParam(":article_status", $article_status);
$stmt->bindParam(":title", $title);
$stmt->bindParam(":content", $content);
$stmt->bindParam(":create_date", $create_date);
$stmt->bindParam(":create_user_id", $create_user_id);
$stmt->bindParam(":update_date", $update_date);
$stmt->bindParam(":update_user_id", $update_user_id);

$stmt->execute();

$articleID=$dbHost->lastInsertID();

echo "文章上架成功！";

    // 圖片上傳
    if ($_FILES["image"]["error"] == 0) {
        $filename=$_FILES["image"]["name"];
        $fileInfo=pathinfo($filename);
        $extension=$fileInfo["extension"];
        $newfilename=time().".$extension";

        if (move_uploaded_file($_FILES["image"]["tmp_name"], "../upload/" . $newfilename)) {
            $now = date("Y-m-d H:i:s");
            $imageUrl = "../upload/" . $newfilename;
            
            $sql = "INSERT INTO image (ImageName,ImageUrl, ImageUploadDate, ImageType, ArticleID) 
            VALUES (:imageName, :imageUrl, :imageUploadDate, :imageType, :articleID)";//關聯
            try {
                $stmtImage = $dbHost->prepare($sql);
                $stmtImage->bindParam(":imageName", $newfilename);
                $stmtImage->bindParam(":imageUrl", $imageUrl);
                $stmtImage->bindParam(":imageUploadDate", $now);
                $stmtImage->bindParam(":imageType", $extension);
                $stmtImage->bindParam(":articleID", $articleID);

                $stmtImage->execute();
                
                echo "圖片資料庫插入成功！";
            } catch (PDOException $e) {
                echo "圖片資料庫插入失敗！<br/>";
                echo "Error: " . $e->getMessage() . "<br/>";
            }
        } else {
            echo "圖片上傳失敗！";
        }
    } else {
        echo "圖片上傳過程中發生錯誤！";
    }

    } catch (PDOException $e) {
        echo "預處理陳述式執行失敗！ <br/>";
        echo "Error: " . $e->getMessage() . "<br/>";
    }


?>