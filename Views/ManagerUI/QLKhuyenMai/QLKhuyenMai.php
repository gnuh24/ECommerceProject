<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../AdminHome.css" />
    <link rel="stylesheet" href="./QLDonHang.css" />

    <!-- Include Pagination.js -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.5/pagination.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.5/pagination.min.js"></script>
    <link rel="stylesheet" href="../../MemberUI/components/paginationjs.css" />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../HelperUI/formatOutput.js"></script>

    <title>Quản lý đơn hàng</title>
</head>

<body>
    <div class="StaffLayout_wrapper__CegPk">
        <?php require_once "../ManagerHeader.php" ?>
        <div class="Manager_wrapper__vOYy">
            <?php require_once "../ManagerMenu.php" ?>
            <div style="padding-left: 16%; width: 100%; padding-right: 2rem">
                <div class="wrapper">
                    <div class="Admin_rightBar__RXnS9">
                        <div style=" display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 1rem;
  padding-bottom: 1rem;">
                            <h2>Quản lý khuyến mãi</h2>
                            <button id="createProductBtn" onclick="toCreateForm()" style="  font-family: Arial;
  font-size: 1.5rem;
  font-weight: 700;
  color: white;
  background-color: rgba(0, 0, 0, 0.5);
  padding: 1rem;
  border-radius: 0.6rem;
  cursor: pointer;">Tạo Khuyến mãi</button>
                        </div>
                        <div class="Admin_boxFeature__ECXnm">
                            <label for=""> Lọc khuyến mãi:</label>
                            <div style="position: relative">
                                <input class="Admin_input__LtEE-" type="date" id="dateStart" />
                            </div>
                            <label for=""> đến </label>
                            <div style="position: relative">
                                <input class="Admin_input__LtEE-" type="date" id="dateEnd" />
                            </div>
                            <div style="position: relative">
                                <select style="height: 3rem; padding: 0.3rem;" class="Admin_input__LtEE-" id="TrangThai">
                                    <option value="">Trạng thái : Tất Cả</option>
                                    <option value="1">Được áp dụng</option>
                                    <option value="0">Không được áp dụng</option>

                                </select>
                            </div>
                        </div>
                        <div class="Admin_boxTable__hLXRJ">
                            <table class="Table_table__BWPy">
                                <thead class="Table_head__FTUog">
                                    <tr>
                                        <th class="Table_th__hCkcg" style="width: 17.5%;">Mã khuyến mãi</th>
                                        <th class="Table_th__hCkcg" style="width: 15%;">Ngày tạo</th>
                                        <th class="Table_th__hCkcg" style="width: 17.5%;">Tên khuyến mãi</th>
                                        <th class="Table_th__hCkcg" style="width: 15%;">Ngày hết hạn</th>
                                        <th class="Table_th__hCkcg" style="width: 15%;">Giảm giá</th>
                                        <th class="Table_th__hCkcg" style="width: 10%;">Trạng thái</th>
                                        <th class="Table_th__hCkcg" style="width: 20%;">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                </tbody>
                            </table>
                            <div id="pagination-container"></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

<script>
    var pageSizeGlobal = 5;
    var currentPage = 1;
    var filter_minOrderTime = null;
    var filter_maxOrderTime = null;
    var filter_status = null;
    const userRole = sessionStorage.getItem('role');

    document.addEventListener('DOMContentLoaded', () => {
        const adminButton = document.getElementById('createProductBtn');
        if (userRole != 'Manager') {
            adminButton.style.display = 'none';
        } else {
            console.log('Phần tử adminButton không tồn tại.');
        }
    });
    $(document).ready(function() {
        loadDataToTable(currentPage, filter_minOrderTime, filter_maxOrderTime, filter_status);

        $("#dateStart").on("change", function() {
            let value = $(this).val();
            filter_minOrderTime = value ? value : null; // Nếu rỗng thì gán null
            currentPage = 1;
            loadDataToTable(currentPage, filter_minOrderTime, filter_maxOrderTime, filter_status);
        });

        $("#dateEnd").on("change", function() {
            let value = $(this).val();
            filter_maxOrderTime = value ? value : null; // Nếu rỗng thì gán null
            currentPage = 1;
            loadDataToTable(currentPage, filter_minOrderTime, filter_maxOrderTime, filter_status);
        });

        $("#TrangThai").on("change", function() {
            filter_status = $(this).val();
            currentPage = 1;
            loadDataToTable(currentPage, filter_minOrderTime, filter_maxOrderTime, filter_status);
        });
    });

    function toCreateForm() {
        window.location.href = "TaoKhuyenMai.php";
    }

    function renderTableBody(data) {
        var tableBody = document.getElementById("tableBody");
        var html = '';
        const userRole = sessionStorage.getItem('role'); // Lấy thông tin vai trò người dùng từ sessionStorage

        if (data.length === 0) {
            html = '<tr><td colspan="7" style="text-align: center;">Không tồn tại khuyến mãi !</td></tr>';
        } else {
            $.each(data, function(index, record) {
                let totalPriceFormat = formatCurrency(record.TotalPrice);

                // Kiểm tra chỉ số hàng và thiết lập kiểu nền cho hàng
                let rowStyle = (index % 2 !== 0) ? 'background-color: white;' : '';

                html += `<tr style="${rowStyle}">`; // Áp dụng kiểu cho hàng
                html += '<td>' + record.Id + '</td>';
                html += '<td>' + convertDateTimeFormat(record.CreateTime) + '</td>';
                html += '<td>' + record.Code + '</td>';
                html += '<td>' + convertDateTimeFormat(record.ExpirationTime) + '</td>';
                html += '<td>' + record.SaleAmount + '</td>';
                html += '<td>' + (record.IsPublic == 1 ? "Đang áp dụng" : "Vô hiệu hóa") + '</td>';

                html += '<td style="display: flex; gap: 5px;">';

                // Nếu là Manager: Hiển thị nút "Sửa" và "Khóa/Mở khóa"
                if (userRole === "Manager") {
                    html += '<a href="./ChiTietKhuyenMai.php?id=' + record.Id + '" class="edit">Sửa</a>';
                    if (record.IsPublic == 1) {
                        html += `<button type="button" 
                            class="update-status"  onclick="toggleVoucherStatus(${record.Id}, 0)" >Khóa</button>`;
                    } else {
                        html += `<button type="button" 
                            class="update-status" style="background-color:red;"  onclick="toggleVoucherStatus(${record.Id}, 1)" >Mở khóa</button>`;
                    }
                }
                // Nếu là Employee: Thay thế nút "Sửa" và "Khóa/Mở khóa" thành nút "Xem chi tiết"
                else if (userRole === "Employee") {
                    html += `<a href="./ChiTietKhuyenMai.php?id=${record.Id}" class="edit">Chi tiết</a>`;
                }
                // Nếu không phải là Manager hay Employee, ẩn các nút
                else {
                    // Không hiển thị bất kỳ nút nào nếu không phải Manager hay Employee
                    html += '';
                }

                html += '</td>';
                html += '</tr>';
            });
        }

        // Cập nhật nội dung cho phần thân bảng
        tableBody.innerHTML = html;
    }


    function toggleVoucherStatus(voucherId, newStatus) {
        // Thiết lập nội dung cảnh báo dựa trên trạng thái mới
        const actionText = newStatus === 1 ? "mở khóa" : "vô hiệu hóa";

        // Hiển thị hộp thoại xác nhận
        Swal.fire({
            title: `Bạn có chắc muốn ${actionText} voucher này không?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: `Đồng ý ${actionText}`,
            cancelButtonText: 'Hủy'
        }).then((result) => {
            // Nếu người dùng xác nhận
            if (result.isConfirmed) {
                // Gọi AJAX để cập nhật trạng thái của voucher
                $.ajax({
                    url: "../../../Controllers/VoucherController.php", // Thay bằng URL thực tế của API
                    type: 'PATCH',
                    data: JSON.stringify({
                        id: voucherId,
                        isPublic: newStatus,
                    }),
                    contentType: 'application/json',
                    success: function(response) {
                        Swal.fire(
                            'Thành công!',
                            `Voucher đã được ${actionText} thành công.`,
                            'success'
                        ).then(() => {
                            // Tải lại dữ liệu của bảng sau khi thay đổi thành công
                            location.reload();
                        });

                    },
                    error: function(error) {
                        Swal.fire(
                            'Lỗi!',
                            'Đã xảy ra lỗi khi gọi API.',
                            'error'
                        );
                        console.error(error);
                    }
                });
            }
        });
    }


    function loadDataToTable(page, minNgayTao, maxNgayTao, trangThai) {

        if (!minNgayTao) {
            minNgayTao = null;
        }
        if (!maxNgayTao) {
            maxNgayTao = null;
        }

        $.ajax({
            type: "GET",
            url: "../../../Controllers/VoucherController.php",
            dataType: "json",

            data: {
                page: page,
                pageSize: pageSizeGlobal,
                from: minNgayTao,
                to: maxNgayTao,
                status: trangThai,
            },
            success: function(response) {
                renderTableBody(response.data);
                setupPagination(response.totalElements, page);
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });

    }

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
                    loadDataToTable(currentPage, filter_minOrderTime, filter_maxOrderTime, filter_status);
                }
            }
        });
    }
</script>

</html>