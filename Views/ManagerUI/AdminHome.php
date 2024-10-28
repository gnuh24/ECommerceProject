<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="./AdminHome.css" />
    <title>Admin Home</title>
</head>

<body>
    <div id="root">
        <div class="App">
            <div class="StaffLayout_wrapper__CegPk">
                <?php require_once "./ManagerHeader.php" ?>
                <div class="Manager_wrapper__vOYy">
                    <?php require_once "./ManagerMenu.php" ?>

                    <?php
                    // Check if 'module' parameter exists in the URL
                    if (isset($_GET['module'])) {
                        $module = $_GET['module']; // Get the value of the 'module' parameter

                        // Include the corresponding file based on the module value
                        switch ($module) {
                            case 'product':
                                require_once "./QLSanPham/QLSanPham.php";
                                break;
                            case 'account':
                                require_once "./QLTaiKhoan/QLTaiKhoan.php";
                                break;
                            case 'category':
                                require_once "./QLLoaiSanPham/QLLoaiSanPham.php";
                                break;
                            case 'brand':
                                require_once "./QLThuongHieu/QLThuongHieu.php";
                                break;
                            case 'order':
                                require_once "./QLDonHang/QLDonHang.php";
                                break;
                            case 'bestSeller':
                                require_once "./QLThongKe/ThongKeChiTIeu.php";
                                break;
                            case 'summaryOrder':
                                require_once "./QLThongKe/ThongKeDonHang.php";
                                break;
                            default:
                                // Optional: handle the case where the module does not match any case
                                echo "Invalid module specified.";
                                break;
                        }
                    } else {
                        // Optional: handle the case where 'module' is not set
                        echo "No module specified.";
                    }

                    // // Example of requiring a different file for testing
                    // require_once "./TestQLTaiKhoan.php";
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>