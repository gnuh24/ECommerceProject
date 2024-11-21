<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../AdminHome.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="QLThuongHieu.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <!-- Include Pagination.js -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.5/pagination.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.5/pagination.css" />

    <title>Quản lý thương hiệu</title>
</head>
<style>
    .paginationjs {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }
</style>

<body>
    <div class="StaffLayout_wrapper__CegPk">
        <?php require_once "../ManagerHeader.php" ?>

        <div class="Manager_wrapper__vOYy">
            <?php require_once "../ManagerMenu.php" ?>

            <div style="padding-left: 16%; width: 100%; padding-right: 2rem">
                <div class="wrapper">
                    <div style="
                        display: flex;
                        padding-top: 1rem;
                        padding-bottom: 1rem;
                    ">
                        <h2>Thương Hiệu</h2>
                        <button style="
                            margin-left: auto;
                            font-family: Arial;
                            font-size: 1.5rem;
                            font-weight: 700;
                            color: white;
                            background-color: rgb(65, 64, 64);
                            padding: 1rem;
                            border-radius: 0.6rem;
                            cursor: pointer;
                        " id="btnAddBrand">
                            <a href="./FormCreateThuongHieu.php"> Thêm Thương Hiệu</a>
                        </button>
                    </div>
                    <br>

                    <div class="boxFeature">
                        <div style="position: relative">
                            <input style="border: 2px solid #333; height: 30px; padding: 0.5rem;" class="Admin_input__LtEE-" placeholder="Tìm kiếm thương hiệu" />
                            <button id="filter-button" style="cursor: pointer;"><i class="fa fa-search"></i></button>
                        </div>
                        <div style="margin-left: auto"></div>
                    </div>

                    <br>
                    <div class="boxTable">
                        <table class="Table_table__BWPy">
                            <thead class="Table_head__FTUog">
                                <tr>
                                    <th style="width: 25%" class="Table_th__hCkcg">Mã thương hiệu</th>
                                    <th class="Table_th__hCkcg">Thương hiệu</th>
                                    <th style="width: 15%" class="Table_th__hCkcg">Action</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody">

                            </tbody>
                        </table>
                    </div>
                    <div id="pagination-container"></div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>

<script>
    fetchDataAndUpdateTable(currentPage, '');
    var currentPage = 1;
    var pageSizeGlobal = 5;
    var search = "";
    const userRole = sessionStorage.getItem('role');

    document.addEventListener('DOMContentLoaded', () => {
        const adminButton = document.getElementById('btnAddBrand');
        if (userRole != 'Manager') {
            adminButton.style.display = 'none';
        } else {
            console.log('Phần tử adminButton không tồn tại.');
        }
    });

    function clearTable() {
        var tableBody = document.querySelector('.Table_table__BWPy tbody');
        tableBody.innerHTML = '';
    }

    function getAllThuongHieu(page, search) {
        // Lấy userRole từ sessionStorage
        const userRole = sessionStorage.getItem('role');

        $.ajax({
            url: '../../../Controllers/BrandController.php',
            type: 'GET',
            dataType: "json",
            data: {
                page: page,
                pageSize: pageSizeGlobal,
                search: search
            },
            success: function(response) {
                var data = response.data;
                var tableBody = document.getElementById("tableBody");
                var tableContent = "";

                if (data.length > 0) {
                    data.forEach(function(record, index) {
                        var trClass = (index % 2 !== 0) ? "Table_data_quyen_1" : "Table_data_quyen_2";
                        var trContent = `
                        <tr style="height: 20%; max-height: 20%;">
                            <td class="${trClass}">${record.Id}</td>
                            <td class="${trClass}">${record.BrandName}</td>
                            <td class="${trClass}">`;

                        // Logic hiển thị nút dựa trên userRole
                        if (record.Id == 1) {
                            trContent += `Mặc định`;
                        } else {
                            if (userRole === "Manager") {
                                trContent += `
                                <button class="edit" onclick="updateThuongHieu(${record.Id}, '${record.BrandName}')">Sửa</button>
                                <button class="delete" onclick="deleteThuongHieu(${record.Id}, '${record.BrandName}')">Xoá</button>`;
                            } else if (userRole === "Employee") {
                                trContent += `
                                <button class="edit" onclick="updateThuongHieu(${record.Id}, '${record.BrandName}')">Xem chi tiết</button>`;
                            }
                        }

                        trContent += `</td></tr>`;

                        // Thêm các hàng trống nếu cần
                        if (data.length < 5 && index === data.length - 1) {
                            for (var i = data.length; i < 5; i++) {
                                var emptyTrClass = (i % 2 !== 0) ? "Table_data_quyen_1" : "Table_data_quyen_2";
                                trContent += `
                                <tr style="height: 20%; max-height: 20%;">
                                    <td class="${emptyTrClass}" style="width: 130px;"></td>
                                    <td class="${emptyTrClass}"></td>
                                    <td class="${emptyTrClass}"></td>
                                </tr>`;
                            }
                        }

                        tableContent += trContent;
                    });
                } else {
                    tableContent = `<tr><td style="text-align: center;" colspan="7">Không có thương hiệu nào thỏa yêu cầu</td></tr>`;
                }

                tableBody.innerHTML = tableContent;
                setupPagination(response.totalElements, page);
            },
            error: function(xhr, status, error) {
                if (xhr.status === 401) {
                    alert('Phiên đăng nhập của bạn đã hết hạn. Vui lòng đăng nhập lại.');
                    window.location.href = '/login';
                } else {
                    console.error('Lỗi khi gọi API: ', error);
                }
            }
        });
    }

    function fetchDataAndUpdateTable(page, search) {
        clearTable();
        getAllThuongHieu(page, search);
    }

    function setupPagination(totalElements, currentPage) {
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
                    currentPage = pagination.pageNumber;
                    fetchDataAndUpdateTable(currentPage, search);
                }
            }
        });
    }

    document.getElementById('filter-button').addEventListener('click', function() {
        search = document.querySelector('.Admin_input__LtEE-').value;
        fetchDataAndUpdateTable(currentPage, search);
    });

    document.querySelector('.Admin_input__LtEE-').addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            search = this.value;
            fetchDataAndUpdateTable(currentPage, search);
        }
    });

    function deleteThuongHieu(brandId, brandName) {
        Swal.fire({
            title: `Bạn có muốn xóa ${brandName} không?`,
            text: "Hành động này sẽ không thể hoàn tác!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../../../Controllers/BrandController.php?id=' + brandId,
                    type: 'DELETE',
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công!',
                            text: 'Xóa thương hiệu thành công !!',
                        }).then(function() {
                            fetchDataAndUpdateTable(currentPage, '');
                        });
                    },
                    error: function(xhr, status, error) {
                        if (xhr.status === 401) {
                            alert('Phiên đăng nhập của bạn đã hết hạn. Vui lòng đăng nhập lại.');
                            window.location.href = '/login';
                        } else {
                            console.error('Lỗi khi gọi API: ', error);
                        }
                    }
                });
            }
        });
    }

    function updateThuongHieu(brandId, brandName) {
        window.location.href = `FormUpdateThuongHieu.php?brandId=${brandId}&brandName=${brandName}`
    }
</script>