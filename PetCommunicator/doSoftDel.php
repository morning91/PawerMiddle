<?php
require_once("../pdoConnect.php");
if (!isset($_POST["PetCommID"])) {
    echo "錯誤";
    exit;
}
if (isset($_POST["page"])) {
    $page = $_POST["page"];
    $orderArray = explode(':', $_POST['order']);
    $orderID = $orderArray[0];
    $orderValue = $orderArray[1];
}
$PetCommID = $_POST["PetCommID"];
$delreason = $_POST["delreason"];
$PetCommUpdateUserID = "admin";
$PetCommUpdateDate = date('Y-m-d H:i:s');

$sql = "UPDATE petcommunicator SET 
    valid = 0, 
    PetCommStatus = '未刊登',
    delreason =  :delreason,
    PetCommUpdateUserID = :PetCommUpdateUserID,
    PetCommUpdateDate = :PetCommUpdateDate
    WHERE PetCommID = :PetCommID";
try {
    $stmt = $dbHost->prepare($sql);
    $stmt->bindParam(':PetCommID', $PetCommID, PDO::PARAM_INT);
    $stmt->bindParam(':delreason', $delreason);
    $stmt->bindParam(':PetCommUpdateUserID', $PetCommUpdateUserID);
    $stmt->bindParam(':PetCommUpdateDate', $PetCommUpdateDate);
    $stmt->execute();
    header("location: SoftDelList.php");
} catch (PDOException $e) {
    echo "預處理陳述式執行失敗！ <br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    $dbHost = NULL;
    exit;
}
$dbHost = NULL;
