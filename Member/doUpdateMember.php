<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("../pdoConnect.php");

// 获取表单提交的值，并设置默认值为 null
$id = $_POST["id"] ?? null;
$name = $_POST["name"] ?? null;
$email = $_POST["email"] ?? null;
$phone = $_POST["phone"] ?? null;


$level = $_POST["level"] ?? null;
$address = $_POST["address"] ?? null;
$birth = $_POST["birth"] ? $_POST["birth"] : null;
$gender = $_POST["gender"] ?? null;
$valid = $_POST["valid"];
$blacklist = $_POST["blacklist"] ?? null;
$nickname = $_POST["nickname"] ?? ''; 
$tel = $_POST["tel"] ?? '';

$date = date('Y-m-d H:i:s');


if (!$id || !$name || !$phone || !$email) {
    echo "<script>alert('ID、姓名、電話和電子郵件不得為空！'); window.location.href = 'Member.php?MemberID=$id';</script>";
    exit;
}


$sql = "UPDATE Member 
        SET MemberName = :name, 
            MemberPhone = :phone, 
            MembereMail = :email, 
            MemberNickName = :nickname, 
            MemberLevel = :level, 
            MemberTel = :tel, 
            MemberAddress = :address, 
            MemberBirth = :birth, 
            MemberGender = :gender, 
            MemberValid = :valid, 
            MemberIsBlacklisted = :blacklist, 
            MemberUpdateDate = :updatedate
        WHERE MemberID = :id";

$stmt = $dbHost->prepare($sql);

try {
    $stmt->execute([
        ':name' => $name,
        ':phone' => $phone,
        ':email' => $email,
        ':nickname' => $nickname,
        ':level' => $level,
        ':tel' => $tel,
        ':address' => $address,
        ':birth' => $birth,
        ':gender' => $gender,
        ':valid' => $valid,
        ':blacklist' => $blacklist,
        ':updatedate' => $date,
        ':id' => $id
    ]);
    echo "<script>alert('會員資料更新成功！'); window.location.href = 'Member.php?MemberID=$id';</script>";
    exit;

} catch (PDOException $e) {
    echo "預處理語句執行失敗！<br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    $dbHost = NULL;
    exit;
}

// 成功后跳转
header("Location: Member.php?MemberID=$id");
$dbHost = null;
