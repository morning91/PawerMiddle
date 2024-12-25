<?php
require_once("../pdoConnect.php");

// 每頁(預設10)
$per_page = isset($_GET["per_page"]) ? $_GET["per_page"] : 10;
// 分頁(預設1)
$page = isset($_GET["p"]) ? $_GET["p"] : 1;
$start_item = ($page - 1) * $per_page;
$orderBy = "ORDER BY ArticleUpdateDate DESC"; // 預設排序

try {
    // 排序邏輯
    $sorter = isset($_GET["sorter"]) ? $_GET["sorter"] : -5;
    switch ($sorter) {
        case 1:
            $orderBy = "ORDER BY ArticleID ASC";
            break;
        case -1:
            $orderBy = "ORDER BY ArticleID DESC";
            break;
        case 2:
            $orderBy = "ORDER BY ArticleTitle ASC";
            break;
        case -2:
            $orderBy = "ORDER BY ArticleTitle DESC";
            break;
        case 3:
            $orderBy = "ORDER BY ArticleStatus ASC";
            break;
        case -3:
            $orderBy = "ORDER BY ArticleStatus DESC";
            break;
        case 4:
            $orderBy = "ORDER BY ArticleStartTime ASC";
            break;
        case -4:
            $orderBy = "ORDER BY ArticleStartTime DESC";
            break;
        case 5:
            $orderBy = "ORDER BY ArticleUpdateDate ASC";
            break;
        case -5:
            $orderBy = "ORDER BY ArticleUpdateDate DESC";
            break;
        default:
            $orderBy = "ORDER BY ArticleUpdateDate DESC";
            break;
    }

    // 初始查詢語句
    $sql = "SELECT article_db.*, image.ImageUrl
            FROM article_db
            LEFT JOIN image ON article_db.ArticleID = image.ArticleID
            WHERE ArticleValid=1 ";

    // 搜尋條件
    if (isset($_GET["searchName"])) {
        $search = $_GET["searchName"];
        $sql .= " AND (ArticleTitle LIKE '%$search%' OR ArticleStartTime LIKE '%$search%')";
    }

    if (isset($_GET["start_time"]) && !empty($_GET["start_time"])) {
        $start_time = $_GET["start_time"];
        $sql .= " AND ArticleStartTime >= '$start_time'";
    }
    if (isset($_GET["end_time"]) && !empty($_GET["end_time"])) {
        $end_time = $_GET["end_time"];
        $sql .= " AND ArticleStartTime <= '$end_time'";
    }

    $sql .= " $orderBy LIMIT $start_item, $per_page";

    // SQL查詢
    $stmt = $dbHost->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 文章總數
    $sqlAll = "SELECT COUNT(*) FROM article_db WHERE ArticleValid=1";
    if (isset($_GET["searchName"])) {
        $search = $_GET["searchName"];
        $sqlAll .= " AND (ArticleTitle LIKE '%$search%' OR ArticleStartTime LIKE '%$search%')";
    }
    if (isset($_GET["start_time"]) && !empty($_GET["start_time"])) {
        $start_time = $_GET["start_time"];
        $sqlAll .= " AND ArticleStartTime >= '$start_time'";
    }
    if (isset($_GET["end_time"]) && !empty($_GET["end_time"])) {
        $end_time = $_GET["end_time"];
        $sqlAll .= " AND ArticleStartTime <= '$end_time'";
    }

    $stmtCount = $dbHost->query($sqlAll);
    $articleCountAll = $stmtCount->fetchColumn();
    $articleAll = count($rows);

    // 總頁數計算
    $total_page = ceil($articleCountAll / $per_page);
} catch (PDOException $e) {
    echo "預處理陳述式執行失敗！ <br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    $dbHost = NULL;
    exit;
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文章管理</title>
    <?php include("../headlink.php") ?>
</head>
<style>
.text-truncate {
    max-width: 180px;
}
</style>

<body>
    <script src="../assets/static/js/initTheme.js"></script>
    <div id="app">
        <?php include("../sidebar.php") ?>
        <div id="main" class='layout-navbar navbar-fixed'>
            <div id="main-content">
                <div class="page-heading">
                    <div class="page-title">
                        <div class="row">
                            <div class="col-12 col-md-6 order-md-1 order-last">
                                <h3>文章管理</h3>
                            </div>
                            <div class="col-12 col-md-6 order-md-2 order-first">
                                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="index.html"><i
                                                    class="fa-solid fa-house"></i></a></li>
                                        <li class="breadcrumb-item active" aria-current="page">文章管理</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
                <!--搜尋框 -->
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="d-flex align-items-end justify-content-between">
                                <div class="form-group col-6">
                                    <label for="">文章標題</label>
                                    <input type="search" class="form-control" name="searchName" placeholder="搜尋文章標題"
                                        value="<?php echo isset($_GET["searchName"]) ?$_GET["searchName"] : ''; ?>">
                                </div>
                                <div class="form-group d-flex justify-content-between align-items-end col-5">
                                    <div class="col-5">
                                        <label for="">發佈時段</label>
                                        <input type="text" class="form-control flatpickr-no-config flatpickr-input"
                                            id="start_time" placeholder="選擇開始日期" name="start_time"
                                            value="<?= $_GET['start_time'] ?? '' ?>">
                                    </div>
                                    <div class="col-2 d-flex align-items-center justify-content-center mb-1">~</div>
                                    <div class="col-5">
                                        <input type="text" class="form-control flatpickr-no-config flatpickr-input"
                                            id="end_time" placeholder="選擇結束日期" name="end_time"
                                            value="<?= $_GET['end_time'] ?? '' ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-3">
                                <button type="submit" class="btn btn-primary me-1 mb-1">查詢</button>
                                <button type="reset" class="btn btn-light-secondary me-1 mb-1" id="clearBtn"><a
                                        href="../Article/ArticleList.php">清除</a></button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- 文章列表 -->
                <div class="card">
                    <div class="card-body">
                        <div
                            class="d-flex justify-content-between align-center dataTable-wrapper dataTable-loading no-footer sortable searchable">
                            <div class="dataTable-container col-6">
                                <div class="dataTable-top ">
                                    <form method="GET" action="">
                                        <!-- 隱藏的搜尋條件，確保搜尋條件不會因為筆數選擇而遺失 -->
                                        <input type="hidden" name="searchName" value="<?= $_GET["searchName"] ?? '' ?>">
                                        <input type="hidden" name="start_time" value="<?= $_GET["start_time"] ?? '' ?>">
                                        <input type="hidden" name="end_time" value="<?= $_GET["end_time"] ?? '' ?>">
                                        <input type="hidden" name="sorter" value="<?= $sorter ?>">
                                        <label>每頁</label>
                                        <div class="dataTable-dropdown">
                                            <select class="dataTable-selector form-select" name="per_page"
                                                onchange="if(this.form) this.form.submit();">
                                                <option value="5" <?= $per_page == 5 ? 'selected' : '' ?>>5</option>
                                                <option value="10" <?= $per_page == 10 ? 'selected' : '' ?>>10</option>
                                                <option value="15" <?= $per_page == 15 ? 'selected' : '' ?>>15</option>
                                                <option value="20" <?= $per_page == 20 ? 'selected' : '' ?>>20</option>
                                                <option value="25" <?= $per_page == 25 ? 'selected' : '' ?>>25</option>
                                            </select>
                                            <label>筆</label>
                                        </div>
                                    </form>
                                </div>

                            </div>
                            <div class="col-1 d-flex justify-content-end align-items-center">
                                <a href="../Article/CreateArticle.php"><button type="submit"
                                        class="btn btn-primary me-1 mb-1">新增</button></a>
                            </div>
                        </div>
                        <table class="table table-striped dataTable-table" id="table1">
                            <thead>
                                <tr>
                                    <th>編號</th>
                                    <th>封面圖片 </th>
                                    <th data-sortable="" class="<?= ($sorter == 2) ? 'asc' : (($sorter == -2) ? 'desc' : '') ?>"
                                        aria-sort="descending">
                                        <a href="ArticleList.php?p=<?= $page ?>&sorter=<?= ($sorter == 2) ? -2 : 2 ?>&searchName=<?= $_GET["searchName"] ?? '' ?>&start_time=<?= $_GET["start_time"] ?? '' ?>&end_time=<?= $_GET["end_time"] ?? '' ?>&per_page=<?= $per_page ?>"
                                            class="dataTable-sorter">文章標題</a>
                                    </th>
                                    <th data-sortable="" class="<?= ($sorter == 3) ? 'asc' : (($sorter == -3) ? 'desc' : '') ?>"
                                        aria-sort="descending">
                                        <a href="ArticleList.php?p=<?= $page ?>&sorter=<?= ($sorter == 3) ? -3 : 3?>&searchName=<?= $_GET["searchName"] ?? '' ?>&start_time=<?= $_GET["start_time"] ?? '' ?>&end_time=<?= $_GET["end_time"] ?? '' ?>&per_page=<?= $per_page ?>"
                                            class="dataTable-sorter">文章狀態</a>
                                    </th>
                                    <th data-sortable="" class="<?= ($sorter == 4) ? 'asc' : (($sorter == -4) ? 'desc' : '') ?>"
                                        aria-sort="descending">
                                        <a href="ArticleList.php?p=<?= $page ?>&sorter=<?= ($sorter == 4) ? -4 : 4?>&searchName=<?= $_GET["searchName"] ?? '' ?>&start_time=<?= $_GET["start_time"] ?? '' ?>&end_time=<?= $_GET["end_time"] ?? '' ?>&per_page=<?= $per_page ?>"
                                            class="dataTable-sorter">發佈時間</a>
                                    </th>
                                    <th data-sortable="" class="<?=($sorter == 5) ? "asc": (($sorter == -5) ? 'desc' : '' )?>"
                                        aria-sort="descending">
                                        <a href="ArticleList.php?p=<?= $page ?>&sorter=<?= ($sorter == 5) ? -5 : 5 ?>&searchName=<?= $_GET["searchName"] ?? '' ?>&start_time=<?= $_GET["start_time"] ?? '' ?>&end_time=<?= $_GET["end_time"] ?? '' ?>&per_page=<?= $per_page ?>"
                                            class="dataTable-sorter">更新時間</a>
                                    </th>
                                </tr>

                            </thead>
                            <tbody>
                                <?php if ($articleAll > 0): ?>
                                <?php $i = $start_item+1; foreach ($rows as $article) : ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td>
                                        <?php if (!empty($article["ImageUrl"])): ?>
                                        <img src="../upload/<?= $article["ImageUrl"] ?>" alt="Image" width="100"
                                            height="50" class="object-fit-cover">
                                        <?php else: ?>
                                        No Image
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-truncate"><?=$article["ArticleTitle"]?></td>
                                    <td><?= $article["ArticleStatus"] ==1 ? "已發布":"草稿"?></td>
                                    <td><?= $article["ArticleStartTime"] ?></td>
                                    <td><?= $article["ArticleUpdateDate"] ?></td>
                                    <td>
                                        <a href="../Article/UpdateArticle.php?id=<?= $article['ArticleID'] ?>"> <i
                                                class="fa-solid fa-pen-to-square fa-lg"></i></a>
                                    </td>
                                    <td>
                                        <a href="javascript:void(0);" class="fa-solid fa-trash-can"
                                            onclick="if (confirm('確定要刪除嗎')) { window.location.href='ArticleDelete.php?id=<?= $article['ArticleID'] ?>'; }">
                                        </a>
                                    </td>
                                    <?php endforeach; ?>
                                    <?php else: ?>
                                    <td colspan="8">目前沒有匹配查詢的文章</td>
                                    <?php endif;?>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($articleCountAll > 0) : ?>
                    <!-- 頁數 -->
                    <div class="dataTable-bottom">
                        <div class="dataTable-info ps-3">
                            顯示第 <?= $start_item + 1 ?> 到第 <?= min($start_item + $per_page, $start_item + $articleAll) ?>
                            筆，總共
                            <?= $articleCountAll ?> 筆
                            <?php endif; ?>
                        </div>
                        <!-- 分頁按鈕 -->
                        <?php if ($total_page > 1): ?>
                        <nav class="dataTable-pagination">
                            <ul class="dataTable-pagination-list pagination pagination-primary">
                                <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="ArticleList.php?searchName=<?= $_GET["searchName"] ?? '' ?>&start_time=<?= $_GET["start_time"] ?? '' ?>&end_time=<?= $_GET["end_time"] ?? '' ?>&per_page=<?= $per_page ?>&p=<?= $page - 1 ?>&sorter=<?= $sorter ?>">
                                        <span aria-hidden="true"><i class="bi bi-chevron-double-left"></i></span>
                                    </a>
                                </li>
                                <?php endif; ?>
                                <?php for ($i = 1; $i <= $total_page; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a href="ArticleList.php?p=<?= $i ?>&per_page=<?= $per_page ?>&searchName=<?= $_GET["searchName"] ?? '' ?>&start_time=<?= $_GET["start_time"] ?? '' ?>&end_time=<?=$_GET["end_time"] ?? '' ?>&sorter=<?= $sorter ?>"
                                        class="page-link"><?= $i ?></a>
                                </li>

                                <?php endfor; ?>
                                <?php if ($page < $total_page): ?>
                                <li class="page-item">
                                    <a class="page-link"
                                        href="ArticleList.php?p=<?= $total_page ?>&searchName=<?= $_GET["searchName"] ?? '' ?>&start_time=<?= $_GET["start_time"] ?? '' ?>&end_time=<?= $_GET["end_time"] ?? '' ?>&per_page=<?= $per_page ?>&sorter=<?= $sorter ?>">
                                        <span aria-hidden="true"><i class="bi bi-chevron-double-right"></i></span>
                                    </a>
                                </li>
                                <?php endif; ?>

                            </ul>
                        </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php include("../footer.php"); ?>
    </div>
    </div>
    </div>

</body>
<?php include("../js.php") ?>

<script>
document.querySelector('form').addEventListener('submit', function(event) {
    const startTime = document.getElementById('start_time').value;
    const endTime = document.getElementById('end_time').value;

    if (endTime && startTime && endTime < startTime) {
        alert("結束日期不能小於開始日期！");
        event.preventDefault();
    }
});
</script>

</html>