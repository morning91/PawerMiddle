<?php
include("../pdoConnect.php");
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 獲取每頁顯示的資料數量，默認為20
$perPage = isset($_GET['perPage']) ? (int)$_GET['perPage'] : 20;

// 獲取當前頁碼，默認為第1頁
$page = isset($_GET["p"]) ? (int)$_GET["p"] : 1;

// 計算全部的訂單數量
$sqlAll = "SELECT * FROM `Order`";

try {
    $stmtAll = $dbHost->prepare($sqlAll);
    $stmtAll->execute();
    $userCountAll = $stmtAll->rowCount();
} catch (PDOException $e) {
    echo "預處理陳述式執行失敗！ <br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    $dbHost = NULL;
    exit;
}


// 計算 SQL 查詢的起始位置
$start = ($page - 1) * $perPage;

$orderClause = "ORDER BY OrderID ASC";
if (isset($_GET["sorter"])) {
    $sorter = (int)$_GET["sorter"];
    switch($sorter) {
        case 1: $orderClause = "ORDER BY OrderID ASC"; break;
        case -1: $orderClause = "ORDER BY OrderID DESC"; break;
        case 4: $orderClause = "ORDER BY OrderDate ASC"; break;
        case -4: $orderClause = "ORDER BY OrderDate DESC"; break;
    }
}

$searchName = isset($_GET["searchName"]) ? $_GET["searchName"] : '';
$dateRange = isset($_GET["dateRange"]) ? $_GET["dateRange"] : '';
$searchStatus = isset($_GET["searchStatus"]) ? $_GET["searchStatus"] : '';
$paymentMethod = isset($_GET["paymentMethod"]) ? $_GET["paymentMethod"] : '';

$sql = "SELECT `Order`.*, Member.MemberName AS Order_Name FROM `Order`
JOIN Member ON Order.MemberID = Member.MemberID";
$conditions = [];
$params = [];

// 添加查詢條件
if (!empty($searchName)) {
    $conditions[] = "MemberName LIKE :searchName";
    $params[':searchName'] = "%$searchName%";
}
if (!empty($dateRange)) {
    // 將日期範圍字串分割成兩個日期部分
    list($startDateString, $endDateString) = explode(' to ', $dateRange);

    // 將日期字串轉換為 DateTime 對象並格式化為 YYYY-MM-DD
    $startDate = DateTime::createFromFormat('F j, Y', trim($startDateString))->format('Y-m-d');
    $endDate = DateTime::createFromFormat('F j, Y', trim($endDateString))->format('Y-m-d');

    // 添加日期範圍條件
    $conditions[] = "OrderDate BETWEEN :startDate AND :endDate";
    $params[':startDate'] = $startDate;
    $params[':endDate'] = $endDate;
}
// 檢查是否有添加"訂單狀態"的篩選
if(!empty($searchStatus)){
    $conditions[] = "OrderDeliveryStatus = :searchStatus";
    $params[':searchStatus'] = "$searchStatus";
}
// 檢查是否有添加"付款方式"的篩選
if(!empty($paymentMethod)){
    $conditions[] = "OrderPaymentMethod = :paymentMethod";
    $params[':paymentMethod'] = "$paymentMethod";
}

// 如果有查詢條件，將它們添加到查詢語句中
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " $orderClause LIMIT :start, :perPage";

$stmt = $dbHost->prepare($sql);

// 綁定查詢參數
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}

$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);

// 執行SQL查詢
try {
    $stmt->execute();
    $userCount = $stmt->rowCount();
} catch (PDOException $e) {
    echo "預處理陳述式執行失敗！ <br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    $dbHost = NULL;
    exit;
}

// 計算查詢的行數
$countSql = "SELECT COUNT(*) FROM `Order` JOIN Member ON Order.MemberID = Member.MemberID";
if (!empty($conditions)) {
    $countSql .= " WHERE " . implode(" AND ", $conditions);
}
$countStmt = $dbHost->prepare($countSql);
foreach ($params as $key => $value) {
    $countStmt->bindValue($key, $value, PDO::PARAM_STR);
}
$countStmt->execute();
$totalRecords = $countStmt->fetchColumn();

// 查詢時不會有分頁是因為被$perPage給限制住了，所以$userCount = $stmt->rowCount();的結果永遠不會超過perPage
if(isset($_GET["searchName"]) || isset($_GET["dateRange"]) || isset($_GET["searchStatus"]) || isset($_GET["paymentMethod"])){
    $totalPage = ceil($totalRecords / $perPage);
}else{
    $totalPage = ceil($userCountAll / $perPage);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>訂單管理</title>
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
                        <div class="row">
                            <div class="col-12 col-md-6 order-md-1 order-last">
                                <h3>訂單管理</h3>
                                <p class="text-subtitle text-muted"></p>
                            </div>
                            <div class="col-12 col-md-6 order-md-2 order-first">
                                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="../index.php"><i class="fa-solid fa-house"></i></a></li>
                                        <li class="breadcrumb-item active" aria-current="page">訂單管理</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <section class="section">
                        <!-- 搜尋Bar -->
                        <div class="card">
                            <div class="card-body">
                                <form action="" method="get">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-4 col-12">
                                            <div class="input-group mb-3">
                                                <!-- $memberDate -->
                                                <span class="input-group-text" for="">選擇日期</span>
                                                <input type="text" class="form-control flatpickr-range flatpickr-input" placeholder="Select date.." value="<?= $dateRange ?>" readonly="readonly" name="dateRange">
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-4 col-12">
                                            <div class="input-group mb-3">
                                                <!-- $memberName -->
                                                <span class="input-group-text" for="">訂購人名稱</span>
                                                <input type="search" id="" class="form-control" placeholder="" value="<?= $searchName ?>" name="searchName">
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-4 col-12">
                                            <div class="input-group mb-3">
                                                <!-- $memberName -->
                                                <span class="input-group-text" for="">訂單狀態</span>
                                                <select class="form-select" name="searchStatus" onchange="this.form.submit()">
                                                    <option value="" <?= ($searchStatus == "") ? 'selected' : '' ?>>全部狀態</option>
                                                    <option value="未出貨" <?= ($searchStatus == "未出貨") ? 'selected' : '' ?>>未出貨</option>
                                                    <option value="處理中" <?= ($searchStatus == "處理中") ? 'selected' : '' ?>>處理中</option>
                                                    <option value="已出貨" <?= ($searchStatus == "已出貨") ? 'selected' : '' ?>>已送達</option>
                                                    <option value="已取消" <?= ($searchStatus == "已取消") ? 'selected' : '' ?>>已取消</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-4 col-12">
                                            <div class="input-group mb-3">
                                                <!-- $memberName -->
                                                <sapn class="input-group-text" for="">付款方式</sapn>
                                                <select class="form-select" name="paymentMethod" onchange="this.form.submit()">
                                                    <option value="" <?= ($paymentMethod == "") ? 'selected' : '' ?>>付款狀態</option>
                                                    <option value="信用卡" <?= ($paymentMethod == "信用卡") ? 'selected' : '' ?>>信用卡</option>
                                                    <option value="轉帳" <?= ($paymentMethod == "轉帳") ? 'selected' : '' ?>>轉帳</option>
                                                    <option value="貨到付款" <?= ($paymentMethod == "貨到付款") ? 'selected' : '' ?>>貨到付款</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary me-1 mb-1">查詢</button>
                                            <?php if(isset($_GET["dateRange"]) || isset($_GET["serachName"])): ?>
                                            <a class="btn btn-light-secondary me-1 mb-1" href="OrderList.php?p=1&sorter=1">清除查詢結果</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
                                    <!-- 每頁Ｎ筆資料 -->
                                    <div class="dataTable-top">
                                        <label>每頁</label>
                                        <div class="dataTable-dropdown"><select class="dataTable-selector form-select" id="perPageSelect">
                                                <option value="5" <?= $perPage == 5 ? 'selected' : '' ?>><a href=""></a>5</option>
                                                <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
                                                <option value="15" <?= $perPage == 15 ? 'selected' : '' ?>>15</option>
                                                <option value="20" <?= $perPage == 20 ? 'selected' : '' ?>>20</option>
                                                <option value="25" <?= $perPage == 25 ? 'selected' : '' ?>>25</option>
                                            </select>
                                        </div>
                                        <label>筆</label>
                                        
                                    </div>
                                    <!-- 會員列表 -->
                                    <div class="dataTable-container">
                                        <?php if ($userCount > 0): 
                                            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                            ?>
                                            <div class="py-2">
                                                <table class="table table-striped dataTable-table">
                                                    <thead>
                                                        <tr>
                                                            <th><a href="#" class="sort-link" data-sorter="1">訂單邊號</th>
                                                            <th>訂購人</th>
                                                            <th>收貨人</th>
                                                            <th>收貨聯絡電話</th>
                                                            <th>付款方式</th>
                                                            <th>收貨地址</th>
                                                            <th>出貨狀態</th>
                                                            <th><a href="#" class="sort-link" data-sorter="4">訂單日期</a></th>
                                                            <th>編輯訂單</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($rows as $order): ?>
                                                            <tr>
                                                                <td><?= $order["OrderID"]; ?></td>
                                                                <!-- 透過join去抓member裡面的使用者名稱 -->
                                                                <td><?= $order["Order_Name"]; ?></td>
                                                                <td><?= $order["OrderReceiver"]; ?></td>
                                                                <td><?= $order["OrderReceiverPhone"]; ?></td>
                                                                <td><?= $order["OrderPaymentMethod"] ?></td>
                                                                <td><?= $order["OrderDeliveryAddress"]; ?></td>
                                                                <td><?= $order["OrderDeliveryStatus"] ?></td>
                                                                <td><?= $order["OrderDate"]; ?></td>
                                                                <td>
                                                                    <a class="btn-primary" href="Order.php?OrderID=<?= $order["OrderID"] ?>"><i class="fa-solid fa-lg fa-pen-to-square"></i></a>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            <?php else: ?>
                                                目前沒有使用者
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <!-- 頁數索引 -->
                                    <div class="dataTable-bottom">
                                        <div class="dataTable-info">顯示 <?= $start + 1 ?> 到 <?= min($start + $perPage, $userCountAll) ?> 共 <?= $userCountAll ?> 筆</div>
                                        <?php if($totalPage > 1): ?>
                                        <nav class="dataTable-pagination">
                                            <ul class="dataTable-pagination-list pagination pagination-primary">
                                            <?php for($i = 1;$i <= $totalPage; $i++): ?>
                                                <li class="<?= $page == $i ? 'active' : '' ?> page-item">
                                                    <a href="#" class="page-link" onclick="changePage(<?= $i ?>)"><?= $i ?></a>
                                                </li>
                                            <?php endfor; ?>
                                            <!-- <li class="pager page-item"><a href="#" data-page="2" class="page-link">›</a></li> -->
                                            </ul>
                                        </nav>
                                        <?php endif; ?>
                                    </div>
                                    <?php $dbHost = null; ?>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <?php include("../js.php");?>
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
    
    <!-- JavaScript -->
    <script>
    const sortLinks = document.querySelectorAll(".sort-link");

    sortLinks.forEach(link => {
        link.addEventListener("click", function(event){
            event.preventDefault(); // 避免跳轉

            // 將data-sorter的值抓出來
            const sorter = parseInt(this.getAttribute("data-sorter"));
            const urlParams = new URLSearchParams(window.location.search);

            // 判斷當前排序是否為正向，如果是正向的話則改為逆向，反之亦然
            const currentSorter = parseInt(urlParams.get('sorter'));
            const newSorter = (currentSorter === sorter) ? -sorter : sorter;

            urlParams.set('sorter', newSorter);

            // 保留搜索條件
            const searchName = document.querySelector('input[name="searchName"]').value;
            const dateRange = document.querySelector('input[name="dateRange"]').value;
            const searchStatus = document.querySelector('select[name="searchStatus"]').value;
            const paymentMethod = document.querySelector('select[name="paymentMethod"]').value;
            
            if(searchName) urlParams.set('searchName', searchName);
            if(dateRange) urlParams.set('dateRange', dateRange);
            if(searchStatus) urlParams.set('searchStatus', searchStatus);
            if(paymentMethod) urlParams.set('paymentMethod', paymentMethod);
            window.location.search = urlParams.toString();
        });
    });

    // 選擇頁面功能
    const selectElement = document.querySelector("#perPageSelect");
    selectElement.addEventListener("change", function(){
        const perPage = this.value;
        changePage(1, perPage);
    });

    function changePage(page, perPage = null){
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('p', page);
        if(perPage !== null){
            urlParams.set('perPage', perPage);
        }

        // 保留serachName 跟 searchLevel
        const searchName = document.querySelector('input[name="searchName"]').value;
        const dateRange = document.querySelector('input[name="dateRange"]').value;
        const searchStatus = document.querySelector('select[name="searchStatus"]').value;
        const paymentMethod = document.querySelector('select[name="paymentMethod"]').value;

        if(searchName) urlParams.set('searchName', searchName);
        if(dateRange) urlParams.set('dateRange', dateRange);
        if(searchStatus) urlParams.set('searchStatus', searchStatus);
        if(paymentMethod) urlParams.set('paymentMethod', paymentMethod);
        window.location.search = urlParams.toString();
    }
    </script>
    <script src="../assets/static/js/components/dark.js"></script>
    <script src="../assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="../assets/compiled/js/app.js"></script>
    


</body>

</html>