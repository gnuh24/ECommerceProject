<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../AdminDemo.css" />
    <link rel="stylesheet" href="./QLDonHang.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title>Quản lý đơn hàng</title>
</head>

<body>
    <div>
        <div class="App">
            <div class="StaffLayout_wrapper__CegPk">
                <?php require_once "../ManagerHeader.php" ?>

                <div>
                    <div>
                        <div class="Manager_wrapper__vOYy">
                            <?php require_once "../ManagerMenu.php" ?>
                            <div style="padding-left: 16%; width: 100%; padding-right: 2rem">
                                <div class="wrapper">
                                    <div class="Admin_rightBar__RXnS9">
                                        <div style="
                                                display: flex;
                                                margin-bottom: 1rem;
                                                align-items: center;
                                            ">
                                            <p class="Admin_title__1Tk48">Quản lí đơn hàng</p>
                                        </div>
                                        <div class="Admin_boxFeature__ECXnm">
                                            <label for=""> Lọc đơn hàng:</label>
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
                                                    <option value="ChoDuyet">Chờ duyệt</option>
                                                    <option value="DaDuyet">Đã duyệt</option>
                                                    <option value="Huy">Hủy</option>
                                                    <option value="DangGiao">Đang giao</option>
                                                    <option value="GiaoThanhCong">Giao thành công</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="Admin_boxTable__hLXRJ">
                                            <table class="Table_table__BWPy">
                                                <thead class="Table_head__FTUog">
                                                    <tr>
                                                        <th class="Table_th__hCkcg">Mã đơn</th>
                                                        <th class="Table_th__hCkcg">Ngày đặt</th>
                                                        <th class="Table_th__hCkcg">Tổng đơn</th>
                                                        <th class="Table_th__hCkcg">Khách hàng</th>
                                                        <th class="Table_th__hCkcg">Số điện thoại</th>
                                                        <th class="Table_th__hCkcg">Trạng thái</th>
                                                        <th class="Table_th__hCkcg">Hành động</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tableBody">
                                                </tbody>
                                            </table>
                                            <div id="pagination" class="pagination">
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
    </div>
</body>

<script>
    var udPage = 1;
    var udminNgayTao = 0;
    var udmaxNgayTao = 0;
    var udtrangThai = "";

    $(document).ready(function() {
        loadDataToTable(udPage, null, null, null);

        $("#dateStart").on("change", function() {
            let value = $(this).val();
            udminNgayTao = value ? convertDateFormat(value) : null; // Nếu rỗng thì gán null
            loadDataToTable(1, udminNgayTao, udmaxNgayTao, udtrangThai);
        });

        $("#dateEnd").on("change", function() {
            let value = $(this).val();
            udmaxNgayTao = value ? convertDateFormat(value) : null; // Nếu rỗng thì gán null
            loadDataToTable(1, udminNgayTao, udmaxNgayTao, udtrangThai);
        });


        $("#TrangThai").on("change", function() {
            udtrangThai = $(this).val();
            loadDataToTable(1, udminNgayTao, udmaxNgayTao, udtrangThai);
        });
    });

    function clearTable() {
        var tableBody = document.getElementById("tableBody");
        tableBody.innerHTML = ''; // Xóa nội dung trong tbody
    }

    function number_format_vnd(number) {
        return Number(number).toLocaleString('vi-VN', {
            style: 'currency',
            currency: 'VND'
        });
    }

    function getUpdateStatusText(status) {
        switch (status) {
            case 'ChoDuyet':
                return 'Duyệt';
            case 'DaDuyet':
                return 'Giao hàng';
            case 'DangGiao':
                return 'Hoàn tất';
            default:
                return 'Cập nhật trạng thái'; // Nội dung mặc định nếu không khớp với bất kỳ trạng thái nào
        }
    }

    function getUpdateStatus(status) {
        switch (status) {
            case 'ChoDuyet':
                return 'DaDuyet';
            case 'DaDuyet':
                return 'DangGiao';
            case 'DangGiao':
                return 'GiaoThanhCong';
        }
    }

    function updateStatus(orderId, currentStatus) {
        Swal.fire({
            title: 'Bạn có chắc chắn?',
            text: "Bạn muốn cập nhật trạng thái của đơn hàng này?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Xác nhận'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: "PATCH",
                    url: "../../../Controllers/OrderStatusController.php?orderId=" + orderId, // Thêm orderId vào URL

                    contentType: "application/json", // Định dạng dữ liệu là JSON
                    data: JSON.stringify({
                        status: currentStatus,
                        updateTime: new Date().toISOString() // Thêm thời gian hiện tại vào dữ liệu
                    }),
                    success: function(response) {
                        Swal.fire('Thành công!', 'Đã cập nhật trạng thái đơn hàng.', 'success');
                        loadDataToTable(udPage, udminNgayTao, udmaxNgayTao, udtrangThai); // Cập nhật lại bảng
                    },
                    error: function(error) {
                        Swal.fire('Thất bại!', error.responseJSON.message || 'Có lỗi xảy ra.', 'error');
                    }
                });
            }
        });
    }


    function renderTableBody(data) {
        console.log(data);
        var tableBody = document.getElementById("tableBody");
        var html = '';
        $.each(data, function(index, record) {
            let totalPriceFormat = number_format_vnd(record.TotalPrice);
            html += '<tr>';
            html += '<td>' + record.Id + '</td>';
            html += '<td>' + record.OrderTime + '</td>'; // Sử dụng orderTime trực tiếp từ BE
            html += '<td>' + totalPriceFormat + '</td>';
            html += '<td>' + record.Fullname + '</td>';
            html += '<td>' + record.PhoneNumber + '</td>';
            html += '<td>' + formatStatus(record.Status) + '</td>';

            html += '<td style="display: flex; gap: 5px;">';
            html += '<a href="./ChiTietDonHang.php?id=' + record.Id + '" class="edit">Chi tiết</a> '; // Nút Chi tiết

            // Nút Cập nhật trạng thái (màu xanh lá) với nội dung tùy thuộc vào trạng thái
            if (record.Status !== 'GiaoThanhCong' && record.Status !== 'Huy') {
                const updateStatusText = getUpdateStatusText(record.Status);
                const nextStatus = getUpdateStatus(record.Status);

                html += `
                    <button 
                        type="button" 
                        class="update-status" 
                        onclick="updateStatus('${record.Id}', '${nextStatus}')"
                    >
                        ${updateStatusText}
                    </button>`;
            }

            // Kiểm tra trạng thái trước khi hiển thị nút Hủy
            if (record.Status === "ChoDuyet") {

                html += `
                      <button 
                          type="button" 
                          class="cancel" 
                          onclick="updateStatus('${record.Id}', 'Huy')"
                      >
                          Hủy
                      </button>`;
            }
            html += '</td>';
            html += '</tr>';
        });
        tableBody.innerHTML = html;
    }

    function convertDateFormat(dateString) {
        // Kiểm tra định dạng đầu vào
        const regex = /^\d{4}-\d{2}-\d{2}$/;
        if (!regex.test(dateString)) {
            throw new Error("Định dạng ngày không hợp lệ. Vui lòng sử dụng 'yyyy-MM-dd'.");
        }

        // Tách các phần của ngày
        const parts = dateString.split('-');
        const year = parts[0];
        const month = parts[1];
        const day = parts[2];

        // Trả về định dạng mới
        return `${year}/${month}/${day}`; // Đổi thành yyyy/mm/dd
    }


    function formatStatus(status) {
        switch (status) {
            case 'ChoDuyet':
                return 'Chờ Duyệt';
            case 'DaDuyet':
                return 'Đã duyệt';
            case 'Huy':
                return 'Đã Hủy';
            case 'DangGiao':
                return 'Đang Giao';
            case 'GiaoThanhCong':
                return 'Giao thành công';
            default:
                return status;
        }
    }

    function loadDataToTable(page, minNgayTao, maxNgayTao, trangThai) {
        clearTable();

        if (!minNgayTao) {
            minNgayTao = null;
        }
        if (!maxNgayTao) {
            maxNgayTao = null;
        }

        $.ajax({
            type: "GET",
            url: "../../../Controllers/OrderController.php",
            dataType: "json",

            data: {
                page: page,
                from: minNgayTao,
                to: maxNgayTao,
                status: trangThai,
            },
            success: function(response) {
                renderTableBody(response.data);
                renderPagination(response.totalPages, page);
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });

    }

    function renderPagination(totalPages, currentPage) {
        var pagination = document.getElementById("pagination");
        var html = '';
        // First button
        if (currentPage > 1) {
            html += '<button class="pageButton" onclick="loadDataToTable(1, udminNgayTao, udmaxNgayTao, udtrangThai)"><<</button>';
        }

        // Previous button
        if (currentPage > 1) {
            html += '<button class="pageButton" onclick="loadDataToTable(' + (currentPage - 1) + ', udminNgayTao, udmaxNgayTao, udtrangThai)">Trước</button>';
        }


        // Calculate start and end page
        var maxPagesToShow = 5;
        var startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
        var endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);

        // Adjust startPage if endPage is at the limit
        if (endPage - startPage < maxPagesToShow - 1) {
            startPage = Math.max(1, endPage - maxPagesToShow + 1);
        }

        // Page numbers
        for (var i = startPage; i <= endPage; i++) {
            if (i === currentPage) {
                html += '<button class="pageButton active">' + i + '</button>';
            } else {
                html += '<button class="pageButton" onclick="loadDataToTable(' + i + ', udminNgayTao, udmaxNgayTao, udtrangThai)">' + i + '</button>';
            }
        }
        // Next button
        if (currentPage < totalPages) {
            html += '<button class="pageButton" onclick="loadDataToTable(' + (currentPage + 1) + ', udminNgayTao, udmaxNgayTao, udtrangThai)">Sau</button>';
        }
        // Last button
        if (currentPage < totalPages) {
            html += '<button class="pageButton" onclick="loadDataToTable(' + totalPages + ', udminNgayTao, udmaxNgayTao, udtrangThai)">>></button>';
        }



        pagination.innerHTML = html;
    }
</script>

</html>