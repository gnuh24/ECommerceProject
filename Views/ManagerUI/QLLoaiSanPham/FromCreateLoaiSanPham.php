<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../AdminDemo.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../QLLoaiSanPham/QLLoaiSanPham.css" />

    <title>Thêm Loại Sản Phẩm</title>
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
                                        <div style="
                          display: flex;
                          padding-top: 1rem;
                          align-items: center;
                          gap: 1rem;
                          padding-bottom: 1rem;
                        "></div>
                                        <form id="submit-form" method="POST">
                                            <input type="hidden" name="action" value="createLoaiSanPham">
                                            <div class="boxFeature">
                                                <div>
                                                    <h2 style="font-size: 2.3rem">Thêm loại sản phẩm</h2>

                                                </div>
                                                <div>
                                                    <a style="
                                                    font-family: Arial;
                                                    font-size: 1.5rem;
                                                    font-weight: 700;
                                                    border: 1px solid rgb(140, 140, 140);
                                                    background-color: white;
                                                    color: rgb(80, 80, 80);
                                                    padding: 1rem 2rem 1rem 2rem;
                                                    border-radius: 0.6rem;
                                                    cursor: pointer;
                                                    " href="./QLLoaiSanPham.php">
                                                        Hủy
                                                    </a>
                                                    <button id="updateLoaiSanPham_save" style="margin-left: 1rem; font-family: Arial; font-size: 1.5rem; font-weight: 700; color: white;  background-color: rgb(65, 64, 64); padding: 1rem 2rem 1rem 2rem; border-radius: 0.6rem; cursor: pointer;">
                                                        Lưu
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="boxTable">

                                                <div style=" display: flex; padding: 0rem 1rem 0rem 1rem; justify-content: space-between;">
                                                    <div>

                                                        <div style="padding-left: 1rem">
                                                            <p class="text">Loại sản phẩm</p>
                                                            <input id="categoryName" class="input" type="text" name="categoryName" style="width: 40rem" />
                                                            <span style="
                                                            margin-left: 1rem;
                                                            font-weight: 700;
                                                            color: rgb(150, 150, 150);
                                                            ">*</span>
                                                        </div>

                                                    </div>
                                                    <div>


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
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<script>
    document.getElementById("submit-form").addEventListener('submit', function check(event) {
        event.preventDefault(); // Ngăn chặn hành động mặc định của form


        let categoryName = document.getElementById("categoryName");

        if (!categoryName.value.trim()) {

            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: 'Tên loại sản phẩm không được để trống',
            });
            categoryName.focus();
            event.preventDefault();
            return;
        }

        //Tạo thông tin nhà cung cấp
        let isCreateLoaiSanPhamComplete = createLoaiSanPham(
            categoryName.value
        );


    });

    function createLoaiSanPham(CategoryName) {



        $.ajax({
            url: '../../../Controllers/CategoryController.php',
            type: 'POST', // POST để tạo mới
            dataType: "json",
            headers: {
                'Content-Type': 'application/json' // Gửi dữ liệu dưới dạng JSON
            },
            data: JSON.stringify({
                CategoryName: CategoryName // Chuẩn bị dữ liệu JSON
            }),
            success: function(data) {
                console.log("Response from server:", data); // Thêm log để kiểm tra phản hồi
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: 'Thêm loại sản phẩm thành công!',
                }).then(function() {
                    window.location.href = 'QLLoaiSanPham.php';
                });
            },
            error: function(xhr) {
                console.error("Error response:", xhr); // Thêm log lỗi để kiểm tra
                // Xử lý tất cả các status code khác (ví dụ: 409, 500)
                switch (xhr.status) {
                    case 409:
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Tên loại sản phẩm đã tồn tại',
                        });
                        break;
                    case 500:
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: 'Đã xảy ra lỗi khi thêm loại sản phẩm: ' + xhr.responseJSON.message,
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