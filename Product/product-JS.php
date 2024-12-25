<!-- 上傳圖片預覽 -->
<script>
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
<!-- 刪除視窗 -->
<script>
    const delBtn = document.querySelector("#delBtn");
    const delAlert = document.querySelector("#delAlert");
    const delAlertCancel = document.querySelector("#delAlertCancel");

    delBtn.addEventListener("click", function(event) {
        event.preventDefault(); // 防止表單提交
        delAlert.classList.remove("d-none");
        delAlert.classList.add("d-flex");
    });

    delAlertCancel.addEventListener("click", function(event) {
        event.preventDefault(); // 防止鏈接的默認行為
        delAlert.classList.remove("d-flex");
        delAlert.classList.add("d-none");
    });
</script>

