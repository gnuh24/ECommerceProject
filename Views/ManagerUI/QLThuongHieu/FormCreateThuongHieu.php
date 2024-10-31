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
    <title>Thêm Thương Hiệu</title>
</head>

<body>
    <div class="StaffLayout_wrapper__CegPk">
        <?php require_once "../ManagerHeader.php" ?>
        <div class="Manager_wrapper__vOYy">
            <div style="padding-left: 3%; width: 100%; padding-right: 2rem">
                <div class="wrapper">
                    <form id="submit-form" method="POST">
                        <input type="hidden" name="action" value="createSupplier">
                        <div class="boxFeature">
                            <h2 style="font-size: 1.5rem">Thêm thương hiệu</h2>
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
                                                    cursor: pointer;
                                                    " href="./QLThuongHieu.php">
                                    Hủy
                                </a>
                                <button id="updateSupplier_save"
                                    style="
                                                    margin-left: 0.5rem; /* Giảm khoảng cách bên trái */
                                                    font-family: Arial;
                                                    font-size: 1rem; /* Giảm kích thước font */
                                                    font-weight: 600; /* Giảm độ đậm */
                                                    color: white;
                                                    background-color: rgb(65, 64, 64);
                                                    padding: 0.5rem 1rem; /* Giảm phần padding */
                                                    border-radius: 0.4rem; /* Giảm độ bo tròn */
                                                    cursor: pointer;">
                                    Lưu
                                </button>
                            </div>
                        </div>
                        <div class="boxTable">

                            <div style=" display: flex; padding: 0rem 1rem 0rem 1rem; justify-content: space-between;">

                                <div style="padding-left: 1rem">
                                    <p class="text">Thương hiệu</p>
                                    <input id="brandName" class="input" type="text" name="brandName" style="width: 20rem; padding: 0.5rem; height: 1rem;" />
                                    <span style="
                                                            margin-left: 1rem;
                                                            font-weight: 700;
                                                            color: rgb(150, 150, 150);
                                                            ">*</span>


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
    document.getElementById("submit-form").addEventListener('submit', function check(event) {
        event.preventDefault();

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

        let isCreateThuongHieuComplete = createThuongHieu(
            brandName.value
        );

    });

    function createThuongHieu(brandName) {
        $.ajax({
            url: '../../../Controllers/BrandController.php', // Đường dẫn đến controller
            type: 'POST',
            dataType: "json",
            headers: {
                'Content-Type': 'application/json' // Đảm bảo gửi dưới dạng JSON
            },
            data: JSON.stringify({
                BrandName: brandName // Gửi thông tin thương hiệu
            }),
            success: function(data) {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: 'Tạo thương hiệu thành công!',
                }).then(function() {
                    window.location.href = 'QLThuongHieu.php'; // Chuyển hướng đến trang quản lý thương hiệu
                });

            },
            error: function(xhr) {
                // Xử lý tất cả các status code khác
                switch (xhr.status) {
                    case 409:
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Thương hiệu đã tồn tại.',
                        });
                        break;
                    case 400:
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Dữ liệu không hợp lệ.',
                        });
                        break;
                    case 500:
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Đã xảy ra lỗi khi tạo thương hiệu: ' + xhr.responseJSON.message,
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