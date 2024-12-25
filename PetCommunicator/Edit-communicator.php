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
        
    </style>
    <?php include("../headlink.php") ?>
    <style>
        #mainTable th:nth-child(1),
        #mainTable td:nth-child(1) {
            width: 10px;
        }

        #mainTable th:nth-child(2),
        #mainTable td:nth-child(2) {
            width: 200px;
        }

        .flatpickr-time {
            display: none;
        }
        textarea {
            resize: none;
            /* 禁用調整大小功能 */
        }    
    </style>
</head>

<body>
    <script src="../assets/static/js/initTheme.js"></script>
    <!-- 刪除警示窗 -->
    <div id="delAlert" class="warningalert justify-content-center align-items-center d-none">
        <form action="doSoftDel.php" method="post">
            <input type="hidden" name="PetCommID" id="" value="<?= $delrow["PetCommID"] ?>">
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
                        <td><?= $row["PetCommID"] ?></td>
                        <td><?= $row["PetCommName"] ?></td>
                        <td><?= $row["PetCommSex"] === "Female" ? "女" : "男" ?></td>
                        <td><?= $row["PetCommStatus"] ?></td>
                    </tr>
                </table>
                <div class="form-group">
                    <label for="" class="">說明</label>
                    <textarea class="form-control mb-2" name="delreason" id="" rows="8"></textarea>
                    <input type="hidden" name="PetCommID" value="<?= $row["PetCommID"] ?>">
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-danger">確定</button>
                    <button id="delAlertCancel" type="button" class="btn btn-secondary">取消</button>
                </div>
            </div>
        </form>
    </div>
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
                        <!-- 主標題 -->
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
                    </div>
                    <section class="section">
                    <a href="petcommunicators.php?p=1" class="btn btn-primary mb-2"><i class="fa-solid fa-chevron-left"></i>回列表</a>
                        <div class="card">
                            <div class="card-body">
                                 <!-- 更新時間 -->
                        <div class="row">
                            <div class="col d-flex justify-content-between">
                                <p>前次更新：<?= $row["PetCommUpdateUserID"] ?>/<?= $row["PetCommUpdateDate"] ?></p>
                                <p>創建時間：<?= $row["PetCommCreateUserID"] ?>/<?= $row["PetCommCreateDate"] ?></p>
                            </div>
                        </div>
                                <!-- 表單 -->
                                <form action="doEdit.php" method="post" enctype="multipart/form-data">
                                    <div id="mainTable" class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
                                        <div class="dataTable-container">
                                            <table class="table table table-striped dataTable-table">
                                                <tr>
                                                    <th rowspan="10">相片</th>
                                                    <td rowspan="10">
                                                        <div class="form-group">
                                                            <div class="mb-3">
                                                                <input class="form-control" type="file" id="formFile" name="PetCommImg"
                                                                    value="<?= $row["PetCommImg"] ?>">
                                                            </div>
                                                            <div class="ratio ratio-1x1 ">
                                                                <img id="imagePreview" class="img-preview object-fit-cover rounded-5" src="./images/<?= $row["PetCommImg"] ?>" alt="Image Preview">
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <th>編號</th>
                                                    <td><?= $row["PetCommID"] ?></td>
                                                    <input class="form-control" type="hidden" value="<?= $row["PetCommID"] ?>" name="PetCommID"></td>
                                                </tr>
                                                <tr>
                                                    <th>名稱</th>
                                                    <td><input class="form-control" type="text" value="<?= $row["PetCommName"] ?>" name="PetCommName"></td>
                                                </tr>
                                                <tr>
                                                    <th>性別</th>
                                                    <td><select name="PetCommSex" id="" class="form-select">
                                                            <option value="male" <?= $row["PetCommSex"] === "male" ? 'selected' : '' ?>>男</option>
                                                            <option value="Female" <?= $row["PetCommSex"] === 'Female' ? 'selected' : '' ?>>女</option>
                                                        </select></td>
                                                </tr>
                                                <tr>
                                                    <th>證照</th>
                                                    <td><input class="form-control" type="text" value="<?= $row["PetCommCertificateid"] ?>" name="PetCommCertificateid">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>取證日期</th>
                                                    <td>
                                                        <div class="form-group">
                                                            <input type="text" class=" form-control  flatpickr-no-config active " placeholder="Select date..." readonly="readonly" value="<?= $row["PetCommCertificateDate"] ?>" name="PetCommCertificateDate">
                                                        </div>
                                                </tr>
                                                <tr>
                                                    <th>服務項目</th>
                                                    <td>
                                                        <input class="form-control" type="text" value="<?= $row["PetCommService"] ?>" name="PetCommService">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>進行方式</th>
                                                    <td><input class="form-control" type="text" value="<?= $row["PetCommApproach"] ?>" name="PetCommApproach">
                                                    </td>

                                                </tr>
                                                <tr>
                                                    <th>預約費用</th>
                                                    <td><input class="form-control" type="text" value="<?= $row["PetCommFee"] ?>" name="PetCommFee">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Eamil</th>
                                                    <td><input class="form-control" type="text" value="<?= $row["PetCommEmail"] ?>" name="PetCommEmail">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>狀態</th>
                                                    <td>
                                                        <select name="PetCommStatus" id="" class="form-control">
                                                            <option value="已刊登" <?= $row["PetCommStatus"] === '已刊登' ? 'selected' : '' ?>>已刊登</option>
                                                            <option value="未刊登" <?= $row["PetCommStatus"] === '未刊登' ? 'selected' : '' ?>>未刊登</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>介紹</th>
                                                    <td colspan="3"><textarea rows="10" class="form-control" type="text" value="<?= $row["PetCommIntroduction"] ?>" name="PetCommIntroduction"><?= $row["PetCommIntroduction"] ?>   
                                                    </textarea></td>
                                                    <input class="form-control" type="hidden" value="<?= $row["valid"] ?>" name="valid">
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary m-2">完成</button>
                                        <button type="button" class="btn btn-danger m-2" id="delBtn">刪除</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
                <?php include("../footer.php") ?>      
        </div>
    </div>
    <?php include("../js.php") ?>
    <script>
        // 彈跳日期選擇窗
        flatpickr('.flatpickr-no-config', {
            enableTime: true,
            dateFormat: "Y-m-d",
        })
        // Edit介面刪除按鈕
        const delBtn = document.querySelector("#delBtn");
        const delAlert = document.querySelector("#delAlert");
        const delAlertCancel = document.querySelector("#delAlertCancel");
        delBtn.addEventListener("click", function() {
            delAlert.classList.remove("d-none");
            delAlert.classList.add("d-flex");
        })
        delAlertCancel.addEventListener("click", function() {
            delAlert.classList.remove("d-flex");
            delAlert.classList.add("d-none");
        });
        // 圖檔匯入
        const formFile = document.querySelector("#formFile")
        formFile.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('imagePreview');
                    img.src = e.target.result;
                    img.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                document.getElementById('imagePreview').style.display = 'none';
            }
        });
    </script>
</body>

</html>