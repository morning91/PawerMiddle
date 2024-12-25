<?php
require_once("../pdoConnect.php");

// 每頁筆數
$per_page = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10; // 預設為 10
$startPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($startPage - 1) * $per_page;
$orderID = 'product_id ';
$orderValue = 'ASC';
$product_id = $_GET["product_id"];

//排序
if (isset($_GET['order'])) {
    $orderArray = explode(':', $_GET['order']);
    $orderID = $orderArray[0];
    $orderValue = $orderArray[1] == 'DESC' ? 'DESC' : 'ASC';
}

// 搜尋
$search = isset($_GET["search"]) ? $_GET["search"] : '';
$brand = isset($_GET["brand"]) ? $_GET["brand"] : ''; // 新增品牌變數
$category = isset($_GET["category"]) ? $_GET["category"] : ''; // 新增類別變數
$sub = isset($_GET["sub"]) ? $_GET["sub"] : ''; // 新增分類變數


$sql = "SELECT * FROM product WHERE product_valid=1";
if ($search) {
    $sql .= " AND product_name LIKE :search";
}
if ($brand) {
    $sql .= " AND product_brand = :brand"; // 根據品牌過濾
}
if ($category) {
    $sql .= " AND product_category_name = :category"; // 根據類別過濾
}
if ($sub) {
    $sql .= " AND product_sub_category = :sub"; // 根據分類過濾
}
$sql .= " ORDER BY $orderID $orderValue LIMIT :limit OFFSET :offset";

$stmt = $dbHost->prepare($sql);

// 綁定參數做篩選搜尋
if ($search) {
    $stmt->bindValue(':search', '%' . $search . '%');
}
if ($brand) {
    $stmt->bindValue(':brand', $brand);
}
if ($category) {
    $stmt->bindValue(':category', $category);
}
if ($sub) {
    $stmt->bindValue(':sub', $sub);
}

$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

// 總商品數顯示分頁
$countPage = "SELECT COUNT(*) FROM product WHERE product_valid=1";
if ($search) {
    $countPage .= " AND product_name LIKE :search";
}
if ($brand) {
    $countPage .= " AND product_brand = :brand"; // 根據品牌過濾
}
if ($category) {
    $countPage .= " AND product_category_name = :category"; // 根據類別過濾
}
if ($sub) {
    $countPage .= " AND product_sub_category = :sub"; // 根據分類過濾
}


$countStmt = $dbHost->prepare($countPage);
if ($search) {
    $countStmt->bindValue(':search', '%' . $search . '%');
}
if ($brand) {
    $countStmt->bindValue(':brand', $brand);
}
if ($category) {
    $countStmt->bindValue(':category', $category);
}
if ($sub) {
    $countStmt->bindValue(':sub', $sub);
}

$countStmt->execute();
$productCount = $countStmt->fetchColumn();

// 計算總頁數
$totalPages = ceil($productCount / $per_page);

try {
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "預處理陳述式執行失敗！ <br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>商品管理</title>
    <?php include("../headlink.php") ?>
    <?php include("../sidebar.php") ?>
    <link rel="stylesheet" href="./css.css">
    <link rel="stylesheet" href="../assets/style.css">
    <style>
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

        .product-img-size {
            min-width: 8rem;
        }

        .product-name-size {
            max-width: 10rem;
        }

        .card {
            /* border-top: 3px solid #435ebe; */
            box-shadow: var(--bs-box-shadow) !important;
        }
    </style>
</head>


<body>
    <script src="../assets/static/js/initTheme.js"></script>
    <!-- 刪除 -->
    <div id="delAlert" class="warningalert d-flex justify-content-center align-items-center">
        <form action="doSoftProductList.php" method="post">
            <input type="hidden" name="product_id" id="" value="<?= $product_id ?>">
            <div class="warningcard card p-4">
                <h1>確定要刪除?</h1>
                <div class="text-end">
                    <button type="submit" class="btn btn-danger">確定</button>
                    <a href="ProductList.php?product_id=<?= $product_id ?>&per_page=<?= $per_page ?>&brand=<?= $brand ?>&category=<?= $category ?>&sub=<?= $sub ?>&order=<?= $orderID ?>:<?= $orderValue ?>&page=<?= $startPage ?>" class="btn btn-secondary">取消</a>
                </div>
            </div>
        </form>
    </div>
    <!--  -->

<div id="app">

    <div id="main" class='layout-navbar navbar-fixed'>
        <header>
        </header>
        <div id="main-content">
            <div class="page-heading">
                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3>商品管理</h3>
                            <p class="text-subtitle text-muted"></p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html"><i class="fa-solid fa-house"></i></a></li>
                                    <li class="breadcrumb-item active" aria-current="page">商品管理</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <section class="section">
                    <div class="card">
                        <div class="card-body">
                            <div class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
                                <div>
                                    <!-- 分類開始 -->
                                    <form class="mb-2" action="" method="get">
                                        <?php if ($productCount > 0) : ?>

                                            <label class="ms-2">品牌</label>
                                            <div class="dataTable-dropdown">
                                                <!-- onchange="this.form.submit() -->
                                                <select name="brand" class="dataTable-selector form-select">
                                                    <option value="">選擇品牌</option>
                                                    <option value="木入森" <?= ($brand == "木入森") ? 'selected' : '' ?>>木入森</option>
                                                    <option value="水魔素" <?= ($brand == "水魔素") ? 'selected' : '' ?>>水魔素</option>
                                                    <option value="陪心" <?= ($brand == "陪心") ? 'selected' : '' ?>>陪心</option>
                                                    <option value="美喵" <?= ($brand == "美喵") ? 'selected' : '' ?>>美喵</option>
                                                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                                                    <!-- <input type="hidden" name="page" value="<?= $startPage > $totalPages ? "1" : $startPage ?>"> -->
                                                    <input type="hidden" name="per_page" value="<?= $per_page ?>">
                                                    <input type="hidden" name="category" value="<?= $category ?>">
                                                    <input type="hidden" name="sub" value="<?= $sub ?>">
                                                    <input type="hidden" name="product_status" value="<?= $product_status ?>">
                                                    <input type="hidden" name="order" value="<?= $orderID . ':' . $orderValue ?>">
                                                </select>

                                            </div>
                                            <label class="ms-2">類別</label>
                                            <div class="dataTable-dropdown">

                                                <select name="category" class="dataTable-selector form-select">
                                                    <option value="">選擇類別</option>
                                                    <option value="犬貓通用" <?= ($category == "犬貓通用") ? 'selected' : '' ?>>犬貓通用</option>
                                                    <option value="犬寶保健" <?= ($category == "犬寶保健") ? 'selected' : '' ?>>犬寶保健</option>
                                                    <option value="貓皇保健" <?= ($category == "貓皇保健") ? 'selected' : '' ?>>貓皇保健</option>
                                                    <option value="沐洗口腔護理" <?= ($category == "沐洗口腔護理") ? 'selected' : '' ?>>沐洗口腔護理</option>
                                                </select>

                                            </div>
                                            <label class="ms-2">分類</label>
                                            <div class="dataTable-dropdown">

                                                <select name="sub" class="dataTable-selector form-select">
                                                    <option value="">選擇分類</option>
                                                    <option value="排毛粉" <?= ($sub == "排毛粉") ? 'selected' : '' ?>>排毛粉</option>
                                                    <option value="魚油粉" <?= ($sub == "魚油粉") ? 'selected' : '' ?>>魚油粉</option>
                                                    <option value="鈣保健" <?= ($sub == "鈣保健") ? 'selected' : '' ?>>鈣保健</option>
                                                    <option value="腸胃保健" <?= ($sub == "腸胃保健") ? 'selected' : '' ?>>腸胃保健</option>
                                                    <option value="關節保健" <?= ($sub == "關節保健") ? 'selected' : '' ?>>關節保健</option>
                                                    <option value="口腔保健" <?= ($sub == "口腔保健") ? 'selected' : '' ?>>口腔保健</option>
                                                    <option value="心臟保健" <?= ($sub == "心臟保健") ? 'selected' : '' ?>>心臟保健</option>
                                                    <option value="皮膚保健" <?= ($sub == "皮膚保健") ? 'selected' : '' ?>>皮膚保健</option>
                                                    <option value="胰臟保健" <?= ($sub == "胰臟保健") ? 'selected' : '' ?>>胰臟保健</option>
                                                    <option value="眼睛保健" <?= ($sub == "眼睛保健") ? 'selected' : '' ?>>眼睛保健</option>
                                                    <option value="沐浴" <?= ($sub == "沐浴") ? 'selected' : '' ?>>沐浴</option>
                                                    <option value="口腔清潔" <?= ($sub == "口腔清潔") ? 'selected' : '' ?>>口腔清潔</option>
                                                </select>

                                            </div>
                                            <label class="ms-2">狀態</label>
                                            <div class="dataTable-dropdown">

                                                <select name="sub" class="dataTable-selector form-select">
                                                    <option value="">選擇狀態</option>
                                                    <option value="沐浴" <?= ($sub == "沐浴") ? 'selected' : '' ?>>沐浴</option>
                                                    <option value="清潔" <?= ($sub == "清潔") ? 'selected' : '' ?>>清潔</option>
                                                    <option value="排毛粉" <?= ($sub == "排毛粉") ? 'selected' : '' ?>>排毛粉</option>
                                                    <option value="魚油粉" <?= ($sub == "魚油粉") ? 'selected' : '' ?>>魚油粉</option>
                                                    <option value="鈣保健" <?= ($sub == "鈣保健") ? 'selected' : '' ?>>鈣保健</option>
                                                    <option value="腸胃保健" <?= ($sub == "腸胃保健") ? 'selected' : '' ?>>腸胃保健</option>
                                                    <option value="關節保健" <?= ($sub == "關節保健") ? 'selected' : '' ?>>關節保健</option>
                                                    <option value="口腔保健" <?= ($sub == "口腔保健") ? 'selected' : '' ?>>口腔保健</option>
                                                    <option value="心臟保健" <?= ($sub == "心臟保健") ? 'selected' : '' ?>>心臟保健</option>
                                                    <option value="皮膚保健" <?= ($sub == "皮膚保健") ? 'selected' : '' ?>>皮膚保健</option>
                                                    <option value="胰臟保健" <?= ($sub == "胰臟保健") ? 'selected' : '' ?>>胰臟保健</option>
                                                    <option value="眼睛保健" <?= ($sub == "眼睛保健") ? 'selected' : '' ?>>眼睛保健</option>
                                                </select>

                                            </div>
                                            <a class="btn btn-primary ms-2" href="ProductList.php">清除</a>

                                            <div class="input-group mt-2">

                                                <input type="hidden" name="per_page" value="<?= $per_page ?>">
                                                <!-- ↑　在選擇筆數的時候搜尋會依照所選的筆數顯示 -->
                                                <input type="search" class="form-control" value="<?php echo isset($_GET["search"]) ? $_GET["search"] : "" ?>" name="search" placeholder="搜尋商品">
                                                <button class="btn btn-primary" type="submit">查詢</button>
                                            </div>
                                    </form>
                                    <!-- <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                                        <input type="hidden" name="page" value="<?= $startPage ?>">
                                        <input type="hidden" name="per_page" value="<?= $per_page ?>"> -->


                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
                                <!-- 控制每頁筆數 -->
                                <div class="d-flex justify-content-between">
                                    <form action="" method="get">
                                        <label class="ms-2">每頁</label>
                                        <div class="dataTable-dropdown">
                                            <select name="per_page" class="dataTable-selector form-select" onchange="this.form.submit()">
                                                <option value="5" <?= ($per_page == 5) ? 'selected' : '' ?>>5</option>
                                                <option value="10" <?= ($per_page == 10) ? 'selected' : '' ?>>10</option>
                                                <option value="15" <?= ($per_page == 15) ? 'selected' : '' ?>>15</option>
                                                <option value="20" <?= ($per_page == 20) ? 'selected' : '' ?>>20</option>
                                                <option value="25" <?= ($per_page == 25) ? 'selected' : '' ?>>25</option>
                                                <input type="hidden" name="brand" value="<?= $brand ?>">
                                                <input type="hidden" name="category" value="<?= $category ?>">
                                                <input type="hidden" name="sub" value="<?= $sub ?>">
                                                <input type="hidden" name="product_status" value="<?= $product_status ?>">
                                                <input type="hidden" name="order" value="<?= $orderID . ':' . $orderValue ?>">
                                            </select>
                                        </div>
                                        <label>筆</label>
                                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                                        <!-- <input type="hidden" name="page" value="<?= $startPage ?>"> 保留當前頁碼 -->
                                    </form>
                                    <div>
                                        <a class="btn btn-primary ms-2" href="create-product.php">新增商品</a>
                                        <a class="btn btn-primary ms-2" href="RepairProduct.php">已下架商品</a>
                                    </div>
                                </div>
                                <div class="dataTable-container">
                                    <!-- 商品內容 -->
                                    <!-- 排序初始是ASC 點了之後更改成 DESC -->
                                    <table class="table table-striped dataTable-table" id="table1">
                                        <thead>
                                            <tr>
                                                <th data-sortable="" class="<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?>" aria-sort="descending"><a href="?page=<?= $startPage ?>&per_page=<?= $per_page ?>&order=product_id:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?>" class="">ID</a></th>
                                                <th data-sortable=""><a href="?page=<?= $startPage ?>&per_page=<?= $per_page ?>&order=product_img:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?>" class="">圖片</a></th>
                                                <th data-sortable=""><a href="?page=<?= $startPage ?>&per_page=<?= $per_page ?>&order=product_name:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?>" class="">名稱</a></th>
                                                <th data-sortable=""><a href="?page=<?= $startPage ?>&per_page=<?= $per_page ?>&order=product_status:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?>" class="">狀態</a></th>
                                                <th data-sortable=""><a href="?page=<?= $startPage ?>&per_page=<?= $per_page ?>&order=product_origin_price:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?>" class="">原價</a></th>
                                                <th data-sortable=""><a href="?page=<?= $startPage ?>&per_page=<?= $per_page ?>&search=<?= urlencode($search) ?>&brand=<?= urlencode($brand) ?>&category=<?= urlencode($category) ?>&sub=<?= urlencode($sub) ?>&order=product_stock:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?>" class="">庫存</a></th>
                                                <th data-sortable=""><a href="?page=<?= $startPage ?>&per_page=<?= $per_page ?>&order=product_start_time:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?>" class="">上架時間</a></th>
                                                <th data-sortable=""><a href="?page=<?= $startPage ?>&per_page=<?= $per_page ?>&order=product_update_date:<?= $orderValue === 'ASC' ? 'DESC' : 'ASC' ?>" class="">下架時間</a></th>
                                                <th data-sortable=""><a href="#" class=" ms-2">商品操作</a></th>
                                                <!-- href="?page=<?= $i ?>&per_page=<?= $per_page ?>&search=<?= urlencode($search) ?>&brand=<?= urlencode($brand) ?>&category=<?= urlencode($category) ?>&sub=<?= urlencode($sub) ?>&product_status=<?= urlencode($product_status) ?>&order=<?= $orderID . ':' . $orderValue ?>" -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- 顯示商品內容 -->
                                            <?php foreach ($rows as $row) : ?>
                                                <tr>
                                                    <td><?= $row["product_id"] ?></td>
                                                    <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
                                                    <td class="product-img-size">
                                                        <div class="ratio ratio-1x1">
                                                            <img class="object-fit-cover" src="./ProductPicUpLoad/<?= $row["product_img"] ?>" alt="<?= $row["product_name"] ?>">
                                                        </div>
                                                    </td>
                                                    <td class="product-name-size"><?= $row["product_name"] ?></td>
                                                    <td><?= $row["product_status"] ?></td>
                                                    <td><?= number_format($row["product_origin_price"]) ?></td>
                                                    <td><?= $row["product_stock"] ?></td>
                                                    <td><?= $row["product_start_time"] ?></td>
                                                    <td><?= $row["product_end_time"] ?></td>
                                                    <td>
                                                        <a title="編輯商品" href="EditProductList.php?product_id=<?= $row['product_id'] ?>"><i class="fa-solid fa-pen-to-square fa-lg m-2"></i></a>
                                                        <a title="檢視商品" href="product.php?product_id=<?= $row['product_id'] ?>"><i class="fa-solid fa-circle-info m-2"></i></a>
                                                        <!-- /ProductList.php?per_page=15&brand=木入森&search=&page=1 -->
                                                        <a title="刪除商品" href="ProductWarringAlert.php?product_id=<?= $row['product_id'] ?>&per_page=<?= $per_page ?>&brand=<?= $brand ?>&category=<?= $category ?>&sub=<?= $sub ?>&order=<?= $orderID ?>:<?= $orderValue ?>&page=<?= $startPage ?>"><i class="fa-solid fa-trash-can m-2"></i></a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    <?php else : ?>
                                        <!-- 如果沒有商品也要顯示篩選條件 -->
                                        <form class="mb-2" action="" method="get">
                                            <label class="ms-2">品牌</label>
                                            <div class="dataTable-dropdown">
                                                <select name="brand" class="dataTable-selector form-select">
                                                    <option value="">選擇品牌</option>
                                                    <option value="木入森" <?= ($brand == "木入森") ? 'selected' : '' ?>>木入森</option>
                                                    <option value="水魔素" <?= ($brand == "水魔素") ? 'selected' : '' ?>>水魔素</option>
                                                    <option value="陪心" <?= ($brand == "陪心") ? 'selected' : '' ?>>陪心</option>
                                                    <option value="美喵" <?= ($brand == "美喵") ? 'selected' : '' ?>>美喵</option>
                                                </select>
                                            </div>
                                            <label class="ms-2">類別</label>
                                            <div class="dataTable-dropdown">
                                                <select name="category" class="dataTable-selector form-select">
                                                    <option value="">選擇類別</option>
                                                    <option value="犬貓通用" <?= ($category == "犬貓通用") ? 'selected' : '' ?>>犬貓通用</option>
                                                    <option value="犬寶保健" <?= ($category == "犬寶保健") ? 'selected' : '' ?>>犬寶保健</option>
                                                    <option value="貓皇保健" <?= ($category == "貓皇保健") ? 'selected' : '' ?>>貓皇保健</option>
                                                    <option value="沐洗口腔護理" <?= ($category == "沐洗口腔護理") ? 'selected' : '' ?>>沐洗口腔護理</option>
                                                </select>
                                            </div>
                                            <label class="ms-2">分類</label>
                                            <div class="dataTable-dropdown">
                                                <select name="sub" class="dataTable-selector form-select">
                                                    <option value="">選擇分類</option>
                                                    <option value="排毛粉" <?= ($sub == "排毛粉") ? 'selected' : '' ?>>排毛粉</option>
                                                    <option value="魚油粉" <?= ($sub == "魚油粉") ? 'selected' : '' ?>>魚油粉</option>
                                                    <option value="鈣保健" <?= ($sub == "鈣保健") ? 'selected' : '' ?>>鈣保健</option>
                                                    <option value="腸胃保健" <?= ($sub == "腸胃保健") ? 'selected' : '' ?>>腸胃保健</option>
                                                    <option value="關節保健" <?= ($sub == "關節保健") ? 'selected' : '' ?>>關節保健</option>
                                                    <option value="口腔保健" <?= ($sub == "口腔保健") ? 'selected' : '' ?>>口腔保健</option>
                                                    <option value="心臟保健" <?= ($sub == "心臟保健") ? 'selected' : '' ?>>心臟保健</option>
                                                    <option value="皮膚保健" <?= ($sub == "皮膚保健") ? 'selected' : '' ?>>皮膚保健</option>
                                                    <option value="胰臟保健" <?= ($sub == "胰臟保健") ? 'selected' : '' ?>>胰臟保健</option>
                                                    <option value="眼睛保健" <?= ($sub == "眼睛保健") ? 'selected' : '' ?>>眼睛保健</option>
                                                    <option value="沐浴" <?= ($sub == "沐浴") ? 'selected' : '' ?>>沐浴</option>
                                                    <option value="口腔清潔" <?= ($sub == "口腔清潔") ? 'selected' : '' ?>>口腔清潔</option>
                                                </select>
                                            </div>
                                            <label class="ms-2">狀態</label>
                                            <div class="dataTable-dropdown">
                                                <select name="product_status" class="dataTable-selector form-select">
                                                    <option value="">選擇狀態</option>
                                                    <option value="已上架" <?= ($product_status == "已上架") ? 'selected' : '' ?>>已上架</option>
                                                    <option value="已下架" <?= ($product_status == "已下架") ? 'selected' : '' ?>>已下架</option>
                                                </select>
                                            </div>
                                            <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                                            <input type="hidden" name="page" value="<?= $startPage ?>">
                                            <input type="hidden" name="per_page" value="<?= $per_page ?>">
                                            <a class="btn btn-primary ms-2" href="ProductList.php">清除</a>
                                            <div class="dataTable-search mt-2">
                                                <form action="">
                                                    <div class="input-group">

                                                        <input type="hidden" name="per_page" value="<?= $per_page ?>"> <!-- 在選擇筆數的時候搜尋會依照所選的筆數顯示 -->
                                                        <input type="search" class="form-control" value="<?php echo isset($_GET["search"]) ? $_GET["search"] : "" ?>" name="search" placeholder="搜尋商品">
                                                        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
                                                    </div>
                                                </form>
                                            </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
                                <div class="d-flex justify-content-between">
                                    <form action="" method="get">
                                        <label class="ms-2">每頁</label>
                                        <div class="dataTable-dropdown">
                                            <select name="per_page" class="dataTable-selector form-select" onchange="this.form.submit()">
                                                <option value="5" <?= ($per_page == 5) ? 'selected' : '' ?>>5</option>
                                                <option value="10" <?= ($per_page == 10) ? 'selected' : '' ?>>10</option>
                                                <option value="15" <?= ($per_page == 15) ? 'selected' : '' ?>>15</option>
                                                <option value="20" <?= ($per_page == 20) ? 'selected' : '' ?>>20</option>
                                                <option value="25" <?= ($per_page == 25) ? 'selected' : '' ?>>25</option>
                                                <input type="hidden" name="brand" value="<?= $brand ?>">
                                                <input type="hidden" name="category" value="<?= $category ?>">
                                                <input type="hidden" name="sub" value="<?= $sub ?>">
                                                <input type="hidden" name="product_status" value="<?= $product_status ?>">
                                            </select>
                                        </div>
                                        <label class="mb-3">筆</label>
                                        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                                        <!-- <input type="hidden" name="page" value="<?= $startPage ?>"> 保留當前頁碼 -->
                                    </form>
                                    <div>
                                        <a class="btn btn-primary ms-2" href="create-product.php">新增商品</a>
                                    </div>
                                </div>
                                </form>
                                <tr>
                                    <td>查無商品</td>
                                </tr>
                            <?php endif; ?>
                            </table>
                            </div>
                            <!-- 下方顯示筆數 以及分頁變化 -->
                            <?php $start_item = ($startPage - 1) * $per_page; ?>
                            <div class="dataTable-bottom">
                                <div class="dataTable-info">顯示 <?= ($offset + 1) ?> 到 <?= min($offset + $per_page, $productCount) ?> 筆，共 <?= $productCount ?> 筆</div>
                                <nav class="dataTable-pagination">
                                    <ul class="dataTable-pagination-list pagination pagination-primary">
                                        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                                            <li class="page-item <?= ($i == $startPage) ? 'active' : '' ?>">
                                                <a class="page-link" href="?page=<?= $i ?>&per_page=<?= $per_page ?>&search=<?= urlencode($search) ?>&brand=<?= urlencode($brand) ?>&category=<?= urlencode($category) ?>&sub=<?= urlencode($sub) ?>&product_status=<?= urlencode($product_status) ?>&order=<?= $orderID . ':' . $orderValue ?>"><?= $i ?></a>
                                            </li>
                                        <?php endfor; ?>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <?php include("../footer.php") ?>
        </div>
    </div>
</div>
</section>
</div>

</div>
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
<script src="../assets/static/js/components/dark.js"></script>
<script src="../assets/extensions/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<?php include("../js.php") ?>
<?php include("./product-js.php") ?>
<script src="../assets/compiled/js/app.js"></script>
<script src="../assets/static/js/initTheme.js"></script>
</body>

</html>