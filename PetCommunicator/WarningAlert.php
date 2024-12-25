<?php
require_once("../pdoConnect.php");


$page = 1;
$start_item = 0;
$per_page = $_GET["perPage"] ? $_GET["perPage"] : 5;
$orderID = 'PetCommID';
$orderValue = 'ASC';


if (isset($_GET['order'])) {
    $orderArray = explode(':', $_GET['order']);
    $orderID = $orderArray[0];
    $orderValue = $orderArray[1] == 'DESC' ? 'DESC' : 'ASC';
}

if (isset($_GET["p"])) {
    $page = $_GET["p"];
    $start_item = ($page - 1) * $per_page;
    if (isset($_GET["del"])) {
        $sqlAll = "SELECT * FROM Petcommunicator WHERE valid=1";
        $stmtAll = $dbHost->prepare($sqlAll);

        if (!isset($_GET["search"])) {
            $sql = "SELECT * FROM Petcommunicator WHERE valid=1 ORDER BY $orderID $orderValue LIMIT $start_item, $per_page ";
            $stmt = $dbHost->prepare($sql);
        }elseif (isset($_GET["search"])) {
            $search = $_GET["search"];
            $sql = "SELECT * FROM Petcommunicator WHERE PetCommName LIKE :search AND valid=1 ORDER BY $orderID $orderValue";
            $stmt = $dbHost->prepare($sql);
            $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        }
        
        $del = $_GET["del"];
        $delsql = "SELECT * FROM Petcommunicator WHERE PetCommID=$del AND valid=1";
        $delstmt = $dbHost->prepare($delsql);
    }
    if (isset($_GET["repost"])) {
        $sqlAll = "SELECT * FROM Petcommunicator WHERE valid=0";
        $stmtAll = $dbHost->prepare($sqlAll);

        if (!isset($_GET["search"])) {
            $sql = "SELECT * FROM Petcommunicator WHERE valid=0 ORDER BY $orderID $orderValue LIMIT $start_item, $per_page ";
            $stmt = $dbHost->prepare($sql);
        }elseif (isset($_GET["search"])) {
            $search = $_GET["search"];
            $sql = "SELECT * FROM Petcommunicator WHERE PetCommName LIKE :search AND valid=0 ORDER BY $orderID $orderValue";
            $stmt = $dbHost->prepare($sql);
            $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        }

        $repost = $_GET["repost"];
        $repostsql = "SELECT * FROM Petcommunicator WHERE PetCommID=$repost AND valid=0";
        $repoststmt = $dbHost->prepare($repostsql);
    }
} else {
    header("location: petcommunicators.php");
}

try {
    $stmtAll->execute();
    $CommCounts = $stmtAll->rowCount();
    if (isset($_GET["del"])) {
        $delstmt->execute();
        $delrow = $delstmt->fetch(PDO::FETCH_ASSOC);
    } elseif (isset($_GET["repost"])) {
        $repoststmt->execute();
        $repostrow = $repoststmt->fetch(PDO::FETCH_ASSOC);
    }
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
$c = ":"
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>寵物溝通師管理</title>
    <style>
        textarea {
            resize: none;
            /* 禁用調整大小功能 */
        }

        .warningalert {
            background: rgba(58, 58, 58, 0.438);
            height: 100%;
            width: 100%;
            position: fixed;
            z-index: 20;
        }

        .warningcard {
            background: #fff;
            width: 30em;
            height: 10em;
            position: relative;
        }

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
    <?php include("../headlink.php") ?>
    <style>
        textarea {
            resize: none;
            /* 禁用調整大小功能 */
        }
    </style>

</head>

<body>
    <script src="../assets/static/js/initTheme.js"></script>
    <!-- 刪除彈跳視窗 -->
    <?php if (isset($_GET["del"])) : ?>
        <div class="warningalert justify-content-center align-items-center d-flex">
            <form action="doSoftDel.php" method="post">
                <input type="hidden" name="PetCommID" id="" value="<?= $delrow["PetCommID"] ?>">
                <input type="hidden" name="valid" id="" value="0">
                <input type="hidden" name="page" id="" value=<?= $page ?>>
                <input type="hidden" name="order" id="" value="<?= $orderID . ':' . $orderValue ?>">
                <div class="warningcard card p-4">
                    <h1>確定要刪除?</h1>
                    <table class="table warningtable">
                        <thead>
                            <tr>
                                <th>編號</th>
                                <th>名稱</th>
                                <th>性別</th>
                                <th>狀態</th>
                            </tr>
                        </thead>
                        <tr>
                            <td><?= $delrow["PetCommID"] ?></td>
                            <td><?= $delrow["PetCommName"] ?></td>
                            <td><?= $delrow["PetCommSex"] === "Female" ? "女" : "男" ?></td>
                            <td><?= $delrow["PetCommStatus"] ?></td>
                        </tr>

                    </table>
                    <div class="form-group">
                        <label for="" class="">說明</label>
                        <textarea class="form-control mb-2" name="delreason" id="" rows="8"></textarea>
                    </div>
                    <div class="text-end">
                        <button type="sbumit" class="btn btn-danger">確定</button>
                        <a href="petcommunicators.php?p=<?= $page ?>&order=<?= $orderID ?>:<?= $orderValue ?>&perPage=<?= $per_page ?><?=isset($_GET["search"]) ? "&search=".$search : ""?>" class="btn btn-secondary">取消</a>
                    </div>
                </div>
            </form>
        </div>
    <?php endif ?>
    <!-- 復原彈跳視窗 -->
    <?php if (isset($_GET["repost"])) : ?>
        <div class="warningalert justify-content-center align-items-center d-flex">
            <form action="doRepost.php" method="post">
                <input type="hidden" name="PetCommID" id="" value="<?= $repostrow["PetCommID"] ?>">
                <input type="hidden" name="valid" id="" value="1">
                <input type="hidden" name="page" id="" value=<?= $page ?>>
                <input type="hidden" name="order" id="" value="<?= $orderID . ':' . $orderValue ?>">
                <div class="warningcard card p-4">
                    <h1>確定要復原?</h1>
                    <table class="table warningtable">
                        <thead>
                            <tr>
                                <th>編號</th>
                                <th>名稱</th>
                                <th>性別</th>

                            </tr>
                        </thead>
                        <tr>
                            <td><?= $repostrow["PetCommID"] ?></td>
                            <td><?= $repostrow["PetCommName"] ?></td>
                            <td><?= $repostrow["PetCommSex"] === "Female" ? "女" : "男" ?></td>

                        </tr>

                    </table>
                    <div class="form-group">
                        <label class="form-label" for="">被刪除原因</label>
                        <textarea class="form-control" name="" id="" readonly><?= $repostrow["delreason"] ?></textarea>
                    </div>
                    <div class="text-end">
                        <button type="sbumit" class="btn btn-success">確定</button>
                        <a href="SoftDelList.php?p=<?= $page ?>&order=<?= $orderID ?>:<?= $orderValue ?>&perPage=<?= $per_page ?><?=isset($_GET["search"]) ? "&search=".$search : ""?>" class="btn btn-secondary">取消</a>
                    </div>
                </div>
            </form>
        </div>
    <?php endif ?>

    <div id="app">
        <?php include("../sidebar.php") ?>
        <div id="main">
            <!-- RWD漢堡 -->
            <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>
            <div class="page-heading">
                <!-- 主標題 -->
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
                    <!-- 背景搜尋框 -->
                    <div class="card">
                        <div class="card-body">
                            <div class="dataTable-search">
                                <form action="">
                                    <div class="input-group ">
                                        <input type="search" class="form-control" name="search" placeholder="請搜尋溝通師名稱..." value="<?=isset($_GET["search"]) ? $search : ""?>" >
                                        <button type="submit" class="btn btn-primary">搜尋</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- 背景主資料清單背景 -->
                    <div class="card">
                        <div class="card-body">
                            <?php if (isset($_GET["search"])) : ?>
                                <a href="petcommunicators.php" class="btn btn-primary mb-2">返回</a>
                            <?php endif ?>
                            <div class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
                                <div class="dataTable-top">
                                    <?php if (!isset($_GET["search"])) : ?>
                                        <label>每頁</label>
                                        <div class="dataTable-dropdown">
                                            <select class="dataTable-selector form-select">
                                                <option value="5" <?= $_GET["perPage"] == 5 ? "selected" : "" ?>>5</option>
                                                <option value="10" <?= $_GET["perPage"] == 10 ? "selected" : "" ?>>10</option>
                                                <option value="15" <?= $_GET["perPage"] == 15 ? "selected" : "" ?>>15</option>
                                                <option value="20" <?= $_GET["perPage"] == 20 ? "selected" : "" ?>>20</option>
                                                <option value="25" <?= $_GET["perPage"] == 25 ? "selected" : "" ?>>25</option>
                                            </select>
                                        </div>
                                        <label>筆</label>
                                        <?php if (!isset($_GET["search"])) : ?>
                                            <div>
                                                <a href="Creat-communicator.php" class="btn btn-primary mb-2">新增師資</a>
                                            </div>
                                        <?php endif ?>
                                    <?php endif ?>
                                </div>
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <a class="nav-link <?= isset($_GET["del"]) ? "active" : "" ?>" aria-current="page" href="petcommunicators.php">全部名單</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" href="StatusList.php">待審核名單</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?= isset($_GET["repost"]) ? "active" : "" ?>" href="SoftDelList.php">刪除名單</a>
                                    </li>
                                </ul>
                                <div class="dataTable-container">
                                    <?php if ($CommCount > 0 && isset($_GET["del"])) : ?>
                                        <table class="table table-striped dataTable-table" id="table1">
                                            <thead>
                                                <tr>
                                                    <th data-sortable="" class="desc" aria-sort="descending"><a href="?p=<?= $page ?>&order=PetCommID:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?>" class="dataTable-sorter">編號</a></th>
                                                    <th data-sortable=""><a href="?p=<?= $page ?>&order=PetCommName:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?>" class="dataTable-sorter">名稱</a></th>
                                                    <th data-sortable=""><a href="?p=<?= $page ?>&order=PetCommSex:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?>" class="dataTable-sorter">性別</a></th>
                                                    <th data-sortable=""><a href="?p=<?= $page ?>&order=PetCommCertificateid:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?>" class="dataTable-sorter">證書編號</a></th>
                                                    <th data-sortable=""><a href="?p=<?= $page ?>&order=PetCommCertificateDate:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?>" class="dataTable-sorter">取證日期</a></th>
                                                    <th data-sortable=""><a href="?p=<?= $page ?>&order=PetCommStatus:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?>" class="dataTable-sorter">刊登狀態</a></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($rows as $user): ?>
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
                                                            <a href="WarningAlert.php?p=<?= $page ?>&order=<?= $orderID ?>:<?= $orderValue ?>&del=<?= $user["PetCommID"] ?>&order=<?= $order ?>&perPage=<?= $per_page ?><?=isset($_GET["search"]) ? "&search=".$search : ""?>"><i class="fa-solid fa-trash-can"></i></a>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-outline-primary card-control " id="cardControl-<?= $user["PetCommID"] ?>"><i class="fa-solid fa-angles-down"></i></button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    <?php elseif ($CommCount > 0 && isset($_GET["repost"])) :  ?>
                                        <table class="table table-striped dataTable-table" id="mainTable">
                                            <thead>
                                                <tr>
                                                    <th data-sortable="" class="desc" aria-sort="descending"><a href="?perPage=<?= $per_page ?>&p=<?= $page ?>&order=PetCommID:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?>" class="dataTable-sorter">編號</a></th>
                                                    <th data-sortable=""><a href="?perPage=<?= $per_page ?>&p=<?= $page ?>&order=PetCommName:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?>" class="dataTable-sorter">名稱</a></th>
                                                    <th data-sortable=""><a href="?perPage=<?= $per_page ?>&p=<?= $page ?>&order=PetCommSex:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?>" class="dataTable-sorter">性別</a></th>
                                                    <th data-sortable=""><a href="?perPage=<?= $per_page ?>&p=<?= $page ?>&order=PetCommCertificateid:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?>" class="dataTable-sorter">刪除者</a></th>
                                                    <th data-sortable=""><a href="?perPage=<?= $per_page ?>&p=<?= $page ?>&order=PetCommCertificateDate:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?>" class="dataTable-sorter">刪除時間</a></th>
                                                    <th data-sortable=""><a href="" class="dataTable-sorter">原因</a></th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
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
                                                            <a href="WarningAlert.php?p=<?= $page ?>&order=<?= $orderID ?>:<?= $orderValue ?>&repost=<?= $user["PetCommID"] ?>&order=<?= $order ?>&perPage=<?= $per_page ?>"><i class="fa-solid fa-user-check"></i></a>
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
                                        <nav aria-label="Page navigation">
                                            <ul class=" pagination pagination-primary">
                                                <?php for ($i = 1; $i <= $total_page; $i++) : ?>
                                                    <li class="page-item <?php if ($page == $i) echo "active" ?>"><a href="petcommunicators.php?p=<?= $i ?>" class="page-link"><?= $i ?></a></li>
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
        const delBtn = document.querySelector("#delBtn");
        const warningAlert = document.querySelector("#warningAlert");
        delBtn.addEventListener("click", function() {
            warningAlert.classList.add('flex');
        })
    </script>
</body>

</html>