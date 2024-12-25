<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文章上架系統</title>
    <link rel="stylesheet" href="../assets/extensions/quill/quill.snow.css">
    <link rel="stylesheet" href="../assets/extensions/quill/quill.bubble.css">
    <link rel="stylesheet" href="../assets/extensions/choices.js/public/assets/styles/choices.css">
    <?php include("../headlink.php") ?>

</head>
<style>
.image-preview-wrapper {
    width: 100%;
    /* 設置預覽框的寬度 */
    height: 250px;
    /* 設置預覽框的高度 */
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    /* 確保圖片不會超出邊界 */
    border-radius: 10px;
    border: 1px solid lightgrey;
}

.image-preview-wrapper img {
    max-width: 100%;
}

#editor-container {
    height: 300px;
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
                            <div class="col-12 col-md-6">
                            <a href="ArticleList.php" class="btn btn-primary mb-5"><i class="fa-solid fa-chevron-left"></i>回列表</a>
                            <h1>新增文章</h1>
                            </div>

                            <div class="col-12 col-md-6 order-md-2 order-first">
                                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="index.html"><i
                                                    class="fa-solid fa-house"></i></a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">文章管理</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 封面圖片 -->
                <form id="articleForm" action="ArticleSubmit.php" method="post" enctype="multipart/form-data">
                    <section class="section">
                        <div class="card">
                            <div class="card-body">
                                <div class="row ">
                                    <div class="mb-3">
                                        <label for="image" class="form-label">封面圖片</label>
                                        <input type="file" class="form-control" id="image" name="image">
                                    </div>
                                </div>
                                <!-- 預留預覽圖片框 -->
                                <div class="col">
                                    <span>圖片預覽</span>
                                </div>
                                <div id="image-preview-wrapper" class="image-preview-wrapper">
                                    <img id="image-preview" src="#" alt="圖片預覽" class="img-fluid d-none">
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- 文章內容 -->
                    <section class="section">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="title" class="form-label required">文章標題</label>
                                    <input type="text" class="form-control" id="title" name="title"
                                        placeholder="輸入文章標題">
                                </div>
                                <div class="mb-3">
                                    <label for="editor-container" class="form-label required">文章內容</label>
                                    <div id="editor-container"></div>
                                    <input type="hidden" id="content" name="content">
                                </div>
                            </div>
                        </div>

                    </section>
                    <section class="section">
                        <div class="card">
                            <div class="card-body">
                                <label for="tag" class="form-label required">文章排程</label>
                                <div class="mb-3">
                                    <label for="start_time" class="form-label">上架時間</label>
                                    <input type="text" class="form-control mb-3 flatpickr-no-config flatpickr-input"
                                        id="start_time" placeholder="文章發佈時間" name="start_time" required>
                                </div>
                                <div class="mb-3">
                                    <label for="end_time" class="form-label">下架時間</label>
                                    <input type="text" class="form-control mb-3 flatpickr-no-config flatpickr-input"
                                        id="end_time" placeholder="文章下架時間" name="end_time" required>
                                </div>

                                <label for="tag" class="form-label mt-5">文章狀態</label>
                                <div class="row px-3 ">
                                    <div class="form-check col-4">
                                        <input class="form-check-input" type="radio" name="flexRadioDefault" value="0"
                                            id="flexRadioDefault1" checked>
                                        <label class="form-check-label" for="flexRadioDefault1">
                                            儲存草稿
                                        </label>
                                    </div>
                                    <div class="form-check col-4">
                                        <input class="form-check-input" type="radio" name="flexRadioDefault" value="1"
                                            id="flexRadioDefault1">
                                        <label class="form-check-label" for="flexRadioDefault1">
                                            發布文章
                                        </label>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </section>
                    <div class="d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary">送出</button>
                    </div>
                </form>
                <div id="result" class="mt-4"></div>
            </div>
        </div>
    </div>


    <?php include("../js.php") ?>
    <!-- Choices JS -->
    <script src="../assets/extensions/choices.js/public/assets/scripts/choices.js"></script>
    <!-- Quill JS -->
    <script src="../assets/extensions/quill/quill.min.js"></script>

    <script>
    //調整編輯器
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{
                    font: []
                }],

                [{
                    size: ['small', false, 'large', 'huge']
                }],

                ['bold', 'italic', 'underline', 'strike'],

                [{
                    list: 'ordered'
                }, {
                    list: 'bullet'
                }],
                [{
                    script: 'sub'
                }, {
                    script: 'super'
                }],


                ['link', 'image'],
                [{
                    color: []
                }, {
                    background: []
                }],
                [{
                    align: []
                }],
            ]
        }
    });


    //上傳圖片
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const imgElement = document.getElementById('image-preview');
                imgElement.src = event.target.result;
                imgElement.classList.remove('d-none'); // 顯示圖片
            };
            reader.readAsDataURL(file);
        }
    });

    //判斷圖檔
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
            const fileInfo = file.name.split('.');
            const extension = fileInfo[fileInfo.length - 1].toLowerCase();

            // 檢查副檔名
            if (!allowedTypes.includes(extension)) {
                alert("只允許上傳 jpg, jpeg, png, bmp,webp 格式的圖檔。");
                e.target.value = ""; // 上傳錯的，清空選擇的檔案
                document.getElementById('image-preview').classList.add('d-none'); // 隱藏預覽圖
                return;
            }

            // 圖片預覽
            const reader = new FileReader();
            reader.onload = function(event) {
                const imgElement = document.getElementById('image-preview');
                imgElement.src = event.target.result;
                imgElement.classList.remove("d-none"); // 顯示圖片
            };
            reader.readAsDataURL(file);
        }
    });



    // 將 Quill 的內容存入隱藏的input
    document.getElementById("content").value = quill.root.innerHTML;

    //提交
    document.getElementById("articleForm").addEventListener("submit", function(e) {
        e.preventDefault();

        const title = document.getElementById("title").value.trim();
        if (!title) {
            alert("文章標題為必填欄位");
            e.preventDefault();
            return;
        }
        const content = quill.root.innerHTML.trim()
        if (!content || content === "<p><br></p>") {
            alert("文章內容為必填欄位");
            e.preventDefault();
            return;
        }

        document.getElementById("content").value = content; // 將內容存到隱藏的input中

        const start_time = document.getElementById("start_time").value;
        if (!start_time || start_time === "00 00:00:00") {
            alert("上架時間必填欄位");
            e.preventDefault();
            return;
        };
        const end_time = document.getElementById("end_time").value;
        if (!end_time) {
            alert("自動設定為永久上架")
            document.getElementById("end_time").value = "9999-12-31";

        };

        if (start_time && end_time && new Date(end_time) < new Date(start_time)) {
            alert('結束日期不能小於開始日期！');
            e.preventDefault();
            return;
        }

        const formData = new FormData(document.getElementById("articleForm"));
        fetch("ArticleSubmit.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert("文章上架成功！");
                window.location.href = "ArticleList.php";
            })
            .catch(error => console.error("上架失敗:", error));
    });
    </script>

</body>

</html>