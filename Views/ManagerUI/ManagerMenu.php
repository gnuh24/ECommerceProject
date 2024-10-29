<style>
    .Sidebar_sideBar__CC4MK {
        position: fixed;
        height: 100dvh;
        width: 15%;
        background-color: rgba(50, 49, 49, 0.918);
    }

    .MenuItemSidebar_menuItem__56b1m.MenuItemSidebar_active__RPq {
        background-color: #333;
        color: #fff;
    }

    .MenuItemSidebar_menuItem__56b1m {
        display: flex;
        align-items: center;
        padding: 1rem;
        font-size: 1.8rem;
        font-weight: 700;
        color: #fff;
        cursor: pointer;
    }

    .active {
        background-color: white;
        color: black;
    }
</style>
<link rel="stylesheet" href="../fontStyle.css" />

<div class="Sidebar_sideBar__CC4MK">
    <a class="MenuItemSidebar_menuItem__56b1m" href="../QLTaiKhoan/QLTaiKhoan.php">
        <span class="MenuItemSidebar_title__LLBtx arial-bold">Tài khoản</span>
    </a>
    <a class="MenuItemSidebar_menuItem__56b1m" href="../QLLoaiSanPham/QLLoaiSanPham.php">
        <span class="MenuItemSidebar_title__LLBtx arial-bold">Loại Sản Phẩm</span>
    </a>
    <a class="MenuItemSidebar_menuItem__56b1m" href="../QLSanPham/QLSanPham.php">
        <span class="MenuItemSidebar_title__LLBtx arial-bold">Sản Phẩm</span>
    </a>
    <a class="MenuItemSidebar_menuItem__56b1m" href="../QLThuongHieu/QLThuongHieu.php">
        <span class="MenuItemSidebar_title__LLBtx arial-bold">Thương hiệu</span>
    </a>
    <a class="MenuItemSidebar_menuItem__56b1m" href="../QLDonHang/QLDonHang.php">
        <span class="MenuItemSidebar_title__LLBtx arial-bold">Đơn Hàng</span>
    </a>
    <a class="MenuItemSidebar_menuItem__56b1m" href="../ThongKe/ThongKeDoanhThuChiTieu.php">
        <span class="MenuItemSidebar_title__LLBtx arial-bold">Thống Kê bán chạy</span>
    </a>
    <a class="MenuItemSidebar_menuItem__56b1m" href="../ThongKe/ThongKeDonHang.php">
        <span class="MenuItemSidebar_title__LLBtx arial-bold">Thống Kê Đơn Hàng</span>
    </a>
</div>

<script>
    // Lấy URL hiện tại và loại bỏ phần /ECommerceProject/Views/ManagerUI/
    const currentPath = window.location.pathname.replace("/ECommerceProject/Views/ManagerUI/", "");

    // Lấy tất cả các thẻ <a>
    const menuItems = document.querySelectorAll('.MenuItemSidebar_menuItem__56b1m');

    // Lặp qua các thẻ <a> và thêm lớp 'active' nếu href trùng khớp với URL hiện tại
    menuItems.forEach(item => {
        // Lấy giá trị href của thẻ <a> và loại bỏ ../
        const hrefPath = item.getAttribute('href').replace("../", "");

        if (hrefPath === currentPath) {
            item.classList.add('active');
        }
    });
</script>