<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="./HomePage.css" />
    <!-- <link rel="stylesheet" href="./login.css" /> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../HelperUI/formatOutput.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOMlI4F/x3Rgx31ZobM4uZ5dI6cuJg6RZ/aXjmD" crossorigin="anonymous">

    <title>Kinh doanh rượu</title>
</head>
<style>
    .sale-label {
        position: absolute;
        top: 10px;
        left: 10px;
        background-color: rgb(146, 26, 26);
        color: white;
        padding: 5px 10px;
        font-weight: bold;
        font-size: 14px;
        /* Nghiêng tem nhãn */
        z-index: 10;
        border-radius: 3px;
    }
</style>

<body>

    <?php require_once "./Header.php"; ?>

    <section id="poster" style="width: 100%; height: auto; padding: 0 5%;">
        <img src="img/poster.jpg" style="max-width: 100%;">
    </section>


    <section>
        <div class="center-text">
            <div class="title_section">
                <div class="bar"></div>
                <h2 class="center-text-share">SẢN PHẨM NỔI BẬT</h2>
            </div>
        </div>
    </section>


    <section id="product">
        <div class="container">
            <div class="row products">
                <!-- Hiển thị vài sản phẩm nổi bật -->
            </div>
        </div>
    </section>
    <div class="text-center my-4">
        <button id="btn-load-more" class="btn btn-primary">Xem thêm các sản phẩm</button>
    </div>

    <!-- Dịch vụ -->
    <section class="Home-titleSection">
        <div class="Home-lineSection-2"></div>
        <h2 class="Home-txtTitle">DỊCH VỤ</h2>
        <div class="Home-lineSection-2"></div>
    </section>

    <section class="Home-service container py-5" style="height: fit-content;">
        <div class="row" style="width:100%;height:fit-content;">
            <!-- Service 1 -->
            <div class="Home-service-child col-12 col-md-4 text-center mb-4">
                <div>
                    <img class="Home-service-img img-fluid" src="img/service-1.jpg" alt="" />
                </div>
                <h2 class="home-heading-service">Giao Hàng nhanh</h2>
                <p class="home-txt-service">
                    Winemart sẽ luôn cố gắng giao hàng nhanh nhất có thể
                </p>
            </div>

            <!-- Service 2 -->
            <div class="Home-service-child col-12 col-md-4 text-center mb-4">
                <div>
                    <img class="Home-service-img img-fluid" src="img/service2.jpg" alt="" />
                </div>
                <h2 class="home-heading-service">Hỗ trợ khách hàng</h2>
                <p class="home-txt-service">
                    Chăm sóc, tư vấn và hỗ trợ khách hàng gọi ngay <br>
                    1900.636.035
                </p>
            </div>

            <!-- Service 3 -->
            <div class="Home-service-child col-12 col-md-4 text-center mb-4">
                <div>
                    <img class="Home-service-img img-fluid" src="img/service3.jpg" alt="" />
                </div>
                <h2 class="home-heading-service">Thanh toán thuận tiện</h2>
                <p class="home-txt-service">
                    Winemart hỗ trợ thanh toán<br />
                    COD "Trong nội thành TP.HCM" và chuyển khoản
                </p>
            </div>
        </div>
    </section>


    <?php require_once "./Footer.php" ?>
</body>


<script>
    // Lắng nghe sự kiện click vào Poster
    document
        .getElementById("poster")
        .addEventListener("click", function() {
            window.location.href = 'Product.php';
        });


    // Lắng nghe sự kiện click vào Xem thêm
    document
        .getElementById("btn-load-more")
        .addEventListener("click", function() {
            window.location.href = 'Product.php';
        });

    // Gọi hàm getAllLoaiSanPham khi trang được tải
    $(document).ready(function() {
        // Kiểm tra xem sessionStorage có chứa 'isFirstTime' không
        if (!sessionStorage.getItem('isFirstTime') || sessionStorage.getItem('isFirstTime') === "true") {
            // Hiển thị hộp thoại xác nhận tuổi nếu chưa có 'isFirstTime'
            Swal.fire({
                title: 'Xác nhận tuổi',
                text: "Bạn có trên 18 tuổi không?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Có',
                cancelButtonText: 'Không'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Người dùng chọn "Có"
                    sessionStorage.setItem('isFirstTime', 'false'); // Đặt isFirstTime = false
                    getAllSanPham(); // Gọi hàm để lấy dữ liệu sản phẩm
                } else {
                    // Người dùng chọn "Không"
                    sessionStorage.setItem('isFirstTime', 'true'); // Hoặc không đặt gì nếu bạn muốn chỉ cảnh báo

                    Swal.fire({
                        title: 'Thông báo',
                        text: 'Bạn phải trên 18 tuổi để truy cập trang web này.',
                        icon: 'error'
                    }).then(() => {
                        window.location.href = "https://thuvienphapluat.vn/chinh-sach-phap-luat-moi/vn/ho-tro-phap-luat/chinh-sach-moi/30881/ban-bia-cho-nguoi-duoi-18-tuoi-se-bi-phat-tu-15-10"; // Điều hướng tới trang khác
                    });
                }
            });
        } else {
            // Nếu 'isFirstTime' đã tồn tại, gọi hàm getAllSanPham() bình thường
            getAllSanPham();
        }
    });



    // Hàm getAllSanPham
    function getAllSanPham() {
        // Gọi API để lấy dữ liệu sản phẩm
        $.ajax({
            url: "../../Controllers/ProductController.php",
            method: "GET",
            dataType: "json",
            data: {
                action: "getAllProductsCommonUser"
            },
            success: function(response) {
                var productContainer = $('#product .products');
                if (response.data && response.data.length > 0) {
                    // Tạo biến lưu trữ nội dung HTML mới
                    var htmlContent = '';
                    // Biến đếm số lượng sản phẩm
                    var count = 0;
                    // Duyệt qua từng sản phẩm và tạo nội dung HTML tương ứng
                    $.each(response.data, function(index, product) {
                        // Kiểm tra nếu số lượng sản phẩm đã đạt tới 4 thì dừng lại
                        if (count >= 6) {
                            return false;
                        }
                        var imageSrc = product.Image;
                        htmlContent += `
                                <div class="col-md-4 col-sm-6 mb-4">
                                    <div class="product-card-content" style="position: relative;">
                                        <a href="ProductDetail.php?maSanPham=${product.Id}">
                                                
                                            <img src="../img/${product.Image}" alt="" style="height: 300px;">
                                        
                            <div class="sale-label" ">
                                                            -${product.Sale === 0 ?"10":product.Sale}% 
                                                        </div>   
                                                                        <div class="product-card-details">
                                                <h4 class="name-product">${product.ProductName}</h4>`;

                        if (product.Sale === 0) {
                            // Calculate the inflated and discounted prices
                            const inflatedPrice = Math.ceil(product.UnitPrice / (90 / 100));
                            const discountPrice = product.UnitPrice;

                            htmlContent += `
                                                <p class="price-tea text-center" style="text-decoration: line-through; color: gray;">
                                                    <i class="fas fa-tag"></i> ${formatCurrency(inflatedPrice)}
                                                </p>
                                                <p class="price-tea text-center" style="color: rgb(146, 26, 26); font-weight: bold;">
                                                    <i class="fas fa-percent"></i> ${formatCurrency(discountPrice)}
                                                </p>
                                            `;
                        } else {
                            // Calculate the sale price
                            const salePrice = product.UnitPrice * (1 - product.Sale / 100);

                            htmlContent += `
                                                <p class="price-tea text-center" style="text-decoration: line-through; color: gray;">
                                                    <i class="fas fa-tag"></i> ${formatCurrency(product.UnitPrice)}
                                                </p>
                                                <p class="price-tea text-center" style="color: rgb(146, 26, 26); font-weight: bold;">
                                                    <i class="fas fa-percent"></i> ${formatCurrency(salePrice)}
                                                </p>
                                            `;
                        }

                        htmlContent += `
                                                <div class="buy-btn-container">
                                                    Mua ngay
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>`;
                        count++;



                        // Tăng biến đếm lên 1 sau mỗi sản phẩm được thêm vào
                    });
                    // Thay đổi nội dung HTML của phần tử sản phẩm
                    productContainer.html(htmlContent);
                } else {
                    // Hiển thị thông báo khi không có sản phẩm
                    productContainer.html('<p>Không có sản phẩm nào.</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });
    }

    function detail(maSanPham) {
        // Chuyển hướng về trang chủ khi click vào hình ảnh
        window.location.href = `ProductDetail.php?maSanPham=${maSanPham}`;
    }
</script>

</html>