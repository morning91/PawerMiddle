<?php
require_once("../pdoConnect.php");
$sqlAll = "SELECT * FROM Petcommunicator WHERE valid=1 AND PetCommStatus = '未刊登'";
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
        $sql = "SELECT * FROM Petcommunicator WHERE valid=1 AND PetCommStatus = '未刊登' ORDER BY $orderID $orderValue LIMIT $start_item, $per_page ";
        $stmt = $dbHost->prepare($sql);
    } elseif (isset($_GET["search"])) {
        $search = $_GET["search"];
        $sql = "SELECT * FROM Petcommunicator WHERE PetCommName LIKE :search AND valid=1 AND PetCommStatus = '未刊登' ORDER BY $orderID $orderValue";
        $stmt = $dbHost->prepare($sql);
        $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    }
} else {
    header("location: StatusList.php?perPage=10&p=1&order=PetCommID:DESC");
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
            width: 25em;
        }

        #mainTable th:nth-child(5),
        #mainTable td:nth-child(5) {
            width: 15em;
        }

        #mainTable th:nth-child(6),
        #mainTable td:nth-child(6) {
            width: 10em;
        }

        .updateDate {
            right: 10px
        }

        .comment-row {
            overflow: hidden;
            transition: max-height 0.5s ease-out;
            max-height: 0;
        }

        .comment-row.open {
            max-height: 500px;
        }

        .dataTable-sorter {
            padding-right: 16px;
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
                <!-- 抬頭標題 -->
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
                                        <input type="search" class="form-control" name="search" placeholder="請搜尋溝通師名稱..." value="<?= isset($_GET["search"]) ? $search : "" ?>">
                                        <input type="hidden" name="perPage" value="<?= $per_page ?>">
                                        <input type="hidden" name="p" value="<?= $page ?>">
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
                                        <a class="nav-link active" href="StatusList.php">待審核名單</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="SoftDelList.php">刪除名單</a>
                                    </li>
                                </ul>
                                <div class="dataTable-container">
                                    <?php if ($CommCount > 0) : ?>
                                        <table class="table table-striped dataTable-table" id="mainTable">
                                            <thead>
                                                <!-- 類別標題 -->
                                                <tr>
                                                    <th data-sortable="" class="<?= $orderID == 'PetCommID' ? ($orderValue === 'ASC' ? 'asc' : 'desc') : '' ?>" aria-sort="descending"><a href="?perPage=<?= $per_page ?>&p=<?= $page ?>&order=PetCommID:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?><?= isset($_GET["search"]) ? "&search=" . $search : "" ?>" class="dataTable-sorter">編號</a></th>
                                                    <th class="<?= $orderID == 'PetCommName' ? ($orderValue === 'ASC' ? 'asc' : 'desc') : '' ?>" data-sortable=""><a href="?perPage=<?= $per_page ?>&p=<?= $page ?>&order=PetCommName:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?><?= isset($_GET["search"]) ? "&search=" . $search : "" ?>" class="dataTable-sorter">名稱</a></th>
                                                    <th class="<?= $orderID == 'PetCommSex' ? ($orderValue === 'ASC' ? 'asc' : 'desc') : '' ?>" data-sortable=""><a href="?perPage=<?= $per_page ?>&p=<?= $page ?>&order=PetCommSex:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?><?= isset($_GET["search"]) ? "&search=" . $search : "" ?>" class="dataTable-sorter">性別</a></th>
                                                    <th class="<?= $orderID == 'PetCommCertificateid' ? ($orderValue === 'ASC' ? 'asc' : 'desc') : '' ?>" data-sortable=""><a href="?perPage=<?= $per_page ?>&p=<?= $page ?>&order=PetCommCertificateid:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?><?= isset($_GET["search"]) ? "&search=" . $search : "" ?>" class="dataTable-sorter">證書編號</a></th>
                                                    <th class="<?= $orderID == 'PetCommCertificateDate' ? ($orderValue === 'ASC' ? 'asc' : 'desc') : '' ?>" data-sortable=""><a href="?perPage=<?= $per_page ?>&p=<?= $page ?>&order=PetCommCertificateDate:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?><?= isset($_GET["search"]) ? "&search=" . $search : "" ?>" class="dataTable-sorter">取證日期</a></th>
                                                    <th class="<?= $orderID == 'PetCommStatus' ? ($orderValue === 'ASC' ? 'asc' : 'desc') : '' ?>" data-sortable=""><a href="?perPage=<?= $per_page ?>&p=<?= $page ?>&order=PetCommStatus:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?><?= isset($_GET["search"]) ? "&search=" . $search : "" ?>" class="dataTable-sorter">刊登狀態</a></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                                </thead>
                                            <tbody>
                                                <?php foreach ($rows as $user): ?>
                                                    <!-- 資料清單 -->
                                                    <tr>
                                                        <td><?= $user["PetCommID"] ?></td>
                                                        <td><?= $user["PetCommName"] ?></td>
                                                        <td><?= $user["PetCommSex"] === "Female" ? "女" : "男" ?></td>
                                                        <td><?= $user["PetCommCertificateid"] ?></td>
                                                        <td><?= $user["PetCommCertificateDate"] ?></td>
                                                        <td><?= $user["PetCommStatus"] ?></td>
                                                        <td>
                                                            <a href="Edit-communicator.php?id=<?= $user["PetCommID"] ?>"> <i class="fa-solid fa-pen-to-square fa-lg"></i></a>
                                                        </td>
                                                        <td>
                                                            <a href="petcommunicator.php?id=<?= $user["PetCommID"] ?>"><i class="fa-solid fa-circle-info"></i></a>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-outline-primary card-control" id="cardControl-<?= $user["PetCommID"] ?>"><i class="fa-solid fa-angles-down"></i></button>
                                                        </td>
                                                    </tr>
                                                    <!-- 動態清單名片 -->
                                                    <tr id="cardlist-<?= $user["PetCommID"] ?>" class="card-list d-none ">
                                                        <td colspan="9">
                                                            <div class="comment position-relative">
                                                                <div class="comment-header">
                                                                    <div class="pr-50">
                                                                        <div class="avatar avatar-2xl">
                                                                            <img src="./images/<?= $user["PetCommImg"] ?>" alt="Avatar">
                                                                        </div>
                                                                    </div>
                                                                    <div class="comment-body">
                                                                        <div class="comment-profileName"><?= $user["PetCommName"] ?></div>
                                                                        <div class="comment-time">Email:<?= $user["PetCommEmail"] ?></div>
                                                                        <div class="comment-message">
                                                                            <p class="list-group-item-text truncate mb-20">
                                                                                [服務項目]<br><?= $user["PetCommService"] ?>
                                                                            </p>
                                                                            <p class="list-group-item-text truncate mb-20">
                                                                                [預約費用]<br><?= $user["PetCommFee"] ?>
                                                                            </p>
                                                                        </div>
                                                                        <a href="petcommunicator.php?id=<?= $user["PetCommID"] ?>" class="btn icon icon-left btn-primary me-2 text-nowrap"><i class="bi bi-eye-fill"></i> ShowALL</a>
                                                                    </div>
                                                                </div>
                                                                <div class="text-end position-absolute updateDate">
                                                                    <p>上次更新:<?= $user["PetCommUpdateDate"] ?></p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    <?php else : ?>
                                        查無溝通師
                                    <?php endif; ?>
                                </div>
                                <!-- 顯示總數 -->
                                <?php if (!isset($_GET["search"])) : ?>
                                    <div class="dataTable-bottom">
                                        <div class="dataTable-info">顯示 <?= $start_item + 1 ?> 到 <?= $start_item + $per_page ?> 共 <?= $CommCounts ?> 筆</div>
                                        <nav aria-label="Page navigation">
                                            <ul class=" pagination pagination-primary">
                                                <?php for ($i = 1; $i <= $total_page; $i++) : ?>
                                                    <li class="page-item <?php if ($page == $i) echo "active" ?>"><a href="StatusList.php?p=<?= $i ?>&perPage=<?= $per_page ?>&order=<?= $order ?>" class="page-link"><?= $i ?></a></li>
                                                <?php endfor; ?>
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
    <script>
        // 名片卡
        const cardControl = document.querySelectorAll('.card-control')
        cardControl.forEach(
            function(button) {
                button.addEventListener('click', function() {
                    const icon = button.querySelector('i');
                    const userId = button.id.split('-')[1];
                    const cardList = document.querySelector(`#cardlist-${userId}`);
                    if (cardList.classList.contains("d-none")) {
                        cardList.classList.remove("d-none");
                        icon.classList.remove("fa-angles-down");
                        icon.classList.add("fa-angles-up");
                    } else {
                        cardList.classList.add("d-none");
                        icon.classList.add("fa-angles-down");
                        icon.classList.remove("fa-angles-up");
                    }
                })
            })
    </script>
</body>

</html>