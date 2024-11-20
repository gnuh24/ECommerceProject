<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../QLTaiKhoan/UserUpdate.css" />
    <link rel="stylesheet" href="../QLTaiKhoan/oneForAll.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title>Tạo Khuyến mãi</title>
</head>

<body>
    <div id="root">
        <div>
            <div class="App">
                <div class="StaffLayout_wrapper__CegPk">
                    <?php require_once "../ManagerHeader.php" ?>

                    <div>
                        <div>
                            <div class="Manager_wrapper__vOYy">

                                <div style="padding-left: 3%; width: 100%; padding-right: 2rem">
                                    <div class="wrapper">
                                        <div style="display: flex; padding-top: 1rem; align-items: center; gap: 1rem; padding-bottom: 1rem;"></div>
                                        <form id="submit-form" method="post">
                                            <div class="boxFeature">
                                                <div>
                                                    <h2 style="font-size: 2.3rem">Tạo khuyến mãi mới</h2>
                                                </div>
                                                <div>
                                                    <a style="font-family: Arial; font-size: 1.5rem; font-weight: 700; border: 1px solid rgb(140, 140, 140); background-color: white; color: rgb(80, 80, 80); padding: 1rem 2rem 1rem 2rem; border-radius: 0.6rem; cursor: pointer;" href="QLKhuyenMai.php">Hủy</a>
                                                    <button id="updateUser_save" style="margin-left: 1rem; font-family: Arial; font-size: 1.5rem; font-weight: 700; color: white; background-color: rgb(65, 64, 64); padding: 1rem 2rem 1rem 2rem; border-radius: 0.6rem; cursor: pointer;">Lưu</button>
                                                </div>
                                            </div>
                                            <div class="boxTable">
                                                <div style="display: flex; padding: 0rem 1rem 0rem 1rem; justify-content: space-around;">
                                                    <div style="padding-left: 1rem; margin-left: 25px;">

                                                        <p class="text">Tên CODE</p>
                                                        <input id="Code" class="input" type="text" name="Code" style="width: 40rem" disabled />
                                                        <span style="margin-left: 1rem; font-weight: 700; color: rgb(150, 150, 150);">*</span>

                                                        <p class="text">Điều kiện áp dụng</p>
                                                        <input name="Condition" id="Condition" type="text" class="input" style="width: 40rem"></input>
                                                        <span style="margin-left: 1rem; font-weight: 700; color: rgb(150, 150, 150);">*</span>


                                                        <p class="text">Giảm giá</p>
                                                        <input id="SaleAmount" class="input" name="SaleAmount" style="width: 40rem" />
                                                        <span style="margin-left: 1rem; font-weight: 700; color: rgb(150, 150, 150);">*</span>

                                                        <p class="text">Trạng thái</p>
                                                        <select id="IsPublic" class="input" name="IsPublic" style="width: 40rem" disabled>
                                                            <option value="">--Chọn trạng thái--</option>
                                                            <option value="1">Mở khóa</option>
                                                            <option value="0">Khóa</option>
                                                        </select>

                                                        <p class="text">THời hạn hết hạn</p>
                                                        <input id="ExpirationTime" type="date" class="input" name="ExpirationTime" style="width: 40rem"></input>
                                                        <span style="margin-left: 1rem; font-weight: 700; color: rgb(150, 150, 150);">*</span>

                                                    </div>


                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>


<script>
    fetchVoucherDetails(<?php echo $_GET['id'] ?>);

    function fetchVoucherDetails(VoucherId) {
        $.ajax({
            url: "../../../Controllers/VoucherController.php",
            type: 'GET',
            dataType: 'json',
            data: {
                Id: VoucherId,
            },

            success: function(data) {
                // Điền dữ liệu vào form
                $('#Code').val(data.data.Code);
                $('#Condition').val(data.data.Condition);
                $('#SaleAmount').val(data.data.SaleAmount); // Sử dụng ID của thương hiệu
                $('#IsPublic').val(data.data.IsPublic); // Sử dụng ID của loại sản phẩm
                $('#ExpirationTime').val(data.data.ExpirationTime.split(' ')[0]);


                // Cập nhật hình ảnh
                $('#xuatAnh').attr('src', 'http://res.cloudinary.com/djhoea2bo/image/upload/v1711511636/' + data.data.image);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }
    document.getElementById("submit-form").addEventListener('submit', function check(event) {
        event.preventDefault(); // Ngăn chặn hành động mặc định của form

        let Code = document.getElementById("Code");
        let Condition = document.getElementById("Condition");
        let SaleAmount = document.getElementById("SaleAmount");
        let IsPublic = document.getElementById("IsPublic");
        let ExpirationTime = document.getElementById("ExpirationTime");

        // Kiểm tra trường không được để trống
        if (!Code.value.trim()) {
            showErrorAlert('Lỗi!', 'Tên CODE không được để trống và không được có dấu cách');
            Code.focus();
            return;
        }
        // Kiểm tra không có dấu cách
        if (/\s/.test(Code.value)) {
            showErrorAlert('Lỗi!', 'Tên CODE không được có dấu cách');
            Code.focus();
            return;
        }
        // Kiểm tra Condition phải là số nguyên dương
        if (!Condition.value.trim() || !/^\d+$/.test(Condition.value) || parseInt(Condition.value) <= 0) {
            showErrorAlert('Lỗi!', 'Điều kiện áp dụng phải là số nguyên dương');
            Condition.focus();
            return;
        }

        // Kiểm tra SaleAmount phải là số nguyên dương
        if (!SaleAmount.value.trim() || !/^\d+$/.test(SaleAmount.value) || parseInt(SaleAmount.value) <= 0) {
            showErrorAlert('Lỗi!', 'Giảm giá phải là số nguyên dương');
            SaleAmount.focus();
            return;
        }
        // Kiểm tra trạng thái không được để trống
        if (!IsPublic.value.trim()) {
            showErrorAlert('Lỗi!', 'Trạng thái không được để trống');
            IsPublic.focus();
            return;
        }

        // Kiểm tra ExpirationTime
        const expirationDate = new Date(ExpirationTime.value);
        const today = new Date();

        if (!ExpirationTime.value) {
            showErrorAlert('Lỗi!', 'Thời hạn hết hạn không được để trống');
            ExpirationTime.focus();
            return;
        }

        if (expirationDate <= today) {
            showErrorAlert('Lỗi!', 'Thời hạn hết hạn phải là ngày tương lai');
            ExpirationTime.focus();
            return;
        }

        updateKhuyenMai(
            <?php echo $_GET['id'] ?>, Code.value,
            Condition.value,
            SaleAmount.value,
            IsPublic.value,
            ExpirationTime.value
        );
        //Sau khi tạo xong chuyển về trang QLKhuyenMai
        showSuccessAlert('Thành công!', 'Tạo Khuyến mãi mới thành công !!', 'QLKhuyenMai.php');
    });





    function showErrorAlert(title, message) {
        Swal.fire({
            title: title,
            text: message,
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }

    function showSuccessAlert(title, message, redirectUrl) {
        Swal.fire({
            title: title,
            text: message,
            icon: 'success',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = redirectUrl;
            }
        });
    }





    function updateKhuyenMai(id, Code, maCondition, SaleAmount, IsPublic, ExpirationTime) {
        var dataToSend = {
            id: id,
            Code: Code,
            maCondition: (maCondition),
            SaleAmount: SaleAmount,
            IsPublic: Number(IsPublic),
            ExpirationTime: ExpirationTime,
            action: "update"
        };
        $.ajax({
            url: '../../../Controllers/VoucherController.php', // Kiểm tra URL chính xác
            type: 'PATCH',
            contentType: 'application/json', // Thiết lập kiểu nội dung là JSON
            data: JSON.stringify(dataToSend), // Chuyển đổi đối tượng thành chuỗi JSON

            success: function(data) {
                console.log(data); // Log dữ liệu trả về để kiểm tra
                if (data) {
                    console.log("Khuyến mãi được sửa thành công!");
                } else {
                    console.log("Đã xảy ra lỗi khi sửa Khuyến mãi!");
                }
            },
            error: function(xhr, status, error) {
                console.error('Error: ' + xhr.status + ' - ' + error);
                console.log(xhr.responseText); // Kiểm tra phản hồi lỗi từ máy chủ
            }
        });
    }
</script>

</html>