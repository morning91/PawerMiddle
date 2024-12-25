<?php
require_once("../pdoConnect.php");

if (!isset($_GET["id"])) {
    echo "請循正常管道進入此頁";
    exit;
}

$id=$_GET["id"];


$sql = "UPDATE article_db SET ArticleValid = 0 WHERE ArticleID = :id";
$stmt=$dbHost->prepare($sql);

try {
    $stmt = $dbHost->prepare($sql);
    $stmt->bindParam(":id",$id,PDO::PARAM_INT);
    $stmt->execute();

    if($stmt->rowCount()> 0){
        echo"               
        <script> alert('刪除成功！')
        window.location.href = 'ArticleList.php';
                </script>";
    exit();
} }catch (PDOException $e) {
    echo "預處理陳述式執行失敗！ <br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
}

    $dbHost=Null;

 ?>