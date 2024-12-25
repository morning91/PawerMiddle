<?php
require_once("../pdoConnect.php");

//取代碼表選項
$sql = "SELECT * FROM SystemCode";
$stmt = $dbHost->prepare($sql);
$stmt->execute();

$PromotionCondition_options = [];
$CalculateType_options = [];
$MemberLevel_options = [];
$PromotionType_options = [];
$IsCumulative_options = [];
$EnableStatus_options = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if ($row['Type'] == 'PromotionCondition') {
        $PromotionCondition_options[] = $row;
    } elseif ($row['Type'] == 'CalculateType') {
        $CalculateType_options[] = $row;
    } elseif ($row['Type'] == 'MemberLevel') {
        $MemberLevel_options[] = $row;
    } elseif ($row['Type'] == 'PromotionType') {
        $PromotionType_options[] = $row;
    } elseif ($row['Type'] == 'EnableStatus') {
        $EnableStatus_options[] = $row;
    } elseif ($row['Type'] == 'IsCumulative') {
        $IsCumulative_options[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>促銷管理</title>

    <?php include("../headlink.php") ?>
</head>

<body>
    <?php include("../modals.php") ?>
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
                    <div class="row">
                        <div class="col-12 col-md-6 order-md-1 order-last">
                            <h3>促銷管理</h3>
                            <p class="text-subtitle text-muted"></p>
                        </div>
                        <div class="col-12 col-md-6 order-md-2 order-first">
                            <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="index.html"><i
                                                class="fa-solid fa-house"></i></a></li>
                                    <li class="breadcrumb-item active" aria-current="page">促銷管理</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <section class="section">
                    <div class="row">
                        <div class="col-12 d-flex justify-content-start mb-2">
                            <a href="index.php" class="btn btn-secondary me-1 mb-1">返回</a>
                        </div>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">主要資訊</h4>
                                    <hr class="mb-0">
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="" class="required">促銷名稱</label>
                                                    <input type="text" name="" class="form-control" id="Name" placeholder="請輸入促銷名稱">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="" class="required">促銷時間</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control flatpickr-no-config flatpickr-input" placeholder="請選擇促銷開始時間" id="StartTime">
                                                        <input type="text" class="form-control flatpickr-no-config flatpickr-input" placeholder="請選擇促銷結束時間" id="EndTime">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="" class="required">消費門檻</label>
                                                    <div class="input-group">
                                                        <select class="form-select" name="" id="PromotionCondition">
                                                            <?php if (!empty($PromotionCondition_options)) : ?>
                                                                <?php foreach ($PromotionCondition_options as $option): ?>
                                                                    <option value="<?= $option['Value'] ?>"
                                                                        <?= $option['Value'] == 1 ? 'selected' : '' ?>>
                                                                        <?= $option['Description'] ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            <?php else : ?>
                                                                無可用選項
                                                            <?php endif;  ?>
                                                        </select>
                                                        <span class="input-group-text ConditionMinArea">滿</span>
                                                        <input type="number" min="0" name="" class="form-control ConditionMinArea" id="ConditionMinValue" placeholder="請輸入最低門檻數字">
                                                        <span class="input-group-text ConditionMinArea">元</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="" class="required">優惠金額</label>
                                                    <div class="input-group">
                                                        <input type="number" min="0" name="" class="form-control" id="Value" placeholder="請輸入數字 並 選擇優惠計算為百分比或固定金額">
                                                        <div class="col-2">
                                                            <select class="form-select" name="" id="CalculateType">
                                                                <?php
                                                                if (!empty($CalculateType_options)) {
                                                                    foreach ($CalculateType_options as $option) {
                                                                        $selected = ($option['Value'] == 1) ? 'selected' : '';
                                                                        echo "<option value='" . $option['Value'] . "' $selected>" . $option['Description'] . "</option>";
                                                                    }
                                                                } else {
                                                                    echo "<option value=''>無可用選項</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12 ConditionMinArea">
                                                <div class="form-group">
                                                    <label for="" class="">滿額可累計優惠</label>
                                                    <select class="form-select" name="" id="IsCumulative">
                                                        <?php
                                                        if (!empty($IsCumulative_options)) {
                                                            foreach ($IsCumulative_options as $option) {
                                                                $selected = ($option['Value'] == 0) ? 'selected' : '';
                                                                echo "<option value='" . $option['Value'] . "' $selected>" . $option['Description'] . "</option>";
                                                            }
                                                        } else {
                                                            echo "<option value=''>無可用選項</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="" class="required">適用會員等級</label>
                                                    <select class="form-select" name="" id="MemberLevel">
                                                        <?php
                                                        if (!empty($MemberLevel_options)) {
                                                            foreach ($MemberLevel_options as $option) {
                                                                $selected = ($option['Value'] == 0) ? 'selected' : '';
                                                                echo "<option value='" . $option['Value'] . "' $selected>" . $option['Description'] . "</option>";
                                                            }
                                                        } else {
                                                            echo "<option value=''>無可用選項</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="" class="required">促銷方式</label>
                                                    <select class="form-select" name="" id="PromotionType">
                                                        <?php
                                                        if (!empty($PromotionType_options)) {
                                                            foreach ($PromotionType_options as $option) {
                                                                $selected = ($option['Value'] == 1) ? 'selected' : '';
                                                                echo "<option value='" . $option['Value'] . "' $selected>" . $option['Description'] . "</option>";
                                                            }
                                                        } else {
                                                            echo "<option value=''>無可用選項</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="" class="required">啟用狀態</label>
                                                    <select class="form-select" name="" id="EnableStatus">
                                                        <?php
                                                        if (!empty($EnableStatus_options)) {
                                                            foreach ($EnableStatus_options as $option) {
                                                                $selected = ($option['Value'] == 1) ? 'selected' : '';
                                                                echo "<option value='" . $option['Value'] . "' $selected>" . $option['Description'] . "</option>";
                                                            }
                                                        } else {
                                                            echo "<option value=''>無可用選項</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card couponarea">
                                <div class="card-header">
                                    <h4 class="card-title">優惠券</h4>
                                    <hr class="mb-0">
                                </div>
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="" class="required">優惠券序號</label>
                                                    <div class="input-group">
                                                        <input type="text" name="" class="form-control" id="CouponSerial" placeholder="請輸入序號，可為中英混雜數字與符號">
                                                        <button
                                                            type="button"
                                                            class="btn btn-primary" id="randombtn">
                                                            隨機產生
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="" class="">優惠券說明</label>
                                                    <input type="text" name="" class="form-control" id="CouponInfo" placeholder="請輸入優惠券說明">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="" class="required">截止領取時間</label>
                                                    <input type="text" class="form-control mb-3 flatpickr-no-config flatpickr-input" placeholder="請輸入截止領取時間" id="CouponReceiveEndTime">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="" class="required">使用次數限制</label>
                                                    <input type="number" min="0" name="" class="form-control" id="CouponUseMax" placeholder="請輸入數字">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-center">
                                <button type="button" class="btn btn-primary me-1 mb-1" id="send">送出</button>
                            </div>
                        </div>
                    </div>
            </div>
            </section>
            <?php include("../footer.php") ?>
        </div>

    </div>
    <?php include("../js.php") ?>
    <script>
        const Name = document.querySelector("#Name");
        const StartTime = document.querySelector("#StartTime");
        const EndTime = document.querySelector("#EndTime");
        const PromotionCondition = document.querySelector("#PromotionCondition");
        const ConditionMinValue = document.querySelector("#ConditionMinValue");
        const CalculateType = document.querySelector("#CalculateType");
        const Value = document.querySelector("#Value");
        const IsCumulative = document.querySelector("#IsCumulative");
        const MemberLevel = document.querySelector("#MemberLevel");
        const PromotionType = document.querySelector("#PromotionType");
        const CouponSerial = document.querySelector("#CouponSerial");
        const CouponInfo = document.querySelector("#CouponInfo");
        const CouponReceiveEndTime = document.querySelector("#CouponReceiveEndTime");
        const CouponUseMax = document.querySelector("#CouponUseMax");
        const EnableStatus = document.querySelector("#EnableStatus");
        const send = document.querySelector("#send");
        const infoModal = new bootstrap.Modal('#infoModal', {
            keyboard: true
        }) // 用bootstrap的 modal來裝訊息
        const info = document.querySelector("#info")

        //判斷促銷方式＝優惠券，優惠券區塊顯示     
        document.addEventListener("DOMContentLoaded", function() {

            // 定義顯示或隱藏 couponarea 區塊的函式
            const couponarea = document.querySelectorAll(".couponarea")

            function toggleCouponArea() {
                if (PromotionType.value == 2) {
                    couponarea.forEach(element => {
                        element.classList.remove('d-none'); // 移除隱藏的 class，顯示優惠券區塊
                    });
                } else {
                    couponarea.forEach(element => {
                        element.classList.add('d-none'); // 添加隱藏的 class，隱藏優惠券區塊
                    });
                }
            }

            // 初次加載時執行一次
            toggleCouponArea();

            // 當 PromotionType 改變時再執行
            PromotionType.addEventListener("change", toggleCouponArea);

            //判斷滿足條件＝訂單滿額，消費門檻顯示      
            const ConditionMinArea = document.querySelectorAll(".ConditionMinArea")

            function toggleConditionMinArea() {
                if (PromotionCondition.value == 2) {
                    ConditionMinArea.forEach(element => {
                        element.classList.remove('d-none'); // 移除隱藏的 class，顯示
                    });
                } else {
                    ConditionMinArea.forEach(element => {
                        element.classList.add('d-none'); // 添加隱藏的 class，隱藏
                    });
                }
            }
            toggleConditionMinArea()

            PromotionCondition.addEventListener("change", toggleConditionMinArea);

        });

        //產生亂數序號
        const randombtn = document.querySelector("#randombtn")

        function generateRandomSerial() {
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+[]{}|;:,.<>?';
            let serial = '';
            for (let i = 0; i < 10; i++) {
                const randomIndex = Math.floor(Math.random() * characters.length);
                serial += characters[randomIndex];
            }
            return serial;
        }
        randombtn.addEventListener("click", function() {
            const randomSerial = generateRandomSerial();
            CouponSerial.value = randomSerial;

        })





        send.addEventListener("click", function() {
            let NameVal = (Name.value !== "") ? Name.value : null;
            let StartTimeVal = (StartTime.value !== "") ? StartTime.value : null;
            let EndTimeVal = (EndTime.value !== "") ? EndTime.value : null;
            let PromotionConditionVal = (PromotionCondition.value !== "") ? PromotionCondition.value : null;
            let ConditionMinValueVal = (ConditionMinValue.value !== "") ? ConditionMinValue.value : null;
            let CalculateTypeVal = (CalculateType.value !== "") ? CalculateType.value : null;
            let ValueVal = (Value.value !== "") ? Value.value : null;
            let IsCumulativeVal = (IsCumulative.value !== "") ? IsCumulative.value : null;
            let MemberLevelVal = (MemberLevel.value !== "") ? MemberLevel.value : null;
            let PromotionTypeVal = (PromotionType.value !== "") ? PromotionType.value : null;
            let CouponSerialVal = (CouponSerial.value !== "") ? CouponSerial.value : null;
            let CouponInfoVal = (CouponInfo.value !== "") ? CouponInfo.value : null;
            let CouponReceiveEndTimeVal = (CouponReceiveEndTime.value !== "") ? CouponReceiveEndTime.value : null;
            let CouponUseMaxVal = (CouponUseMax.value !== "") ? CouponUseMax.value : null;
            let EnableStatusVal = (EnableStatus.value !== "") ? EnableStatus.value : null;

            $.ajax({
                    method: "POST",
                    url: "/G5midTerm/Promotion/doCreateDiscount.php",
                    dataType: "json",
                    data: {
                        Name: NameVal,
                        StartTime: StartTimeVal,
                        EndTime: EndTimeVal,
                        PromotionCondition: PromotionConditionVal,
                        ConditionMinValue: ConditionMinValueVal,
                        CalculateType: CalculateTypeVal,
                        Value: ValueVal,
                        IsCumulative: IsCumulativeVal,
                        MemberLevel: MemberLevelVal,
                        PromotionType: PromotionTypeVal,
                        CouponSerial: CouponSerialVal,
                        CouponInfo: CouponInfoVal,
                        CouponReceiveEndTime: CouponReceiveEndTimeVal,
                        CouponUseMax: CouponUseMaxVal,
                        EnableStatus: EnableStatusVal
                    } //如果需要
                })
                .done(function(response) {
                    let status = response.status;
                    if (status == 0) {
                        info.innerHTML = response.message;
                        infoModal.show();
                        return;
                    }
                    if (status == 1) {
                        info.innerHTML = response.message
                        infoModal.show();
                        return;
                    }


                }).fail(function(jqXHR, textStatus) {
                    console.log("Request failed: " + textStatus);
                });
        })
    </script>

</body>

</html>