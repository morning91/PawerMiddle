<?php
require_once("../pdoConnect.php");
$sqlAll = "SELECT * FROM Petcommunicator WHERE valid=0";
$stmtAll = $dbHost->prepare($sqlAll);


$page = 1;
$start_item = 0;
$per_page = isset($_GET["perPage"]) ? $_GET["perPage"] : 5;
$orderID = 'PetCommID';
$orderValue = 'ASC';

if (isset($_GET["p"]) && isset($_GET["order"]) && isset($_GET["perPage"])) {
    $order = $_GET['order'];
    $orderArray = explode(':', $_GET['order']);
    $orderID = $orderArray[0];
    $orderValue = $orderArray[1] == 'DESC' ? 'DESC' : 'ASC';
    $page = $_GET["p"];
    $start_item = ($page - 1) * $per_page;
    if (!isset($_GET["search"])) {
        $sql = "SELECT * FROM Petcommunicator WHERE valid=0 ORDER BY $orderID $orderValue LIMIT $start_item, $per_page ";
        $stmt = $dbHost->prepare($sql);
    }elseif (isset($_GET["search"])) {
        $search = $_GET["search"];
        $sql = "SELECT * FROM Petcommunicator WHERE PetCommName LIKE :search AND valid=0 ORDER BY $orderID $orderValue";
        $stmt = $dbHost->prepare($sql);
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    }
} else {
    header("location: SoftDelList.php?perPage=10&p=1&order=PetCommID:DESC");
}

try {
    $stmtAll->execute();
    $CommCounts = $stmtAll->rowCount();


    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $CommCount = $stmt->rowCount();
} catch (PDOException $e) {
    echo "預處理陳述式執行失敗！ <br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    $dbHost = NULL;
    exit;
}
$total_page = ceil($CommCounts / $per_page);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>寵物溝通師管理</title>
    <?php include("../headlink.php") ?>
    <style>
        #mainTable th:nth-child(1),
        #mainTable td:nth-child(1) {
            width: 5em;
        }

        #mainTable th:nth-child(2),
        #mainTable td:nth-child(2) {
            width: 10em;
        }

        #mainTable th:nth-child(3),
        #mainTable td:nth-child(3) {
            width: 5em;
        }

        #mainTable th:nth-child(4),
        #mainTable td:nth-child(4) {
            width: 10em;
        }
    </style>
</head>

<body>
    <script src="../assets/static/js/initTheme.js"></script>
    <div id="app">
        <?php include("../sidebar.php") ?>
        <div id="main">
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>
            <div class="page-heading">
                <!-- 標題抬頭 -->
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3>寵物溝通師列表</h3>
                            <p class="text-subtitle text-muted"></p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html"><i class="fa-solid fa-house"></i></a></li>
                                    <li class="breadcrumb-item active" aria-current="page">寵物溝通師管理</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <section class="section">
                    <!-- 搜尋框 -->
                    <div class="card">
                        <div class="card-body">
                            <div class="dataTable-search">
                                <form action="">
                                    <div class="input-group ">
                                        <input type="search" class="form-control" name="search" placeholder="請搜尋溝通師名稱..." value="<?=isset($_GET["search"]) ? $search : ""?>">
                                        <input type="hidden" name="perPage" value="<?=$per_page?>">
                                        <input type="hidden" name="p" value="<?=$page?>">
                                        <input type="hidden" name="order" value="PetCommID:DESC">
                                        <button type="submit" class="btn btn-primary">搜尋</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <?php if (isset($_GET["search"])) : ?>
                                <a href="petcommunicators.php" class="btn btn-primary mb-2">返回</a>
                            <?php endif ?>
                            <div class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
                                <div class="dataTable-top">
                                    <!-- 每頁筆數 -->
                                    <?php if (!isset($_GET["search"])) : ?>
                                        <label>每頁</label>
                                        <div class="dataTable-dropdown">
                                            <form action="">
                                                <select class="dataTable-selector form-select" name="perPage" onchange="this.form.submit()">
                                                    <option value="5" <?= $_GET["perPage"] == 5 ? "selected" : "" ?>>5</option>
                                                    <option value="10" <?= $_GET["perPage"] == 10 ? "selected" : "" ?>>10</option>
                                                    <option value="15" <?= $_GET["perPage"] == 15 ? "selected" : "" ?>>15</option>
                                                    <option value="20" <?= $_GET["perPage"] == 20 ? "selected" : "" ?>>20</option>
                                                    <option value="25" <?= $_GET["perPage"] == 25 ? "selected" : "" ?>>25</option>
                                                </select>
                                                <input type="hidden" name="p" value="1">
                                                <input type="hidden" name="order" value="<?= $order ?>">
                                            </form>
                                        </div>
                                        <label>筆</label>
                                        <?php if (!isset($_GET["search"])) : ?>
                                            <div>
                                                <a href="Creat-communicator.php" class="btn btn-primary mb-2">新增師資</a>
                                            </div>
                                        <?php endif ?>
                                    <?php endif ?>
                                </div>
                                <!-- 頁籤 -->
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a class="nav-link" aria-current="page" href="petcommunicators.php">全部名單</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="StatusList.php">待審核名單</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link active" href="SoftDelList.php">刪除名單</a>
                                    </li>
                                </ul>
                                <div class="dataTable-container">
                                    <?php if ($CommCount > 0) : ?>
                                        <table class="table table-striped dataTable-table" id="mainTable">
                                            <thead>
                                                <!-- 類別標題,排序製作 -->
                                                <tr>
                                                    <th data-sortable="" class="<?= $orderID == 'PetCommID' ? ($orderValue === 'ASC' ? 'asc' : 'desc') : '' ?>" aria-sort="descending"><a href="?perPage=<?= $per_page ?>&p=<?= $page ?>&order=PetCommID:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?><?=isset($_GET["search"]) ? "&search=".$search : ""?>" class="dataTable-sorter">編號</a></th>
                                                    <th class="<?= $orderID == 'PetCommName' ? ($orderValue === 'ASC' ? 'asc' : 'desc') : '' ?>" data-sortable=""><a href="?perPage=<?= $per_page ?>&p=<?= $page ?>&order=PetCommName:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?><?=isset($_GET["search"]) ? "&search=".$search : ""?>" class="dataTable-sorter">名稱</a></th>
                                                    <th class="<?= $orderID == 'PetCommSex' ? ($orderValue === 'ASC' ? 'asc' : 'desc') : '' ?>" data-sortable=""><a href="?perPage=<?= $per_page ?>&p=<?= $page ?>&order=PetCommSex:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?><?=isset($_GET["search"]) ? "&search=".$search : ""?>" class="dataTable-sorter">性別</a></th>
                                                    <th class="<?= $orderID == 'PetCommUpdateUserID' ? ($orderValue === 'ASC' ? 'asc' : 'desc') : '' ?>" data-sortable=""><a href="?perPage=<?= $per_page ?>&p=<?= $page ?>&order=PetCommUpdateUserID:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?><?=isset($_GET["search"]) ? "&search=".$search : ""?>" class="dataTable-sorter">刪除者</a></th>
                                                    <th class="<?= $orderID == 'PetCommUpdateDate' ? ($orderValue === 'ASC' ? 'asc' : 'desc') : '' ?>" data-sortable=""><a href="?perPage=<?= $per_page ?>&p=<?= $page ?>&order=PetCommUpdateDate:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?><?=isset($_GET["search"]) ? "&search=".$search : ""?>" class="dataTable-sorter">刪除時間</a></th>
                                                    <th class="<?= $orderID == 'delreason' ? ($orderValue === 'ASC' ? 'asc' : 'desc') : '' ?>" data-sortable=""><a href="?perPage=<?= $per_page ?>&p=<?= $page ?>&order=delreason:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?><?=isset($_GET["search"]) ? "&search=".$search : ""?>" class="dataTable-sorter">原因</a></th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- 已刪除資料清單顯示,檢視.復原按鈕 -->
                                                <?php foreach ($rows as $user): ?>
                                                    <tr>
                                                        <td><?= $user["PetCommID"] ?></td>
                                                        <td><?= $user["PetCommName"] ?></td>
                                                        <td><?= $user["PetCommSex"] === "Female" ? "女" : "男" ?></td>
                                                        <td><?= $user["PetCommUpdateUserID"] ?></td>
                                                        <td><?= $user["PetCommUpdateDate"] ?></td>
                                                        <td><?= $user["delreason"] ?></td>
                                                        <td>
                                                            <a href="petcommunicator.php?id=<?= $user["PetCommID"] ?>"><i class="fa-solid fa-circle-info"></i></a>
                                                        </td>
                                                        <td>
                                                            <a href="WarningAlert.php?p=<?= $page ?>&order=<?= $orderID ?>:<?= $orderValue ?>&repost=<?= $user["PetCommID"] ?>&order=<?= $order ?>&perPage=<?= $per_page ?><?=isset($_GET["search"]) ? "&search=".$search : ""?>"><i class="fa-solid fa-user-check"></i></a>
                                                        </td>
                                                    </tr>
                                                <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    <?php else : ?>
                                        查無溝通師
                                    <?php endif; ?>
                                </div>
                                <?php if (!isset($_GET["search"])) : ?>
                                    <div class="dataTable-bottom">
                                        <div class="dataTable-info">顯示 <?= $start_item + 1 ?> 到 <?= $start_item + $per_page ?> 共 <?= $CommCounts ?> 筆</div>
                                        <!-- 動態分頁 -->
                                        <nav aria-label="Page navigation example">
                                            <ul class="pagination pagination-primary">
                                                <?php
                                                $display_pages = 3;
                                                $start = max(1, $page - floor($display_pages / 2));
                                                $end = min($total_page, $start + $display_pages - 1);
                                                if ($end - $start + 1 < $display_pages) {
                                                    $start = max(1, $end - $display_pages + 1);
                                                }
                                                $start = max(1, $start);
                                                ?>
                                                <li class="page-item <?= $page == 1 ? "d-none" : "" ?>"><a class="page-link" href="SoftDelList.php?p=<?= $page - 1 ?>&perPage=<?= $per_page ?>&order=<?= $order ?>">
                                                        <span aria-hidden="true"><i class="bi bi-chevron-left "></i></span>
                                                    </a></li>
                                                <?php for ($i = $start; $i <= $end; $i++) : ?>
                                                    <li class="page-item <?= $page == $i ? "active" : "" ?>"><a class="page-link" href="SoftDelList.php?p=<?= $i ?>&perPage=<?= $per_page ?>&order=<?= $order ?>"><?= $i ?></a></li>
                                                <?php endfor; ?>
                                                <li class="page-item <?= $page == $total_page ? "d-none" : "" ?>"><a class="page-link" href="SoftDelList.php?p=<?= $page + 1 ?>&perPage=<?= $per_page ?>&order=<?= $order ?>">
                                                        <span aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
                                                    </a></li>
                                            </ul>
                                        </nav>
                                    </div>
                                <?php endif ?>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <?php include("../footer.php") ?>
        </div>
    </div>
    <?php include("../js.php") ?>

</body>

</html>