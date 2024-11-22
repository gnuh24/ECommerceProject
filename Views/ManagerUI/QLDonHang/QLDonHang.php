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

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <!-- Đảm bảo thêm extension Select của DataTables -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.3.3/css/select.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>

    <script src="../../HelperUIformatOutput.js"> </script>
    <title>Quản lý đơn hàng</title>
</head>
<style>
    #updateSelected {
        background-color: #4CAF50;
        /* Màu nền xanh lá */
        color: white;
        /* Màu chữ trắng */
        font-size: 16px;
        /* Kích thước chữ */
        padding: 10px 20px;
        /* Khoảng cách xung quanh chữ */
        border: none;
        /* Bỏ viền */
        border-radius: 5px;
        /* Bo tròn góc */
        cursor: pointer;
        /* Thay đổi con trỏ khi rê chuột vào */
        transition: background-color 0.3s, transform 0.2s;
        /* Hiệu ứng chuyển màu nền và thu phóng khi hover */
    }

    #updateSelected:hover {
        background-color: #45a049;
        /* Màu nền khi hover (xanh lá đậm hơn) */
        transform: scale(1.05);
        /* Phóng to nút khi hover */
    }

    #updateSelected:active {
        background-color: #3e8e41;
        /* Màu nền khi click (xanh lá tối hơn) */
        transform: scale(0.98);
        /* Thu nhỏ nút khi click */
    }

    #updateSelected:disabled {
        background-color: #cccccc;
        /* Màu nền khi nút bị vô hiệu hóa */
        cursor: not-allowed;
        /* Thay đổi con trỏ khi nút không thể bấm */
    }
</style>

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
                            <button id="updateSelected" type="button">Cập nhật trạng thái cho các đơn hàng đã chọn</button>
                        </div>
                        <div class="Admin_boxTable__hLXRJ">


                            <table id="orderTable" class="display">
                                <thead>

                                    <tr>
                                        <th></th>
                                        <th style="text-align: center;">Mã đơn</th>
                                        <th style="text-align: center;">Ngày đặt</th>
                                        <th style="text-align: center;">Tổng đơn</th>
                                        <th style="text-align: center;">Khách hàng</th>
                                        <th style="text-align: center;">Số điện thoại</th>
                                        <th style="text-align: center;">Trạng thái</th>
                                        <th style="text-align: center; width: 30%;">Hành động</th>
                                    </tr>

                                </thead>
                                <tbody id="orderTableBody">
                                    <!-- Dữ liệu sẽ được thêm vào đây -->
                                </tbody>
                            </table>

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
    var filter_search = "";
    document.getElementById("dateStart").value = "";
    document.getElementById("dateEnd").value = "";
    document.getElementById("TrangThai").value = "";

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

    async function updateStatus(orderId, nextStatus, currnetStatus) {
        const result = await Swal.fire({
            title: 'Bạn có chắc chắn?',
            text: "Bạn muốn cập nhật trạng thái của đơn hàng này?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Xác nhận'
        });

        if (result.isConfirmed) {
            try {
                // Lấy chi tiết đơn hàng
                const response = await $.ajax({
                    type: "GET",
                    url: "../../../Controllers/OrderDetailController.php",
                    dataType: "json",
                    data: {
                        orderId: orderId,
                        action: "getOrderDetailById"
                    }
                });

                const orderList = response.data;

                // Xử lý từng đơn hàng
                for (const order of orderList) {
                    try {
                        if (currnetStatus === "ChoDuyet" && nextStatus === "DaDuyet") {
                            let formData = {
                                action: "down",
                                id: order.ProductId,
                                amount: order.Quantity
                            };

                            // Gọi API để cập nhật sản phẩm
                            await $.ajax({
                                type: "PATCH",
                                url: "../../../Controllers/ProductController.php",
                                dataType: "json",
                                contentType: "application/json",
                                data: JSON.stringify(formData),
                                success: function(response) {
                                    console.log(response)
                                }
                            });
                        } else if (currnetStatus === "DaDuyet" && nextStatus === "Huy") {
                            let formData = {
                                action: "up",
                                id: order.ProductId,
                                amount: order.Quantity
                            };

                            // Gọi API để hủy cập nhật sản phẩm
                            await $.ajax({
                                type: "PATCH",
                                url: "../../../Controllers/ProductController.php",
                                dataType: "json",
                                contentType: "application/json",
                                data: JSON.stringify(formData)
                            });
                        }
                    } catch (error) {
                        console.log(error.responseJSON.details.message)
                        // Hiển thị thông báo lỗi cho từng sản phẩm nhưng không dừng toàn bộ quá trình
                        console.error(`Lỗi khi xử lý sản phẩm ${order.ProductId}:`, error);
                        await Swal.fire('Cập nhật thất bại!',
                            error.responseJSON.details.message, 'error');
                        return;
                    }
                }

                // Cập nhật trạng thái đơn hàng
                await $.ajax({
                    type: "POST",
                    url: "../../../Controllers/OrderStatusController.php",
                    dataType: "json",
                    data: {
                        orderId: orderId,
                        status: nextStatus
                    }
                });

                // Hiển thị thông báo thành công
                await Swal.fire('Thành công!', 'Đã cập nhật trạng thái đơn hàng.', 'success');
                loadDataToTable(currentPage, filter_minOrderTime, filter_maxOrderTime, filter_status); // Cập nhật lại bảng
            } catch (error) {
                // Kiểm tra và hiển thị thông báo lỗi an toàn
                const errorMessage = (error.responseJSON && error.responseJSON.message) ? error.responseJSON.message : 'Có lỗi xảy ra.';
                await Swal.fire('Thất bại!', errorMessage, 'error');
                loadDataToTable(currentPage, filter_minOrderTime, filter_maxOrderTime, filter_status); // Cập nhật lại bảng

            }
        }
    }





    function formatDateToHHMMSSDDMMYYYY(dateString) {
        // Kiểm tra nếu dateString không hợp lệ
        if (!dateString) return dateString;

        // Tách phần ngày giờ từ chuỗi (nếu có giờ)
        const [datePart, timePart] = dateString.split(" "); // Tách phần ngày và phần giờ
        const [year, month, day] = datePart.split("-"); // Tách ngày, tháng, năm từ phần ngày
        const [hours, minutes, seconds] = timePart.split(":"); // Tách giờ, phút, giây từ phần giờ

        // Đảm bảo rằng tất cả các phần đều có đúng số chữ số
        if (year && month && day && hours && minutes && seconds) {
            return `${hours.padStart(2, '0')}:${minutes.padStart(2, '0')}:${seconds.padStart(2, '0')} ${day.padStart(2, '0')}-${month.padStart(2, '0')}-${year}`;
        }

        return dateString; // Nếu không hợp lệ, trả về giá trị gốc
    }

    function formatDateToDDMMYYYYHHMMSS(dateString) {
        // Kiểm tra nếu dateString không hợp lệ
        if (!dateString) return dateString;

        // Tách phần ngày giờ từ chuỗi (nếu có giờ)
        const [datePart, timePart] = dateString.split(" "); // Tách phần ngày và phần giờ
        const [year, month, day] = datePart.split("-"); // Tách ngày, tháng, năm từ phần ngày
        const [hours, minutes, seconds] = timePart ? timePart.split(":") : ["00", "00", "00"]; // Tách giờ, phút, giây từ phần giờ

        // Đảm bảo rằng tất cả các phần đều hợp lệ và trả về định dạng DD-MM-YYYY HH:MM:SS
        if (year && month && day) {
            return `${day.padStart(2, '0')}-${month.padStart(2, '0')}-${year} ${hours.padStart(2, '0')}:${minutes.padStart(2, '0')}:${seconds.padStart(2, '0')}`;
        }

        return dateString; // Nếu không hợp lệ, trả về giá trị gốc
    }

    function renderTableBody(data) {
        $('#orderTable').DataTable({
            destroy: true, // Phá hủy bảng cũ nếu có
            pageLength: 10, // Số hàng trên mỗi trang
            data: data, // Dữ liệu được truyền vào bảng

            columns: [{
                    data: 'Id',
                    render: function(data, type, row) {
                        // Bao gồm cả Id và Status vào giá trị của checkbox, ngăn cách bởi dấu gạch dưới
                        if (row.Status === "ChoDuyet" || row.Status === "DaDuyet" || row.Status === "DangGiao") {
                            return `<input type="checkbox" class="select-row" value="${data}_${row.Status}_'${formatDateToHHMMSSDDMMYYYY(row.OrderTime)}'">`;
                        } else {
                            return `<input type="checkbox" class="select-row" value="${data}_${row.Status}_'${formatDateToHHMMSSDDMMYYYY(row.OrderTime)}'" disabled>`;
                        }
                    }

                }, {
                    data: 'Id',

                },
                {
                    data: 'OrderTime',
                    render: function(data, type, row) {
                        return data
                    }
                },
                {
                    data: 'TotalPrice',
                    render: function(data, type, row) {
                        return formatCurrency(data)
                    }
                },
                {
                    data: 'Fullname'
                },
                {
                    data: 'PhoneNumber'
                },
                {
                    data: 'Status',
                    render: function(data, type, row) {
                        return fromEnumStatusToText(data);
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        let actions = `<a href="./ChiTietDonHang.php?id=${row.Id}" class="edit">Chi tiết</a>`;

                        if (row.Status !== 'GiaoThanhCong' && row.Status !== 'Huy') {
                            const updateStatusText = fromCurrentStatusToNextStatusText(row.Status);
                            const nextStatus = fromCurrentStatusToNextStatus(row.Status);
                            actions += `
                    <button 
                        type="button" 
                        class="update-status" 
                        onclick="updateStatus('${row.Id}', '${nextStatus}', '${row.Status}')"
                    >
                        ${updateStatusText}
                    </button>`;
                        }

                        if (row.Status === "ChoDuyet" || row.Status === "DaDuyet") {
                            actions += `
                    <button 
                        type="button" 
                        class="cancel" 
                        onclick="updateStatus('${row.Id}', 'Huy', '${row.Status}')"
                    >
                        Hủy
                    </button>`;
                        }

                        return actions;
                    }
                }
            ],
            language: {
                search: "Tìm kiếm:",
                lengthMenu: "Hiển thị _MENU_ đơn hàng",
                info: "Hiển thị _START_ đến _END_ trong tổng số _TOTAL_ đơn hàng",
                paginate: {
                    previous: "Trước",
                    next: "Sau"
                },
                emptyTable: "Không tồn tại đơn hàng !"
            }
        });



    }

    $('#updateSelected').on('click', function() {
        var selectedRows = $('#orderTable tbody .select-row:checked'); // Lấy các checkbox đã chọn
        console.log('Số checkbox đã chọn: ', selectedRows.length); // Kiểm tra số lượng checkbox đã chọn

        if (selectedRows.length > 0) {
            Swal.fire({
                title: 'Bạn có chắc chắn?',
                text: `Bạn muốn cập nhật trạng thái cho ${selectedRows.length} đơn hàng?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Xác nhận'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tạo một mảng để lưu các đối tượng Id và Status cần cập nhật
                    var orders = [];
                    selectedRows.each(function() {
                        var [id, status, time] = $(this).val().split('_'); // Tách Id và Status từ giá trị của checkbox
                        orders.push({
                            Id: id,
                            currentStatus: status,
                            nextStatus: fromCurrentStatusToNextStatus(status),
                            time: time
                        });
                    });
                    orders.sort((a, b) => {
                        // Chuyển đổi chuỗi thời gian thành đối tượng Date
                        const timeA = new Date(a.time.split(" ").reverse().join(" ")); // Định dạng lại thành "DD-MM-YYYY HH:mm:ss"
                        const timeB = new Date(b.time.split(" ").reverse().join(" "));

                        // So sánh thời gian
                        return timeA - timeB; // Sắp xếp từ lâu nhất đến gần nhất
                    });

                    updateStatusAll(orders);
                }
            });
        } else {
            alert('Vui lòng chọn ít nhất một đơn hàng.');
        }
    });


    async function updateStatusAll(data) {
        if (data.length === 0) {
            Swal.fire('Không có đơn hàng nào được chọn', '', 'error');
            return;
        }

        const result = await Swal.fire({
            title: 'Bạn có chắc chắn?',
            text: `Bạn muốn cập nhật trạng thái của ${data.length} đơn hàng?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Xác nhận'
        });

        if (!result.isConfirmed) {
            return; // Bỏ qua nếu không xác nhận
        }

        for (const item of data) {
            const {
                Id: orderId,
                nextStatus,
                currentStatus
            } = item;

            try {
                // Lấy chi tiết đơn hàng
                const response = await $.ajax({
                    type: "GET",
                    url: "../../../Controllers/OrderDetailController.php",
                    dataType: "json",
                    data: {
                        orderId: orderId,
                        action: "getOrderDetailById"
                    }
                });

                const orderList = response.data;

                // Xử lý từng sản phẩm trong đơn hàng
                for (const order of orderList) {
                    if (currentStatus === "ChoDuyet" && nextStatus === "DaDuyet") {
                        let formData = {
                            action: "down",
                            id: order.ProductId,
                            amount: order.Quantity
                        };

                        // Gọi API để cập nhật sản phẩm
                        await $.ajax({
                            type: "PATCH",
                            url: "../../../Controllers/ProductController.php",
                            dataType: "json",
                            contentType: "application/json",
                            data: JSON.stringify(formData)
                        });
                    } else if (currentStatus === "DaDuyet" && nextStatus === "Huy") {
                        let formData = {
                            action: "up",
                            id: order.ProductId,
                            amount: order.Quantity
                        };

                        // Gọi API để hủy cập nhật sản phẩm
                        await $.ajax({
                            type: "PATCH",
                            url: "../../../Controllers/ProductController.php",
                            dataType: "json",
                            contentType: "application/json",
                            data: JSON.stringify(formData)
                        });
                    }
                }

                // Cập nhật trạng thái đơn hàng
                await $.ajax({
                    type: "POST",
                    url: "../../../Controllers/OrderStatusController.php",
                    dataType: "json",
                    data: {
                        orderId: orderId,
                        status: nextStatus
                    }
                });

            } catch (error) {
                const errorMessage = (error.responseJSON && error.responseJSON.details && error.responseJSON.details.message) ?
                    `Có lỗi xảy ra khi xử lý đơn hàng ${orderId}: ${error.responseJSON.details.message}` :
                    `Có lỗi xảy ra khi xử lý đơn hàng ${orderId}.`;

                await Swal.fire('Thất bại!', errorMessage, 'error');
                loadDataToTable(currentPage, filter_minOrderTime, filter_maxOrderTime, filter_status); // Cập nhật lại bảng
                return;
            }
        }

        // Cập nhật lại bảng sau khi xử lý tất cả đơn hàng
        loadDataToTable(currentPage, filter_minOrderTime, filter_maxOrderTime, filter_status);
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
                search: filter_search
            },
            success: function(response) {
                renderTableBody(response.data);
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });

    }
</script>

</html>