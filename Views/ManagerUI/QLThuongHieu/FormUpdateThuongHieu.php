<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../AdminHome.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="./QLThuongHieu.css" />

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <title>Cập nhật nhà cung cấp</title>
</head>

<body>
    <div class="StaffLayout_wrapper__CegPk">
        <?php require_once "../ManagerHeader.php" ?>

        <div class="Manager_wrapper__vOYy">
            <div style="padding-left: 3%; width: 100%; padding-right: 2rem">
                <div class="wrapper">
                    <form id="submit-form" method="post">
                        <input type="hidden" name="action" value="updateSupplier">
                        <div class="boxFeature">
                            <div>
                                <h2 style="font-size: 1.5rem">Cập nhật thông tin thương hiệu</h2>
                            </div>
                            <div>
                                <a style="
                                font-family: Arial;
                                font-size: 1rem; /* Giảm kích thước font */
                                font-weight: 600; /* Giảm độ đậm */
                                border: 1px solid rgb(140, 140, 140);
                                background-color: white;
                                color: rgb(80, 80, 80);
                                padding: 0.5rem 1rem; /* Giảm phần padding */
                                border-radius: 0.4rem; /* Giảm độ bo tròn */
                                cursor: pointer;" href="./QLThuongHieu.php">Quay lại</a>
                                <button id="updateSupplier_save" style="
                                margin-left: 0.5rem; /* Giảm khoảng cách bên trái */
                                font-family: Arial;
                                font-size: 1rem; /* Giảm kích thước font */
                                font-weight: 600; /* Giảm độ đậm */
                                color: white;
                                background-color: rgb(65, 64, 64);
                                padding: 0.5rem 1rem; /* Giảm phần padding */
                                border-radius: 0.4rem; /* Giảm độ bo tròn */
                                cursor: pointer;">Lưu</button>
                            </div>
                        </div>

                        <div class="boxTable">
                            <div style="
                            display: flex; 
                            padding: 0rem 1rem 0rem 1rem; 
                            justify-content: space-between;">
                                <div>
                                    <?php

                                    $brandId = "";
                                    $brandName = "";

                                    if (isset($_GET['brandId'])) {
                                        // Lấy các tham số được gửi từ AJAX
                                        $brandId = $_GET['brandId'];
                                        $brandName = $_GET['brandName'];
                                    }


                                    ?>

                                    <div style="display: flex; gap: 2rem">
                                        <div>
                                            <p class="text">Mã thương hiệu<span style="color: red; margin-left: 10px;">🔒</span></p>
                                            <input id="brandId" class="input" name="brandId" readonly value=<?php echo $brandId ?> />
                                        </div>
                                    </div>

                                    <p class="text">Thương hiệu</p>
                                    <input id="brandName" class="input" type="text" name="brandName" value=<?php echo $brandName ?> />';


                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </form>
    </div>
    </div>
    </div>

    </div>
</body>


<script>
    const userRole = sessionStorage.getItem('role');

    document.addEventListener('DOMContentLoaded', () => {
        const adminButton = document.getElementById('updateSupplier_save');
        if (userRole != 'Manager') {
            adminButton.style.display = 'none';
        } else {
            console.log('Phần tử adminButton không tồn tại.');
        }
    });
    document.getElementById("updateSupplier_save").addEventListener('click', function check(event) {
        event.preventDefault(); // Ngăn chặn hành động mặc định của form

        let brandId = document.getElementById("brandId");
        let brandName = document.getElementById("brandName");

        if (!brandName.value.trim()) {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: 'Tên thương hiệu không được để trống',
            });
            brandName.focus();
            event.preventDefault();
            return;
        }
        updateThuongHieu(brandId.value, brandName.value)
    })

    function updateThuongHieu(brandId, brandName) {
        // Dữ liệu gửi đi được định dạng dưới dạng JSON
        var data = JSON.stringify({
            Id: brandId,
            BrandName: brandName
        });

        $.ajax({
            url: '../../../Controllers/BrandController.php',
            type: 'PATCH',
            dataType: "json",
            headers: {
                'Content-Type': 'application/json' // Đảm bảo gửi dưới dạng JSON
            },
            data: data,
            success: function(data) {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: 'Thay đổi thương hiệu mới thành công!',
                }).then(() => {
                    window.location.href = 'QLThuongHieu.php'; // Chuyển hướng đến trang quản lý thương hiệu
                });
            },
            error: function(xhr) {
                console.error('Error: ' + xhr.status + ' - ' + xhr.responseText);

                // Xử lý các mã lỗi cụ thể
                switch (xhr.status) {
                    case 409:
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Tên thương hiệu đã tồn tại.',
                        });
                        break;
                    case 404:
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Không tìm thấy thương hiệu để cập nhật.',
                        });
                        break;
                    case 500:
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Đã xảy ra lỗi khi cập nhật thương hiệu: ' + xhr.responseJSON.message,
                        });
                        break;
                    default:
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Đã xảy ra lỗi không xác định.',
                        });
                }
            }
        });
    }
</script>

</html>