<?php
require_once("../pdoConnect.php");

// 確認有這個ID
if (!isset($_GET["product_id"])) {
    echo "請正確帶入 get product_id 變數";
    exit;
}

$product_id = $_GET["product_id"];
$sql = "SELECT * FROM product
WHERE product_id = '$product_id' AND product_valid=1
";


$stmt = $dbHost->prepare($sql);

try {
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $productCount = count($rows); // 可以撈到商品資訊
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
    <title>商品內容</title>

    <?php include("../headlink.php") ?>
    <style>
        .ratio-4x3 {
            --bs-aspect-ratio: 88%
        }
        .product-img-size {
            height: 30.75rem;
        }

        .card {
            /* border-top: 3px solid #435ebe; */
            box-shadow: var(--bs-box-shadow) !important;
        }
    </style>
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
                                <h3>商品內容</h3>
                                <p class="text-subtitle text-muted"></p>
                            </div>
                            <div class="col-12 col-md-6 order-md-2 order-first">
                                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="index.html"><i class="fa-solid fa-house"></i></a></li>
                                        <li class="breadcrumb-item active" aria-current="page">商品內容</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <section class="section">
                        <!-- 回列表及編輯 -->
                        <div class="py-2 mb-3 d-flex justify-content-between">
                            <a class="btn btn-primary" href="ProductList.php" title="回商品管理"><i class="fa-solid fa-chevron-left"></i>回列表</a></a>
                            <a class="btn btn-primary" href="EditProductList.php?product_id=<?= $product_id ?>" title="編輯商品"><i class="fa-solid fa-pen-to-square"></i></a>
                        </div>
                        <!-- 檢視內容 -->
                        <div class="card">
                            <div class="card-body">
                                <div class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
                                    <div class="container">
                                        <div class="row ">
                                            <?php if ($productCount > 0) : ?>
                                                <?php foreach ($rows as $row) : ?>
                                                    <div class="col-lg-6">
                                                        <div class="ratio ratio-1x1 product-img-size">
                                                            <img class="" src="./ProductPicUpLoad/<?= $row["product_img"] ?>" alt="<?= $row["product_name"] ?>">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <table class="table table-bordered">
                                                            <tr>
                                                                <th>上架時間</th>
                                                                <td><?= $row["product_start_time"] ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th>下架時間</th>
                                                                <td><?= $row["product_end_time"] ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th>商品編號</th>
                                                                <td><?= $row["product_id"] ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th>商品狀態</th>
                                                                <td><?= $row["product_status"] ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th>商品名稱</th>
                                                                <td><?= $row["product_name"] ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th>品牌</th>
                                                                <td><?= $row["product_brand"] ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th>類別</th>
                                                                <td><?= $row["product_category_name"] ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th>分類</th>
                                                                <td><?= $row["product_sub_category"] ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th>原價</th>
                                                                <td><?= number_format($row["product_origin_price"], 0) ?></td>
                                                            </tr>
                                                            <!-- <tr>
                                                                <th>售價</th>
                                                                <td><?= $row["product_sale_price"] ?></td>
                                                            </tr> -->
                                                            <tr>
                                                                <th>庫存</th>
                                                                <td><?= $row["product_stock"] ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th>建立時間</th>
                                                                <td><?= $row["product_create_date"] ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th>上次更新時間</th>
                                                                <td><?= $row["product_update_date"] ?></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    <table class="mt-3">
                                                        <tr>
                                                            <th>商品介紹</th>
                                                        </tr>
                                                        <tr>
                                                            <td><?= $row["product_info"] ?></td>
                                                        </tr>
                                                    </table>
                                                <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            </section>
        </div>
        <?php include("../footer.php") ?>
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
    <script src="../assets/compiled/js/app.js"></script>
</body>

</html>