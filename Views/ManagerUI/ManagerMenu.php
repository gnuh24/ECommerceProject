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
    <a class="MenuItemSidebar_menuItem__56b1m" id="QLTaiKhoan" href="../QLTaiKhoan/QLTaiKhoan.php">
        <span class="MenuItemSidebar_title__LLBtx arial-bold">Tài khoản</span>
    </a>
    <a class="MenuItemSidebar_menuItem__56b1m" id="QLLoaiSanPham" href="../QLLoaiSanPham/QLLoaiSanPham.php">
        <span class="MenuItemSidebar_title__LLBtx arial-bold">Loại Sản Phẩm</span>
    </a>
    <a class="MenuItemSidebar_menuItem__56b1m" id="QLSanPham" href="../QLSanPham/QLSanPham.php">
        <span class="MenuItemSidebar_title__LLBtx arial-bold">Sản Phẩm</span>
    </a>
    <a class="MenuItemSidebar_menuItem__56b1m" id="QLThuongHieu" href="../QLThuongHieu/QLThuongHieu.php">
        <span class="MenuItemSidebar_title__LLBtx arial-bold">Thương hiệu</span>
    </a>
    <a class="MenuItemSidebar_menuItem__56b1m" id="QLKhuyenMai" href="../QLKhuyenMai/QLKhuyenMai.php">
        <span class="MenuItemSidebar_title__LLBtx arial-bold">Khuyến mãi</span>
    </a>
    <a class="MenuItemSidebar_menuItem__56b1m" id="QLDonHang" href="../QLDonHang/QLDonHang.php">
        <span class="MenuItemSidebar_title__LLBtx arial-bold">Đơn Hàng</span>
    </a>
    <a class="MenuItemSidebar_menuItem__56b1m" id="ThongKeBanChay" href="../ThongKe/ThongKeDoanhThuChiTieu.php">
        <span class="MenuItemSidebar_title__LLBtx arial-bold">Thống Kê bán chạy</span>
    </a>
    <a class="MenuItemSidebar_menuItem__56b1m" id="ThongKeDonHang" href="../ThongKe/ThongKeDonHang.php">
        <span class="MenuItemSidebar_title__LLBtx arial-bold">Thống Kê Đơn Hàng</span>
    </a>
</div>

<script>

    const roleSession = sessionStorage.getItem("role");
    const currentPath = window.location.pathname.replace("/ECommerceProject/Views/ManagerUI/", "");
    const menuItems = document.querySelectorAll('.MenuItemSidebar_menuItem__56b1m');

    menuItems.forEach(item => {
        // Lấy giá trị href của thẻ <a> và loại bỏ ../
        const hrefPath = item.getAttribute('href').replace("../", "");

        if (hrefPath === currentPath) {
            item.classList.add('active');
        }
    });
</script>