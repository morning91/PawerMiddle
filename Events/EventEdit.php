<?php
require_once("../pdoConnect.php"); // 連接資料庫



$id = $_GET["id"];

$sql = "
    SELECT OfficialEvent.*, Vendor.VendorName, Image.ImageUrl
    FROM OfficialEvent 
    JOIN Vendor ON OfficialEvent.VendorID = Vendor.VendorID 
    LEFT JOIN (
        SELECT EventID, ImageUrl
        FROM Image
        WHERE ImageUploadDate = (
            SELECT MAX(ImageUploadDate)
            FROM Image
            WHERE EventID = :id
        )
    ) AS Image ON OfficialEvent.EventID = Image.EventID
    WHERE OfficialEvent.EventID = :id
    LIMIT 1
";

try {
    $stmt = $dbHost->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    // var_dump($event); //先檢查一下有沒有抓到資料最後要刪掉
    if (!$event) {
        echo "找不到ID為 {$id} 的活動";
        exit;
    }

    $imageUrl = htmlspecialchars($event["ImageUrl"] ?? "");
} catch (PDOException $e) {
    echo "資料庫錯誤: " . $e->getMessage();
    exit;
}

// 獲取所有供應商的數據（如果需要的話）
$vendorSql = "SELECT VendorID, VendorName FROM Vendor";
$vendorStmt = $dbHost->query($vendorSql);
$vendors = $vendorStmt->fetchAll(PDO::FETCH_ASSOC);

// $imageSql = "SELECT ImageName,ImageUrl FROM Image";
// $imageStmt = $dbHost->query($imageSql);
// $images = $imageStmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($event)) {
    $eventRegion = $event['EventRegion'];
    $currentCity = $event['EventCity'];
    $eventLocation = $event['EventLocation']; // 假設 EventLocation 現在只包含詳細地址
    $cities = [
        'north' => ['基隆市', '臺北市', '新北市', '桃園市', '新竹市', '新竹縣', '宜蘭縣'],
        'central' => ['苗栗縣', '臺中市', '彰化縣', '南投縣', '雲林縣'],
        'south' => ['嘉義市', '嘉義縣', '臺南市', '高雄市', '屏東縣'],
        'east' => ['花蓮縣', '臺東縣']
    ];
} else {
    // 如果 $event 不存在，設置默認值
    $eventRegion = '';
    $currentCity = '';
    $detailAddress = '';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventEdit</title>
    <?php include("../headlink.php") ?>

    <!-- Quill Editor -->
    <link rel="stylesheet" href="../assets/extensions/quill/quill.snow.css">
    <link rel="stylesheet" href="../assets/extensions/choices.js/public/assets/styles/choices.css">
    <style>
        .image-preview-wrapper {
            width: 100%;
            /* 設置預覽框的寬度 */
            height: 400px;
            /* 設置預覽框的高度 */
            border: 1px solid lightgrey;
            border-radius: 0 0 4px 4px;
            /* 虛線框表示預覽區域 */
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            /* 確保圖片不會超出邊界 */
            /* 背景顏色，用於佔位 */
            margin-bottom: -0.8rem;
        }


        .imagePreviewFileName {
            border-radius: 4px 4px 0 0;
        }

        .image-preview-wrapper img {
            max-width: 100%;
            /* max-height: 100%; */
        }

        .choices__inner {
            background: #fff;
            font-size: 16px;
        }

        #editor-container {
            height: 300px;
        }

        .ql-editor {
            min-height: 200px;
            max-height: 500px;
            overflow-y: auto;
            /* 允許垂直滾動 */
        }

        .updatedatefooter {
            font-size: 12px;
        }
    </style>
</head>

<body>
    <script src="../assets/static/js/initTheme.js"></script>
    <div id="app">
        <?php include("../sidebar.php") ?>
        <div id="main">
            <header class="">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>

                </a>

            </header>
            <!-- Event -->
            <form id="EventEditForm" action="./pdoUpdateEvent.php" method="POST" enctype="multipart/form-data">

                <input type="hidden" name="id" value="<?= $id ?>">
                <div class="page-heading">
                    <div class="page-title">
                        <div class="row">
                            <div class="col-12 col-md-6 order-md-1 order-last">
                                <h3 class="">編輯活動 </h3>
                                <button type="button" class="btn btn-primary mb-4"> <a class="text-white" href="./OfficialEventsList.php?p=1&order=99"><i class="fa-solid fa-chevron-left"></i>回列表</a></button>
                            </div>
                            <div class="col-12 col-md-6 order-md-2 order-first">
                                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="index.html"><i class="fa-solid fa-house"></i></a></li>
                                        <li class="breadcrumb-item active" aria-current="page">活動管理</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <!-- 預覽圖片的區域 -->
                            <div class="mb-3">
                                <label for="image" class="form-label">封面圖片</label>
                                <input class="form-control" type="file" id="image" name="image" onchange="previewImage(event)">
                                <!-- 預留圖片預覽框 -->
                                <div id="image-preview-wrapper" class="image-preview-wrapper d-none">
                                    <img id="image-preview" src="#" alt="图片预览" class="img-fluid" />
                                </div>
                                <!-- 原始圖片帶入框 -->
                                <div id="EventImage" class="image-preview-wrapper">
                                    <?php if (!empty($event["ImageUrl"])): ?>

                                        <img src=".<?= $event["ImageUrl"] ?>" alt="Image Preview" />
                                    <?php else: ?>
                                        <p>沒有圖片</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="EventTitle" class="form-label col-3 required">活動標題</label>
                            <div class="col">
                                <input type="text" class="form-control mb-3" id="EventTitle" name="EventTitle"
                                    value="<?= $event["EventTitle"] ?>" placeholder="輸入活動標題" required>
                            </div>
                        </div>
                        <div class="mb-3"> <label for="eventTime" class="form-label col-3">活動時間</label>
                            <div class="row">
                                <div class="col">
                                    <input id="EventStartTime" name="EventStartTime" type="text" class="form-control flatpickr-no-config flatpickr-input active"
                                        placeholder="開始時間" value="<?= $event["EventStartTime"] ?>" required>
                                </div>

                                <div class=" col">
                                    <input id="EventEndTime" name="EventEndTime" type="text" class="form-control  flatpickr-no-config flatpickr-input active " placeholder="結束時間" value="<?= $event["EventEndTime"] ?>" required>
                                </div>
                            </div>

                        </div>
                        <!-- <div class="mb-3 mt-1">
                            <label for="editor-container" class="form-label">活動內容</label>
                            <div id="full">
                            </div>
            <input type="hidden" id="EventInfo" name="EventInfo" require>
                    
                        </div> -->
                        <div class="mb-3 mt-1">
                            <label for="editor-container" class="form-label">活動內容</label>
                            <div id="full"> <?php echo "$event[EventInfo]" ?>
                            </div>

                            <input type="hidden" id="EventInfo" name="EventInfo" require>
                        </div>
                        <!-- <div class="mb-3">
                            <label for="eventTag" class="form-label col-3">活動標籤</label>
                            <div class="form-group">
                                <select class="choices form-select multiple-remove" multiple="multiple" id="eventTag" name="eventTag">
                                    <option value="cat">貓皇</option>
                                    <option value="dog" selected>狗</option>
                                    <option value="basicHealth">基礎保健</option>
                                    <option value="skin">皮毛保養</option>
                                    <option value="innerHealth">肝臟保養</option>
                                    <option value="eyeHealth">眼睛保護</option>
                                    <option value="pet" selected>寵物</option>
                                </select>
                            </div>
                        </div> -->
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="vendorList" class="form-label">主辦廠商</label>
                            <select class="choices form-select" id="vendorList" name="VendorID" required>
                                <?php foreach ($vendors as $vendor): ?>
                                    <option value="<?= $vendor['VendorID'] ?>"
                                        <?= ($vendor["VendorID"] == $event["VendorID"]) ? 'selected' : '' ?>>
                                        <?= $vendor["VendorName"] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="row">
                                <div class="col">
                                    <label for="EventSignStartTime" class="form-label col-3">報名開始</label>
                                    <input id="EventSignStartTime" name="EventSignStartTime" type="text" class="form-control flatpickr-no-config flatpickr-input active " placeholder="報名開始時間" value="<?= $event["EventSignStartTime"] ?>" readonly="readonly">
                                </div>
                                <div class="col">
                                    <label for="EventSignEndTime" class="form-label">報名結束</label>
                                    <input id="EventSignEndTime" name="EventSignEndTime" type="text" class="form-control flatpickr-no-config flatpickr-input active " placeholder="報名結束時間" value="<?= $event["EventSignEndTime"] ?>" readonly="readonly">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="row">
                                <div class="col">
                                    <label for="EventFee" class="form-label">活動金額</label>
                                    <input class="form-control" id="EventFee" placeholder="請輸入活動金額" name="EventFee" value="<?= number_format($event["EventFee"], 0) ?>">
                                </div>
                                <div class="col">
                                    <label for="EventParticipantLimit" class="form-label">人數上限</label>
                                    <input class="form-control" id="EventParticipantLimit" placeholder="請輸入人數上限" name="EventParticipantLimit" value="<?= $event["EventParticipantLimit"] ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex align-items-center">
                                <label class="form-label me-3 mb-0" for="eventType">活動類型</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="eventType" id="eventTypeOnline" value="online"
                                            <?= ($event["EventRegion"] == null || $event["EventRegion"] == "") ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="eventTypeOnline">
                                            線上活動
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="eventType" id="eventTypeOffline" value="offline"
                                            <?= ($event["EventRegion"] != null && $event["EventRegion"] != "") ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="eventTypeOffline">
                                            實體活動
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="EventAddress" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <select class="form-select" id="EventRegion" name="EventRegion">
                                        option selected disabled>選擇區域</option>
                                        <option value="north" <?= $eventRegion == 'north' ? 'selected' : '' ?>>北部</option>
                                        <option value="central" <?= $eventRegion == 'central' ? 'selected' : '' ?>>中部</option>
                                        <option value="south" <?= $eventRegion == 'south' ? 'selected' : '' ?>>南部</option>
                                        <option value="east" <?= $eventRegion == 'east' ? 'selected' : '' ?>>東部</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" id="EventCity" name="EventCity">
                                        <option selected disabled>選擇縣市</option>
                                        <?php if ($eventRegion && isset($cities[$eventRegion])): ?>
                                            <?php foreach ($cities[$eventRegion] as $city): ?>
                                                <option value="<?= $event["EventCity"] ?>" <?= $event['EventCity'] == $currentCity ? 'selected' : '' ?>><?= $event['EventCity'] ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md">
                                    <input type="text" class="form-control" id="EventLocation" name="EventLocation"
                                        placeholder="詳細地址" value="<?= $event["EventLocation"] ?>">
                                </div>
                            </div>
                        </div>


                        <div class="mb-3">
                            <div class="row">
                                <div class="col"><label for="EventPublishStartTime" class="form-label">上架日期</label>
                                    <input
                                        type="text"
                                        class="form-control mb-3 flatpickr-no-config flatpickr-input active" id="EventPublishStartTime" name="EventPublishStartTime" placeholder="上架時間" value="<?= $event["EventPublishStartTime"] ?>">
                                </div>
                                <div class="col"><label for="EventPublishEndTime" class="form-label">下架日期</label>
                                    <input
                                        type="text"
                                        class="form-control mb-3 flatpickr-no-config flatpickr-input active"
                                        id="EventPublishEndTime"
                                        placeholder="下架時間" name="EventPublishEndTime" value="<?= $event["EventPublishEndTime"] ?>">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 d-flex align-items-center">
                            <label class="form-label me-3 mb-0" for="EventStatus">發布狀態</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="EventStatus" id="eventStatusPublished" value="published" <?php echo $event["EventStatus"] == "published" ? "checked" : ""; ?>>
                                    <label class="form-check-label" for="eventStatusPublished">
                                        發布活動
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="EventStatus" id="eventStatusDraft" value="draft" <?php echo $event["EventStatus"] == "draft" ? "checked" : ""; ?>>
                                    <label class="form-check-label" for="eventStatusDraft">
                                        儲存草稿
                                    </label>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>
                <p class="updatedatefooter text-body-tertiary text-end pe-3">最近更新時間：<?= $event["EventUpdateDate"] ?></p>
                <div class="d-flex justify-content-center my-3">
                    <button id="send" type="submit" class="btn btn-primary me-2">儲存</button>
                    <button id="delete" type="submit" class="btn btn-danger "><a class="text-white" href="pdoDeleteEvent.php?id=<?= $event["EventID"] ?>">刪除</a></button>
                </div>
            </form>

            <?php include("../footer.php") ?>
        </div>

    </div>

    </div>
    <?php include("../js.php") ?>



    <!-- QuillEditor -->
    <script src="../assets/compiled/js/app.js"></script>
    <script src="../assets/extensions/quill/quill.min.js"></script>
    <script src="../assets/static/js/pages/quill.js"></script>
    <!-- <script src="../assets/extensions/quill/quill.min.js"></script> -->
    <script>
        // document.addEventListener('DOMContentLoaded', function() {
        //     // 假設 #full 是您想要使用的 Quill 編輯器
        //     var quill = Quill.find(document.querySelector('#full'));

        //     if (!quill) {
        //         console.error('Quill editor not found');
        //         return;
        //     }


        //     document.getElementById('EventEditForm').addEventListener('submit', function(e) {
        //         e.preventDefault();
        //         const EventTitle = document.getElementById('EventTitle').value;
        //         const EventInfo = quill.root.innerHTML;
        //         document.getElementById('EventInfo').value = EventInfo;

        //         console.log('EventInfo content:', EventInfo); // 用於調試

        //         // 如果驗證通過，提交表單
        //         this.submit();
        //     });

        // });
        // 預覽框與已帶圖片的切換
        function previewImage(event) {
            const EventImage = document.getElementById('EventImage')
            const previewWrapper = document.getElementById('image-preview-wrapper');
            const previewImage = document.getElementById('image-preview');
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewWrapper.classList.remove('d-none'); // 显示预览框
                    EventImage.classList.add('d-none'); // 显示预览框
                };

                reader.readAsDataURL(file);
            } else {
                previewWrapper.classList.add('d-none'); // 隐藏预览框
            }
        }

        document.getElementById('image').addEventListener('change', function(event) {
            const file = event.target.files[0]; // 取得上傳的圖片
            const imagePreview = document.getElementById('image-preview');

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    imagePreview.src = e.target.result; // 設置預覽圖片的 src 屬性為上傳圖片的路徑
                    imagePreview.classList.remove('d-none'); // 顯示預覽圖片
                };

                reader.readAsDataURL(file); // 讀取圖片，並在讀取完成後觸發 onload 事件
            } else {
                imagePreview.src = "#";
                imagePreview.classList.add('d-none'); // 沒有選擇圖片時隱藏預覽圖片
            }
        });

        //實體地址填入欄位
        document.addEventListener('DOMContentLoaded', function() {
            const eventTypeRadios = document.querySelectorAll('input[name="eventType"]');
            const addressFields = document.querySelectorAll('#EventAddress select, #EventAddress input');
            const regionSelect = document.getElementById('EventRegion');
            const citySelect = document.getElementById('EventCity');

            const cityOptions = {
                north: ['基隆市', '臺北市', '新北市', '宜蘭縣'],
                central: ['苗栗縣', '臺中市', '彰化縣', '南投縣', '雲林縣'],
                south: ['嘉義市', '嘉義縣', '臺南市', '高雄市', '屏東縣'],
                east: ['花蓮縣', '臺東縣']
            };

            function toggleAddressFields() {
                const isOffline = document.getElementById('eventTypeOffline').checked;
                addressFields.forEach(field => {
                    field.disabled = !isOffline;
                });
            }

            eventTypeRadios.forEach(radio => {
                radio.addEventListener('change', toggleAddressFields);
            });

            regionSelect.addEventListener('change', function() {
                const selectedRegion = this.value;
                citySelect.innerHTML = '<option selected disabled>選擇縣市</option>';

                if (cityOptions[selectedRegion]) {
                    cityOptions[selectedRegion].forEach(city => {
                        const option = document.createElement('option');
                        option.value = city;
                        option.textContent = city;
                        citySelect.appendChild(option);
                    });
                }
            });

            // Initial state
            toggleAddressFields();
        });
        // Initialize Quill editor with image upload option
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Choices
            const choicesElements = document.querySelectorAll('.choices');
            choicesElements.forEach(element => {
                new Choices(element, {
                    allowHTML: true
                });
            });

            // Handle form submission
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const elementIds = {
                'EventTitle': '活動標題',
                "vendorList": "主辦廠商",
                'EventSignStartTime': '報名開始時間',
                'EventSignEndTime': '報名結束時間',
                'EventStartTime': '活動開始時間',
                'EventEndTime': '活動結束時間',
                'EventPublishStartTime': '活動上架時間',
                'EventPublishEndTime': '活動下架時間',
                'EventParticipantLimit': '報名人數限制',
                'EventFee': '活動金額'
            };
            const defaultDate = "9999-12-31 23:59:59";
            const defaultParticipantLimit = 9999;
            const defaultEventFee = 0;

            // 初始化 Quill 編輯器
            var quill = Quill.find(document.querySelector('#full'));
            if (!quill) {
                console.error('Quill editor not found');
                return;
            }

            document.querySelector("#send").addEventListener("click", function(e) {
                e.preventDefault(); // 阻止默認的提交行為

                let errors = [];
                const dates = {};

                // 獲取 Quill 編輯器的內容並設置到隱藏的 input 字段
                const EventInfo = quill.root.innerHTML;
                document.getElementById('EventInfo').value = EventInfo;

                console.log('EventInfo content:', EventInfo); // 用於調試


                // 檢查日期輸入、人數限制和活動費用並進行驗證
                for (const [id, name] of Object.entries(elementIds)) {
                    const element = document.getElementById(id);
                    if (element) {
                        if (id === 'EventParticipantLimit') {
                            if (!element.value.trim()) {
                                errors.push(`${name}未填寫，預設為9999人`);
                                element.value = defaultParticipantLimit;
                            } else {
                                const limit = parseInt(element.value);
                                if (isNaN(limit) || limit <= 0) {
                                    errors.push(`${name}必須是正整數`);
                                }
                            }
                        } else if (id === 'EventFee') {
                            if (!element.value.trim()) {
                                errors.push(`${name}未填寫，預設為0元`);
                                element.value = defaultEventFee;
                            } else {
                                const fee = parseFloat(element.value);
                                if (isNaN(fee) || fee < 0) {
                                    errors.push(`${name}必須是非負數`);
                                }
                            }
                        } else if (id == 'EventTitle') {
                            if (!element.value.trim()) {
                                errors.push(`${name}不能為空`);
                            }
                        } else if (id === 'vendorList') {
                            if (!element.value.trim()) {
                                errors.push(`${name}不能為空`);
                            } else {
                                const vendorID = parseInt(element.value);
                                if (isNaN(vendorID) || vendorID <= 0) {
                                    errors.push(`${name}必須是正整數`);
                                }
                            }
                        } else {
                            if (!element.value.trim()) {
                                errors.push(`${name}未填寫，預設為9999-12-31 23:59:59`);
                                element.value = defaultDate;
                            }
                            dates[id] = new Date(element.value);
                            if (isNaN(dates[id].getTime())) {
                                errors.push(`${name}格式不正確`);
                            }
                        }
                    } else {
                        errors.push(`請輸入${name}欄位資訊`);
                    }
                }

                // 日期邏輯驗證
                if (dates.EventSignEndTime < dates.EventSignStartTime) {
                    errors.push("報名結束時間不能早於報名開始時間");
                }
                if (dates.EventEndTime < dates.EventStartTime) {
                    errors.push("活動結束時間不能早於活動開始時間");
                }
                if (dates.EventPublishEndTime < dates.EventPublishStartTime) {
                    errors.push("下架時間不能早於上架時間");
                }

                // 如果有錯誤，顯示所有錯誤訊息
                if (errors.length > 0) {
                    alert("以下項目未完成或有誤：\n" + errors.join("\n"));
                    console.log("表單驗證失敗，未提交");
                } else {
                    // 如果所有驗證都通過，提交表單
                    document.getElementById('EventEditForm').submit();
                }
            });
        });
    </script>
    <script src="../assets/static/js/pages/form-element-select.js"></script>

</body>

</html>