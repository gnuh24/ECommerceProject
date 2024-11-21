<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="./HomePage.css" />
    <link rel="stylesheet" href="./Product.css" />
    <link rel="stylesheet" href="./components/paginationjs.css" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Include Pagination.js -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.5/pagination.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.5/pagination.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOMlI4F/x3Rgx31ZobM4uZ5dI6cuJg6RZ/aXjmD" crossorigin="anonymous">


    <script src="../HelperUI/formatOutput.js"></script>


    <title>Các sản phẩm</title>
</head>
<style>
    .sale-badge {
        position: absolute;
        /* Định vị tuyệt đối */
        top: 10px;
        /* Cách từ trên cùng */
        right: 10px;
        /* Cách từ bên phải */
        width: 100px;
        /* Điều chỉnh kích thước của hình ảnh nếu cần */
        height: auto;
        /* Đảm bảo giữ tỷ lệ */
        z-index: 10;
        /* Đảm bảo hình ảnh nằm trên các phần tử khác */
    }

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

    .sale-banner {
        position: absolute;
        /* Position it relative to the parent */
        top: 10px;
        /* Distance from the top */
        right: 10px;
        /* Distance from the right */
        background-color: rgba(255, 0, 0, 0.8);
        /* Semi-transparent red background */
        color: white;
        /* Text color */
        padding: 5px 10px;
        /* Padding around the text */
        border-radius: 5px;
        /* Rounded corners */
        font-size: 14px;
        /* Font size */
        z-index: 10;
        /* Ensure it appears above the image */
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        /* Subtle shadow for depth */
    }

    .sale-banner i {
        margin-right: 5px;
        /* Space between icon and text */
        color: yellow;
        /* Color of the star icon */
    }

    #filter-menu {
        border-right: 5px solid #ddd;
        /* Thêm đường viền để phân cách */
        padding-right: 15px;
        /* Tạo khoảng cách bên phải cho bộ lọc */
    }

    #product {
        padding-left: 15px;
        /* Tạo khoảng cách bên trái cho sản phẩm */
    }
</style>

<body>
    <?php require_once "./Header.php"; ?>
    <div class="container-fluid bg-white p-3 rounded mb-4">
        <div class="row">
            <!-- Cột cho filter menu -->
            <div id="filter-menu" class="col-12 col-md-2 mb-4">
                <div class="bg-white p-3 rounded">
                    <label for="price-filter" class="form-label text-danger fw-bold">Giá:</label>
                    <select id="price-filter" class="form-select form-select-sm bg-danger text-white mb-3">
                        <option value="">Tất cả</option>
                        <option value="low">Dưới 1 triệu</option>
                        <option value="medium">Từ 1 đến 3 triệu</option>
                        <option value="high">Trên 3 triệu</option>
                    </select>

                    <label for="brand-filter" class="form-label text-danger fw-bold">Thương hiệu:</label>
                    <select id="brand-filter" class="form-select form-select-sm bg-danger text-white mb-3">
                        <!----------------- Hiển thị menu Loại sản phẩm ------------------->
                    </select>

                    <label for="category-filter" class="form-label text-danger fw-bold">Loại sản phẩm:</label>
                    <select id="category-filter" class="form-select form-select-sm bg-danger text-white mb-3">
                        <!----------------- Hiển thị menu Thương hiệu ------------------->
                    </select>

                    <!-- Bộ lọc Nồng Độ Cồn -->
                    <label for="alcohol-content-filter" class="form-label text-danger fw-bold">Nồng độ cồn:</label>
                    <select id="alcohol-content-filter" class="form-select form-select-sm bg-danger text-white mb-3">
                        <option value="">Tất cả</option>
                        <option value="1-15">1-15%</option>
                        <option value="15-30">15-30%</option>
                        <option value="30-50">30-50%</option>
                        <option value="50-100">50-100%</option>
                    </select>

                    <!-- Bộ lọc Thể Tích -->
                    <label for="volume-filter" class="form-label text-danger fw-bold">Thể tích:</label>
                    <select id="volume-filter" class="form-select form-select-sm bg-danger text-white mb-3">
                        <option value="">Tất cả</option>
                        <option value="under-500">Dưới 500ml</option>
                        <option value="500-1l">500ml đến 1l</option>
                        <option value="above-1l">1l trở lên</option>
                    </select>

                    <button id="reset-button" class="btn btn-danger">
                        <i class="fa-solid fa-rotate-right"></i>
                    </button>
                </div>
            </div>


            <!-- Cột cho phần sản phẩm -->
            <div class="col-12 col-md-10">
                <section id="product" style="padding: 0 5%;">
                    <div class="products">
                        <!-- Hiển thị vài sản phẩm nổi bật -->
                    </div>
                </section>

                <div id="pagination-container"></div>
            </div>
        </div>
    </div>

    <?php require_once "./Footer.php" ?>

</body>


<script>
    var currentPage = 1; // Track the current page
    var pageSizeGlobal = 12;
    var currentFilters = {
        search: '',
        minPrice: 0,
        maxPrice: 1000000000,
        brandId: 0,
        categoryId: 0,
        minAlcohol: 0,
        maxAlcohol: 0,
        minVolume: 0,
        maxVolume: 0
    };

    // Lấy tham số từ URL
    const urlParams = new URLSearchParams(window.location.search);

    // Lấy giá trị của searchFromAnotherPage và gán vào search
    const searchFromAnotherPage = urlParams.get('searchFromAnotherPage');
    if (searchFromAnotherPage) {
        currentFilters.search = searchFromAnotherPage;
    }

    $(document).ready(function() {
        getCategories(); // Load categories from the server
        getBrands(); // Load brands from the server
        getAllSanPham(currentPage); // Replace filterProducts(currentPage) with getAllSanPham(currentPage)
    });

    // Lắng nghe sự kiện click vào id "reset-button"
    document.getElementById("reset-button").addEventListener("click", function() {
        window.location.href = "Product.php";
    });


    // Lắng nghe sự kiện change cho thanh lọc giá
    document.getElementById("price-filter").addEventListener("change", function() {
        currentPage = 1;

        // Update minPrice and maxPrice based on the selected price range
        const priceFilter = document.getElementById("price-filter").value;
        setPriceRange(priceFilter);

        // Gọi lại hàm lọc sản phẩm khi giá trị thay đổi
        getAllSanPham(currentPage);

    });

    // Lắng nghe sự kiện change cho thanh lọc thương hiệu
    document.getElementById("brand-filter").addEventListener("change", function() {
        currentPage = 1;

        // Update brandId based on the selected brand
        const brandFilter = document.getElementById("brand-filter").value;
        currentFilters.brandId = brandFilter === "" ? 0 : parseInt(brandFilter);

        // Gọi lại hàm lọc sản phẩm khi giá trị thay đổi
        getAllSanPham(currentPage);

    });
    document.getElementById("volume-filter").addEventListener("change", function() {
        currentPage = 1;
        console.log(2)
        const priceFilter = document.getElementById("volume-filter").value;
        setVolume(priceFilter);
        // Gọi lại hàm lọc sản phẩm khi giá trị thay đổi
        getAllSanPham(currentPage);
    });
    document.getElementById("alcohol-content-filter").addEventListener("change", function() {
        currentPage = 1;
        const priceFilter = document.getElementById("alcohol-content-filter").value;
        setAlcohol(priceFilter);
        // Gọi lại hàm lọc sản phẩm khi giá trị thay đổi
        getAllSanPham(currentPage);
    });

    // Lắng nghe sự kiện change cho thanh lọc loại sản phẩm
    document.getElementById("category-filter").addEventListener("change", function() {
        currentPage = 1;

        // Update categoryId based on the selected category
        const categoryFilter = document.getElementById("category-filter").value;
        currentFilters.categoryId = categoryFilter === "" ? 0 : parseInt(categoryFilter);

        // Gọi lại hàm lọc sản phẩm khi giá trị thay đổi
        getAllSanPham(currentPage);

    });

    function setAlcohol(alcoholFilter) {
        switch (alcoholFilter) {
            case "1-15":
                currentFilters.minAlcohol = 1;
                currentFilters.maxAlcohol = 15;
                break;
            case "15-30":
                currentFilters.minAlcohol = 15;
                currentFilters.maxAlcohol = 30;
                break;
            case "30-50":
                currentFilters.minAlcohol = 30;
                currentFilters.maxAlcohol = 50;
                break;
            case "50-100":
                currentFilters.minAlcohol = 50;
                currentFilters.maxAlcohol = 100;
                break;
            default:
                currentFilters.minAlcohol = '';
                currentFilters.maxAlcohol = '';
                break;
        }
    }

    function setVolume(volumeFilter) {
        switch (volumeFilter) {
            case "under-500":
                currentFilters.minVolume = 0;
                currentFilters.maxVolume = 500;
                break;
            case "500-1l":
                currentFilters.minVolume = 500;
                currentFilters.maxVolume = 1000;
                break;
            case "above-1l":
                currentFilters.minVolume = 1000;
                currentFilters.maxVolume = null;
                break;
            default:
                currentFilters.minVolume = '';
                currentFilters.maxVolume = '';
                break;
        }
    }

    function setPriceRange(priceFilter) {
        switch (priceFilter) {
            case "low":
                currentFilters.minPrice = 0;
                currentFilters.maxPrice = 1000000;
                break;
            case "medium":
                currentFilters.minPrice = 1000000;
                currentFilters.maxPrice = 3000000;
                break;
            case "high":
                currentFilters.minPrice = 3000000;
                currentFilters.maxPrice = 1000000000; // Above 3 million, no limit
                break;
            default:
                currentFilters.minPrice = 0;
                currentFilters.maxPrice = 1000000000; // No limit
                break;
        }
    }

    function getAllSanPham(page) {
        $('#loading-indicator').show();

        // If categoryId or brandId is 0, set it to null
        const brandId = currentFilters.brandId === 0 ? null : currentFilters.brandId;
        const categoryId = currentFilters.categoryId === 0 ? null : currentFilters.categoryId;

        $.ajax({
            url: "../../Controllers/ProductController.php",
            method: "GET",
            dataType: "json",
            data: {
                action: "getAllProductsCommonUser",
                pageNumber: page,
                pageSize: pageSizeGlobal,
                search: currentFilters.search,
                minPrice: currentFilters.minPrice,
                maxPrice: currentFilters.maxPrice,
                brandId: brandId,
                categoryId: categoryId,
                minAlcohol: currentFilters.minAlcohol,
                maxAlcohol: currentFilters.maxAlcohol,
                minVolume: currentFilters.minVolume,
                maxVolume: currentFilters.maxVolume
            },
            success: function(response) {
                const productContainer = $('#product .products');
                if (response.data && response.data.length > 0) {
                    let htmlContent = '';
                    $.each(response.data, function(index, product) {
                        htmlContent += `
                                        <form id="productForm_${product.Id}" method="post" action="ProductDetail.php?maSanPham=${product.Id}">
                                            <div class="row">
                                                <a href="ProductDetail.php?maSanPham=${product.Id}" class="text-center" style="display: block; position: relative;">
                                                    <img src="../img/${product.Image}" alt="" style="width:100%;height:300px;">
                                                    <div class="sale-label" ">
                                                                    -${product.Sale === 0 ?"10":product.Sale}% 
                                                                </div>               
                                                <div class="product-card-content">
                                                        <div class="price">
                                                            <h4 class="name-product">${product.ProductName}</h4>`;

                        if (product.Sale === 0) {
                            const inflatedPrice = Math.ceil(product.UnitPrice / (90 / 100));
                            const discountPrice = product.UnitPrice;

                            htmlContent += `
                                                                                <p class="price-tea" style="text-decoration: line-through; color: gray;">
                                                                                    <i class="fas fa-tag"></i> ${formatCurrency(inflatedPrice)}
                                                                                </p>
                                                                                <p class="price-tea" style="color: rgb(146, 26, 26); font-weight: bold;">
                                                                                    <i class="fas fa-percent"></i> ${formatCurrency(discountPrice)}
                                                                                </p>`;
                        } else {
                            const salePrice = product.UnitPrice * (1 - product.Sale / 100);

                            htmlContent += `
                                                                                <p class="price-tea" style="text-decoration: line-through; color: gray;">
                                                                                    <i class="fas fa-tag"></i> ${formatCurrency(product.UnitPrice)}
                                                                                </p>
                                                                                <p class="price-tea" style="color: rgb(146, 26, 26); font-weight: bold;">
                                                                                    <i class="fas fa-percent"></i> ${formatCurrency(salePrice)}
                                                                                </p>`;
                        }

                        htmlContent += `
                                                        </div>
                                                        <div class="buy-btn-container">
                                                            MUA NGAY
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </form>
                                    `;



                    });

                    // Update product list and pagination
                    productContainer.html(htmlContent);
                    setupPagination(response.totalElements, page);

                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });

                } else {
                    productContainer.html('<p style="font-size: 24px; text-align: center;">Không có sản phẩm nào.</p>');
                }

                $('#loading-indicator').hide();
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
                $('#loading-indicator').hide();
                alert("Có lỗi xảy ra khi tải dữ liệu sản phẩm.");
            }
        });
    }

    function setupPagination(totalElements, currentPage) {

        //Kiểm tra xem nếu totalPage ít hơn 1 thì ẩn luôn =))
        const totalPage = Math.ceil(totalElements / pageSizeGlobal);
        totalPage <= 1 ? $('#pagination-container').hide() : $('#pagination-container').show();

        // Config paging
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
                    getAllSanPham(currentPage); // Fetch new data for the selected page
                }
            }
        });

    }

    function getCategories() {
        $.ajax({
            url: "../../controllers/CategoryController.php",
            method: "GET",
            dataType: "json",
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    // Xóa tất cả các option hiện có trong dropdown
                    $('#category-filter').empty();
                    // Thêm option "Tất cả"
                    $('#category-filter').append('<option value="">Tất cả</option>');
                    // Duyệt qua danh sách loại sản phẩm và thêm từng option vào dropdown
                    $.each(response.data, function(index, category) {
                        $('#category-filter').append(`<option value="${category.Id}">${category.CategoryName}</option>`);
                    });
                } else {
                    console.log("Không có loại sản phẩm nào được trả về từ API.");
                }
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });
    }

    function getBrands() {
        $.ajax({
            url: "../../controllers/BrandController.php",
            method: "GET",
            dataType: "json",
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    // Xóa tất cả các option hiện có trong dropdown
                    $('#brand-filter').empty();
                    // Thêm option "Tất cả"
                    $('#brand-filter').append('<option value="">Tất cả</option>');
                    // Duyệt qua danh sách loại sản phẩm và thêm từng option vào dropdown
                    $.each(response.data, function(index, category) {
                        $('#brand-filter').append(`<option value="${category.Id}">${category.BrandName}</option>`);
                    });
                } else {
                    console.log("Không có loại sản phẩm nào được trả về từ API.");
                }
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });
    }

    function detail(maSanPham) {
        window.location.href = `ProductDetail.php?maSanPham=${maSanPham}`;
    }
</script>

</html>