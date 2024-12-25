<?php
require_once("../pdoConnect.php");
$id = $_GET["id"];

$sql = "SELECT * FROM Petcommunicator WHERE PetCommID=?";
$stmt = $dbHost->prepare($sql);
try {
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "預處理陳述式執行失敗！ <br/>";
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
    <title>寵物溝通師-<?= $row["PetCommName"] ?></title>

    <?php include("../headlink.php") ?>
    <style>
        #mainTable th:nth-child(1),
        #mainTable td:nth-child(1) {
            width: 20px;
        }

        #mainTable th:nth-child(2),
        #mainTable td:nth-child(2) {
            width: 200px;
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
                    <div class="page-title">
                        <!-- 標題抬頭 -->
                        <div class="row">
                            <div class="col-12 col-md-6 order-md-1 order-last">
                                <h3>寵物溝通師-<?= $row["PetCommName"] ?></h3>
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
                        <!-- 更新.創建時間顯示 -->
                        
                    </div>
                    <section class="section">
                    <a href="petcommunicators.php?p=1" class="btn btn-primary mb-2"><i class="fa-solid fa-chevron-left"></i>回列表</a>
                        <div class="card">
                            <div class="card-body">
                            <div class="row">
                            <div class="col d-flex justify-content-between">
                                <p>前次更新：<?= $row["PetCommUpdateUserID"] ?>/<?= $row["PetCommUpdateDate"] ?></p>
                                <p>創建時間：<?= $row["PetCommCreateUserID"] ?>/<?= $row["PetCommCreateDate"] ?></p>
                            </div>
                        </div>
                                
                                <!-- 個人資料主內容 -->
                                <div id="mainTable" class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
                                    <div class="dataTable-container">
                                        <table class="table table table-striped dataTable-table">
                                            <tr>
                                                <th rowspan="10">相片</th>
                                                <td rowspan="10">
                                                    <div class="ratio ratio-1x1 object-fit-cover">
                                                        <img class="object-fit-contain rounded-5" src="./images/<?= $row["PetCommImg"] ?>" alt="">
                                                    </div>
                                                </td>
                                                <th>編號</th>
                                                <td><?= $row["PetCommID"] ?></td>
                                            </tr>
                                            <tr>
                                                <th>名稱</th>
                                                <td><?= $row["PetCommName"] ?></td>
                                            </tr>
                                            <tr>
                                                <th>性別</th>
                                                <td><?= $row["PetCommSex"] == "Female" ? "女" : "男" ?></td>
                                            </tr>
                                            <tr>
                                                <th>證照</th>
                                                <td><?= $row["PetCommCertificateid"] ?></td>
                                            </tr>
                                            <tr>
                                                <th>取證日期</th>
                                                <td><?= $row["PetCommCertificateDate"] ?></td>
                                            </tr>
                                            <tr>
                                                <th>服務項目</th>
                                                <td><?= $row["PetCommService"] ?></td>
                                            </tr>
                                            <tr>
                                                <th>進行方式</th>
                                                <td><?= $row["PetCommApproach"] ?></td>
                                            </tr>
                                            <tr>
                                                <th>預約費用</th>
                                                <td><?= $row["PetCommFee"] ?></td>
                                            </tr>
                                            <tr>
                                                <th>Eamil</th>
                                                <td><?= $row["PetCommEmail"] ?></td>
                                            </tr>
                                            <tr>
                                                <th>狀態</th>
                                                <td><?= $row["PetCommStatus"] ?></td>
                                            </tr>
                                            <tr>
                                                <th>介紹</th>
                                                <td colspan="3"><?=  nl2br($row["PetCommIntroduction"]) ?></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <?php include("../footer.php") ?>
            </div>
        </div>
    </div>
    <?php include("../js.php") ?>
</body>

</html>