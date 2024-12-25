<?php
require_once("../pdoConnect.php");

$ID = $_POST["ID"];
$Name = $_POST["Name"];
$StartTime = $_POST["StartTime"];
$EndTime = $_POST["EndTime"];
$PromotionCondition = $_POST["PromotionCondition"];
$ConditionMinValue = $_POST["ConditionMinValue"];
$CalculateType = $_POST["CalculateType"];
$Value = $_POST["Value"];
$IsCumulative = $_POST["IsCumulative"];
$MemberLevel = $_POST["MemberLevel"];
$PromotionType = $_POST["PromotionType"];
$CouponSerial = $_POST["CouponSerial"];
$CouponInfo = $_POST["CouponInfo"];
$CouponReceiveEndTime = $_POST["CouponReceiveEndTime"];
$CouponUseMax = $_POST["CouponUseMax"];
$EnableStatus = $_POST["EnableStatus"];
$now = date('Y-m-d H:i:s');

//檢查不可為空
$errors = [];
if (empty($Name)) {
    $errors[] = '<span class="text-danger fw-bold">促銷名稱</span>不能為空';
}
if (empty($StartTime)) {
    $errors[] = '<span class="text-danger fw-bold">開始時間</span>不能為空';
}
if (empty($EndTime)) {
    $errors[] = '<span class="text-danger fw-bold">結束時間</span>不能為空';
}
if (empty($Value)) {
    $errors[] = '<span class="text-danger fw-bold">優惠金額</span>不能為空';
}

if ($EndTime < $StartTime) {
    $errors[] = '<span class="text-danger fw-bold">結束時間</span>不可小於<span class="text-danger fw-bold">開始時間</span>';
}

if ($PromotionCondition == 2 && empty($ConditionMinValue)) {
    $errors[] = '若為訂單滿額，須填寫<span class="text-danger fw-bold">消費門檻值</span>';
}

if ($PromotionType == 2) {
    if (empty($CouponSerial)) {
        $errors[] = '促銷方式為優惠券，<span class="text-danger fw-bold">優惠券序號</span>不能為空';
    }
    // if (empty($CouponInfo)) {
    //     $errors[] = '促銷方式為優惠券，<span class="text-danger fw-bold">優惠券說明</span>不能為空';
    // }
    if (empty($CouponReceiveEndTime)) {
        $errors[] = '促銷方式為優惠券，<span class="text-danger fw-bold">截止領取時間</span>不能為空';
    }
    if (empty($CouponUseMax)) {
        $errors[] = '促銷方式為優惠券，<span class="text-danger fw-bold">使用次數限制</span>不能為空';
    }
}


if (!empty($errors)) {
    $error_message = implode('<br>', $errors);
    echo json_encode(['status' => 0, 'message' => $error_message]);
    exit;
}

if ($PromotionType == 1) {
    $CouponSerial = NULL;
    $CouponInfo = NULL;
    $CouponReceiveEndTime = NULL;
    $CouponUseMax = NULL;
}

if ($PromotionCondition == 1) {
    $ConditionMinValue = NULL;
}


$sql = "UPDATE Discount SET
    Name = :Name,
    StartTime = :StartTime,
    EndTime = :EndTime,
    PromotionCondition = :PromotionCondition,
    ConditionMinValue = :ConditionMinValue,
    CalculateType = :CalculateType,
    Value = :Value,
    IsCumulative = :IsCumulative,
    MemberLevel = :MemberLevel,
    PromotionType = :PromotionType,
    CouponSerial = :CouponSerial,
    CouponInfo = :CouponInfo,
    CouponReceiveEndTime = :CouponReceiveEndTime,
    CouponUseMax = :CouponUseMax,
    EnableStatus = :EnableStatus,
    UpdateDate = :UpdateDate,
    UpdateUserID = 1
WHERE ID = :ID";

$stmt = $dbHost->prepare($sql);

try {
    $stmt->execute([
        // ':Name' => $Name ?: null, 不可以用?: 會把value=0當作null
        ':ID' => $ID,
        ':Name' => ($Name !== "" && isset($Name)) ? $Name : null,
        ':StartTime' => ($StartTime !== "" && isset($StartTime)) ? $StartTime : null,
        ':EndTime' => ($EndTime !== "" && isset($EndTime)) ? $EndTime : null,
        ':PromotionCondition' => ($PromotionCondition !== "" && isset($PromotionCondition)) ? $PromotionCondition : null,
        ':ConditionMinValue' => ($ConditionMinValue !== "" && isset($ConditionMinValue)) ? $ConditionMinValue : null,
        ':CalculateType' => ($CalculateType !== "" && isset($CalculateType)) ? $CalculateType : null,
        ':Value' => ($Value !== "" && isset($Value)) ? $Value : null,
        ':IsCumulative' => ($IsCumulative !== "" && isset($IsCumulative)) ? $IsCumulative : null,
        ':MemberLevel' => ($MemberLevel !== "" && isset($MemberLevel)) ? $MemberLevel : null,
        ':PromotionType' => ($PromotionType !== "" && isset($PromotionType)) ? $PromotionType : null,
        ':CouponSerial' => ($CouponSerial !== "" && isset($CouponSerial)) ? $CouponSerial : null,
        ':CouponInfo' => ($CouponInfo !== "" && isset($CouponInfo)) ? $CouponInfo : null,
        ':CouponReceiveEndTime' => ($CouponReceiveEndTime !== "" && isset($CouponReceiveEndTime)) ? $CouponReceiveEndTime : null,
        ':CouponUseMax' => ($CouponUseMax !== "" && isset($CouponUseMax)) ? $CouponUseMax : null,
        ':EnableStatus' => ($EnableStatus !== "" && isset($EnableStatus)) ? $EnableStatus : null,
        ':UpdateDate' => ($now !== "" && isset($now)) ? $now : null,
    ]);
    echo json_encode(['status' => 1, 'message' => '修改成功']);
} catch (PDOException $e) {
    echo json_encode(['status' => 0, 'message' => 'Database error: ' . $e->getMessage()]);
}
