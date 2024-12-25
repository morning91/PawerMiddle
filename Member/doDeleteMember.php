<?php
require_once("../pdoConnect.php");

if(!isset($_GET["MemberID"])){
    echo "請由正確管道進入";
    exit;
}

$id = $_GET["MemberID"];

$sql = "UPDATE Member SET MemberValid = 0 WHERE MemberID='$id'";

$stmt = $dbHost->prepare($sql);

try{   
    $stmt->execute();
}catch(PDOException $e){
    echo "預處理陳述式執行失敗！ <br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    $db_host = NULL;
    exit;
}


$dbHost = null;

header("location: MemberList.php?p=1&sorter=1");
?>