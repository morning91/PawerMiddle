<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>sign-up</title>
    <link rel="shortcut icon" href="data:image/svg+xml,%3csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%2033%2034'%20fill-rule='evenodd'%20stroke-linejoin='round'%20stroke-miterlimit='2'%20xmlns:v='https://vecta.io/nano'%3e%3cpath%20d='M3%2027.472c0%204.409%206.18%205.552%2013.5%205.552%207.281%200%2013.5-1.103%2013.5-5.513s-6.179-5.552-13.5-5.552c-7.281%200-13.5%201.103-13.5%205.513z'%20fill='%23435ebe'%20fill-rule='nonzero'/%3e%3ccircle%20cx='16.5'%20cy='8.8'%20r='8.8'%20fill='%2341bbdd'/%3e%3c/svg%3e" type="image/x-icon">
    <link rel="shortcut icon" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACEAAAAiCAYAAADRcLDBAAAEs2lUWHRYTUw6Y29tLmFkb2JlLnhtcAAAAAAAPD94cGFja2V0IGJlZ2luPSLvu78iIGlkPSJXNU0wTXBDZWhpSHpyZVN6TlRjemtjOWQiPz4KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyIgeDp4bXB0az0iWE1QIENvcmUgNS41LjAiPgogPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4KICA8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0iIgogICAgeG1sbnM6ZXhpZj0iaHR0cDovL25zLmFkb2JlLmNvbS9leGlmLzEuMC8iCiAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgIHhtbG5zOnBob3Rvc2hvcD0iaHR0cDovL25zLmFkb2JlLmNvbS9waG90b3Nob3AvMS4wLyIKICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIgogICAgeG1sbnM6c3RFdnQ9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZUV2ZW50IyIKICAgZXhpZjpQaXhlbFhEaW1lbnNpb249IjMzIgogICBleGlmOlBpeGVsWURpbWVuc2lvbj0iMzQiCiAgIGV4aWY6Q29sb3JTcGFjZT0iMSIKICAgdGlmZjpJbWFnZVdpZHRoPSIzMyIKICAgdGlmZjpJbWFnZUxlbmd0aD0iMzQiCiAgIHRpZmY6UmVzb2x1dGlvblVuaXQ9IjIiCiAgIHRpZmY6WFJlc29sdXRpb249Ijk2LjAiCiAgIHRpZmY6WVJlc29sdXRpb249Ijk2LjAiCiAgIHBob3Rvc2hvcDpDb2xvck1vZGU9IjMiCiAgIHBob3Rvc2hvcDpJQ0NQcm9maWxlPSJzUkdCIElFQzYxOTY2LTIuMSIKICAgeG1wOk1vZGlmeURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiCiAgIHhtcDpNZXRhZGF0YURhdGU9IjIwMjItMDMtMzFUMTA6NTA6MjMrMDI6MDAiPgogICA8eG1wTU06SGlzdG9yeT4KICAgIDxyZGY6U2VxPgogICAgIDxyZGY6bGkKICAgICAgc3RFdnQ6YWN0aW9uPSJwcm9kdWNlZCIKICAgICAgc3RFdnQ6c29mdHdhcmVBZ2VudD0iQWZmaW5pdHkgRGVzaWduZXIgMS4xMC4xIgogICAgICBzdEV2dDp3aGVuPSIyMDIyLTAzLTMxVDEwOjUwOjIzKzAyOjAwIi8+CiAgICA8L3JkZjpTZXE+CiAgIDwveG1wTU06SGlzdG9yeT4KICA8L3JkZjpEZXNjcmlwdGlvbj4KIDwvcmRmOlJERj4KPC94OnhtcG1ldGE+Cjw/eHBhY2tldCBlbmQ9InIiPz5V57uAAAABgmlDQ1BzUkdCIElFQzYxOTY2LTIuMQAAKJF1kc8rRFEUxz9maORHo1hYKC9hISNGTWwsRn4VFmOUX5uZZ36oeTOv954kW2WrKLHxa8FfwFZZK0WkZClrYoOe87ypmWTO7dzzud97z+nec8ETzaiaWd4NWtYyIiNhZWZ2TvE946WZSjqoj6mmPjE1HKWkfdxR5sSbgFOr9Ll/rXoxYapQVik8oOqGJTwqPL5i6Q5vCzeo6dii8KlwpyEXFL519LjLLw6nXP5y2IhGBsFTJ6ykijhexGra0ITl5bRqmWU1fx/nJTWJ7PSUxBbxJkwijBBGYYwhBgnRQ7/MIQIE6ZIVJfK7f/MnyUmuKrPOKgZLpEhj0SnqslRPSEyKnpCRYdXp/9++msneoFu9JgwVT7b91ga+LfjetO3PQ9v+PgLvI1xkC/m5A+h7F32zoLXug38dzi4LWnwHzjeg8UGPGbFfySvuSSbh9QRqZ6H+Gqrm3Z7l9zm+h+iafNUV7O5Bu5z3L/wAdthn7QIme0YAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAJTSURBVFiF7Zi9axRBGIefEw2IdxFBRQsLWUTBaywSK4ubdSGVIY1Y6HZql8ZKCGIqwX/AYLmCgVQKfiDn7jZeEQMWfsSAHAiKqPiB5mIgELWYOW5vzc3O7niHhT/YZvY37/swM/vOzJbIqVq9uQ04CYwCI8AhYAlYAB4Dc7HnrOSJWcoJcBS4ARzQ2F4BZ2LPmTeNuykHwEWgkQGAet9QfiMZjUSt3hwD7psGTWgs9pwH1hC1enMYeA7sKwDxBqjGnvNdZzKZjqmCAKh+U1kmEwi3IEBbIsugnY5avTkEtIAtFhBrQCX2nLVehqyRqFoCAAwBh3WGLAhbgCRIYYinwLolwLqKUwwi9pxV4KUlxKKKUwxC6ZElRCPLYAJxGfhSEOCz6m8HEXvOB2CyIMSk6m8HoXQTmMkJcA2YNTHm3congOvATo3tE3A29pxbpnFzQSiQPcB55IFmFNgFfEQeahaAGZMpsIJIAZWAHcDX2HN+2cT6r39GxmvC9aPNwH5gO1BOPFuBVWAZue0vA9+A12EgjPadnhCuH1WAE8ivYAQ4ohKaagV4gvxi5oG7YSA2vApsCOH60WngKrA3R9IsvQUuhIGY00K4flQG7gHH/mLytB4C42EgfrQb0mV7us8AAMeBS8mGNMR4nwHamtBB7B4QRNdaS0M8GxDEog7iyoAguvJ0QYSBuAOcAt71Kfl7wA8DcTvZ2KtOlJEr+ByyQtqqhTyHTIeB+ONeqi3brh+VgIN0fohUgWGggizZFTplu12yW8iy/YLOGWMpDMTPXnl+Az9vj2HERYqPAAAAAElFTkSuQmCC" type="image/png">
    <link rel="stylesheet" crossorigin href="../assets/compiled/css/app.css">
    <link rel="stylesheet" crossorigin href="../assets/compiled/css/app-dark.css">
    <link rel="stylesheet" crossorigin href="../assets/compiled/css/auth.css">
    <?php include("../headlink.php"); ?>
</head>

<body>

    <?php include("../Member/modals.php"); ?>
    <script src="../assets/static/js/initTheme.js"></script>
    <div id="auth">

        <div class="row h-100">
            <div class="col-lg-5 col-12">
                <div id="auth-left">
                    <!-- <div class="auth-logo">
                        <h1>MFEE57 - G5</h1>
                    </div> -->
                    <h1 class="auth-title">Sign Up</h1>

                    <form class="form" id="createForm" method="post">
                        <div class="form-group position-relative has-icon-left mb-4">
                            <label for="first-name-vertical">會員姓名 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" id="name" value="">
                        </div>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <label for="first-name-vertical">會員帳號 <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="account" id="account" value="">
                        </div>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <label for="password-vertical">會員密碼 <span class="text-danger">*</span></label>
                            <input type="text" id="password" class="form-control" name="password" placeholder="Password" value="">
                        </div>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <label for="password-vertical">重新輸入密碼 <span class="text-danger">*</span></label>
                            <input type="text" id="repassword" class="form-control" name="repassword" id="repassword" placeholder="rePassword" value="">
                        </div>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <label for="first-name-vertical">會員暱稱</label>
                            <input type="text" id="nickname" class="form-control" name="nickname" placeholder="" value="">
                        </div>

                        <!-- <div class="form-group position-relative has-icon-left mb-4">
                            <label for="email-id-vertical">會員等級</label>
                            <select class="form-select" id="level" name="level">
                                <option>銅</option>
                                <option>銀</option>
                                <option>金</option>
                            </select>
                        </div> -->

                        <div class="form-group position-relative has-icon-left mb-4">
                            <label for="contact-info-vertical">電子信箱</label>
                            <input type="email" id="email" class="form-control" name="email" placeholder="" value="">
                        </div>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <label for="password-vertical">手機號碼 <span class="text-danger">*</span></label>
                            <input type="tel" id="phone" class="form-control" name="phone" placeholder="" value="">
                        </div>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <label for="first-name-vertical">聯絡電話</label>
                            <input type="tel" id="tel" class="form-control" name="tel" placeholder="" value="">
                        </div>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <label for="email-id-vertical">聯絡地址</label>
                            <input type="text" id="address" class="form-control" name="address" placeholder="" value="">
                        </div>

                        <div class="form-group position-relative has-icon-left mb-4">
                            <label for="contact-info-vertical">出生日期</label>
                            <input type="text" class="form-control mb-3 flatpickr-no-config flatpickr-input active" id="birth" placeholder="Select date.." name="birth" readonly="readonly" value="">
                        </div>


                        <div class="form-group position-relative has-icon-left mb-4">
                            <label for="password-vertical">性別</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="男">男</option>
                                <option value="女">女</option>
                                <option value="其他">其他</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow-lg mt-5">註冊</button>
                    </form>
                    <div class="text-center mt-5 text-lg fs-4">
                        <p class='text-gray-600'>Already have an account? <a href="loginPage.php" class="font-bold">Log
                                in</a>.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 d-none d-lg-block">
                <div id="auth-right">
                    <!-- <img class="object-fit-cover" src="../assets/images/2AE14CDD-1265-470C-9B15F49024186C10_source.webp" alt=""> -->
                </div>
            </div>
        </div>

    </div>
    <?php include("../js.php") ?>
    <script>
        const createModal = new bootstrap.Modal('#createModal', {
            keyboard: true
        }) // 用bootstrap的 modal來裝訊息
        const info = document.querySelector("#modalBody .info");
        const form = document.querySelector("#createForm");

        form.addEventListener("submit", function(event) {
            event.preventDefault(); // 阻止表单的默认提交行为

            const formData = new FormData(form);

            $.ajax({
                    method: "POST",
                    url: "../Member/doCreateMember.php",
                    data: formData,
                    dataType: "json",
                    processData: false,
                    contentType: false
                })
                .done(function(response) {
                    let status = response.status;
                    if (status == 0 || status == 1) {
                        info.textContent = response.message;
                        createModal.show();
                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                });
        });
    </script>

</body>


</html>