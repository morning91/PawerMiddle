<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 送貨明細
if (!isset($_GET["OrderID"])) {
    echo "請正確帶入正確id變數";
    // exit的功能為輸出一個訊息後退出當前的腳本，強制結束後面的程式
    exit;
}
$id = $_GET["OrderID"];
require_once("../pdoConnect.php");
$sql = "SELECT `Order`.*, Member.MemberName AS Order_Name FROM `Order`
JOIN Member ON Order.MemberID = Member.MemberID
WHERE OrderID = :OrderID";

// 將slq的資料回傳回變數裡面
$stmt = $dbHost->prepare($sql);
try {
    $stmt->execute([
        ":OrderID" => $id
    ]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $usersCount = $stmt->rowCount();
} catch (PDOException $e) {
    echo "預處理陳述式執行失敗！ <br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    $dbHost = NULL;
    exit;
}
// 訂單明細

$orderSql = "SELECT orderDetail.*, product.product_img AS productImg
             FROM orderDetail 
             JOIN product ON orderDetail.ProductID = product.product_id
             WHERE orderDetail.OrderID = :OrderID";
$orderStmt = $dbHost->prepare($orderSql);

try {
    $orderStmt->execute([":OrderID" => $id]);
    $orderRows = $orderStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "預處理陳述式執行失敗！ <br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    $dbHost = NULL;
    exit;
}
// , Discount.CalculateType AS `valueType`, Discount.Value AS `value`,
// Discount.CouponInfo AS info, Discount.Name AS couponName
$couponSql = "SELECT Discount.*, Order.OrderID, Order.OrderCouponID 
            FROM Discount
            JOIN `Order` ON Discount.ID = Order.OrderCouponID
            WHERE Order.OrderID = :OrderID";
$couponStmt = $dbHost->prepare($couponSql);

try {
    $couponStmt->execute([":OrderID" => $id]);
    $couponRow = $couponStmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "預處理陳述式執行失敗！ <br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    $dbHost = NULL;
    exit;
}

// 優惠卷種類
$couponType = isset($couponRow["CalculateType"]) ? $couponRow["CalculateType"] : "";
// 優惠卷轉型成int
$couponInt = isset($couponRow["Value"]) ? (int)$couponRow["Value"] : "";
// 折扣後的價錢
$discountedValue = 0;
?>

<!doctype html>
<html lang="en">

<head>
    <title>Order</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <?php include("../headlink.php") ?>
</head>

<body>
    <script src="../assets/static/js/initTheme.js"></script>
    <div id="app">
        <?php include("../sidebar.php") ?>
        <div id="main" class='layout-navbar navbar-fixed'>
            <header>
            </header>
            <div id="main-content">
                <div class="page-heading">
                    <div class="page-title">
                        <a href="OrderList.php" class="btn btn-primary"><i class="fa-solid fa-chevron-left"></i>回列表</a>
                        <div class="row my-3">
                            <div class="col-12 col-md-6 order-md-1 order-last">
                                <h3>訂單明細</h3>
                                <p class="text-subtitle text-muted"></p>
                            </div>
                            <div class="col-12 col-md-6 order-md-2 order-first">
                                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="index.html"><i class="fa-solid fa-house"></i></a></li>
                                        <li class="breadcrumb-item active" aria-current="page"><a href="OrderList.php?p=1&sorter=1">訂單資訊</a></li>
                                        <li class="breadcrumb-item active" aria-current="page"><?= $row["OrderID"] ?></a></li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <section class="section">
                        <!-- 訂單資訊 -->
                        <div class="card">
                            <div class="card-body">
                                <form class="form form-vertical" action="doUpdateOrder.php" method="post">
                                    <div class="form-body">
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group opacity-75">
                                                    <label for="first-name-vertical">訂單編號 :</label>
                                                    <input readonly class="form-control" type="text" name="id" value="<?= $row["OrderID"] ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group opacity-75">
                                                    <label for="first-name-vertical">訂購人姓名:</label>
                                                    <input type="text" id="first-name-vertical" class="form-control" disabled="disabled" name="" placeholder="" value="<?= $row["Order_Name"] ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group opacity-75">
                                                    <label for="email-id-vertical">訂單金額:</label>
                                                    <input type="text" id="first-name-vertical" class="form-control" disabled="disabled" name="" placeholder="" value="<?= $row["OrderTotalPrice"] ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group opacity-75">
                                                    <label for="contact-info-vertical">優惠卷:</label>
                                                    <input type="text" id="first-name-vertical" class="form-control" disabled="disabled" name="" placeholder="" value="<?= $row["OrderCouponID"] ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group opacity-75">
                                                    <label for="password-vertical">付款方式:</label>
                                                    <input type="text" id="first-name-vertical" class="form-control" disabled="disabled" name="" placeholder="" value="<?= $row["OrderPaymentMethod"] ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group opacity-75">
                                                    <label for="first-name-vertical">付款狀態:</label>
                                                    <input type="text" id="first-name-vertical" class="form-control" disabled="disabled" name="" placeholder="" value="<?= $row["OrderPaymentStatus"] ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group opacity-75">
                                                    <label for="email-id-vertical">收貨人:</label>
                                                    <input type="text" id="first-name-vertical" class="form-control" disabled="disabled" name="" placeholder="" value="<?= $row["OrderReceiver"] ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group opacity-75">
                                                    <label for="contact-info-vertical">收貨人電話:</label>
                                                    <input type="text" id="first-name-vertical" class="form-control" disabled="disabled" name="" placeholder="" value="<?= $row["OrderReceiverPhone"] ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group opacity-75">
                                                    <label for="password-vertical">收貨地址:</label>
                                                    <input type="text" id="first-name-vertical" class="form-control" disabled="disabled" name="" placeholder="" value="<?= $row["OrderDeliveryAddress"] ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="first-name-vertical">訂單狀態:</label>
                                                    <select class="form-select" id="basicSelect" name="orderStatus">
                                                        <option value="未出貨" <?= ($row["OrderDeliveryStatus"] == "未出貨") ? "selected" : "" ?>>未出貨</option>
                                                        <option value="處理中" <?= ($row["OrderDeliveryStatus"] == "處理中") ? "selected" : "" ?>>處理中</option>
                                                        <option value="已出貨" <?= ($row["OrderDeliveryStatus"] == "已出貨") ? "selected" : "" ?>>已出貨</option>
                                                        <option value="已送達" <?= ($row["OrderDeliveryStatus"] == "已送達") ? "selected" : "" ?>>已送達</option>
                                                        <option value="已取消" <?= ($row["OrderDeliveryStatus"] == "已取消") ? "selected" : "" ?>>已取消</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group opacity-75">
                                                    <label for="email-id-vertical">收據類型:</label>
                                                    <input type="text" id="first-name-vertical" class="form-control" disabled="disabled" name="" placeholder="" value="<?= $row["OrderReceiptType"] ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group opacity-75">
                                                    <label for="password-vertical">發票載具:</label>
                                                    <input type="text" id="first-name-vertical" class="form-control" disabled="disabled" name="" placeholder="" value="<?= $row["OrderReceiptCarrier"] ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group opacity-75">
                                                    <label for="first-name-vertical">訂單備註:</label>
                                                    <input type="text" id="first-name-vertical" class="form-control" disabled="disabled" name="" placeholder="" value="<?= $row["OrderNote"] ?>">
                                                </div>
                                            </div>
                                            <div class="col-12 d-flex justify-content-end">
                                                <button type="submit" class="btn btn-primary me-1 mb-1">儲存</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div class="table-responsive">
                                    <table class="table table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>訂單編號</th>
                                                <th></th>
                                                <th>商品名稱</th>
                                                <th>數量</th>
                                                <th>金額</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $firstRow = true; // 初始化一個變數來跟踪是否是第一行
                                            $totalPrice = 0;
                                            foreach ($orderRows as $orderRow):
                                                $price = $orderRow["ProductOriginPrice"] * $orderRow["ProductAmount"];
                                                $totalPrice += $price;
                                            ?>
                                                <tr>
                                                    <?php if ($firstRow): ?>
                                                        <td><?= $orderRow["OrderID"] ?></td>
                                                        <?php $firstRow = false; ?>
                                                    <?php else: ?>
                                                        <td></td>
                                                    <?php endif; ?>
                                                    <td><img style="width: 50px;" src="../Product/ProductPicUpLoad/<?= $orderRow["productImg"] ?>" alt=""></td>
                                                    <td><?= $orderRow["ProductName"] ?></td>
                                                    <td><?= $orderRow["ProductAmount"] ?></td>
                                                    <td><?= $price ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            <tr>
                                                <td>折扣</td>
                                                <td><?= (!empty($couponRow["Name"])) ? $couponRow["Name"] : "無" ?></td>
                                                <td>
                                                    <?php
                                                    if(!empty($couponType)){
                                                        if ($couponType == 1) {
                                                            echo $couponInt / 10 . "折";
                                                        } else {
                                                            echo "折" . $couponInt . "元";
                                                        } 
                                                    }else{
                                                        echo "";
                                                    }
                                                    
                                                    ?>
                                                </td>
                                                <td>原價： <?= $totalPrice ?></td>
                                                <td>折抵金額：
                                                    <?php
                                                    if(!empty($couponRow["OrderCouponID"])){
                                                        if ($couponType == 1) {
                                                            $discountedValue = $totalPrice * ($couponInt / 100);
                                                            echo $totalPrice -= $discountedValue;
                                                        } else {
                                                            $discountedValue = $totalPrice -= $couponInt;
                                                            echo $couponInt;
                                                        }
                                                    }else{
                                                        echo "0";
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="d-flex justify-content-end my-3">
                                        <p>總金額 : <?= (number_format($discountedValue) == 0) ? $totalPrice : $discountedValue ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            </section>
        </div>
    </div>
    <?php include("../js.php"); ?>
    <footer>
        <div class="footer clearfix mb-0 text-muted">
            <div class="float-start">
            </div>
            <div class="float-end">
            </div>
        </div>
    </footer>
    </div>
    </div>
    <script>
        src = "https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity = "sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin = "anonymous"
    </script>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
</body>

</html>