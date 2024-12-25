
 <?php
require_once("../pdoConnect.php");
if (!isset($_POST["PetCommID"])) {
    echo "錯誤";
    exit;
}

$page = $_POST["page"];
$PetCommID = $_POST["PetCommID"];
$valid = $_POST["valid"];
$orderArray = explode(':', $_POST['order']);
$orderID = $orderArray[0];
$orderValue = $orderArray[1];
$PetCommUpdateUserID = "admin";
$PetCommUpdateDate = date('Y-m-d H:i:s');

$sql = "UPDATE petcommunicator SET 
    valid = 1, 
    PetCommStatus = '未刊登',
    delreason =  NULL,
    PetCommUpdateUserID = :PetCommUpdateUserID,
    PetCommUpdateDate = :PetCommUpdateDate
    WHERE PetCommID = :PetCommID";
try {
    $stmt = $dbHost->prepare($sql);
    $stmt->bindParam(':PetCommID', $PetCommID, PDO::PARAM_INT);
    $stmt->bindParam(':PetCommUpdateUserID', $PetCommUpdateUserID);
    $stmt->bindParam(':PetCommUpdateDate', $PetCommUpdateDate);
    $stmt->execute();
    header("location: StatusList.php");
} catch (PDOException $e) {
    echo "預處理陳述式執行失敗！ <br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    $dbHost = NULL;
    exit;
}
$dbHost= NULL;
?>
