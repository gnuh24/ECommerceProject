<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="./HomePage.css" />
    <!-- <link rel="stylesheet" href="./login.css" /> -->
    <link rel="stylesheet" href="./Product.css" />
    <link rel="stylesheet" href="./components/paginationjs.css" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Include Pagination.js -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.5/pagination.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.5/pagination.min.js"></script>


    <script src="../HelperUI/formatOutput.js"></script>


    <title>Các sản phẩm</title>
</head>

<body>
    <?php require_once "./Header.php"; ?>

    <!-- Thanh lọc menu -->
    <div id="filter-menu" class="container-fluid bg-white p-3 rounded mb-4 d-flex align-items-end">
        <div class="row d-flex justify-content-around" style="width:80%">

            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <label for="price-filter" class="form-label text-danger fw-bold">Giá:</label>
                <select id="price-filter" class="form-select form-select-sm bg-danger text-white">
                    <option value="">Tất cả</option>
                    <option value="low">Dưới 1 triệu</option>
                    <option value="medium">Từ 1 đến 3 triệu</option>
                    <option value="high">Trên 3 triệu</option>
                </select>
            </div>

            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <label for="brand-filter" class="form-label text-danger fw-bold">Thương hiệu :</label>
                <select id="brand-filter" class="form-select form-select-sm bg-danger text-white">
                    <!----------------- Hiển thị menu Loại sản phẩm  ------------------->
                </select>
            </div>

            <div class="col-12 col-md-3 mb-2 mb-md-0">
                <label for="category-filter" class="form-label text-danger fw-bold">Loại sản phẩm:</label>
                <select id="category-filter" class="form-select form-select-sm bg-danger text-white">
                    <!----------------- Hiển thị menu Thương hiệu ------------------->
                </select>
            </div>

        </div>

        <button id="reset-button" class="btn btn-danger mt-2 mt-md-0">
            <i class="fa-solid fa-rotate-right"></i>
        </button>



    </div>


    <section id="product" style="padding: 0 5%;">
        <div class="products">
            <!-- Hiển thị vài sản phẩm nổi bật -->
        </div>

    </section>

    <div id="pagination-container"></div>

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
        categoryId: 0
    };

    $(document).ready(function() {
        getCategories(); // Load categories from the server
        getBrands(); // Load brands from the server
        getAllSanPham(currentPage); // Replace filterProducts(currentPage) with getAllSanPham(currentPage)
    });

    // Lắng nghe sự kiện click vào id "reset-button"
    document.getElementById("reset-button").addEventListener("click", function() {
        // Reset tất cả các thanh lọc về giá trị mặc định
        document.getElementById("searchSanPham").value = "";
        document.getElementById("price-filter").value = "";
        document.getElementById("brand-filter").value = "";
        document.getElementById("category-filter").value = "";

        // Reset the filters in currentFilters object
        currentPage = 1;
        currentFilters.search = '';
        currentFilters.minPrice = 0;
        currentFilters.maxPrice = 1000000000;
        currentFilters.brandId = 0;
        currentFilters.categoryId = 0;

        // Gọi lại hàm filterProducts với các giá trị mặc định
        getAllSanPham(currentPage);
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

    // Lắng nghe sự kiện change cho thanh lọc loại sản phẩm
    document.getElementById("category-filter").addEventListener("change", function() {
        currentPage = 1;

        // Update categoryId based on the selected category
        const categoryFilter = document.getElementById("category-filter").value;
        currentFilters.categoryId = categoryFilter === "" ? 0 : parseInt(categoryFilter);

        // Gọi lại hàm lọc sản phẩm khi giá trị thay đổi
        getAllSanPham(currentPage);

    });

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
                categoryId: categoryId
            },
            success: function(response) {
                const productContainer = $('#product .products');
                if (response.data && response.data.length > 0) {
                    let htmlContent = '';
                    $.each(response.data, function(index, product) {
                        htmlContent += `
                            <form id="productForm_${product.Id}" method="post" action="ProductDetail.php?maSanPham=${product.Id}">
                                <div class="row">
                                    <a href="ProductDetail.php?maSanPham=${product.Id}" class="text-center" style="display: block;">
                                        <img src="http://res.cloudinary.com/djhoea2bo/image/upload/v1711511636/${product.Image}" alt="" style="height: 300px;">
                                        <div class="product-card-content">
                                            <div class="price">
                                                <h4 class="name-product">${product.ProductName}</h4>
                                                <p class="price-tea">${formatCurrency(product.UnitPrice)}</p>
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