<?php
require_once("../pdoConnect.php"); // 連接資料庫

$sql = "SELECT VendorID, VendorName FROM Vendor WHERE VendorValid = 1";
$stmt = $dbHost->prepare($sql);
$stmt->execute();
$vendors = $stmt->fetchAll(PDO::FETCH_ASSOC); // 獲取所有 vendor 的數據
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CreateEvent</title>
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
            margin-bottom: -0.6rem;
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

        .page-heading {
            margin: 0 0 -1rem;
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

            <div class="page-heading">

                <div class="page-title">
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3 class="">新增活動</h3>
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
                <form id="creatEventForm" action="./pdoCreateEvent.php" method="POST" enctype="multipart/form-data">
                    <div class="card">
                        <div class="card-body">
                            <!-- 預覽圖片的區域 -->
                            <div class="mb-3">
                                <label for="image" class="form-label">封面圖片</label>
                                <input type="file" class="form-control imagePreviewFileName" id="image" accept="image/*" name="image">
                                <!-- 預留預覽圖片框 -->
                                <div id="image-preview-wrapper" class="image-preview-wrapper">
                                    <img id="image-preview" src="#" alt="圖片預覽" class="img-fluid d-none" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="EventTitle" class="form-label col-3 required">活動標題</label>
                                <div class="col">
                                    <input type="text" class="form-control mb-3" id="EventTitle" name="EventTitle" placeholder="輸入活動標題" required>
                                </div>
                            </div>
                            <div class="mb-3"> <label for="eventTime" class="form-label col-3">活動時間</label>
                                <div class="row gy-3">
                                    <div class="col">
                                        <input id="EventStartTime" name="EventStartTime" type="text" class="form-control  flatpickr-no-config flatpickr-input active " placeholder="開始時間" readonly="readonly">
                                    </div>
                                    <div class="col">
                                        <input id="EventEndTime" name="EventEndTime" type="text" class="form-control  flatpickr-no-config flatpickr-input active " placeholder="結束時間" readonly="readonly">
                                    </div>
                                </div>

                            </div>
                            <div class="mb-3 mt-1">
                                <label for="editor-container" class="form-label">活動內容</label>
                                <div id="full">
                                </div>
                                <input type="hidden" id="EventInfo" name="EventInfo">
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
                                <label for="vendorList" class="form-label required">主辦廠商</label>
                                <select class="choices form-select" id="vendorList" name="VendorID">
                                    <option value="">請選擇廠商</option>
                                    <?php foreach ($vendors as $vendor): ?>
                                        <option value="<?php echo $vendor['VendorID']; ?>">
                                            <?php echo htmlspecialchars($vendor['VendorName']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col">
                                        <label for="EventSignStartTime" class="form-label col-3">報名開始</label>
                                        <input id="EventSignStartTime" name="EventSignStartTime" type="text" class="form-control flatpickr-no-config flatpickr-input active " placeholder="報名開始時間" readonly="readonly">
                                    </div>
                                    <div class="col">
                                        <label for="EventSignEndTime" class="form-label">報名結束</label>
                                        <input id="EventSignEndTime" name="EventSignEndTime" type="text" class="form-control flatpickr-no-config flatpickr-input active " placeholder="報名結束時間" readonly="readonly">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="row">
                                    <div class="col">
                                        <label for="EventFee" class="form-label">活動金額</label>
                                        <input class="form-control" id="EventFee" placeholder="請輸入活動金額" name="EventFee">
                                    </div>
                                    <div class="col">
                                        <label for="EventParticipantLimit" class="form-label">人數上限</label>
                                        <input class="form-control" id="EventParticipantLimit" placeholder="請輸入人數上限" name="EventParticipantLimit">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex align-items-center">
                                    <label class="form-label me-3 mb-0" for="eventType">活動類型</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="eventType" id="eventTypeOnline" value="online" checked>
                                            <label class="form-check-label" for="eventTypeOnline">
                                                線上活動
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="eventType" id="eventTypeOffline" value="offline">
                                            <label class="form-check-label" for="eventTypeOffline">
                                                實體活動
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="EventAddress" class="mb-4">
                                <!-- <label class="form-label">活動地址</label>  -->
                                <div class="row g-3">
                                    <div class="col-md-2">
                                        <select class="form-select" id="EventRegion" name="EventRegion" disabled>
                                            <option selected disabled>選擇區域</option>
                                            <option value="north">北部</option>
                                            <option value="central">中部</option>
                                            <option value="south">南部</option>
                                            <option value="east">東部</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-select" id="EventCity" name="EventCity" disabled>
                                            <option selected disabled>選擇縣市</option>
                                            <!-- Options will be populated dynamically based on the selected region -->
                                        </select>
                                    </div>
                                    <div class="col-md">
                                        <input type="text" class="form-control" id="EventLocation" name="EventLocation" placeholder="詳細地址" disabled>
                                    </div>
                                </div>
                            </div>


                            <div class="mb-3">
                                <div class="row">
                                    <div class="col"><label for="EventPublishStartTime" class="form-label">上架日期</label>
                                        <input
                                            type="text"
                                            class="form-control mb-3 flatpickr-no-config flatpickr-input active" id="EventPublishStartTime" name="EventPublishStartTime" placeholder="上架時間" readonly="readonly">
                                    </div>
                                    <div class="col"><label for="EventPublishEndTime" class="form-label">下架日期</label>
                                        <input
                                            type="text"
                                            class="form-control mb-3 flatpickr-no-config flatpickr-input active"
                                            id="EventPublishEndTime"
                                            placeholder="下架時間" name="EventPublishEndTime" readonly="readonly">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 d-flex align-items-center">
                                <label class="form-label me-3 mb-0" for="EventStatus">發布狀態</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="EventStatus" id="eventStatusPublished" value="published">
                                        <label class="form-check-label" for="eventStatusPublished">
                                            發布活動
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="EventStatus" id="eventStatusDraft" value="draft" checked>
                                        <label class="form-check-label" for="eventStatusDraft">
                                            儲存草稿
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="d-flex justify-content-center mt-3 mb-3">
                <button id="send" type="submit" class="btn btn-primary">送出</button>
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

        //     document.getElementById('creatEventForm').addEventListener('submit', function(e) {
        //         e.preventDefault();
        //         const EventTitle = document.getElementById('EventTitle').value;
        //         const EventInfo = quill.root.innerHTML;
        //         document.getElementById('EventInfo').value = EventInfo;

        //         console.log('EventInfo content:', EventInfo); // 用於調試

        //         // 如果驗證通過，提交表單
        //         this.submit();
        //     });

        //     // 其他事件監聽器和邏輯保持不變...
        // });
        // 預覽圖片
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
                    document.getElementById('creatEventForm').submit();
                }
            });
        });
        // document.addEventListener('DOMContentLoaded', function() {
        //     const elementIds = {
        //         'EventTitle': '活動標題',
        //         "vendorList": "主辦廠商",
        //         'EventSignStartTime': '報名開始時間',
        //         'EventSignEndTime': '報名結束時間',
        //         'EventStartTime': '活動開始時間',
        //         'EventEndTime': '活動結束時間',
        //         'EventPublishStartTime': '活動上架時間',
        //         'EventPublishEndTime': '活動下架時間',
        //         'EventParticipantLimit': '報名人數限制',
        //         'EventFee': '活動金額'
        //     };
        //     const defaultDate = "9999-12-31 23:59:59";
        //     const defaultParticipantLimit = 9999;
        //     const defaultEventFee = 0;

        //     document.querySelector("#send").addEventListener("click", function(e) {
        //         e.preventDefault(); // 阻止默認的提交行為

        //         let errors = [];
        //         const dates = {};

        //         // 檢查日期輸入、人數限制和活動費用並進行驗證
        //         for (const [id, name] of Object.entries(elementIds)) {
        //             const element = document.getElementById(id);
        //             if (element) {
        //                 if (id === 'EventParticipantLimit') {
        //                     if (!element.value.trim()) {
        //                         errors.push(`${name}未填寫，預設為9999人`);
        //                         element.value = defaultParticipantLimit;
        //                     } else {
        //                         const limit = parseInt(element.value);
        //                         if (isNaN(limit) || limit <= 0) {
        //                             errors.push(`${name}必須是正整數`);
        //                         }
        //                     }
        //                 } else if (id === 'EventFee') {
        //                     if (!element.value.trim()) {
        //                         errors.push(`${name}未填寫，預設為0元`);
        //                         element.value = defaultEventFee;
        //                     } else {
        //                         const fee = parseFloat(element.value);
        //                         if (isNaN(fee) || fee < 0) {
        //                             errors.push(`${name}必須是非負數`);
        //                         }
        //                     }
        //                 } else if (id == 'EventTitle') {
        //                     if (!element.value.trim()) {
        //                         errors.push(`${name}不能為空`);
        //                     }
        //                 } else if (id === 'vendorList') {
        //                     if (!element.value.trim()) {
        //                         errors.push(`${name}不能為空`);
        //                     } else {
        //                         const vendorID = parseInt(element.value);
        //                         if (isNaN(vendorID) || vendorID <= 0) {
        //                             errors.push(`${name}必須是正整數`);
        //                         }
        //                     }
        //                 } else {
        //                     if (!element.value.trim()) {
        //                         errors.push(`${name}未填寫，預設為9999-12-31 23:59:59`);
        //                         element.value = defaultDate;
        //                     }
        //                     dates[id] = new Date(element.value);
        //                     if (isNaN(dates[id].getTime())) {
        //                         errors.push(`${name}格式不正確`);
        //                     }
        //                 }
        //             } else {
        //                 errors.push(`請輸入${name}欄位資訊`);
        //             }
        //         }
        //         // 檢查活動標題
        //         const eventTitleElement = document.getElementById('EventTitle');
        //         // 日期邏輯驗證
        //         if (dates.EventSignEndTime < dates.EventSignStartTime) {
        //             errors.push("報名結束時間不能早於報名開始時間");
        //         }
        //         if (dates.EventEndTime < dates.EventStartTime) {
        //             errors.push("活動結束時間不能早於活動開始時間");
        //         }
        //         if (dates.EventPublishEndTime < dates.EventPublishStartTime) {
        //             errors.push("下架時間不能早於上架時間");
        //         }

        //         // 如果有錯誤，顯示所有錯誤訊息
        //         if (errors.length > 0) {
        //             alert("以下項目未完成或有誤：\n" + errors.join("\n"));
        //             console.log("表單驗證失敗，未提交");
        //         } else {
        //             // 如果所有驗證都通過，提交表單
        //             // alert("表單驗證成功，已提交");
        //             document.getElementById('creatEventForm').submit();
        //         }
        //     });
        // });
    </script>
    <!-- <script>
        // Function to validate times and show alerts

        //老師改的正確版驗證提交表單
        const eventSignStartTime = document.getElementById('EventSignStartTime');
        const eventSignEndTime = document.getElementById('EventSignEndTime');
        const eventStartTime = document.getElementById('EventStartTime');
        const eventEndTime = document.getElementById('EventEndTime');
        const eventPublishStartTime = document.getElementById('EventPublishStartTime');
        const eventPublishEndTime = document.getElementById('EventPublishEndTime');
        const now = new Date();


        document.querySelector("#send").addEventListener("click", function(e) {
            e.preventDefault();

            const eventSignStartTime = new Date(document.getElementById('EventSignStartTime').value);
            const eventSignEndTime = new Date(document.getElementById('EventSignEndTime').value);
            const eventStartTime = new Date(document.getElementById('EventStartTime').value);
            const eventEndTime = new Date(document.getElementById('EventEndTime').value);
            const eventPublishStartTime = new Date(document.getElementById('EventPublishStartTime').value);
            const eventPublishEndTime = new Date(document.getElementById('EventPublishEndTime').value);

            // Validation logic
            if (eventSignEndTime < eventSignStartTime) {
                alert("報名結束時間不能早於報名開始時間！");
                return;
            }

            if (eventEndTime < eventStartTime) {
                alert("活動結束時間不能早於活動開始時間！");
                return;
            }

            if (eventPublishEndTime < eventPublishStartTime) {
                alert("下架時間不能早於上架時間！");
                return;
            }

            document.getElementById('creatEventForm').submit();
        })
    </script> -->
    <script src="../assets/static/js/pages/form-element-select.js"></script>

</body>

</html>