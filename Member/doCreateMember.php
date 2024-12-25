<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("../pdoConnect.php");


$name = $_POST["name"];
$account = $_POST["account"];
$password = $_POST["password"];
$rePassword = $_POST["repassword"];
// switch($_POST["level"]){
//             case "銅":$level = 1;break; 
//             case "銀":$level = 2;break;
//             case "金":$level = 3;break;
//         };
$birth = $_POST["birth"] ? $_POST["birth"] : null;
$email = $_POST["email"];
$phone = $_POST["phone"];
$address = $_POST["address"];
$gender = $_POST["gender"];

$now = date('Y-m-d H:i:s');

// 從前台進入不會有這兩個選項
// $valid = $_POST["valid"];
// $blacklist = $_POST["blacklist"];

$tel = isset($_POST["tel"]) ? $_POST["tel"] : ""; // 可null
$nickname = isset($_POST["nickname"]) ? $_POST["nickname"] : ""; // 可null
// 從 POST 請求中獲取表單資料
// 檢查是否是透過繳交表單進入此頁
$errorMsg = [];
if(empty($account))$errorMsg[] = "帳號不得為空";
if(empty($name))$errorMsg[] = "名子不得為空";
if(empty($password))$errorMsg[] = "密碼不得為空";
if($rePassword != $password)$errorMsg[] = "兩次密碼不同，請重新輸入";
if(empty($phone))$errorMsg[] = "電話不得為空";

// if(empty($address))$errorMsg[] = "地址不得為空";
// if(empty($gender))$errorMsg[] = "性別不得為空";
// if(empty($birth))$errorMsg[] = "生日不得為空";
// if(empty($valid))$errorMsg[] = "有效會員不得為空";
// if(empty($blacklist))$errorMsg[] = "黑名單不得為空";
// if(empty($email))$errorMsg[] = "電子郵件不得為空";

$password = md5($password); // 加密密碼

$sqlCheck = "SELECT * FROM `Member` WHERE MemberAccount = '$account'";
$stmt = $dbHost->prepare($sqlCheck);
try{
    $stmt->execute();
}catch(PDOException $e){
    echo "預處理陳述式執行失敗！ <br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    $db_host = NULL;
    exit;
}
$userAccount = $stmt->rowCount();
if($userAccount>0){
    $errorMsg[] = "該帳號已存在";
}


if(!empty($errorMsg)){
    $error_message = implode('、', $errorMsg);
    echo json_encode(['status' => 0, 'message' => $error_message]);
    exit;
}
// 準備 SQL 語句
$sql = "INSERT INTO Member (
            MemberAccount, MemberName, MemberPassword, 
            MemberNickName, MembereMail, MemberPhone, MemberTel, 
            MemberAddress, MemberBirth, MemberGender, 
            MemberCreateDate, MemberUpdateDate
        ) VALUES (
            :account, :name, :password, 
            :nickname, :email, :phone, :tel, 
            :address, :birth, :gender, 
            :now, :now
        )";

// 準備和執行 SQL 語句
$stmt = $dbHost->prepare($sql);

try {
    $stmt->execute([
        ":account" => $account,
        ":name" => $name,
        ":password" => $password,
        ":nickname" => $nickname,
        // ":level" => $level,
        ":email" => $email,
        ":phone" => $phone,
        ":tel" => $tel,
        ":address" => $address,
        ":birth" => $birth,
        ":gender" => $gender,
        ":now" => $now,
    ]);

    echo json_encode(['status' => 1, 'message' => '新增成功']);
} catch (PDOException $e) {
    echo json_encode(['status' => 0, 'message' => '新增失敗, ERROR: ' . urlencode($e->getMessage())]);
    echo "預處理陳述式執行失敗！ <br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    $db_host = NULL;
    exit;
}    

?>
