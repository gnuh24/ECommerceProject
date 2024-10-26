<style>
    /* Card sản phẩm */
    .product-card-content {
        padding: 15px;
        background-color: #fff;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        text-align: center;
    }

    .product-card-content img {
        width: 100%;
        height: auto;
        object-fit: cover;
        border-radius: 8px;
    }

    /* Button Container */
    .row .buy-btn-container {
        margin-top: 16px;
        /* margin-bottom: 12px; */
        color: white;
        /* Màu chữ */
        border: 1px solid rgb(146, 26, 26);
        /* Đường viền màu đỏ */
        background: rgb(146, 26, 26);
        /* Màu nền đỏ */
        padding: 8px 16px;
        cursor: pointer;
        text-transform: uppercase;
        border-radius: 5px;
        /* Bo góc */
        display: inline-block;
        text-align: center;
        /* Căn giữa văn bản */
        font-weight: bold;
        /* Đậm chữ */
        transition: background-color 0.3s, border-color 0.3s;
        /* Hiệu ứng chuyển tiếp khi hover */
    }

    .row .buy-btn-container:hover {
        background-color: white;
        border-color: rgb(146, 26, 26);
        color: rgb(146, 26, 26);
    }

    .row .name-product {
        width: 100%;
        font-family: Muli;
        font-size: 16px;
        font-weight: 700;
        text-align: center;
        margin-top: 18px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        padding: 0 8px;
    }


    .row .price {
        font-family: Muli;
        font-size: 18px;
        font-weight: 700;
        color: #8a733f;
        text-align: center;
        margin-top: 24px;
        margin-bottom: 10px;
    }
</style>
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