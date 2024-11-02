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
                                        <th class="Table_th__hCkcg" style="width: 10%;">Mã đơn</th>
                                        <th class="Table_th__hCkcg" style="width: 15%;">Ngày đặt</th>
                                        <th class="Table_th__hCkcg" style="width: 10%;">Tổng đơn</th>
                                        <th class="Table_th__hCkcg" style="width: 15%;">Khách hàng</th>
                                        <th class="Table_th__hCkcg" style="width: 10%;">Số điện thoại</th>
                                        <th class="Table_th__hCkcg" style="width: 10%;">Trạng thái</th>
                                        <th class="Table_th__hCkcg" style="width: 30%;">Hành động</th>
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

    function updateStatus(orderId, nextStatus, currnetStatus) {
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
                    type: "GET",
                    url: "../../../Controllers/OrderDetailController.php", // Thêm orderId vào URL
                    dataType: "json",
                    data: {
                        orderId: orderId,
                        action: "getOrderDetailById"
                    },
                    success: function(response) {
                        response = JSON.stringify(response, null, 2);
                        const orderList  = JSON.parse(response).data;
                            nextStatus, currnetStatus
                            console.log(nextStatus);
                            console.log(currnetStatus);
                        // Tạo HTML cho mỗi đơn hàng và thêm vào phần tử
                        orderList.forEach(order => {
                            if (currnetStatus === "ChoDuyet" && nextStatus === "DaDuyet") {
                                let formData = {
                                    action: "down",
                                    id: order.ProductId,
                                    amount: order.Quantity
                                };
                                
                                $.ajax({
                                    type: "PATCH",
                                    url: "../../../Controllers/ProductController.php", // Thêm orderId vào URL
                                    dataType: "json",
                                    contentType: "application/json", // Đặt loại nội dung là JSON
                                    data: JSON.stringify(formData), // Chuyển đổi formData thành chuỗi JSON
                                    success: function(response) {
                                    },
                                    error: function(error) {
                                        Swal.fire('Thất bại!', error.responseJSON.message || 'Có lỗi xảy ra.', 'error');
                                    }
                                });
                            } else if (currnetStatus === "DaDuyet" && nextStatus === "Huy") {
                                console.log("Bấm nút!");

                                let formData = {
                                    action: "up",
                                    id: order.ProductId,
                                    amount: order.Quantity
                                };
                                
                                $.ajax({
                                    type: "PATCH",
                                    url: "../../../Controllers/ProductController.php", // Thêm orderId vào URL
                                    dataType: "json",
                                    contentType: "application/json", // Đặt loại nội dung là JSON
                                    data: JSON.stringify(formData), // Chuyển đổi formData thành chuỗi JSON
                                    success: function(response) {
                                        console.log("Call API hủy thành công !");
                                    },
                                    error: function(error) {
                                        Swal.fire('Thất bại!', error.responseJSON.message || 'Có lỗi xảy ra.', 'error');
                                    }
                                });
                            }
                        });

                        

                        $.ajax({
                            type: "POST",
                            url: "../../../Controllers/OrderStatusController.php", // Thêm orderId vào URL
                            dataType: "json",
                            data: {
                                orderId: orderId,
                                status: nextStatus
                            },
                            success: function(response) {
                                Swal.fire('Thành công!', 'Đã cập nhật trạng thái đơn hàng.', 'success');
                                loadDataToTable(currentPage, filter_minOrderTime, filter_maxOrderTime, filter_status); // Cập nhật lại bảng
                            },
                            error: function(error) {
                                Swal.fire('Thất bại!', error.responseJSON.message || 'Có lỗi xảy ra.', 'error');
                            }
                        });
                    },
                    error: function(error) {
                        Swal.fire('Thất bại!', error.responseJSON.message || 'Có lỗi xảy ra.', 'error');
                    }
                });

                
            }
        });
    }

    function renderTableBody(data) {
        var tableBody = document.getElementById("tableBody");
        var html = '';

        // Check if data is empty
        if (data.length === 0) {
            html = '<tr><td colspan="7" style="text-align: center;">Không tồn tại đơn hàng !</td></tr>';
        } else {
            $.each(data, function(index, record) {
                let totalPriceFormat = formatCurrency(record.TotalPrice);

                // Check if the index is even; if true, set background color to white
                let rowStyle = (index % 2 !== 0) ? 'background-color: white;' : '';

                html += `<tr style="${rowStyle}">`; // Apply row style
                html += '<td>' + record.Id + '</td>';
                html += '<td>' + convertDateTimeFormat(record.OrderTime) + '</td>';
                html += '<td>' + totalPriceFormat + '</td>';
                html += '<td>' + record.Fullname + '</td>';
                html += '<td>' + record.PhoneNumber + '</td>';
                html += '<td>' + fromEnumStatusToText(record.Status) + '</td>';

                html += '<td style="display: flex; gap: 5px;">';
                html += '<a href="./ChiTietDonHang.php?id=' + record.Id + '" class="edit">Chi tiết</a> '; // Nút Chi tiết

                // Nút Cập nhật trạng thái (màu xanh lá) với nội dung tùy thuộc vào trạng thái
                if (record.Status !== 'GiaoThanhCong' && record.Status !== 'Huy') {
                    const updateStatusText = fromCurrentStatusToNextStatusText(record.Status);
                    const nextStatus = fromCurrentStatusToNextStatus(record.Status);

                    html += `
                        <button 
                            type="button" 
                            class="update-status" 
                            onclick="updateStatus('${record.Id}', '${nextStatus}', '${record.Status}')"
                        >
                            ${updateStatusText}
                        </button>`;
                }

                // Kiểm tra trạng thái trước khi hiển thị nút Hủy
                if (record.Status === "ChoDuyet" || record.Status === "DaDuyet") {

                    html += `
                        <button 
                            type="button" 
                            class="cancel" 
                            onclick="updateStatus('${record.Id}', 'Huy', '${record.Status}')"
                        >
                            Hủy
                        </button>`;
                }

                html += '</td>';
                html += '</tr>';
            });

        }

        // Update the inner HTML of the table body
        tableBody.innerHTML = html;
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
            url: "../../../Controllers/OrderController.php",
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