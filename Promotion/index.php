<?php
require_once("../pdoConnect.php");

session_start();
//刪除成功或失敗會在dodelete存SESSION訊息，用AJAX讀回來後有多寫重新整理，讀頁面時把SESSION訊息顯示出來並在刪除
if (isset($_SESSION['SESmessage'])) {
    $message = $_SESSION['SESmessage'];
    echo "
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('#info').innerHTML = '$message';
            var infoModal = new bootstrap.Modal(document.querySelector('#infoModal'));
            infoModal.show();
        });
    </script>";
    unset($_SESSION['SESmessage']); // 顯示後清除訊息
}

// 用GET取得查詢條件的變數
$searchName = isset($_GET["searchName"]) ? $_GET["searchName"] : '';
$searchPromotionType = isset($_GET["searchPromotionType"]) ? $_GET["searchPromotionType"] : '';
$searchStartTime = isset($_GET["searchStartTime"]) ? $_GET["searchStartTime"] : '';
$searchEndTime = isset($_GET["searchEndTime"]) ? $_GET["searchEndTime"] : '';
$searchEnableStatus = isset($_GET["searchEnableStatus"]) ? $_GET["searchEnableStatus"] : '';
$searchCalculateType = isset($_GET["searchCalculateType"]) ? $_GET["searchCalculateType"] : '';


// 定義陣列要存查詢條件SQL語法與對應參數
$conditions = [];
$params = [];

// 檢查每項條件若不為空(不包含“”)，將對應的SQL並加到condition內(condition是SQL語法，用佔位符，在pdo execute時用params定義SQL的變數)
if (isset($searchName) && $searchName !== "") {
    $conditions[] = "d.Name LIKE :searchName";
    $params[':searchName'] = "%" . $searchName . "%";
}

if (isset($searchPromotionType)  && $searchPromotionType !== "") {
    $conditions[] = "d.PromotionType = :searchPromotionType";
    $params[':searchPromotionType'] = $searchPromotionType;
}

if (isset($searchStartTime) && $searchStartTime !== "") {
    $conditions[] = "d.StartTime >= :searchStartTime ";
    $params[':searchStartTime'] = $searchStartTime;
}

if (isset($searchEndTime) && $searchEndTime !== "") {
    $conditions[] = "d.EndTime <= :searchEndTime ";
    $params[':searchEndTime'] = $searchEndTime;
}

if ((isset($searchStartTime) && $searchStartTime !== "") && (isset($searchEndTime) && $searchEndTime !== "")) {
    $conditions[] = "(d.StartTime >= :searchStartTime AND d.EndTime <= :searchEndTime)";
    $params[':searchStartTime'] = $searchStartTime;
    $params[':searchEndTime'] = $searchEndTime;
}

if (isset($searchEnableStatus)  && $searchEnableStatus !== "") {
    $conditions[] = "d.EnableStatus = :searchEnableStatus";
    $params[':searchEnableStatus'] = $searchEnableStatus;
}

if (isset($searchCalculateType)  && $searchCalculateType !== "") {
    $conditions[] = "d.CalculateType = :searchCalculateType";
    $params[':searchCalculateType'] = $searchCalculateType;
}


//分頁 , 頁碼
$per_page = isset($_GET["per_page"]) ? (int)$_GET["per_page"] : 10; // 每頁顯示的筆數，預設10筆
$page = isset($_GET["page"]) ? (int)$_GET["page"] : 1; // 當前頁數，預設第1頁
$offset = ($page - 1) * $per_page; // 計算查詢的起始點

//排序
$sortBy = isset($_GET["sortBy"]) ? $_GET["sortBy"] : 'ID'; // 默認按ID排序
$sortOrder = isset($_GET["sortOrder"]) ? $_GET["sortOrder"] : 'desc'; // 默認升序排序

$sqlAll = "SELECT 
d.*,
sc1.Description AS PromotionConditionDP,
sc2.Description AS CalculateTypeDP,
sc3.Description AS MemberLevelDP, 
sc4.Description AS PromotionTypeDP,
sc5.Description AS EnableStatusDP 
FROM Discount d 
JOIN SystemCode sc1 ON d.PromotionCondition = sc1.Value AND sc1.Type='PromotionCondition'
JOIN SystemCode sc2 ON d.CalculateType = sc2.Value AND sc2.Type='CalculateType'
JOIN SystemCode sc3 ON d.MemberLevel = sc3.Value AND sc3.Type='MemberLevel'
JOIN SystemCode sc4 ON d.PromotionType = sc4.Value AND sc4.Type='PromotionType'
JOIN SystemCode sc5 ON d.EnableStatus = sc5.Value AND sc5.Type='EnableStatus'
WHERE IsValid = 1";

// 如果存查詢條件的condition非空，則在sql最後加上所有的條件
if (!empty($conditions)) {
    $sqlAll .= " AND " . implode(" AND ", $conditions);
}

// echo $sqlAll;
// print_r($params);

// 先執行查詢來獲取符合條件的總數
$stmtAll = $dbHost->prepare($sqlAll);

try {
    $stmtAll->execute($params);
    $discountAll = $stmtAll->fetchAll(PDO::FETCH_ASSOC);
    $discountAllcount = $stmtAll->rowCount();
} catch (PDOException $e) {
    echo "預處理陳述式執行失敗！ <br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    $dbHost = NULL;
    exit;
}

// 依照查詢總數來算出共有多少頁
$total_page = ceil($discountAllcount / $per_page);

// SQL加上排序變數
$sqlAll .= " ORDER BY $sortBy $sortOrder";

// SQL+查詢條件，加入選擇每頁幾筆功能，使用LIMIT
$sql = $sqlAll . " LIMIT :offset, :per_page";
// 為一頁資料查詢做準備
$stmtsql = $dbHost->prepare($sql);
// 只查出一頁N筆的資料的sql
try {
    // 先綁定參數，再執行語句
    foreach ($params as $key => &$val) {
        $stmtsql->bindParam($key, $val);
    }
    $stmtsql->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmtsql->bindValue(':per_page', $per_page, PDO::PARAM_INT);
    $stmtsql->execute();
    $discountsql = $stmtsql->fetchAll(PDO::FETCH_ASSOC);
    $discountcount = $stmtsql->rowCount();
} catch (PDOException $e) {
    echo "預處理陳述式執行失敗！ <br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    $db_host = NULL;
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>促銷管理</title>

    <?php include("../headlink.php") ?>
</head>

<body>
    <?php include("../modals.php") ?>


    <script src="../assets/static/js/initTheme.js"></script>
    <div id="app">
        <?php include("../sidebar.php") ?>
        <div id="main">
            <header>
                <a href="#" class="burger-btn d-block d-xl-none mb-3">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>
            <div class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3>促銷管理</h3>
                            <p class="text-subtitle text-muted"></p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html"><i
                                                class="fa-solid fa-house"></i></a></li>
                                    <li class="breadcrumb-item active" aria-current="page">促銷管理</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <section class="section">
                    <form action="/G5midTerm/Promotion/index.php" method="GET" id="searchform">
                        <div class="card">
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-lg-3 col-md-4 col-12">
                                        <div class="input-group mb-3">
                                            <span class="input-group-text" id="basic-addon1">促銷名稱</span>
                                            <input type="text" class="form-control" placeholder="請輸入促銷名稱" aria-label="Username" aria-describedby="basic-addon1" name="searchName"
                                                value="<?= $searchName ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-12">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">促銷時間</span>
                                            </div>
                                            <input type="text" class="form-control mb-3 flatpickr-no-config flatpickr-input" placeholder="請輸入促銷開始時間" readonly="readonly" name="searchStartTime" value="<?= $searchStartTime ?>">
                                            <input type="text" class="form-control mb-3 flatpickr-no-config flatpickr-input" placeholder="請輸入促銷結束時間" readonly="readonly" name="searchEndTime" value="<?= $searchEndTime ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-12">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="inputGroupSelect01">計算方式</label>
                                            <select class="form-select" id="inputGroupSelect01" name="searchCalculateType">
                                                <option value="" <?= ($searchCalculateType == "") ? 'selected' : '' ?>>全部</option>
                                                <option value="1" <?= ($searchCalculateType == 1) ? 'selected' : '' ?>>%</option>
                                                <option value="2" <?= ($searchCalculateType == 2) ? 'selected' : '' ?>>元</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-12">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="inputGroupSelect01">促銷方式</label>
                                            <select class="form-select" id="inputGroupSelect01" name="searchPromotionType">
                                                <option value="" <?= ($searchPromotionType == "") ? 'selected' : '' ?>>全部</option>
                                                <option value="1" <?= ($searchPromotionType == 1) ? 'selected' : '' ?>>自動套用</option>
                                                <option value="2" <?= ($searchPromotionType == 2) ? 'selected' : '' ?>>優惠券</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-4 col-12">
                                        <div class="input-group mb-3">
                                            <label class="input-group-text" for="inputGroupSelect01">啟用狀態</label>
                                            <select class="form-select" id="inputGroupSelect01" name="searchEnableStatus">
                                                <option value="" <?= ($searchEnableStatus == "") ? 'selected' : '' ?>>全部</option>
                                                <option value="1" <?= ($searchEnableStatus == 1) ? 'selected' : '' ?>>啟用</option>
                                                <option value="0" <?= ($searchEnableStatus == 0) ? 'selected' : '' ?>>停用</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <!-- <button type="submit" class="btn btn-primary me-1 mb-1"><i class="fa-solid fa-magnifying-glass" id="searchbtn"></i></button> -->
                                        <button type="submit" class="btn btn-primary me-1 mb-1" id="searchbtn">查詢</button>
                                        <!-- <a class="btn btn-light-secondary me-1 mb-1" href="index.php" id="resetbtn"><i class="fa-solid fa-delete-left"></i></a> -->
                                        <a class="btn btn-light-secondary me-1 mb-1" href="index.php" id="resetbtn">清除</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
                                    <?php if ($discountcount > 0): ?>
                                        <div class="dataTable-top">
                                            <div class="col-auto">
                                                <label>每頁</label>
                                                <div class="dataTable-dropdown">
                                                    <select class="dataTable-selector form-select" id="itemsPerPage" name="per_page" onchange="this.form.submit()">
                                                        <option value="5" <?= ($per_page == 5) ? 'selected' : '' ?>>5</option>
                                                        <option value="10" <?= ($per_page == 10) ? 'selected' : '' ?>>10</option>
                                                        <option value="15" <?= ($per_page == 15) ? 'selected' : '' ?>>15</option>
                                                        <option value="20" <?= ($per_page == 20) ? 'selected' : '' ?>>20</option>
                                                        <option value="25" <?= ($per_page == 25) ? 'selected' : '' ?>>25</option>
                                                    </select>

                                                </div>
                                                <label>筆</label>

                                            </div>
                                            <div class="col-auto">
                                                <!-- <a class="btn btn-primary me-1 mb-1" href="DiscountCreate.php"><i class="fa-solid fa-plus"></i></a> -->
                                                <a class="btn btn-primary me-1 mb-1" href="DiscountCreate.php">新增</a>
                                            </div>
                                        </div>

                                        <div class="dataTable-container table-responsive">
                                            <table class="table table-striped table-hover dataTable-table mb-0">
                                                <thead>
                                                    <tr class="text-nowrap">
                                                        <th class="<?php if ($sortBy == "ID") {
                                                                        echo $sortOrder == "asc" ? 'asc' : 'desc';
                                                                    } ?>" onclick="sortTable('ID')">
                                                            <a href="#" class="dataTable-sorter">ID</a>
                                                        </th>
                                                        <th class="<?php if ($sortBy == "Name") {
                                                                        echo $sortOrder == "asc" ? 'asc' : 'desc';
                                                                    } ?>" onclick="sortTable('Name')">
                                                            <a href="#" class="dataTable-sorter">促銷名稱</a>
                                                        </th>
                                                        <th class="<?php if ($sortBy == "StartTime") {
                                                                        echo $sortOrder == "asc" ? 'asc' : 'desc';
                                                                    } ?>" onclick="sortTable('StartTime')" style="min-width:190px">
                                                            <a href="#" class="dataTable-sorter">促銷時間</a>
                                                        </th>
                                                        <th class="<?php if ($sortBy == "PromotionCondition") {
                                                                        echo $sortOrder == "asc" ? 'asc' : 'desc';
                                                                    } ?>" onclick="sortTable('PromotionCondition')">
                                                            <a href="#" class="dataTable-sorter">消費門檻</a>
                                                        </th>
                                                        <th class="<?php if ($sortBy == "ConditionMinValue") {
                                                                        echo $sortOrder == "asc" ? 'asc' : 'desc';
                                                                    } ?>" onclick="sortTable('ConditionMinValue')">
                                                            <a href="#" class="dataTable-sorter">門檻值</a>
                                                        </th>
                                                        <th class="<?php if ($sortBy == "Value") {
                                                                        echo $sortOrder == "asc" ? 'asc' : 'desc';
                                                                    } ?>" onclick="sortTable('Value')">
                                                            <a href="#" class="dataTable-sorter">優惠金額</a>
                                                        </th>
                                                        <!-- <th class="<?php if ($sortBy == "CalculateType") {
                                                                            echo $sortOrder == "asc" ? 'asc' : 'desc';
                                                                        } ?>" onclick="sortTable('CalculateType')">
                                                        <a href="#" class="dataTable-sorter">計算方式</a>
                                                    </th> -->
                                                        <th class="<?php if ($sortBy == "MemberLevel") {
                                                                        echo $sortOrder == "asc" ? 'asc' : 'desc';
                                                                    } ?>" onclick="sortTable('MemberLevel')">
                                                            <a href="#" class="dataTable-sorter">會員等級</a>
                                                        </th>
                                                        <th class="<?php if ($sortBy == "PromotionType") {
                                                                        echo $sortOrder == "asc" ? 'asc' : 'desc';
                                                                    } ?>" onclick="sortTable('PromotionType')">
                                                            <a href="#" class="dataTable-sorter">促銷方式</a>
                                                        </th>
                                                        <th class="<?php if ($sortBy == "EnableStatus") {
                                                                        echo $sortOrder == "asc" ? 'asc' : 'desc';
                                                                    } ?>" onclick="sortTable('EnableStatus')">
                                                            <a href="#" class="dataTable-sorter">啟用狀態</a>
                                                        </th>
                                                        <th></th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    <?php foreach ($discountsql as $discount): ?>
                                                        <tr>
                                                            <td><?= $discount["ID"] ?></td>
                                                            <td><?= $discount["Name"] ?></td>
                                                            <td><?= $discount["StartTime"] ?> ~<br> <?= $discount["EndTime"] ?></td>
                                                            <td><?= $discount["PromotionConditionDP"] ?></td>
                                                            <td><?php if ($discount["ConditionMinValue"] != 0) {
                                                                    echo number_format($discount["ConditionMinValue"]) . "元";
                                                                } ?></td>
                                                            <td><?= number_format($discount["Value"]) . $discount["CalculateTypeDP"] ?></td>
                                                            <!-- <td><?= $discount["CalculateTypeDP"] ?></td> -->
                                                            <td><?= $discount["MemberLevelDP"] ?></td>
                                                            <td><?= $discount["PromotionTypeDP"] ?></td>
                                                            <td><?= $discount["EnableStatusDP"] ?></td>
                                                            <td>
                                                                <a class="" href="DiscountEdit.php?id=<?= $discount["ID"] ?>"> <i class="fa-solid fa-pen-to-square fa-lg"></i></a>
                                                            </td>
                                                            <td>
                                                                <a href="#" class=" delete-btn"
                                                                    data-id="<?= $discount["ID"] ?>"
                                                                    data-name="<?= $discount["Name"] ?>"
                                                                    data-starttime="<?= $discount["StartTime"] ?>"
                                                                    data-endtime="<?= $discount["EndTime"] ?>"><i class="fa-solid fa-trash-can fa-lg"></i></a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>

                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="dataTable-bottom mt-3">
                                            <div class="dataTable-info">顯示 <?= $offset + 1 ?> 到 <?= min($offset + $per_page, $discountAllcount) ?> 筆，共 <?= $total_page ?> 頁，共 <?= $discountAllcount ?> 筆</div>
                                            <nav aria-label="Page navigation example">
                                                <ul class="pagination pagination-primary">
                                                    <!-- 回到第一頁 -->
                                                    <li class="page-item <?= ($page == 1) ? 'd-none' : '' ?>">
                                                        <a class="page-link" href="#" id="firstPage">
                                                            <span aria-hidden="true"><i class="fa-solid fa-angles-left"></i></span>
                                                        </a>
                                                    </li>
                                                    <!-- 上一頁 -->
                                                    <li class="page-item <?= ($page == 1) ? 'd-none' : '' ?>">
                                                        <a class="page-link" href="#" id="prevPage">
                                                            <span aria-hidden="true"><i class="fa-solid fa-angle-left"></i></span>
                                                        </a>
                                                    </li>

                                                    <!-- 顯示頁碼 -->
                                                    <?php
                                                    //限制頁碼只出現前兩筆與後兩筆
                                                    $start_page = max(1, $page - 2);  // 確保開始頁碼不小於1
                                                    $end_page = min($total_page, $page + 2);  // 確保結束頁碼不大於總頁數

                                                    for ($i = $start_page; $i <= $end_page; $i++): ?>
                                                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                                            <a class="page-link page-link-js" href="#" data-page="<?= $i ?>">
                                                                <?= $i ?>
                                                            </a>
                                                        </li>
                                                    <?php endfor; ?>

                                                    <!-- 下一頁 -->
                                                    <li class="page-item <?= ($page == $total_page) ? 'd-none' : '' ?>">
                                                        <a class="page-link" href="#" id="nextPage">
                                                            <span aria-hidden="true"><i class="fa-solid fa-angle-right"></i></span>
                                                        </a>
                                                    </li>
                                                    <!-- 回到最後一頁 -->
                                                    <li class="page-item <?= ($page == $total_page) ? 'd-none' : '' ?>">
                                                        <a class="page-link" href="#" id="lastPage">
                                                            <span aria-hidden="true"><i class="fa-solid fa-angles-right"></i></span>
                                                        </a>
                                                    </li>

                                                </ul>
                                            </nav>
                                        </div>
                                    <?php else : ?>
                                        查無資料，請重新設定查詢條件。
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    </form>

                </section>
                <?php include("../footer.php") ?>
            </div>
        </div>

    </div>
    </div>
    <?php include("../js.php") ?>


    <script>
        // 統一定義infomodal與info
        const infoModal = new bootstrap.Modal('#infoModal', {
            keyboard: true
        })
        const info = document.querySelector("#info")

        // 檢查查詢條件邏輯
        const searchbtn = document.querySelector("#searchbtn")
        const searchStartTime = document.querySelector("[name='searchStartTime']");
        const searchEndTime = document.querySelector("[name='searchEndTime']");
        const searchform = document.querySelector("#searchform")
        const now = new Date();


        searchbtn.addEventListener("click", function(e) {
            e.preventDefault();
            let searchStartTimeVal = searchStartTime.value
            let searchEndTimeVal = searchEndTime.value

            // Validation logic
            if (searchStartTimeVal !== "" && searchEndTimeVal !== "") {
                if (searchEndTimeVal < searchStartTimeVal) {
                    info.innerHTML = '<span class="text-danger fw-bold">結束時間</span>不可小於<span class="text-danger fw-bold">開始時間</span>';
                    infoModal.show();
                    return
                }
            }

            searchform.submit();
        })


        // 點擊刪除按鈕後，將用data-set的方式，將資料傳至modal並顯示modal
        const deleteButtons = document.querySelectorAll('.delete-btn');
        const deleteModal = new bootstrap.Modal('#deleteModal', {
            keyboard: true
        })
        const confirmDeleteButton = document.getElementById('confirmDelete');

        let currentDeleteId = null;

        deleteButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                //取出被點擊按鈕的data-id
                currentDeleteId = this.getAttribute('data-id');

                // 輸入錯誤訊息內容
                document.querySelector('#delete-info').innerHTML = `
                    <p><strong>ID:</strong> <span>${this.getAttribute('data-id')}</span></p>
                    <p><strong>促銷名稱:</strong> <span>${this.getAttribute('data-name')}</span></p>
                    <p><strong>開始時間:</strong> <span>${this.getAttribute('data-starttime')}</span></p>
                    <p><strong>結束時間:</strong> <span>${this.getAttribute('data-endtime')}</span></p>
                `;

                deleteModal.show();
            });
        });

        //點擊確認刪除，真正執行刪除
        confirmDeleteButton.addEventListener('click', function() {
            if (currentDeleteId) {
                $.ajax({
                        method: "POST",
                        url: "doDeleteDiscount.php",
                        dataType: "json",
                        data: {
                            id: currentDeleteId,
                        }
                    })
                    .done(function(response) {
                        let status = response.status;
                        if (status == 0 || status == 1) {
                            // 保留現在的查詢條件並重新加載頁面，讓被刪除資料消失
                            window.location.href = window.location.pathname + window.location.search;
                        }
                    })
                    .fail(function(jqXHR, textStatus) {
                        console.log("Request failed: " + textStatus);
                    });
            }
        });
    </script>
    <script>
        const urlParams = new URLSearchParams(window.location.search); //將當前URL中的查詢參數（即 ?key=value 這些部分）解析為 URLSearchParams 物件，方便操作。
        //點擊表頭時，
        function sortTable(sortBy) {
            // 設定排序的欄位
            urlParams.set('sortBy', sortBy);

            // 設定排序的順序
            let currentOrder = urlParams.get('sortOrder');
            let newOrder = (currentOrder === 'asc') ? 'desc' : 'asc';
            urlParams.set('sortOrder', newOrder);

            // 更新URL並重新導向
            window.location.href = window.location.pathname + '?' + urlParams.toString();
        }


        //  點擊頁碼時，用JS保留URL參數，並用page參數更新page
        const pageLinks = document.querySelectorAll('.page-link-js');

        pageLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault(); //阻止導航到 href 指定的地址。因為後面要通過JavaScript來控制頁面的跳轉。
                const page = this.getAttribute('data-page'); //從被點擊的頁碼連結中獲取 data-page 屬性

                urlParams.set('page', page); //這行代碼將 page 參數設置為當前點擊的頁碼值。URLSearchParams 的 set 方法可以更新已存在的參數或添加新的參數。

                window.location.href = window.location.pathname + '?' + urlParams.toString(); //這行代碼組合了新的URL，window.location.pathname 是當前頁面的路徑（不含查詢參數），再加上我們處理好的查詢參數，然後將新URL設置為 window.location.href，這樣就實現了頁面的跳轉。
            });
        });

        const prevPageLink = document.querySelector('#prevPage');
        const nextPageLink = document.querySelector('#nextPage');
        const firstPageLink = document.querySelector('#firstPage');
        const lastPageLink = document.querySelector('#lastPage');

        if (prevPageLink) {
            prevPageLink.addEventListener('click', function(e) {
                e.preventDefault();
                let page = parseInt(urlParams.get('page') || '1') - 1; //從URL參數中獲取當前頁碼，如果沒有找到頁碼，則默認為第1頁，然後減去1得到上一頁的頁碼。
                if (page < 1) page = 1; //確保頁碼不會小於1（因為沒有第0頁）。

                urlParams.set('page', page);
                window.location.href = window.location.pathname + '?' + urlParams.toString();
            });
        }

        if (nextPageLink) {
            nextPageLink.addEventListener('click', function(e) {
                e.preventDefault();
                let page = parseInt(urlParams.get('page') || '1') + 1; //這是計算下一頁的頁碼，將當前頁碼加1。
                if (page > <?= $total_page ?>) page = <?= $total_page ?>; //確保頁碼不會超過總頁數。

                urlParams.set('page', page); //更新 page 參數為計算出的新頁碼。
                window.location.href = window.location.pathname + '?' + urlParams.toString(); //將新的頁碼應用到URL並跳轉頁面。
            });
        }

        if (firstPageLink) {
            firstPageLink.addEventListener('click', function(e) {
                e.preventDefault();
                let page = 1;
                urlParams.set('page', page);
                window.location.href = window.location.pathname + '?' + urlParams.toString();
            });
        }

        if (lastPageLink) {
            lastPageLink.addEventListener('click', function(e) {
                e.preventDefault();
                let page = <?= $total_page ?>;
                urlParams.set('page', page);
                window.location.href = window.location.pathname + '?' + urlParams.toString();
            });
        }
    </script>


</body>

</html>