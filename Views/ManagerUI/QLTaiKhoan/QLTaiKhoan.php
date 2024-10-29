<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Quản lý tài khoản</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

     <!-- Include Pagination.js -->
     <link rel="stylesheet" href="../../MemberUI/components/paginationjs.css" />

     <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.5/pagination.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.5/pagination.min.js"></script>

    <link rel="stylesheet" href="oneForAll.css" />
    <link rel="stylesheet" href="Admin.css" />


</head>

<body>

                <div class="StaffLayout_wrapper__CegPk">
                    <?php require_once "../ManagerHeader.php" ?>
               
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
                                     
                                            <select id="selectRole" style="height: 3rem; padding: 0.3rem;">
                                                <option value="">Tất cả quyền</option>
                                                <option value="Manager">Manager</option>
                                                <option value="Admin">Admin</option>
                                                <option value="User">User</option>
                                                <option value="Employee">Employee</option>
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
                                                <tbody id="tableBody1">

                                                </tbody>
                                            </table>
                                            <div id="pagination-container"></div>
                                            </div>
                                    </div>
                                </div>
                            </div>
    
                </div>
      
</body>


<script>
    // Khởi tạo trang hiện tại
    var currentPage = 1;
    var pageSizeGlobal = 5;
    var search = "";
    var status = "";
    var role = "";
    
    // Lắng nghe sự kiện click trên nút logout
    document.addEventListener('DOMContentLoaded', function() {
      fetchDataAndUpdateTable(currentPage, '', '');
    });

    function clearTable() {
        var tableBody = document.querySelector('.Table_table__BWPy tbody');
        tableBody.innerHTML = ''; // Xóa nội dung trong tbody
    }


    function getAllTaiKhoan(page) {
    $.ajax({
        url: '../../../Controllers/AccountController.php',
        type: 'GET',
        dataType: "json",
        data: {
            page: page,
            pageSize:pageSizeGlobal,
            search: search,
            action: 'getAccountById',
            status: status,
            role: role  // Thêm role vào dữ liệu gửi lên server
        },
        success: function(response) {
            var data = response.data;
            var tableBody = document.getElementById("tableBody1"); // Lấy thẻ tbody của bảng
            var tableContent = ""; // Chuỗi chứa nội dung mới của tbody

            if (data.length > 0) {
                data.forEach(function(record, index) {
                    var trClass = (index % 2 !== 0) ? "Table_data_quyen_1" : "Table_data_quyen_2"; // Xác định class của hàng

                    // Xác định trạng thái và văn bản của nút dựa trên trạng thái của tài khoản
                    var buttonText = (record.Status === 0) ? "Mở khóa" : "Khóa";
                    var buttonClass = (record.Status === 0) ? "unlock" : "block";
                    var buttonData = (record.Status === 0) ? "unlock" : "block";
                    var trContent = `
                    <form id="updateForm${record.Id}" method="post" action="FormUpdateTaiKhoan.php">
                      <tr style="height: 20%" ; max-height:=20%;>
                            <td class="${trClass}" style="width: 130px;">${record.Id}</td>
                            <td class="${trClass}">${record.Email}</td>
                            <td class="${trClass}">${record.CreateTime}</td>
                            <td class="${trClass}">${record.Status === 0 ? "Khóa" : "Hoạt động"}</td>`;
                    
                            if (record.Role === "Admin") {
                                // Hiển thị quyền cho tài khoản là Admin
                                trContent += `<td class="${trClass}">${record.Role}</td>`;
                                trContent += `<td class="${trClass}">
                                                <button class="${buttonClass}" data-action="${buttonData}" onClick="handleLockUnlock(${record.Id}, ${record.Status})">${buttonText}</button>
                                            </td>`;
                            } else {
                                // Hiển thị thẻ select cho tài khoản không phải User hoặc Admin
                                trContent += `<td class="${trClass}">
                                                <select class="role-select" id="roleSelect${record.Id}" 
                                                     onchange="confirmRoleChange(${record.Id}, this.value, '${record.Role}')" 
                                                        style="font-size: 14px; padding: 4px 8px; height: auto; min-height: 36px;">
                                                        <option value="User" ${record.Role === "User" ? "selected" : ""}>User</option>
                                                        <option value="Manager" ${record.Role === "Manager" ? "selected" : ""}>Manager</option>
                                                        <option value="Employee" ${record.Role === "Employee" ? "selected" : ""}>Employee</option>
                                                </select>
                                            </td>`;

                                trContent += `<td class="${trClass}">
                                                <button class="${buttonClass}" data-action="${buttonData}" onClick="handleLockUnlock(${record.Id}, ${record.Status})">${buttonText}</button>
                                            </td>`;
                            }


                    trContent += `</tr></form>`;
                    tableContent += trContent; // Thêm nội dung của hàng vào chuỗi tableContent

                    setupPagination(response.totalElements, page);
                });
            } else {
                tableContent = `<tr><td style="text-align: center;" colspan="7">Không có tài khoản nào thỏa yêu cầu</td></tr>`;
            }

            // Thiết lập lại nội dung của tbody bằng chuỗi tableContent
            tableBody.innerHTML = tableContent;

            // Tạo phân trang
            setupPagination(response.totalElements, page);
        },
        error: function(xhr, status, error) {
            if (xhr.status === 401) {
                alert('Phiên đăng nhập của bạn đã hết hạn. Vui lòng đăng nhập lại.');
                window.location.href = '/login';
            } else {
                console.error('Lỗi khi gọi API: ', error);
            }
        }
    });
}



function confirmRoleChange(accountId, newRole, oldRole) {
    const confirmation = confirm("Bạn có chắc chắn muốn thay đổi quyền thành " + newRole + " không?");
    const selectElement = document.getElementById("roleSelect" + accountId);

    if (confirmation) {
        // Gửi yêu cầu cập nhật quyền mới lên server
        $.ajax({
            url: '../../../Controllers/AccountController.php',
            type: 'POST',
            data: {
                action: 'updateRole',
                Id: accountId,
                Role: newRole
            },
            success: function(response) {
                alert("Cập nhật quyền thành công!");
            },
            error: function(xhr, status, error) {
                alert("Có lỗi xảy ra trong quá trình cập nhật quyền.");
                console.error('Lỗi khi gọi API: ', error);
            }
        });
    } else {
        // Nếu người dùng không đồng ý, reset giá trị select về giá trị cũ
        selectElement.value = oldRole;  // Khôi phục giá trị cũ
    }
}


    function fetchDataAndUpdateTable(page) {
        clearTable();
        getAllTaiKhoan(page);
    }


   function setupPagination(totalElements, currentPage) {

        //Kiểm tra xem nếu totalPage ít hơn 1 thì ẩn luôn =))
        const totalPage = Math.ceil(totalElements / pageSizeGlobal);
        totalPage <= 1 ? $('#pagination-container').hide() : $('#pagination-container').show();

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
                    getAllTaiKhoan(currentPage);                
                }
            }
        });
    }


   // Hàm xử lý sự kiện khi select Quyen thay đổi
document.querySelector('#selectQuyen').addEventListener('change', function() {
    status = this.value;
    fetchDataAndUpdateTable(currentPage);
});

// Hàm xử lý sự kiện khi nút tìm kiếm được click
document.getElementById('searchButton').addEventListener('click', function() {
    search= document.querySelector('.Admin_input__LtEE-').value;
    fetchDataAndUpdateTable(currentPage);
});

// Hàm xử lý sự kiện khi select Role thay đổi
document.querySelector('#selectRole').addEventListener('change', function() {
    role = this.value;
    fetchDataAndUpdateTable(currentPage); 
});

// Bắt sự kiện khi người dùng ấn phím Enter trong ô tìm kiếm
document.querySelector('.Admin_input__LtEE-').addEventListener('keypress', function(event) {
    // Kiểm tra xem phím được ấn có phải là Enter không (mã phím 13)
    if (event.key === 'Enter') {
      
        event.preventDefault();
        // Lấy giá trị của ô tìm kiếm và của select quyền
        search= document.querySelector('.Admin_input__LtEE-').value;
        fetchDataAndUpdateTable(currentPage); // Gọi hàm với 4 tham số
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