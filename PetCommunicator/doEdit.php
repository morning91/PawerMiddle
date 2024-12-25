<?php
require_once("../pdoConnect.php");

if (!isset($_POST["PetCommName"])) {
    echo "錯誤";
    exit;
}

$imgSql='';

if ($_FILES["PetCommImg"]["error"] == 0) {
    $filename = $_FILES["PetCommImg"]["name"];
    $fileInfo = pathinfo($filename);
    $extension = $fileInfo["extension"];
    $newFilename = time() . ".$extension";
    if (move_uploaded_file($_FILES["PetCommImg"]["tmp_name"], "./images/" . $newFilename)) {
        $imgSql = ", PetCommImg = :PetCommImg";
    } else {
        echo "圖檔轉移失敗";
    }
} else {
    echo "上傳錯誤代碼：" . $_FILES["PetCommImg"]["error"];
    echo "upload fail!";
}
$PetCommID = $_POST["PetCommID"];
$PetCommName = $_POST["PetCommName"];
$PetCommSex = $_POST["PetCommSex"];
$PetCommCertificateid = $_POST["PetCommCertificateid"];
$PetCommCertificateDate = $_POST["PetCommCertificateDate"];
$PetCommService = $_POST["PetCommService"];
$PetCommApproach = $_POST["PetCommApproach"];
$PetCommFee = $_POST["PetCommFee"];
$PetCommEmail = $_POST["PetCommEmail"];
$PetCommStatus = $_POST["PetCommStatus"];
$PetCommIntroduction = $_POST["PetCommIntroduction"];
$valid = $_POST["valid"];
$PetCommUpdateUserID = "admin";
$PetCommUpdateDate = date('Y-m-d H:i:s');

$sql = "UPDATE petcommunicator SET 
    PetCommName = :PetCommName,
    PetCommSex = :PetCommSex,
    PetCommCertificateid = :PetCommCertificateid,
    PetCommCertificateDate = :PetCommCertificateDate,
    PetCommService = :PetCommService,
    PetCommApproach = :PetCommApproach,
    PetCommFee = :PetCommFee,
    PetCommEmail = :PetCommEmail,
    PetCommStatus = :PetCommStatus,
    PetCommIntroduction = :PetCommIntroduction,
    valid = :valid,
    PetCommUpdateDate = :PetCommUpdateDate" 
    .$imgSql."
    WHERE PetCommID = :PetCommID";
try {
    $stmt = $dbHost->prepare($sql);
    $stmt->bindParam(':PetCommName', $PetCommName);
    $stmt->bindParam(':PetCommSex', $PetCommSex);
    if (isset($newFilename)) {
        $stmt->bindParam(':PetCommImg', $newFilename);
    }
    $stmt->bindParam(':PetCommCertificateid', $PetCommCertificateid);
    $stmt->bindParam(':PetCommCertificateDate', $PetCommCertificateDate);
    $stmt->bindParam(':PetCommService', $PetCommService);
    $stmt->bindParam(':PetCommApproach', $PetCommApproach);
    $stmt->bindParam(':PetCommFee', $PetCommFee);
    $stmt->bindParam(':PetCommEmail', $PetCommEmail);
    $stmt->bindParam(':PetCommStatus', $PetCommStatus);
    $stmt->bindParam(':PetCommIntroduction', $PetCommIntroduction);
    $stmt->bindParam(':valid', $valid);
    $stmt->bindParam(':PetCommUpdateDate', $PetCommUpdateDate);
    $stmt->bindParam(':PetCommID', $PetCommID, PDO::PARAM_INT);

    $stmt->execute();
    header("location: petcommunicator.php?id=$PetCommID");
} catch (PDOException $e) {
    echo "預處理陳述式執行失敗！ <br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    $dbHost = NULL;
    exit;
}
$dbHost = null;
