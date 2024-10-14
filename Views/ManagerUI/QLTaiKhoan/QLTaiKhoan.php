<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="oneForAll.css" />
    <link rel="stylesheet" href="Admin.css" />
    <title>Quản lý tài khoản</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div id="root">
        <div>
            <div class="App">
                <div class="StaffLayout_wrapper__CegPk">
                    <?php require_once "../ManagerHeader.php" ?>
                    <div>
                        <div>
                            <div class="Manager_wrapper__vOYy">
                                <?php require_once "../ManagerMenu.php" ?>

                                <div style="padding-left: 16%; width: 100%; padding-right: 2rem">
                                    <div class="wrapper">
                                        <div style="
                          display: flex;
                          padding-top: 1rem;
                          padding-bottom: 1rem;
                        ">
                                            <h2>Quản lý tài khoản</h2>
                                            <!-- <a href="FormCreateTaiKhoan.php" id="createAccountButton">Tạo Tài Khoản</a> -->
                                        </div>
                                        <div class="Admin_boxFeature__ECXnm">
                                            <div style="position: relative;">
                                                <input class="Admin_input__LtEE-" placeholder="Tìm kiếm tài khoản">
                                            </div>
                                            <select id="selectQuyen" style="height: 3rem; padding: 0.3rem;">
                                                <option value="">Trạng thái: tất cả</option>
                                                <option value="1">Hoạt động</option>
                                                <option value="0">Khóa</option>
                                            </select>
                                            <button id="searchButton" style="">Tìm kiếm</button>
                                        </div>
                                        <div class="Admin_boxTable__hLXRJ">
                                            <table class="Table_table__BWPy">
                                                <thead class="Table_head__FTUog">
                                                    <tr>
                                                        <th class="Table_th__hCkcg">Mã Tài Khoản</th>
                                                        <th class="Table_th__hCkcg">Email</th>
                                                        <th class="Table_th__hCkcg">Ngày tạo</th>
                                                        <th class="Table_th__hCkcg">Trạng thái</th>
                                                        <th class="Table_th__hCkcg">Quyền</th>
                                                        <th class="Table_th__hCkcg">Thao tác</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tableBody">

                                                </tbody>
                                            </table>
                                            <div class="pagination"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    // Khởi tạo trang hiện tại
    var currentPage = 1;

    // Lắng nghe sự kiện click trên nút logout
    document.addEventListener('DOMContentLoaded', function() {
        var logoutButton = document.getElementById('logoutButton');
        if (logoutButton) {
            logoutButton.addEventListener('click', function() {
                sessionStorage.removeItem('key');
                window.location.href = '../../MemberUI/Login/AdminLoginUI.php';
            });
        }


        fetchDataAndUpdateTable(currentPage, '', '');
    });

    function clearTable() {
        var tableBody = document.querySelector('.Table_table__BWPy tbody');
        tableBody.innerHTML = ''; // Xóa nội dung trong tbody
    }


    function getAllTaiKhoan(page, search, status) {
        $.ajax({
            url: '../../../Controllers/AccountController.php',
            type: 'GET',
            dataType: "json",

            data: {
                page: page,
                search: search,
                action: 'getAccountById',
                status: status,
            },
            success: function(response) {
                var data = response.data;
                var tableBody = document.getElementById("tableBody"); // Lấy thẻ tbody của bảng
                var tableContent = ""; // Chuỗi chứa nội dung mới của tbody

                if (data.length > 0) {
                    data.forEach(function(record, index) {
                        var trClass = (index % 2 !== 0) ? "Table_data_quyen_1" : "Table_data_quyen_2"; // Xác định class của hàng

                        // Xác định trạng thái và văn bản của nút dựa trên trạng thái của tài khoản
                        var buttonText = (record.Status === 0) ? "Mở khóa" : "Khóa";
                        var buttonClass = (record.Status === 0) ? "unlock" : "block";
                        var buttonData = (record.Status === 0) ? "unlock" : "block";
                        var trContent = `
                        <form id="updateForm" method="post" action="FormUpdateTaiKhoan.php">
                            <tr style="height: 20%"; max-height: 20%;>
                                <td class="${trClass}" style="width: 130px;">${record.Id}</td>
                                <td class="${trClass}">${record.Email}</td>
                                <td class="${trClass}">${record.CreateTime}</td>
                                <td class="${trClass}">${record.Status === 0 ? "Khóa" : "Hoạt động"}</td>
                                <td class="${trClass}">${record.Role}</td>`;

                        if (record.Role === "User") {
                            trContent += `<td class="${trClass}">
                                        <button class="${buttonClass}" data-action="${buttonData}" onClick="handleLockUnlock(${record.Id}, ${record.Status})">${buttonText}</button>
                                    </td>`;
                        }



                        trContent += `</tr></form>`;
                        // Nếu chỉ có ít hơn 5 phần tử và đã duyệt đến phần tử cuối cùng, thêm các hàng trống vào
                        if (data.length < 5 && index === data.length - 1) {
                            for (var i = data.length; i < 5; i++) {
                                var emptyTrClass = (i % 2 !== 0) ? "Table_data_quyen_1" : "Table_data_quyen_2"; // Xác định class của hàng trống
                                trContent += `
                                <form id="emptyForm" method="post" action="FormUpdateTaiKhoan.php">
                                    <tr style="height: 20%"; max-height: 20%;>
                                        <td class="${emptyTrClass}" style="width: 130px;"></td>
                                        <td class="${emptyTrClass}"></td>
                                        <td class="${emptyTrClass}"></td>
                                        <td class="${emptyTrClass}"></td>
                                        <td class="${emptyTrClass}"></td>
                                        <td class="${emptyTrClass}"></td>
                                        <td class="${emptyTrClass}"></td>
                                    </tr>
                                </form>`;
                            }
                        }
                        tableContent += trContent; // Thêm nội dung của hàng vào chuỗi tableContent
                    });
                } else {
                    tableContent = `<tr ><td style="text-align: center;" colspan="7">Không có tài khoản nào thỏa yêu cầu</td></tr>`;
                }


                // Thiết lập lại nội dung của tbody bằng chuỗi tableContent
                tableBody.innerHTML = tableContent;


                // Tạo phân trang
                createPagination(page, response.totalPages);
            },
            error: function(xhr, status, error) {
                if (xhr.status === 401) {
                    alert('Phiên đăng nhập của bạn đã hết hạn. Vui lòng đăng nhập lại.');
                    window.location.href = '/login'; // Chuyển hướng đến trang đăng nhập
                } else {
                    console.error('Lỗi khi gọi API: ', error);
                }
            }
        });
    }


    function fetchDataAndUpdateTable(page, search, status) {
        //Clear dữ liệu cũ
        clearTable();

        getAllTaiKhoan(page, search, status);
    }

    // Hàm tạo nút phân trang
    function createPagination(currentPage, totalPages) {
        var paginationContainer = document.querySelector('.pagination');
        var searchValue = document.querySelector('.Admin_input__LtEE-').value;
        var quyenValue = document.querySelector('#selectQuyen').value;
        // Xóa nút phân trang cũ (nếu có)
        paginationContainer.innerHTML = '';
        if (totalPages > 1) {
            // Tạo nút cho từng trang và thêm vào chuỗi HTML
            var paginationHTML = '';
            for (var i = 1; i <= totalPages; i++) {
                paginationHTML += '<button class="pageButton">' + i + '</button>';
            }
            // Thiết lập nút phân trang vào paginationContainer
            paginationContainer.innerHTML = paginationHTML;
            // Thêm sự kiện click cho từng nút phân trang
            paginationContainer.querySelectorAll('.pageButton').forEach(function(button, index) {
                button.addEventListener('click', function() {

                    fetchDataAndUpdateTable(index + 1, searchValue, quyenValue); // Thêm 1 vào index để chuyển đổi về trang 1-indexed
                });
            });

            // Đảm bảo rằng currentPage nằm trong phạm vi hợp lệ
            if (currentPage >= 1 && currentPage <= totalPages) {
                // Đánh dấu trang hiện tại
                paginationContainer.querySelector('.pageButton:nth-child(' + currentPage + ')').classList.add('active');
            } else {
                console.error('currentPage is out of bounds');
            }
        }
    }


    // Hàm xử lý sự kiện khi select Quyen thay đổi
    document.querySelector('#selectQuyen').addEventListener('change', function() {
        var searchValue = document.querySelector('.Admin_input__LtEE-').value;
        var quyenValue = this.value;
        fetchDataAndUpdateTable(currentPage, searchValue, quyenValue);
    });
    // Hàm xử lý sự kiện khi nút tìm kiếm được click
    document.getElementById('searchButton').addEventListener('click', function() {
        var searchValue = document.querySelector('.Admin_input__LtEE-').value;
        var quyenValue = document.querySelector('#selectQuyen').value;
        fetchDataAndUpdateTable(currentPage, searchValue, quyenValue);
    });
    // Bắt sự kiện khi người dùng ấn phím Enter trong ô tìm kiếm
    document.querySelector('.Admin_input__LtEE-').addEventListener('keypress', function(event) {
        // Kiểm tra xem phím được ấn có phải là Enter không (mã phím 13)
        if (event.key === 'Enter') {
            // Ngăn chặn hành động mặc định của phím Enter (ví dụ: gửi form)
            event.preventDefault();
            // Lấy giá trị của ô tìm kiếm và của select quyền
            var searchValue = this.value;
            var quyenValue = document.querySelector('#selectQuyen').value;
            fetchDataAndUpdateTable(currentPage, searchValue, quyenValue);
        }
    });

    // Hàm xử lý sự kiện cho nút khóa / mở khóa
    function handleLockUnlock(maTaiKhoan, trangThai) {
        // Xác định trạng thái mới dựa trên trạng thái hiện tại
        var newTrangThai = trangThai === 0 ? 1 : 0;

        Swal.fire({
            title: `Bạn có muốn ${newTrangThai === 0 ? 'khóa' : 'mở khóa'} tài khoản ${maTaiKhoan} không?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Đồng ý',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                var formData = new FormData();
                formData.append('Id', maTaiKhoan);
                formData.append('Status', newTrangThai); // Gửi đúng giá trị trạng thái mới (0 hoặc 1)
                formData.append('action', 'updateAccount'); // Gửi thêm action cho server biết

                $.ajax({
                    url: '../../../Controllers/AccountController.php',
                    type: 'POST',
                    dataType: 'json',

                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.status === 200) {
                            var alertContent = newTrangThai === 0 ? "khóa" : "mở khóa";
                            Swal.fire('Thành công!', `Bạn đã ${alertContent} thành công !!`, 'success');
                            fetchDataAndUpdateTable(currentPage, "", null);
                        } else {
                            Swal.fire('Lỗi!', 'Đã xảy ra lỗi khi cập nhật trạng thái.', 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Lỗi khi gọi API: ', error);
                    }
                });
            }
        });
    }
</script>

</html>