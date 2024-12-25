<?php
require_once("../pdoConnect.php");

if (!isset($_GET['order']) && !isset($_GET['search']) && !isset($_GET['start_time']) && !isset($_GET['end_time'])) {
    header("Location: OfficialEventsList.php?p=1&order=99");
    exit;
} // 預設頁面，在沒有GET到其他值的情況下，會自動導到此路徑

$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
$per_page = ($per_page > 0) ? $per_page : 10; // 確保 $per_page 不為零
$start_item = ($page - 1) * $per_page;
$search = $_GET["search"] ?? "";
$start_time = $_GET["start_time"] ?? "";
$end_time = $_GET["end_time"] ?? "";
$order = isset($_GET["order"]) ? intval($_GET["order"]) : 99;


// $sql .= " LIMIT $start_item, $per_page";
try {

    $sql = "SELECT OfficialEvent.*, 
    IFNULL(EventParticipants.ParticipantCount, 0) AS ParticipantCount,
    CASE
        WHEN IFNULL(EventParticipants.ParticipantCount, 0) >= OfficialEvent.EventParticipantLimit THEN '已額滿'
        WHEN CURRENT_TIMESTAMP > OfficialEvent.EventSignEndTime THEN '截止報名'
        WHEN CURRENT_TIMESTAMP BETWEEN OfficialEvent.EventSignStartTime AND OfficialEvent.EventSignEndTime THEN '報名中'
        ELSE '未開放'
    END AS newEventStatus
FROM OfficialEvent
LEFT JOIN (
 SELECT EventID, COUNT(*) AS ParticipantCount
 FROM EventParticipants
 WHERE RegistrationStatus = 1
 GROUP BY EventID
) AS EventParticipants ON OfficialEvent.EventID = EventParticipants.EventID
WHERE OfficialEvent.EventValid = 1";
    //之前在HTML裡的判斷，之後可以比對參考，改用上面的寫法更快，newEventStatus 是後來新增的變數
    //  <?php
    //  $currentDate = new DateTime();
    //  $EventSignStartTime = new DateTime($event["EventSignStartTime"]);
    //  $EventSignEndTime = new DateTime($event["EventSignEndTime"]);
    //  if ($event["ParticipantCount"] >= $event["EventParticipantLimit"]) {
    //      $status = "已額滿";
    //  } elseif ($currentDate > $EventSignEndTime) {
    //      $status = "截止報名";
    //  } elseif ($currentDate >= $EventSignStartTime && $currentDate <= $EventSignEndTime) {
    //      $status = "報名中";
    //  } else {
    //      $status = "未開放";
    //  }
    //  

    // 處理搜尋
    if (isset($_GET["search"])) {
        $search = $_GET["search"];
        $search = htmlspecialchars($search); // 處理特殊字符，防止 XSS
        $sql .= " AND EventTitle LIKE '%$search%'";
    }
    if (isset($_GET["start_time"]) && !empty($_GET["start_time"])) {
        $start_time = $_GET["start_time"];
        $sql .= " AND EventStartTime >= '$start_time'";
    }
    if (isset($_GET["end_time"]) && !empty($_GET["end_time"])) {
        $end_time = $_GET["end_time"];
        $sql .= " AND EventEndTime <= '$end_time'";
    }

    // 處理排序
    if (isset($_GET["order"]) && !empty($_GET["order"])) {
        $order = $_GET["order"];
        $page = $_GET["p"];
        $start_item = ($page - 1) * $per_page;
        switch ($order) {
            case 99:
                $sql .= " ORDER BY EventUpdateDate DESC";
                break;
            case 1:
                $sql .= " ORDER BY EventTitle ASC";
                break;
            case 2:
                $sql .= " ORDER BY EventTitle DESC";
                break;
            case 3:
                $sql .= " ORDER BY CASE newEventStatus
                WHEN '未開放' THEN 1
                WHEN '報名中' THEN 2
                WHEN '截止報名' THEN 3
                WHEN '已額滿' THEN 4
            END ASC";
                break;
            case 4:
                $sql .= " ORDER BY CASE newEventStatus
                WHEN '未開放' THEN 4
                WHEN '報名中' THEN 3
                WHEN '截止報名' THEN 2
                WHEN '已額滿' THEN 1
            END ASC";
                break;
            case 5:
                $sql .= " ORDER BY EventStartTime DESC";
                break;
            case 6:
                $sql .= " ORDER BY EventStartTime ASC";
                break;
            case 7:
                $sql .= " ORDER BY EventLocation ASC";
                break;
            case 8:
                $sql .= " ORDER BY EventLocation DESC";
                break;
            case 9:
                $sql .= " ORDER BY 	EventParticipantLimit ASC";
                break;
            case 10:
                $sql .= " ORDER BY 	EventParticipantLimit DESC";
                break;
            case 11:
                $sql .= " ORDER BY ParticipantCount ASC";
                break; //預留給篩選已報名欄位
            case 12:
                $sql .= " ORDER BY ParticipantCount DESC";
                break; //預留給篩選已報名欄位
            case 13:
                $sql .= " ORDER BY 	EventFee ASC";
                break;
            case 14:
                $sql .= " ORDER BY 	EventFee DESC";
                break;
            case 15:
                $sql .= " ORDER BY 	EventStatus ASC";
                break;
            case 16:
                $sql .= " ORDER BY 	EventStatus DESC";
                break;
            default:
                header("location:OfficialEventsList.php?p=1&order=0");
                exit;
        }
    }
    $sql .= " LIMIT $start_item, $per_page"; // 根據選擇的行數顯示相應數量的結果

    // 檢查 $dbHost 是否為 null
    if ($dbHost === null) {
        throw new Exception("數據庫連接失敗");
    }

    $stmt = $dbHost->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // 總數量計算
    $sqlCount = "SELECT COUNT(*) FROM OfficialEvent WHERE EventValid = 1";
    if (isset($_GET["search"])) {
        $search = $_GET["search"];
        $search = htmlspecialchars($search); // htmlspecialchars是用來處理特殊字符，防止 XSS的先放
        $sqlCount .= " AND EventTitle LIKE '%$search%'";
    }
    if (isset($_GET["start_time"]) && !empty($_GET["start_time"])) {
        $start_time = $_GET["start_time"];
        $sqlCount .= " AND EventStartTime >= '$start_time'";
    }
    if (isset($_GET["end_time"]) && !empty($_GET["end_time"])) {
        $end_time = $_GET["end_time"];
        $sqlCount .= " AND EventEndTime <= '$end_time'";
    }

    $stmtCount = $dbHost->query($sqlCount);
    $eventCountAll = $stmtCount->fetchColumn();
    $eventCount = count($rows);

    if ($eventCountAll > 0) {
        $total_Page = ceil($eventCountAll / $per_page);
    } else {
        $total_Page = 1; // 預設沒有結果時的頁數位置
    }
} catch (PDOException $e) {
    echo "預處理陳述式執行失敗！<br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    $dbHost = NULL;
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OfficialEventList</title>

    <?php include("../headlink.php") ?>
    <style>
        .flatpickr-time {
            display: none;
        }

        .form-switch {
            margin-left: 1.2rem;
        }

        .text-truncate {
            max-width: 200px;
            min-width: 120px;
        }

        .searchbox2 {
            flex-grow: 1;
        }

        .searchbox3 {
            flex: 1 1 auto;
            width: 12%;
        }
    </style>
</head>

<body>
    <script src="../assets/static/js/initTheme.js"></script>
    <div id="app">
        <?php include("../sidebar.php") ?>
        <div id="main">
            <header class="">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>
            <div class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3 class="">活動管理</h3>
                            <p class="text-subtitle text-muted"></p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html"><i class="fa-solid fa-house"></i></a></li>
                                    <li class="breadcrumb-item active" aria-current="page">活動管理</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <section class="section">
                    <div class="card">
                        <div class="card-body">
                            <form action="">
                                <div class="row align-items-center">
                                    <div class="col-lg-6 col-md-4 col-12">
                                        <div class="form-group">
                                            <label class="" for="">活動查詢</label>
                                            <input type="hidden" name="p" value="1"> <!-- 將頁碼固定為 1 -->
                                            <input type="hidden" name="order" value="<?= $order ?>">
                                            <input type="search" id="" class="form-control" placeholder="請輸入活動標題關鍵字" name="search" value="<?php echo isset($_GET["search"]) ? htmlspecialchars($_GET["search"]) : "" ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-4 col-12 searchbox2">
                                        <div class="row form-group align-items-center">
                                            <label for="">活動時間</label>
                                            <div class="col">
                                                <input type="search" class=" form-control  flatpickr-no-config active " placeholder="開始時間" readonly="readonly" name="start_time" value="<?php echo isset($_GET['start_time']) ? $_GET['start_time'] : '' ?>">
                                            </div>
                                            -
                                            <div class="col">
                                                <input type="text" class=" form-control  flatpickr-no-config active " placeholder="結束時間" readonly="readonly" name="end_time" value="<?php echo isset($_GET['end_time']) ? $_GET['end_time'] : '' ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-md-4 col-12 searchbox3">
                                        <div class="col d-flex align-items-center pt-3 justify-content-end">
                                            <button type="submit" class="btn btn-primary me-1 mb-1">查詢</button>
                                            <button type="reset" class="btn btn-light-secondary me-1 mb-1"><a href="./OfficialEventsList.php?p=1&order=99" class="text-body">清除</a> </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- ///////////////////////////////////////////////// -->
                    <div class="card">
                        <div class="card-body">
                            <div class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns  ps-2 pe-2">
                                <div class="row align-items-center">
                                    <div class="dataTable-top ">
                                        <div class="col-auto">
                                            <form action="" method="get">
                                                <input type="hidden" name="p" value="<?= $page ?>">
                                                <input type="hidden" name="order" value="<?= $order ?>">
                                                <input type="hidden" name="search" value="<?php echo isset($search) ? $search : ''; ?>">
                                                <input type="hidden" name="start_time" value="<?php echo isset($start_time) ? $start_time : ''; ?>">
                                                <input type="hidden" name="end_time" value="<?php echo isset($end_time) ? $end_time : ''; ?>">
                                                <label>每頁</label>
                                                <div class="dataTable-dropdown">
                                                    <select name="per_page" class="dataTable-selector form-select" onchange="this.form.p.value=1; if(this.form)this.form.submit();">
                                                        <option value="5" <?= ($per_page == 5) ? 'selected' : '' ?>>5</option>
                                                        <option value="10" <?= ($per_page == 10) ? 'selected' : '' ?>>10</option>
                                                        <option value="15" <?= ($per_page == 15) ? 'selected' : '' ?>>15</option>
                                                        <option value="20" <?= ($per_page == 20) ? 'selected' : '' ?>>20</option>
                                                    </select>
                                                </div>
                                                <label>筆</label>
                                            </form>
                                        </div>
                                        <div class="col-auto text-end align-conter-center">
                                            <button class="btn btn-primary" type="button"><a class="text-white" href="./CreateEvent.php"> 新增 </a></button>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <div class="dataTable-container">
                                <?php if ($eventCount > 0): ?>

                                    <table class="table table-striped dataTable-table" id="table1">
                                        <thead class="">
                                            <tr>

                                                <th data-sortable=""><a href="OfficialEventsList.php?p=<?= $page ?>&order=<?php if ($order == 1) echo "2";
                                                                                                                            else echo "1"; ?>&per_page=<?= $_GET["per_page"] ?? "" ?>&search=<?= $_GET["search"] ?? "" ?>&start_time=<?= $_GET["start_time"] ?? "" ?>&end_time=<?= $_GET["end_time"] ?? "" ?>"
                                                        class="dataTable-sorter">活動標題</a></th>
                                                <th data-sortable=""><a href="OfficialEventsList.php?p=<?= $page ?>&order=<?php if ($order == 3) echo "4";
                                                                                                                            else echo "3"; ?>&per_page=<?= $_GET["per_page"] ?? "" ?>&search=<?= $_GET["search"] ?? "" ?>&start_time=<?= $_GET["start_time"] ?? "" ?>&end_time=<?= $_GET["end_time"] ?? "" ?>" class="dataTable-sorter">活動狀態</a></th>
                                                <th data-sortable=""><a href="OfficialEventsList.php?p=<?= $page ?>&order=<?php if ($order == 5) echo "6";
                                                                                                                            else echo "5"; ?>&per_page=<?= $_GET["per_page"] ?? "" ?>&search=<?= $_GET["search"] ?? "" ?>&start_time=<?= $_GET["start_time"] ?? "" ?>&end_time=<?= $_GET["end_time"] ?? "" ?>" class="dataTable-sorter">活動日期</a></th>
                                                <th data-sortable=""><a href="OfficialEventsList.php?p=<?= $page ?>&order=<?php if ($order == 7) echo "8";
                                                                                                                            else echo "7"; ?>&per_page=<?= $_GET["per_page"] ?? "" ?>&search=<?= $_GET["search"] ?? "" ?>&start_time=<?= $_GET["start_time"] ?? "" ?>&end_time=<?= $_GET["end_time"] ?? "" ?>" class="dataTable-sorter">地區</a></th>

                                                <th data-sortable=""><a href="OfficialEventsList.php?p=<?= $page ?>&order=<?php if ($order == 11) echo "12";
                                                                                                                            else echo "11"; ?>&per_page=<?= $_GET["per_page"] ?? "" ?>&search=<?= $_GET["search"] ?? "" ?>&start_time=<?= $_GET["start_time"] ?? "" ?>&end_time=<?= $_GET["end_time"] ?? "" ?>" class="dataTable-sorter">報名人數</a></th>
                                                <th data-sortable=""><a href="OfficialEventsList.php?p=<?= $page ?>&order=<?php if ($order == 13) echo "14";
                                                                                                                            else echo "13"; ?>&per_page=<?= $_GET["per_page"] ?? "" ?>&search=<?= $_GET["search"] ?? "" ?>&start_time=<?= $_GET["start_time"] ?? "" ?>&end_time=<?= $_GET["end_time"] ?? "" ?>" class="dataTable-sorter">金額</a></th>
                                                <th data-sortable=""><a href="OfficialEventsList.php?p=<?= $page ?>&order=<?php if ($order == 15) echo "16";
                                                                                                                            else echo "15"; ?>&per_page=<?= $_GET["per_page"] ?? "" ?>&search=<?= $_GET["search"] ?? "" ?>&start_time=<?= $_GET["start_time"] ?? "" ?>&end_time=<?= $_GET["end_time"] ?? "" ?>" class="dataTable-sorter">上架狀態</a></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($rows as $event): ?>
                                                <tr>
                                                    <!-- <td>全網站</td> -->

                                                    <td class="text-truncate"><?= $event["EventTitle"] ?></td>

                                                    <td><?= $event["newEventStatus"] ?></td>
                                                    <td>
                                                        <ul class="list-group list-unstyled">
                                                            <?php $newEventStartTime = (new DateTime($event["EventStartTime"]))->format('Y-m-d H:i');
                                                            $newEventEndTime = (new DateTime($event["EventEndTime"]))->format('Y-m-d H:i') ?>
                                                            <li><?= $newEventStartTime ?></li>
                                                            <li><?= $newEventEndTime ?></li>
                                                        </ul>
                                                    </td>
                                                    <?php
                                                    $location = '線上'; // 預設為線上活動
                                                    if (strpos($event["EventRegion"], 'north') !== false) {
                                                        $location = '北部';
                                                    } elseif (strpos($event["EventRegion"], 'central') !== false) {
                                                        $location = '中部';
                                                    } elseif (strpos($event["EventRegion"], 'south') !== false) {
                                                        $location = '南部';
                                                    } elseif (strpos($event["EventRegion"], 'east') !== false) {
                                                        $location = '東部';
                                                    }
                                                    ?><td><?= $location ?></td>
                                                    <!-- <td><?= $event["EventParticipantLimit"] ?></td> -->
                                                    <td class=""><?= $event["ParticipantCount"] ?>/<?= $event["EventParticipantLimit"] ?></td>
                                                    <?php
                                                    $EventFee = $event["EventFee"];
                                                    $newEventFee = number_format($EventFee, 0) ?>
                                                    <td class=""><?= $newEventFee ?>

                                                    </td>
                                                    <td>
                                                        <div class="form-check form-switch">
                                                            <?php
                                                            $newEventStatus = $event["EventStatus"];
                                                            ?>
                                                            <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault_<?= $event["EventID"] ?>"
                                                                <?= $newEventStatus == 'published' ? 'checked' : '' ?>>

                                                            <label class="form-check-label" for="flexSwitchCheckDefault"></label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <a href="./EventEdit.php?id=<?= $event["EventID"] ?>"> <i class="fa-solid fa-pen-to-square fa-lg"></i></a>
                                                    </td>
                                                    <td>
                                                        <a href="pdoDeleteEvent.php?id=<?= $event["EventID"] ?>"><i class="fa-solid fa-trash-can fa-lg"></i></a>
                                                    </td>


                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <p class="ps-2 pt-3 text-muted">沒有相關的活動，請重新輸入關鍵詞</p>
                                <?php endif; ?>
                            </div>
                            <div class="dataTable-bottom">
                                <div class="dataTable-info">顯示 <?= $start_item + 1 ?> 到 <?= min($start_item + $per_page, $eventCountAll) ?> 筆，共 <?= $total_Page ?> 頁，共 <?= $eventCountAll ?> 筆</div>
                                <nav class="dataTable-pagination justify-content-center">
                                    <ul class="dataTable-pagination-list pagination pagination-primary">
                                        <li class="pager page-item">
                                            <a href="OfficialEventsList.php?p=<?= max(1, $page - 1) ?>&order=<?= $order ?>&per_page=<?= $_GET["per_page"] ?? "" ?>&search=<?= $_GET["search"] ?? "" ?>&start_time=<?= $_GET["start_time"] ?? "" ?>&end_time=<?= $_GET["end_time"] ?? "" ?>" data-page="1" class="page-link">‹</a>
                                        </li>
                                        <?php for ($i = 1; $i <= $total_Page; $i++): ?>
                                            <li class="page-item">
                                                <a href="OfficialEventsList.php?p=<?= $i ?>&order=<?= $order ?>&per_page=<?= $_GET["per_page"] ?? "" ?>&search=<?= $_GET["search"] ?? "" ?>&start_time=<?= $_GET["start_time"] ?? "" ?>&end_time=<?= $_GET["end_time"] ?? "" ?>" data-page="<?= $i ?>" class="page-link <?php if ($page == $i) echo "active" ?>">
                                                    <?= $i ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>
                                        <li class="pager page-item">
                                            <a href="OfficialEventsList.php?p=<?= min($total_Page, $page + 1) ?>&order=<?= $order ?>&per_page=<?= $_GET["per_page"] ?? "" ?>&search=<?= $_GET["search"] ?? "" ?>&start_time=<?= $_GET["start_time"] ?? "" ?>&end_time=<?= $_GET["end_time"] ?? "" ?>" data-page="<?= ($i + 1) ?>" class="page-link">›</a>
                                        </li>
                                    </ul>
                                </nav>

                            </div>
                        </div>
                    </div>
            </div>
            </section>
            <?php include("../footer.php") ?>
        </div>

    </div>
    </div>
    <?php include("../js.php") ?>
    <script>
        flatpickr('.flatpickr-no-config', {
            enableTime: true,
            dateFormat: "Y-m-d",
        })

        // 控制更改狀態
        document.querySelectorAll('.form-check-input').forEach(function(checkbox) {

            checkbox.addEventListener('change', function() {
                // 获取开关的状态
                const isChecked = this.checked;
                // 根据状态设置新的事件状态
                const newStatus = isChecked ? 'published' : 'draft';
                // 获取事件 ID
                const id = this.id.split('_')[1];

                // 使用 AJAX 发送请求到 PHP 脚本
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'pdoStatusUpdate.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                        console.log('Status updated successfully');
                    }
                };
                xhr.send('event_id=' + id + '&status=' + newStatus);
            });
        });
    </script>



</body>

</html>