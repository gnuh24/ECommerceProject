<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="SignedHomePage.css">
    <link rel="stylesheet" href="Cart.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Giỏ hàng</title>
</head>
<style>
    .btn-outline-danger:hover {
        background-color: rgb(146, 26, 26) !important;
        color: white !important;
    }
</style>

<body>
    <?php require_once "../Header/SignedHeader.php" ?>

    <div>

        <section>
            <div class="center-text" style="margin-top: 20px;">
                <div class="title_section">
                    <div class="bar"></div>
                    <h2 class="center-text-share">Giỏ Hàng Của Bạn</h2>
                </div>
            </div>
        </section>

        <section class="show_cart py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mb-4">
                        <div class="wrapListCart">
                            <div class="listCart">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="wrapInfoOrder p-4 bg-light border rounded">
                            <p class="titleOrder text-center fw-bold mb-4">Thông tin đơn hàng</p>
                            <div class="wrapPriceTotal d-flex justify-content-between">
                                <p class="titlePriceTotal">Tổng giá trị:</p>
                                <p class="priceTotal fw-bold">0đ</p>
                            </div>
                            <button class="btn btn-danger w-100 my-3 hidden btnCheckout" style="background-color:rgb(146, 26, 26) !important;" onclick="toCreateOrder()">Tiến hành đặt hàng</button>
                            <a href="SignedProduct.php">
                                <button class="btn btn-outline-danger w-100" style=" border:1px solid rgb(146, 26, 26) !important;color:rgb(146, 26, 26) ;">Tiếp tục mua hàng</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- Footer -->
        <?php require_once "../Footer/Footer.php" ?>

    </div>

</body>


<script>
    var maTaiKhoan = JSON.parse(sessionStorage.getItem("id"));

    function toCreateOrder() {
        var numberOfItemsInCart = $('.cartItem').length;
        if (numberOfItemsInCart === 0) {
            Swal.fire({
                title: 'Lỗi!',
                text: 'Giỏ hàng của bạn đang trống. Vui lòng thêm sản phẩm vào giỏ hàng trước khi đặt hàng.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        } else {
            Swal.fire({
                title: 'Xác nhận đặt hàng',
                text: "Bạn có chắc chắn muốn đặt hàng không?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Đồng ý',
                cancelButtonText: 'Hủy bỏ'
            }).then((result) => {
                if (result.isConfirmed) {
                    thanhToan(maTaiKhoan);
                }
            });
        }
    }

    function thanhToan(maTaiKhoan) {
        window.location.href = `CreateOrder.php?maTaiKhoan=${maTaiKhoan}`;
    }

    function bindCartItemEvents() {
        $('.increase').on('click', function() {
            var productId = $(this).closest('.cartItem').attr('id');
            var quantityElem = $(`#quantity_${productId}`);
            var currentQuantity = parseInt(quantityElem.text());
            updateQuantity(productId, currentQuantity + 1);
        });

        $('.decrease').on('click', function() {
            var productId = $(this).closest('.cartItem').attr('id');
            var quantityElem = $(`#quantity_${productId}`);
            var currentQuantity = parseInt(quantityElem.text());
            if (currentQuantity > 1) {
                updateQuantity(productId, currentQuantity - 1);
            }
        });

        $('.btnRemove').on('click', function() {
            var productId = $(this).closest('.cartItem').attr('id');

            // Tạo URL với query string để gửi dữ liệu
            var url = '../../../Controllers/CartItemController.php?productId=' + productId + '&accountId=' + maTaiKhoan;

            $.ajax({
                url: url, // Gửi dữ liệu thông qua query string
                method: 'DELETE',
                dataType: "json",

                success: function(response) {
                    if (response.status === 200) {
                        $('#' + productId).remove(); // Xóa item khỏi giao diện
                        $('.priceTotal').text(formatMoney(response.data)); // Cập nhật tổng tiền
                    } else {
                        Swal.fire({
                            title: 'Lỗi!',
                            text: response.message || 'Có lỗi xảy ra, vui lòng thử lại.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    Swal.fire({
                        title: 'Lỗi!',
                        text: 'Có lỗi xảy ra khi xóa sản phẩm.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

    }

    function fetchCartItems() {
        $.ajax({
            url: '../../../Controllers/CartItemController.php',
            method: 'GET',
            data: {
                id: maTaiKhoan
            },
            dataType: 'json', // Đảm bảo rằng response được xử lý là JSON
            success: function(response) {
                // Kiểm tra xem phản hồi có dữ liệu hay không
                if (!Array.isArray(response.data) || response.data.length === 0) {
                    $('.listCart').html('<p>Giỏ hàng của bạn đang trống.</p>'); // Thông báo giỏ hàng trống
                    $('.priceTotal').text(formatMoney(0));
                    $('.btnCheckout').addClass('hidden');
                    return; // Dừng hàm nếu không có sản phẩm
                }

                var cartHTML = '';
                var totalAmount = 0;

                response.data.forEach(function(item) {
                    cartHTML += `
                    <div class='cartItem' id='${item.ProductId}'>
                        <a href='#' class='img'><img class='img' src='http://res.cloudinary.com/djhoea2bo/image/upload/v1711511636/${item.Image}' /></a>
                        <div class='inforCart'>
                            <div class='quantity'>
                                <label for='quantity_${item.ProductId}' class='labelQuantity'>Số lượng:</label>
                                <div style="display:flex;">
                                    <button class='btnQuantity decrease' data-id='${item.ProductId}'>-</button>
                                    <div class='txtQuantity' id='quantity_${item.ProductId}'>${item.Quantity}</div>
                                    <button class='btnQuantity increase' data-id='${item.ProductId}'>+</button>
                                </div>
                            </div>
                            <div class='unitPrice'>
                                <label for='unitPrice_${item.ProductId}' class='labelUnitPrice'>Đơn giá:</label>
                                <div class='txtUnitPrice' id='unitPrice_${item.ProductId}'>${formatMoney(item.UnitPrice)}</div>
                            </div>
                        </div>
                        <div class='wrapTotalPriceOfCart'>
                            <div class='totalPriceOfCart' style="height:100%">
                                <label for='totalPrice_${item.ProductId}' class='labelTotalPrice'>Thành tiền:</label>
                                <p class='valueTotalPrice' id='totalPrice_${item.ProductId}'>${formatMoney(item.Total)}</p>
                            </div>
                            <button class='btnRemove' data-id='${item.ProductId}'>
                                <i class='fa-solid fa-xmark'></i>
                            </button>
                        </div>
                    </div>`;
                    totalAmount += item.Total;
                });

                $('.listCart').html(cartHTML);
                $('.priceTotal').text(formatMoney(totalAmount));

                $('.btnCheckout').toggleClass('hidden', totalAmount === 0); // Ẩn hoặc hiện nút thanh toán

                bindCartItemEvents(); // Gọi hàm gán sự kiện
            },
            error: function(xhr, status, error) {
                console.error(error);
                Swal.fire({
                    title: 'Lỗi!',
                    text: 'Có lỗi xảy ra khi lấy giỏ hàng, vui lòng thử lại.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    }


    function formatMoney(amount) {
        return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + "đ";
    }
    $(document).ready(function() {
        fetchCartItems();
    });

    function updateQuantity(productId, quantity) {
        const unitPriceElem = document.getElementById(`unitPrice_${productId}`);
        const unitPrice = parseInt(unitPriceElem ? unitPriceElem.innerText.replace(/[^0-9]/g, '') : 0);
        const totalPrice = unitPrice * quantity;

        const jsonData = JSON.stringify({
            accountId: maTaiKhoan,
            productId: productId,
            unitPrice: unitPrice,
            quantity: quantity,
            total: totalPrice
        });

        $.ajax({
            url: '../../../Controllers/CartItemController.php?orderId=' + productId,
            type: 'PATCH',
            dataType: 'json',
            data: jsonData, // Gửi dữ liệu dưới dạng JSON
            contentType: 'application/json', // Thiết lập content type
            success: function(response) {

                fetchCartItems(); // Cập nhật giỏ hàng
            },
            error: function(xhr, status, error) {
                console.error(error);
                // Kiểm tra mã trạng thái phản hồi
                if (xhr.status === 400) {
                    // Nếu mã trạng thái là 400, hiển thị thông báo lỗi cụ thể
                    const response = JSON.parse(xhr.responseText); // Phân tích phản hồi JSON
                    const errorMessage = response.message || 'Có lỗi xảy ra, vui lòng thử lại.';

                    Swal.fire({
                        title: 'Lỗi!',
                        text: errorMessage, // Hiển thị thông báo cụ thể
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                } else {
                    // Xử lý các lỗi khác nếu cần
                    Swal.fire({
                        title: 'Lỗi!',
                        text: 'Có lỗi xảy ra khi cập nhật số lượng.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            }
        });
    }
</script>


</html>