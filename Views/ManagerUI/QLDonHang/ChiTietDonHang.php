<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../AdminHome.css" />
    <link rel="stylesheet" href="./QLDonHang.css" />
    <link rel="stylesheet" href="./detail_donhang.css">
    <script src="../../HelperUI/formatOutput.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Quản lý đơn hàng</title>
</head>

<body>
    <div class="StaffLayout_wrapper__CegPk">
        <?php require_once "../ManagerHeader.php" ?>
        <div class="Manager_wrapper__vOYy">
            <?php require_once "../ManagerMenu.php" ?>

            <div style="padding-left: 16%; width: 100%; padding-right: 2rem">
                <div class="wrapper">
                    <div class="orderManagement_order_history">
                        <div class="detail__wrapper">
                            <p class="title">Chi tiết đơn hàng: <span id="orderID"></span></p>
                            <ul class="order_status__wrapper" id="order_status">
                                <!-- Order status will be populated here -->
                            </ul>
                            <div class="transaction_info__wrapper">
                                <div class="receive_info__wrapper">
                                    <p class="title">Thông tin người nhận:</p>
                                    <div class="divider"></div>
                                    <div class="receive_info">
                                        <p class='name' id='hoten'></p>
                                        <p id='diachigiaohang'></p>
                                        <p id='sodienthoai'></p>
                                        <p id='ngaysinh'></p>
                                        <p id='gioitinh'></p>
                                        <p id="orderTime"></p>
                                        <p id="tinhtrang"></p>
                                    </div>
                                </div>
                                <div class="payment_method__wrapper">
                                    <p class="title">Phương thức thanh toán:</p>
                                    <div class="divider"></div>
                                    <p id="tenphuongthuc"><br></p>
                                </div>
                                <div class="note_wrapper">
                                    <p class="title">Ghi chú:</p>
                                    <div class="divider"></div>
                                    <p id="note"></p> <!-- Added note -->
                                </div>
                            </div>
                            <div class="transaction_items__wrapper">
                                <p class="transaction_name">Trạng thái:
                                    <span class="" id="trangthai"></span>
                                </p>
                                <div class="divider"></div>
                                <div class="transaction_list" id="transaction_list">
                                    <!-- Order details will be populated here -->
                                </div>
                            </div>
                            <div class="order_total__wrapper">
                                <div>
                                    <p>Tổng tiền hàng:</p>
                                    <p id="tongTienHang"></p>
                                </div>
                                <div>
                                    <p>Giảm giá:</p>
                                    <p id="giamGia"></p>
                                </div>
                                <div>
                                    <p>Thành tiền:</p>
                                    <p id="totalPrice"></p>
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
    var orderId = new URLSearchParams(window.location.search).get('id');

    document.getElementById("orderID").innerText = orderId;

    getOrderDetailsAndStatus(orderId);


    function getOrderDetailsAndStatus(orderId) {
        $.ajax({
            url: `../../../Controllers/OrderController.php`,
            type: 'GET',
            dataType: "json",
            data: {
                id: orderId
            },

            success: function(response) {
                var data = response.data;
                console.log(data)
                // Handle order details
                var transaction_list = document.getElementById("transaction_list");
                transaction_list.innerHTML = "";
                var items = "";
                var tongTienHang = 0;
                data.details.forEach(element => {
                    console.log(data)
                    items += `<div class='transaction_item'>
                        <img src='../../img/${element.Image}' alt=''>
                        <div class='item_info__wrapper'>
                            <div class='item_info'>
                                <p class='name'>${element.ProductName}</p>
                            </div>
                            <div class='item_info'>
                                <p class='quantity'>${element.Quantity}</p>
                                <p class='price'>${formatCurrency(element.UnitPrice*element.Quantity)}</p>
                            </div>
                        </div>
                    </div>
                    <div class='divider'></div>`;
                    tongTienHang += element.UnitPrice*element.Quantity;
                });
                transaction_list.innerHTML = items;

                document.getElementById("hoten").innerHTML = `<span>Họ tên:</span> ${data.info.Fullname}`;
                document.getElementById("diachigiaohang").innerHTML = `<span>Địa chỉ: </span>${data.info.Address}`;
                document.getElementById("sodienthoai").innerHTML = `<span>Số điện thoại: </span>${data.info.PhoneNumber}`;
                document.getElementById("note").innerText = data.info.Note; // Added note

                document.getElementById("tongTienHang").innerText = formatCurrency(tongTienHang);
                document.getElementById("giamGia").innerText = formatCurrency(tongTienHang - data.info.TotalPrice);
                document.getElementById("totalPrice").innerText = formatCurrency(data.info.TotalPrice);

                document.getElementById("orderTime").innerHTML = `<span>Thời gian đặt: </span>${convertDateTimeFormat(data.info.OrderTime)}`; // Added order time
                document.getElementById("tenphuongthuc").innerHTML = `<span>Phương thức thanh toán: </span>${data.info.Payment}`;
                document.getElementById("tinhtrang").innerHTML = `<span>Tình trạng: </span>${data.info.isPaid==0?'Chưa thanh toán':'Đã thanh toán'}`;

                // Handle order status
                var order_status = document.getElementById("order_status");
                var order_status_content = '';

                data.orderStatuses.forEach(status => {
                    let currentStatusText = fromEnumStatusToText(status.Status);
                    let currentOrderTimeText = convertDateTimeFormat(status.UpdateTime);

                    if (status.Status === 'Huy') {
                        order_status_content += `<div class="order_status cancelled">
                            <li>${currentStatusText}<br>${currentOrderTimeText}</li>
                        </div>`;
                    } else {
                        order_status_content += `<div class="order_status completed">
                            <li>${currentStatusText}<br>${currentOrderTimeText}</li>
                        </div>`;
                    }
                });

                order_status.innerHTML = order_status_content;
            },
            error: function(xhr, status, error) {
                console.error('Lỗi khi gọi API: ', error);
            }
        });
    }
</script>

</html>