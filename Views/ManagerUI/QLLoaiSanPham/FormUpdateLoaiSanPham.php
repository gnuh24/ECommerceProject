<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../AdminHome.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>C:\xampp\htdocs\ECommerceProject\Views\ManagerUI\
    <link rel="stylesheet" href="./QLLoaiSanPham.css" />
    <title>Cập nhật loại sản phẩm</title>
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
                            <h2 style="font-size: 1.5rem">Cập nhật thông tin loại sản phẩm</h2>
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
                                cursor: pointer;"
                                    href="./QLLoaiSanPham.php">Quay lại</a>
                                <button id="updateLoaiSanPham_save" style="margin-left: 1rem; 
                                margin-left: 0.5rem; /* Giảm khoảng cách bên trái */
                                font-family: Arial;
                                font-size: 1rem; /* Giảm kích thước font */
                                font-weight: 600; /* Giảm độ đậm */
                                color: white;
                                background-color: rgb(65, 64, 64);
                                padding: 0.5rem 1rem;
                                border-radius: 0.4rem; 
                                cursor: pointer;">Lưu</button>
                            </div>
                        </div>
                        <div class="boxTable">
                            <div style="display: flex; padding: 0rem 1rem 0rem 1rem; justify-content: space-between;">
                                <div>
                                    <?php

                                    $id = "";
                                    $categoryName =  "";

                                    if (isset($_GET['id']) && isset($_GET['categoryName'])) {
                                        // Lấy các tham số được gửi từ AJAX
                                        $id = $_GET['id'];
                                        $categoryName = $_GET['categoryName'];
                                    }
                                    echo '
                                        <div style="padding-left: 1rem">

                                            <div style="display: flex; gap: 2rem">
                                                <div>
                                                    <p class="text">Mã loại sản phẩm<span style="color: red; margin-left: 10px;">🔒</span></p>
                                                    <input id="Id" class="input" name="Id" readonly value="' . ($id) . '" />
                                                </div>
                                            </div>

                                            <p class="text">Loại sản phẩm</p>
                                            <input id="CategoryName" class="input" type="text" name="CategoryName" value="' . ($categoryName) . '" />

                                        </div>';

                                    ?>

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
        const adminButton = document.getElementById('updateLoaiSanPham_save');
        if (userRole != 'Manager') {
            adminButton.style.display = 'none';
        } else {
            console.log('Phần tử adminButton không tồn tại.');
        }
    });
    document.getElementById("updateLoaiSanPham_save").addEventListener('click', function check(event) {
        event.preventDefault(); // Ngăn chặn hành động mặc định của form

        let Id = document.getElementById("Id");
        let CategoryName = document.getElementById("CategoryName");

        // Kiểm tra tên loại sản phẩm không để trống
        if (!CategoryName.value.trim()) {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: 'Tên loại sản phẩm không được để trống',
            });
            CategoryName.focus();
            return;
        }

        // Bắt đầu cập nhật thông tin loại sản phẩm sau khi đã qua các bước xác nhận
        updateLoaiSanPham(Id.value, CategoryName.value);
    });

    function updateLoaiSanPham(Id, CategoryName) {
        $.ajax({
            url: '../../../Controllers/CategoryController.php',
            type: 'PATCH',
            dataType: "json",
            headers: {
                'Content-Type': 'application/json' // Đảm bảo gửi dưới dạng JSON
            },
            data: JSON.stringify({
                Id: Id,
                CategoryName: CategoryName
            }),
            success: function(data) {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: 'Cập nhật loại sản phẩm thành công!',
                }).then(function() {
                    window.location.href = 'QLLoaiSanPham.php';
                });

            },
            error: function(xhr) {
                // Xử lý tất cả các status code khác (ví dụ: 409, 404, 500)
                switch (xhr.status) {
                    case 409:
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Tên loại sản phẩm đã tồn tại',
                        });
                        break;
                    case 404:
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Không tìm thấy loại sản phẩm để cập nhật',
                        });
                        break;
                    case 500:
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Đã xảy ra lỗi khi cập nhật loại sản phẩm: ' + xhr.responseJSON.message,
                        });
                        break;
                    default:
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Đã xảy ra lỗi không xác định',
                        });
                }
            }
        });
    }
</script>


</html>