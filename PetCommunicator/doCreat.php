<?php
require_once("../pdoConnect.php");

$PetCommName = $_POST["PetCommName"];
$PetCommSex = $_POST["PetCommSex"];
$PetCommCertificateid = $_POST["PetCommCertificateid"];
$PetCommCertificateDate = $_POST["PetCommCertificateDate"];
$PetCommEmail = $_POST["PetCommEmail"];
$PetCommService = $_POST["PetCommService"];
$PetCommApproach = $_POST["PetCommApproach"];
$PetCommFee = $_POST["PetCommFee"];
$PetCommIntroduction = $_POST["PetCommIntroduction"];
$PetCommStatus = $_POST["PetCommStatus"];
$valid = $_POST["valid"];
$now = date('Y-m-d H:i:s');
// 圖檔處理
if ($_FILES["PetCommImg"]["error"] == 0) {
    $filename = $_FILES["PetCommImg"]["name"];
    $fileInfo = pathinfo($filename);
    $extension = $fileInfo["extension"];
    $newFilename = time() . ".$extension";
    echo $newFilename;

    if (move_uploaded_file($_FILES["PetCommImg"]["tmp_name"], "./images/". $newFilename)) {
        
        $sql = "INSERT INTO petcommunicator (
        PetCommName, 
        PetCommSex, 
        PetCommImg, 
        PetCommCertificateid, 
        PetCommCertificateDate, 
        PetCommEmail,
        PetCommService, 
        PetCommApproach, 
        PetCommFee, 
        PetCommIntroduction, 
        PetCommStatus, 
        valid, 
        PetCommCreateDate,
        PetCommCreateUserID,
        PetCommUpdateUserID,
        PetCommUpdateDate
        ) VALUES (
        :PetCommName, 
        :PetCommSex, 
        :PetCommImg, 
        :PetCommCertificateid, 
        :PetCommCertificateDate, 
        :PetCommEmail,
        :PetCommService, 
        :PetCommApproach, 
        :PetCommFee, 
        :PetCommIntroduction, 
        :PetCommStatus, 
        :valid, 
        :PetCommCreateDate,
        :PetCommCreateUserID,
        :PetCommUpdateUserID,
        :PetCommUpdateDate
        )";
    } else {
        echo "上傳圖檔失敗";
    }
} else {
    echo "上傳錯誤代碼：" . $_FILES["PetCommImg"]["error"];
    echo "upload fail!";
}
try {
    $stmt = $dbHost->prepare($sql);

    $stmt->bindParam(':PetCommName', $PetCommName);
    $stmt->bindParam(':PetCommSex', $PetCommSex);
    $stmt->bindParam(':PetCommImg', $newFilename);
    $stmt->bindParam(':PetCommCertificateid', $PetCommCertificateid);
    $stmt->bindParam(':PetCommCertificateDate', $PetCommCertificateDate);
    $stmt->bindParam(':PetCommEmail', $PetCommEmail);
    $stmt->bindParam(':PetCommService', $PetCommService);
    $stmt->bindParam(':PetCommApproach', $PetCommApproach);
    $stmt->bindParam(':PetCommFee', $PetCommFee);
    $stmt->bindParam(':PetCommIntroduction', $PetCommIntroduction);
    $stmt->bindParam(':PetCommStatus', $PetCommStatus);
    $stmt->bindParam(':valid', $valid);
    $stmt->bindParam(':PetCommCreateDate', $now);
    $stmt->bindValue(':PetCommCreateUserID', 'Ben');
    $stmt->bindValue(':PetCommUpdateUserID', 'Ben');
    $stmt->bindValue(':PetCommUpdateDate', $now);

    $stmt->execute();
    $last_id = $dbHost->lastInsertId();

    header("location: petcommunicator.php?id=$last_id");
} catch (PDOException $e) {
    echo "預處理陳述式執行失敗！ <br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    $dbHost = NULL;
    exit;
}
$dbHost = null;
