<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (!isset($_GET["MemberID"])) {
    echo "請正確帶入正確id變數";
    // exit的功能為輸出一個訊息後退出當前的腳本，強制結束後面的程式
    exit;
}
$id = $_GET["MemberID"];
require_once("../pdoConnect.php");
$sql = "SELECT * FROM Member WHERE MemberID = :MemberID AND MemberValid = '1'";

// 將slq的資料回傳回變數裡面
$stmt = $dbHost->prepare($sql);
try {
    $stmt->execute([
        ":MemberID" => $id
    ]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $usersCount = $stmt->rowCount();
} catch (PDOException $e) {
    echo "預處理陳述式執行失敗！ <br/>";
    echo "Error: " . $e->getMessage() . "<br/>";
    $dbHost = NULL;
    exit;
}

// $birth = ""
// 最愛的商品功能 (待修改)
// if($usersCount>0){
//     $title = $row["name"];

//     $sqlFavorite = "SELECT user_like.*, product.name AS product_name, product.id AS product_id
//     FROM user_like
//     JOIN product ON user_like.product_id = product.id
//     WHERE user_like.user_id = $id
//     ";
//     $resultFavorite = $conn->query($sqlFavorite);
//     $rowProducts = $resultFavorite->fetch_all(MYSQLI_ASSOC);

// }else{
//     $title="使用者不存在";
// };

?>

<!doctype html>
<html lang="en">

<head>
    <title>user</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <?php include("../headlink.php") ?>
</head>

<body>
    <?php include("../Member/modals.php"); ?>
    <script src="../assets/static/js/initTheme.js"></script>
    <div id="app">
        <?php include("../sidebar.php") ?>
        <div id="main" class='layout-navbar navbar-fixed'>
            <header>
            </header>
            <div id="main-content">
                <div class="page-heading">
                    <div class="page-title">
                        <a href="MemberList.php" class="btn btn-primary"><i class="fa-solid fa-chevron-left"></i>回列表</a>
                        <div class="row my-3">
                            <div class="col-12 col-md-6 order-md-1 order-last">
                                <h3>修改資料</h3>
                                <p class="text-subtitle text-muted"></p>
                            </div>
                            <div class="col-12 col-md-6 order-md-2 order-first">
                                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href=""><i class="fa-solid fa-house"></i></a></li>
                                        <li class="breadcrumb-item active" aria-current="page"><a href="MemberList.php?p=1&sorter=1">會員管理</a></li>
                                        <li class="breadcrumb-item active" aria-current="page"><?= $row["MemberName"] ?></a></li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <section class="section">
                        <!-- 會員資訊 -->
                        <div class="card">
                            <div class="card-body">
                                <div class="card-content">
                                    <form class="form form-vertical" action="doUpdateMember.php" method="post">
                                        <div class="form-body">
                                            <div class="row">
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="first-name-vertical">會員編號 :</label>
                                                        <input readonly type="text" class="form-control" name="id" value="<?= $row["MemberID"] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="first-name-vertical">會員姓名 <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="name" value="<?= $row["MemberName"] ?>">
                                                    </div>
                                                </div>
                                                <!-- <div class="col-md-6 col-12 d-none">
                                            <div class="form-group">
                                                <label hidden for="email-id-vertical">PCID</label>
                                                <input hidden type="text" id="email-id-vertical" class="form-control" name="pcid" placeholder="" value="<?= $row["MemberPCID"] ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="contact-info-vertical">Admin</label>
                                                <input type="text" id="contact-info-vertical" class="form-control" name="admin" placeholder="Mobile" value="<?= $row["MemberAdmin"] ?>">
                                            </div>
                                        </div> -->
                                                <!-- <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="password-vertical">會員密碼 <span class="text-danger">*</span></label>
                                                        <input type="text" id="password-vertical" class="form-control" name="password" placeholder="Password" value="<?= $row["MemberPassword"] ?>">
                                                    </div>
                                                </div> -->
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="first-name-vertical">會員暱稱</label>
                                                        <input type="text" id="first-name-vertical" class="form-control" name="nickname" placeholder="" value="<?= $row["MemberNickName"] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="email-id-vertical">會員等級</label>
                                                        <select class="form-select" id="basicSelect" name="level">
                                                            <option value="1" <?= ($row["MemberLevel"] == 1) ? "selected" : '' ?>>銅</option>
                                                            <option value="2" <?= ($row["MemberLevel"] == 2) ? "selected" : '' ?>>銀</option>
                                                            <option value="3" <?= ($row["MemberLevel"] == 3) ? "selected" : '' ?>>金</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="contact-info-vertical">電子郵件 <span class="text-danger">*</span></label>
                                                        <input type="email" id="contact-info-vertical" class="form-control" name="email" placeholder="" value="<?= $row["MembereMail"] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="password-vertical">手機號碼 <span class="text-danger">*</span></label>
                                                        <input type="text" id="password-vertical" class="form-control" name="phone" placeholder="" value="<?= $row["MemberPhone"] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="first-name-vertical">聯絡電話</label>
                                                        <input type="tel" id="first-name-vertical" class="form-control" name="tel" placeholder="" value="<?= $row["MemberTel"] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="email-id-vertical">聯絡地址</label>
                                                        <input type="text" id="email-id-vertical" class="form-control" name="address" placeholder="" value="<?= $row["MemberAddress"] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="contact-info-vertical">出生日期</label>
                                                        <!-- <?php $birth = substr($row["MemberBirth"], 0, 10) ?> -->
                                                        <!-- <input type="text" class="form-control mb-3 flatpickr-no-config flatpickr-input active" placeholder="Select date.." name="birth" readonly="readonly" value="<?= $birth ?>"> -->
                                                        <input type="text" class="form-control flatpickr-no-config active birth" placeholder="" readonly="readonly" name="birth" value="<?= $row["MemberBirth"] ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="password-vertical">性別</label>
                                                        <select class="form-select" id="basicSelect" name="gender">
                                                            <option value="0" <?= ($row["MemberGender"] == 0) ? "selected" : '' ?>>男</option>
                                                            <option value="1" <?= ($row["MemberGender"] == 1) ? "selected" : '' ?>>女</option>
                                                            <option value="2" <?= ($row["MemberGender"] == 2) ? "selected" : '' ?>>其他</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="first-name-vertical">帳號狀態</label>
                                                        <select class="form-select" id="basicSelect" name="valid">
                                                            <option value="1" <?= ($row["MemberValid"] == 1) ? "selected" : '' ?>>有效</option>
                                                            <option value="0" <?= ($row["MemberValid"] == 0) ? "selected" : '' ?>>無效</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12">
                                                    <div class="form-group">
                                                        <label for="email-id-vertical">黑名單狀態</label>
                                                        <select class="form-select" id="basicSelect" name="blacklist">
                                                            <option value="0" <?= ($row["MemberIsBlacklisted"] == 0) ? "selected" : '' ?>>關閉</option>
                                                            <option value="1" <?= ($row["MemberIsBlacklisted"] == 1) ? "selected" : '' ?>>開啟</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-12 opacity-75">
                                                    <div class="form-group">
                                                        <label for="contact-info-vertical">建立日期 : </label>
                                                        <input type="text" class="form-control mb-3 flatpickr-no-config flatpickr-input active" placeholder="" name="" disabled="disabled" value="<?= $row["MemberCreateDate"] ?>">
                                                    </div>
                                                </div>
                                                <!-- <div class="col-md-6 col-12 d-none">
                                            <div class="form-group">
                                                <label for="password-vertical">Created_UserID</label>
                                                <input type="text" id="password-vertical" class="form-control" name="createuserid" placeholder="" value="<?= $row["MemberCreateUserID"] ?>">
                                            </div>
                                        </div> -->
                                                <div class="col-md-6 col-12 opacity-75">
                                                    <div class="form-group">
                                                        <label for="contact-info-vertical">更新日期 : </label>
                                                        <input type="text" class="form-control mb-3 flatpickr-no-config flatpickr-input active" placeholder="" name="" disabled="disabled" value="<?= $row["MemberUpdateDate"] ?>">
                                                    </div>
                                                </div>
                                                <!-- <div class="col-md-6 col-12 d-none">
                                            <div class="form-group">
                                                <label for="password-vertical">Uptate_UserID</label>
                                                <input type="text" id="password-vertical" class="form-control" name="updateuserid" placeholder="" value="<?= $row["MemberUpdateUserID"] ?>">
                                            </div>
                                        </div> -->
                                                <div class="col-12 d-flex justify-content-center">
                                                    <button type="submit" class="btn btn-primary me-1 mb-1">儲存</button>
                                                    <a href="#" class="btn btn-danger delete-button me-1 mb-1" data-member-id="<?= $id ?>">刪除</a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
            </section>
        </div>
    </div>
    <?php include("../js.php"); ?>
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
    <script>
        // 日期彈出視窗更改
        flatpickr('.birth', {
            enableTime: true,
            dateFormat: "Y-m-d",
        })

        // 刪除會員警示modal
        const deleteButtons = document.querySelectorAll('.delete-button'); // 選擇所有的刪除按鈕
        const confirmDeleteButton = document.getElementById('confirmDeleteButton');
        let memberIDToDelete = null;

        deleteButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                memberIDToDelete = this.getAttribute('data-member-id'); // 獲取要刪除的會員的ID
                const confirmModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
                confirmModal.show();
            });
        });

        // 確認是否刪除
        confirmDeleteButton.addEventListener('click', function() {
            if (memberIDToDelete) {
                window.location.href = 'doDeleteMember.php?MemberID=' + memberIDToDelete;
            }
        });
    </script>
    
    <script>
        src = "https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity = "sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin = "anonymous"
    </script>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>
</body>

</html>