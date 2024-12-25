<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>溝通師新增</title>

    <?php include("../headlink.php") ?>
    <style>
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
                            <h3>溝通師新增</h3>
                            <p class="text-subtitle text-muted"></p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html"><i class="fa-solid fa-house"></i></a></li>
                                    <li class="breadcrumb-item active" aria-current="page">溝通師新增</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <section class="section">
                    <a href="petcommunicators.php?p=1" class="btn btn-primary mb-2"><i class="fa-solid fa-chevron-left"></i>回列表</a>
                    <form action="doCreat.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
                        <input type="hidden" id="" class="form-control" placeholder="" name="PetCommStatus" value="未刊登">
                        <input type="hidden" id="" class="form-control" placeholder="" name="valid" value="1">
                        <div class="card">
                            <div class="card-body">

                                <div class="row">
                                    <!-- 左側欄位 -->
                                    <div class="col-6">
                                        <div class="form-group">
                                            <div class="mb-3 mt-2">
                                                <label for="formFile" class="required">相片</label>
                                                <input class="form-control" type="file" id="formFile" name="PetCommImg">
                                            </div>
                                            <div class="ratio ratio-4x3 border">
                                                <img id="imagePreview" class="img-preview object-fit-contain rounded-5" src="" alt="Image Preview" style="display: none;">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 右側欄位 -->
                                    <div class="col-6">
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="" class="required mt-2">名稱</label>
                                                <input type="text" id="" class="form-control" placeholder="" name="PetCommName" required>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="" class="required">性別</label>
                                                <select class="dataTable-selector form-select" name="PetCommSex" required>
                                                    <option value="male">男</option>
                                                    <option value="female">女</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="" class="required">Eamil</label>
                                                <input type="email" id="" class="form-control" placeholder="" name="PetCommEmail" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="" class="required">證照編號</label>
                                            <input type="text" id="" class="form-control" name="PetCommCertificateid" value="動溝證字第">
                                        </div>
                                        <div class="form-group">
                                            <label for="" class="form-label required">取證日期</label>
                                            <input type="text" class=" form-control  flatpickr-no-config active " placeholder="Select date..." readonly="readonly" name="PetCommCertificateDate">
                                        </div>
                                        <div class="col">
                                            <div class="form-group mt-2">
                                                <label for="">服務項目</label>
                                                <input type="text" id="" class="form-control" placeholder="" name="PetCommService">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="">進行方式</label>
                                                <input type="text" id="" class="form-control" placeholder="" name="PetCommApproach">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="form-group">
                                                <label for="">預約費用</label>
                                                <input type="text" id="" class="form-control" placeholder="" name="PetCommFee">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="">介紹</label>
                                            <textarea rows="10" class="form-control" placeholder="" name="PetCommIntroduction"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- 送出表單按鈕 -->
                        <div class="col-12 d-flex justify-content-center m-2">
                            <button type="submit" class="btn btn-primary me-1 mb-1">送出</button>
                        </div>
                    </form>
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
        // 表單驗證
        function validateForm() {
            var fileInput = document.getElementById('formFile');
            if (!fileInput.files.length) {
                alert('請上傳相片');
                return false; // 阻止表單提交
            }
            return true; // 允許表單提交
        }
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