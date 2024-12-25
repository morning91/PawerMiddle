<?php

require_once("../pdoConnect.php");

if (!isset($_POST["productPicName"])) {
    header("location: create-product.php");
    exit;
}

$productPicName = $_POST["productPicName"];


if ($_FILES["pic"]["error"] == 0) {
    $filename = $_FILES["pic"]["name"];
    $fileInfo = pathinfo($filename);
    $extension = $fileInfo["extension"];
    
    $newFilename = time() . ".$extension";


    
    if (move_uploaded_file($_FILES["pic"]["tmp_name"], "./ProductPicUpLoad/" . $newFilename)) {
        $now = date('Y-m-d H:i:s');

        
        exit;
        $sql = "INSERT INTO product (product_name, product_img, product_create_date) VALUES (:product_name, :product_img, :created_at)";
        $stmt = $dbHost->prepare($sql);
        
        
        $stmt->bindParam(':title', $productPicName);
        $stmt->bindParam(':name', $newFilename);
        $stmt->bindParam(':created_at', $now);

        if ($stmt->execute()) {
            header("location: ProductList.php");
            exit;
        } else {
            echo "Error: " . $stmt->errorInfo()[2];
        }

        echo "upload success!";
    } else {
        echo "upload fail!";
    }
}
