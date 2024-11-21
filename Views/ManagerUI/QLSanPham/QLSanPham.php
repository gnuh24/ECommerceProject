<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Chỉ tải một lần jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <!-- Cấu hình meta -->
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../AdminHome.css" />
    <link rel="stylesheet" href="QLSanPham.css" />

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Pagination.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.5/pagination.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.5/pagination.min.js"></script>

    <title>Quản lý sản phẩm</title>
</head>

<style>
    .paginationjs {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }
</style>

<body>

    <!-- Modal chỉnh sửa sản phẩm -->
    <div id="editProductModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Chỉnh sửa sản phẩm</h2>


        </div>
    </div>

    <div class="App">
        <div class="StaffLayout_wrapper__CegPk">
            <?php require_once "../ManagerHeader.php" ?>

            <div>
                <div>
                    <div class="Manager_wrapper__vOYy">
                        <?php require_once "../ManagerMenu.php" ?>

                        <div class="wrapper">
                            <div style="">
                                <h2>Quản lý sản Phẩm</h2>
                                <button id="createProductBtn" onclick="toCreateForm()">Tạo Sản Phẩm</button>
                            </div>
                            <!-- Thanh lọc menu -->
                            <div id="filter-menu">
                                <input type="text" placeholder="Tìm kiếm theo tên sản phẩm" id="searchSanPham" name="searchSanPham">
                                <button id="filter-button"><i class="fa-solid fa-magnifying-glass"></i></button>


                                <label for="state-filter">Trạng thái:</label>
                                <select id="state-filter">
                                    <option value="all">Tất cả</option>
                                    <option value="true">Kinh doanh</option>
                                    <option value="false">Ngừng kinh doanh</option>

                                </select>

                                <label for="category-filter">Loại sản phẩm:</label>
                                <select id="category-filter">
                                    <!-- Hiển thị menu LoaiSanPham -->
                                </select>


                                <label for="brand-filter">Thương hiệu:</label>
                                <select id="brand-filter">
                                </select>

                                <button id="reset-button"><i class="fa-solid fa-rotate-right"></i></button>
                            </div>

                            <div>
                                <div class="boxTable" style="width: 100%;">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th style="width: 5%; text-align: center;">ID</th>
                                                <th style="width: 10%; text-align: center;">Minh họa</th>
                                                <th style="width: 25%;">Tên Sản Phẩm</th>
                                                <th style="width: 10%; text-align: center;">Giá Tiền</th>
                                                <th style="width: 10%; text-align: center;">Trạng thái</th>
                                                <th style="width: 10%;text-align: center;">Loại sản phẩm</th>
                                                <th style="width: 10%;text-align: center;">Thương hiệu</th>

                                                <th style="width: 5%; text-align: center;">Số Lượng</th>
                                                <th style="width: 10%; text-align: center;">Thao Tác</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableBody"></tbody>
                                    </table>
                                    <div id="pagination-container">

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
    var currentPage = 1; // Track the current page
    var pageSizeGlobal = 5;
    const userRole = sessionStorage.getItem('role');

    document.addEventListener('DOMContentLoaded', () => {
        const adminButton = document.getElementById('createProductBtn');
        if (userRole != 'Manager') {
            adminButton.style.display = 'none';
        } else {
            console.log('Phần tử adminButton không tồn tại.');
        }
    });

    function toCreateForm() {
        window.location.href = "FormCreateSanPham.php";
    }

    function getAllSanPham(page, search, trangThai, maLoaiSanPham, brandId) {
        let data = {
            page: page,
            pageSize: pageSizeGlobal,
            search: search,
            action: 'getAllProductsAdmin'
        };

        if (trangThai !== 'all') {
            data.status = trangThai;
        }

        if (maLoaiSanPham !== 0) {
            data.categoryId = maLoaiSanPham;
        }

        if (brandId !== 0) {
            data.brandId = brandId;
        }

        // Lấy userRole từ sessionStorage
        const userRole = sessionStorage.getItem('role');

        // Gọi API để lấy dữ liệu sản phẩm
        $.ajax({
            url: "../../../Controllers/ProductController.php",
            method: "GET",
            dataType: "json",
            data: data,
            success: function(response) {
                var tableBody = document.getElementById("tableBody");
                var tableContent = "";

                if (response.content.length > 0) {
                    response.content.forEach(function(record) {
                        var trangThai = record.status ? "Kinh doanh" : "Ngừng kinh doanh";
                        var buttonText = record.status ? "Khóa" : "Mở khóa";
                        var buttonClass = record.status ? "block" : "unlock";
                        var buttonData = record.status ? "block" : "unlock";

                        // Nút sửa hoặc xem chi tiết dựa vào userRole
                        let actionButton = "";
                        if (userRole === "Manager") {
                            actionButton = `<button class="edit" onclick="toUpdate(${record.id})">Sửa</button>`;
                        } else if (userRole === "Employee") {
                            actionButton = `<button class="edit" onclick="toUpdate(${record.id})">Xem chi tiết</button>`;
                        }

                        // Nút khóa/mở khóa chỉ hiển thị nếu userRole là Manager
                        let lockUnlockButton = "";
                        if (userRole === "Manager") {
                            lockUnlockButton = `<button class="${buttonClass}" data-action="${buttonData}" 
                            onclick="handleLockUnlock(${record.id}, ${record.status})">${buttonText}</button>`;
                        }

                        var trContent = `
                        <tr>
                            <td style="text-align: center;">${record.id}</td>
                            <td><img style="width: 100%;height:auto;" src="../../img/${record.image}" alt="Product Image"></td>
                            <td>${record.productName}</td>
                            <td style="text-align: center;">${record.price}</td>
                            <td style="text-align: center;">${trangThai}</td>
                            <td style="text-align: center;">${record.category.categoryName}</td>
                            <td style="text-align: center;">${record.brand.brandName}</td>
                            <td style="text-align: center;">${record.quantity}</td>
                            <td>
                                ${actionButton}
                                ${lockUnlockButton}
                            </td>
                        </tr>`;
                        tableContent += trContent;
                    });
                } else {
                    tableContent = `<tr><td style="text-align: center;" colspan="10">Không có sản phẩm nào thỏa yêu cầu</td></tr>`;
                }

                tableBody.innerHTML = tableContent;
                setupPagination(response.totalElements, page);
            },

            error: function(xhr, status, error) {
                console.error('Lỗi khi gọi API: ', error);
            }
        });
    }


    // Hàm xử lý sự kiện cho nút khóa / mở khóa
    function handleLockUnlock(maSanPham, trangThai) {
        var newTrangThai = trangThai === 0 ? true : false; // Đảo ngược trạng thái
        // Hiển thị hộp thoại xác nhận bằng SweetAlert2
        Swal.fire({
            title: `Bạn có muốn ${newTrangThai ===  false ? 'khóa' : 'mở khóa'} sản phẩm ${maSanPham} không?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                var dataToSend = {
                    action: "update",
                    id: maSanPham,
                    status: newTrangThai
                };
                // Gọi hàm updateTaiKhoan bằng Ajax
                $.ajax({
                    url: "../../../Controllers/ProductController.php",
                    type: 'PATCH',
                    contentType: 'application/json', // Thiết lập kiểu nội dung là JSON
                    data: JSON.stringify(dataToSend),
                    success: function(response) {
                        // Nếu cập nhật thành công, reload bảng
                        var alertContent = newTrangThai === 0 ? "khóa" : "mở khóa";
                        Swal.fire('Thành công!', `Bạn đã ${alertContent} thành công !!`, 'success');
                        filterProducts(currentPage);
                    },
                    error: function(xhr, status, error) {
                        console.error('Lỗi khi gọi API: ', error);
                    }
                });
            }
        });
    }

    var currentPage = 1;
    document.addEventListener('DOMContentLoaded', function() {
        filterProducts(currentPage);
        getCategories();
        getBrand()
    });


    // Lắng nghe sự kiện click vào id "reset-button"
    document.getElementById("reset-button").addEventListener("click", function() {
        // Reset tất cả các thanh lọc về giá trị mặc định
        document.getElementById("searchSanPham").value = "";
        document.getElementById("state-filter").value = "";
        document.getElementById("category-filter").value = "";
        document.getElementById("brand-filter").value = "";

        currentPage = 1;

        getAllSanPham(currentPage, "", "", 0, 0);

    });

    function formatCurrency(number) {
        // Chuyển đổi số thành chuỗi và đảm bảo nó là số nguyên
        number = parseInt(number);

        // Sử dụng hàm toLocaleString() để định dạng số tiền
        // và thêm đơn vị tiền tệ "đ" vào cuối chuỗi
        return number.toLocaleString('vi-VN', {
            style: 'currency',
            currency: 'VND'
        });
    }



    // Lắng nghe sự kiện change cho thanh lọc loại sản phẩm
    document.getElementById("category-filter").addEventListener("change", function() {
        currnetPage = 1;
        // Gọi lại hàm lọc sản phẩm khi giá trị thay đổi
        filterProducts(currentPage);
    });

    // Lắng nghe sự kiện change cho thanh lọc trạng thái
    document.getElementById("state-filter").addEventListener("change", function() {
        currentPage = 1;

        // Gọi lại hàm lọc sản phẩm khi giá trị thay đổi
        filterProducts(currentPage);
    });

    // Lắng nghe sự kiện change cho thanh lọc thương hiệu
    document.getElementById("brand-filter").addEventListener("change", function() {
        currentPage = 1;

        // Gọi lại hàm lọc sản phẩm khi giá trị thay đổi
        filterProducts(currentPage);
    });



    // Hàm lọc sản phẩm
    function filterProducts(page) {
        // Lấy giá trị từ thanh tìm kiếm
        var searchText = document.getElementById("searchSanPham").value;

        // Lấy giá trị từ thanh lọc thể tích
        var stateFilter = document.getElementById("state-filter").value;


        // Lấy giá trị từ thanh lọc loại sản phẩm
        var categoryFilter = document.getElementById("category-filter").value;
        if (categoryFilter == "") {
            categoryFilter = 0;
        }

        var brandFilter = document.getElementById("brand-filter").value;
        if (brandFilter == "") {
            brandFilter = 0;
        }

        // Gọi hàm lọc sản phẩm với các tham số vừa lấy được
        getAllSanPham(page, searchText, stateFilter, categoryFilter, brandFilter);

    }

    // Lắng nghe sự kiện click vào nút search
    document.getElementById("filter-button").addEventListener("click", function(event) {
        currentPage = 1;
        event.preventDefault();

        // Lấy giá trị từ thanh tìm kiếm
        var searchText = document.getElementById("searchSanPham").value;




        // Lấy giá trị từ thanh lọc thể tích
        var stateFilter = document.getElementById("state-filter").value;


        // Lấy giá trị từ thanh lọc loại sản phẩm
        var categoryFilter = document.getElementById("category-filter").value;
        if (categoryFilter == "") {
            categoryFilter = 0;
        }

        var brandFilter = document.getElementById("brand-filter").value;
        if (brandFilter == "") {
            brandFilter = 0;
        }


        getAllSanPham(currentPage, searchText, stateFilter, categoryFilter, brandFilter);

    });

    function setupPagination(totalElements, currentPage) {

        //Kiểm tra xem nếu totalPage ít hơn 1 thì ẩn luôn =))
        const totalPage = Math.ceil(totalElements / pageSizeGlobal);
        totalPage <= 1 ? $('#pagination-container').hide() : $('#pagination-container').show();

        $('#pagination-container').pagination({
            dataSource: Array.from({
                length: totalElements
            }, (_, i) => i + 1),

            pageSize: pageSizeGlobal,
            showPrevious: true,
            showNext: true,
            pageNumber: currentPage,

            callback: function(data, pagination) {
                if (pagination.pageNumber !== currentPage) {
                    currentPage = pagination.pageNumber; // Update current page
                    filterProducts(currentPage); // Fetch new data for the selected page
                }
            }
        });
    }


    function toUpdate(maSanPham) {
        window.location.href = `FormUpdateSanPham.php?maSanPham=${maSanPham}`;
    }

    function getCategories() {
        $.ajax({
            // no paging
            url: "../../../Controllers/CategoryController.php",
            method: "GET",
            dataType: "json",
            success: function(response) {
                var categoryFilter = $('#category-filter');
                var htmlContent = '';

                // Duyệt qua danh sách loại sản phẩm và tạo option cho select
                $.each(response.data, function(index, category) {
                    htmlContent += `<option value="${category.Id}">${category.CategoryName}</option>`;
                });

                // Thêm tùy chọn "Tất cả"
                htmlContent = '<option value="">Tất cả</option>' + htmlContent;

                // Thiết lập nội dung HTML cho select
                categoryFilter.html(htmlContent);
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });
    }

    function getBrand() {
        $.ajax({
            //no pag
            url: "../../../Controllers/BrandController.php",
            method: "GET",
            dataType: "json",
            success: function(response) {
                var brandFilter = $('#brand-filter'); // Thay đổi tên phần tử nếu cần
                var htmlContent = '';

                // Duyệt qua danh sách thương hiệu và tạo option cho select
                $.each(response.data, function(index, brand) {
                    htmlContent += `<option value="${brand.Id}">${brand.BrandName}</option>`; // Sử dụng 'brandName'
                });

                // Thêm tùy chọn "Tất cả"
                htmlContent = '<option value="">Tất cả</option>' + htmlContent;

                // Thiết lập nội dung HTML cho select
                brandFilter.html(htmlContent);
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });
    }
</script>



</html>