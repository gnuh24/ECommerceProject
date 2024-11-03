<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="./HomePage.css">
    <link rel="stylesheet" href="./Cart.css">
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
    <?php require_once "./Header.php" ?>

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
                            <a href="Product.php">
                                <button class="btn btn-outline-danger w-100" style=" border:1px solid rgb(146, 26, 26) !important;color:rgb(146, 26, 26) ;">Tiếp tục mua hàng</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- Footer -->
        <?php require_once "./Footer.php" ?>

    </div>

</body>


<script>
    $(document).ready(function() {
        fetchCartItems();
    });
    var maTaiKhoan = JSON.parse(sessionStorage.getItem("id"));
    console.log("id: " + maTaiKhoan)

    function toCreateOrder() {
        var numberOfItemsInCart = $('.cartItem').length;

        var role = sessionStorage.getItem('role'); // or sessionStorage.getItem('role');
        if (role) {
            // The user is logged in and has a role, proceed with the order confirmation
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
                        window.location.href = `CreateOrder.php?maTaiKhoan=${maTaiKhoan}`;
                    }
                });
            }
        } else {
            // The user is not logged in or doesn't have a role, redirect to the login page
            Swal.fire({
                title: 'Thông báo!',
                text: 'Bạn cần đăng nhập để thực hiện thanh toán.',
                icon: 'warning',
                confirmButtonText: 'Đăng nhập'
            }).then(() => {
                // Redirect to the login page
                window.location.href = './Login/LoginUI.php';
            });
        }
    }


    function bindCartItemEvents() {
        // Sự kiện tăng số lượng
        $('.increase').off('click').on('click', function() {
            var productId = $(this).closest('.cartItem').attr('id');
            var quantityElem = $(`#quantity_${productId}`);
            var currentQuantity = parseInt(quantityElem.text());
            updateQuantity(productId, currentQuantity + 1);
        });

        // Sự kiện giảm số lượng
        $('.decrease').off('click').on('click', function() {

            var productId = $(this).closest('.cartItem').attr('id');
            var quantityElem = $(`#quantity_${productId}`);
            var currentQuantity = parseInt(quantityElem.text());
            if (currentQuantity > 1) {
                updateQuantity(productId, currentQuantity - 1);
            }
        });

        // Sự kiện xóa sản phẩm
        $('.btnRemove').off('click').on('click', function() {

            var productId = $(this).closest('.cartItem').attr('id');

            // Lấy giỏ hàng từ localStorage
            let cart = JSON.parse(localStorage.getItem('cart')) || [];

            // Tìm và xóa sản phẩm khỏi giỏ hàng
            cart = cart.filter(item => item.productId != productId);

            // Cập nhật lại localStorage
            localStorage.setItem('cart', JSON.stringify(cart));

            // Xóa sản phẩm khỏi giao diện
            $(`#${productId}`).remove();

            // Tính lại tổng giá trị giỏ hàng
            let totalAmount = cart.reduce((acc, item) => acc + item.total, 0);
            $('.priceTotal').text(formatMoney(totalAmount));

            // Ẩn hoặc hiện nút thanh toán dựa vào tổng số tiền
            $('.btnCheckout').toggleClass('hidden', totalAmount === 0);

            // Hiển thị thông báo nếu giỏ hàng trống
            if (cart.length === 0) {
                $('.listCart').html('<p>Giỏ hàng của bạn đang trống.</p>');
            }
        });

    }


    function fetchCartItems() {
        // Lấy giỏ hàng từ localStorage
        const cart = JSON.parse(localStorage.getItem('cart')) || [];

        // Kiểm tra nếu giỏ hàng trống
        if (cart.length === 0) {
            $('.listCart').html('<p>Giỏ hàng của bạn đang trống.</p>'); // Thông báo giỏ hàng trống
            $('.priceTotal').text(formatMoney(0));
            $('.btnCheckout').addClass('hidden');
            return; // Dừng hàm nếu không có sản phẩm
        }

        let cartHTML = '';
        let totalAmount = 0;

        // Duyệt qua các sản phẩm trong giỏ hàng
        cart.forEach(function(item) {
            cartHTML += `
            <div class='cartItem' id='${item.productId}'>
                <a href='#' class='img'><img class='img' src='../img/${item.image}' /></a>
                <div class='inforCart' style="display:flex;">
                    <div class='productName' style="width: 60%;">
                        <label for='productName_${item.productId}' class='labelproductName'>Tên sản phẩm:</label>
                        <div class='txtproductName' id='productName_${item.productId}'>${item.productName}</div>
                    </div>
                    <div class='quantity' style="width: 20%;">
                        <label for='quantity_${item.productId}' class='labelQuantity'>Số lượng:</label>
                        <div style="display:flex;">
                            <button class='btnQuantity decrease' data-id='${item.productId}'>-</button>
                            <div class='txtQuantity' id='quantity_${item.productId}'>${item.quantity}</div>
                            <button class='btnQuantity increase' data-id='${item.productId}'>+</button>
                        </div>
                    </div>
                    <div class='unitPrice' style="width: 20%;">
                        <label for='unitPrice_${item.productId}' class='labelUnitPrice'>Đơn giá:</label>
                        <div class='txtUnitPrice' id='unitPrice_${item.productId}'>${formatMoney(item.unitPrice)}</div>
                    </div>
                </div>
                <div class='wrapTotalPriceOfCart'>
                    <div class='totalPriceOfCart' style="height:100%">
                        <label for='totalPrice_${item.productId}' class='labelTotalPrice'>Thành tiền:</label>
                        <p class='valueTotalPrice' id='totalPrice_${item.productId}'>${formatMoney(item.quantity * item.unitPrice)}</p>
                    </div>
                    <button class='btnRemove' data-id='${item.productId}'>
                        <i class='fa-solid fa-xmark'></i>
                    </button>
                </div>
            </div>`;
            totalAmount += item.quantity * item.unitPrice;
        });

        // Hiển thị giỏ hàng và tổng giá trị
        $('.listCart').html(cartHTML);
        $('.priceTotal').text(formatMoney(totalAmount));

        // Ẩn hoặc hiện nút thanh toán dựa vào tổng số tiền
        $('.btnCheckout').toggleClass('hidden', totalAmount === 0);

        // Gọi hàm gán sự kiện cho các nút giỏ hàng
        bindCartItemEvents()
    }


    function formatMoney(amount) {
        return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") + "đ";
    }



    function updateQuantity(productId, quantity) {
        // Lấy giỏ hàng từ localStorage
        let cart = JSON.parse(localStorage.getItem('cart')) || [];

        // Tìm sản phẩm cần cập nhật
        const itemIndex = cart.findIndex(item => item.productId == productId);

        if (itemIndex !== -1) {
            // Cập nhật số lượng sản phẩm
            cart[itemIndex].quantity = quantity;
            cart[itemIndex].total = cart[itemIndex].quantity * cart[itemIndex].unitPrice;

            // Cập nhật lại localStorage
            localStorage.setItem('cart', JSON.stringify(cart));

            // Cập nhật giao diện
            document.getElementById(`quantity_${productId}`).innerText = quantity;
            document.getElementById(`totalPrice_${productId}`).innerText = formatMoney(cart[itemIndex].total);

            // Cập nhật tổng giá trị giỏ hàng
            let totalAmount = cart.reduce((acc, item) => acc + item.total, 0);
            $('.priceTotal').text(formatMoney(totalAmount));

            // Ẩn hoặc hiện nút thanh toán dựa vào tổng số tiền
            $('.btnCheckout').toggleClass('hidden', totalAmount === 0);
        }
    }
</script>


</html>