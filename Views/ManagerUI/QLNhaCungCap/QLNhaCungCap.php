<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../AdminDemo.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="QLNhaCungCap.css" />
  <!-- <link rel="stylesheet" href="../bootstrap-5.3.2-dist/css/bootstrap.min.css"> -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <title>Quản lý thương hiệu</title>
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
                      <h2>Thương Hiệu</h2>
                      <button style="
                            margin-left: auto;
                            font-family: Arial;
                            font-size: 1.5rem;
                            font-weight: 700;
                            color: white;
                            background-color: rgb(65, 64, 64);
                            padding: 1rem;
                            border-radius: 0.6rem;
                            cursor: pointer;
                          ">
                        <a href="./FormCreateNhaCungCap.php"> Thêm Thương Hiệu</a>
                      </button>
                    </div>
                    <br>

                    <div class="boxFeature">
                      <div style="position: relative">
                        <input class="Admin_input__LtEE-" placeholder="Tìm kiếm thương hiệu" />
                        <button id="searchButton" style="cursor: pointer;"><i class="fa fa-search"></i></button>
                      </div>
                      <div style="margin-left: auto"></div>
                    </div>

                    <br>
                    <div class="boxTable">
                      <table class="Table_table__BWPy">
                        <thead class="Table_head__FTUog">
                          <tr>
                            <th class="Table_th__hCkcg">Mã thương hiệu</th>
                            <th class="Table_th__hCkcg">Thương hiệu</th>
                            <!-- <th class="Table_th__hCkcg">Email</th>
                            <th class="Table_th__hCkcg">Số điện thoại</th> -->
                            <th class="Table_th__hCkcg">Xoá</th>
                          </tr>
                        </thead>
                        <tbody id="tableBody">

                        </tbody>
                      </table>
                    </div>
                    <div class="pagination">
                      <?php
                      for ($i = 1; $i <= $totalPage; $i++) {
                        echo '<button class="pageButton" onclick="fetchDataAndUpdateTable(' . $i . ')">' . $i . '</button>';
                      }
                      ?>
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


</html>


<script>
  // Hàm để xóa hết các dòng trong bảng
  function clearTable() {
    var tableBody = document.querySelector('.Table_table__BWPy tbody');
    tableBody.innerHTML = ''; // Xóa nội dung trong tbody
  }


  function getAllNhaCungCap(page, search) {
    $.ajax({
      url: '../../../Controllers/BrandController.php',
      type: 'GET',
      dataType: "json",

      data: {
        page: page,
        search: search
      },
      success: function (response) {
        var data = response.data; // Lấy mảng dữ liệu từ API
        var tableBody = document.getElementById("tableBody"); // Lấy thẻ tbody của bảng
        var tableContent = ""; // Chuỗi chứa nội dung mới của tbody

        // Duyệt qua mảng dữ liệu và tạo các hàng mới cho tbody
        if (data.length > 0) {
          data.forEach(function (record, index) {
            var trClass = (index % 2 !== 0) ? "Table_data_quyen_1" : "Table_data_quyen_2"; // Xác định class của hàng
            var trContent = `
                        <form id="updateForm" method="post" action="FormUpdateNhaCungCap.php">
                            <tr style="height: 20%"; max-height: 20%;>
                            <td class="${trClass}">${record.Id}</td>
                            <td class="${trClass}">${record.BrandName}</td>
                            <td class="${trClass}">`;

            if (record.Id == 1) {
              trContent += `Mặc định`;;
            } else {
              trContent += `
                        <button class="edit" onclick="updateNhaCungCap(${record.Id}, '${record.BrandName}')">Sửa</button>
                        <button class="delete" onclick="deleteNhaCungCap(${record.Id}, '${record.BrandName}')">Xoá</button>`;
            }
            trContent += `</tr></form>`;
            // Nếu chỉ có ít hơn 10 phần tử và đã duyệt đến phần tử cuối cùng, thêm các hàng trống vào
            if (data.length < 5 && index === data.length - 1) {
              for (var i = data.length; i < 5; i++) {
                var emptyTrClass = (i % 2 !== 0) ? "Table_data_quyen_1" : "Table_data_quyen_2"; // Xác định class của hàng trống
                trContent += `
                                <form id="emptyForm" method="post" action="FormUpdateNhaCungCap.php">
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

        // Tạo phân trang (giả sử `createPagination` là hàm bạn đã định nghĩa để tạo phân trang)
        createPagination(page, response.totalPages);
      },

      error: function (xhr, status, error) {
        if (xhr.status === 401) {
          alert('Phiên đăng nhập của bạn đã hết hạn. Vui lòng đăng nhập lại.');
          window.location.href = '/login'; // Chuyển hướng đến trang đăng nhập
        } else {
          console.error('Lỗi khi gọi API: ', error);
        }
      }
    });
  }




  // Hàm để gọi getAllNhaCungCap và cập nhật dữ liệu và phân trang
  function fetchDataAndUpdateTable(page, search) {
    //Clear dữ liệu cũ
    clearTable();

    // Gọi hàm getAllTaiKhoan và truyền các giá trị tương ứng
    getAllNhaCungCap(page, search);

    // Tạo phân trang
    createPagination(page);
  }

  // Khởi tạo trang hiện tại
  var currentPage = 1;
  fetchDataAndUpdateTable(currentPage, '');

  // Hàm tạo nút phân trang
  function createPagination(currentPage, totalPages) {
    var paginationContainer = document.querySelector('.pagination');
    var searchValue = document.querySelector('.Admin_input__LtEE-').value;

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
      paginationContainer.querySelectorAll('.pageButton').forEach(function (button, index) {
        button.addEventListener('click', function () {
          // Gọi hàm fetchDataAndUpdateTable khi người dùng click vào nút phân trang
          fetchDataAndUpdateTable(index + 1, searchValue); // Thêm 1 vào index để chuyển đổi về trang 1-indexed
        });
      });

      // Đánh dấu trang hiện tại
      paginationContainer.querySelector('.pageButton:nth-child(' + currentPage + ')').classList.add('active'); // Sửa lại để chỉ chọn trang hiện tại
    }
  }



  // Hàm xử lý sự kiện khi nút tìm kiếm được click
  document.getElementById('searchButton').addEventListener('click', function () {
    var searchValue = document.querySelector('.Admin_input__LtEE-').value;

    // Truyền giá trị của biến currentPage vào hàm fetchDataAndUpdateTable
    fetchDataAndUpdateTable(currentPage, searchValue, '');
  });

  // Bắt sự kiện khi người dùng ấn phím Enter trong ô tìm kiếm
  document.querySelector('.Admin_input__LtEE-').addEventListener('keypress', function (event) {
    // Kiểm tra xem phím được ấn có phải là Enter không (mã phím 13)
    if (event.key === 'Enter') {
      // Ngăn chặn hành động mặc định của phím Enter (ví dụ: gửi form)
      event.preventDefault();

      // Lấy giá trị của ô tìm kiếm và của select quyền
      var searchValue = this.value;

      // Truyền giá trị của biến currentPage vào hàm fetchDataAndUpdateTable
      fetchDataAndUpdateTable(currentPage, searchValue);
    }
  });

  function deleteNhaCungCap(brandId, brandName) {
    // Sử dụng Swal thay vì hộp thoại confirm
    Swal.fire({
      title: `Bạn có muốn xóa  ${brandName} không?`,
      text: "Hành động này sẽ không thể hoàn tác!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Xóa',
      cancelButtonText: 'Hủy'
    }).then((result) => {
      // Nếu người dùng nhấn nút Xóa
      if (result.isConfirmed) {
        // Thực hiện gọi Ajax để xóa nhà cung cấp
        $.ajax({
          url: '../../../Controllers/BrandController.php?id=' + brandId,
          type: 'DELETE',

          success: function (response) {
            Swal.fire({
              icon: 'success',
              title: 'Thành công!',
              text: 'Xóa thương hiệu thành công !!',
            }).then(function () {
              fetchDataAndUpdateTable(currentPage, '');
            });
          },
          error: function (xhr, status, error) {
            Swal.fire({
              icon: 'error',
              title: 'Lỗi!',
              text: 'Đã xảy ra lỗi khi gọi API',
            });
            console.error('Lỗi khi gọi API: ', xhr, status, error);
          }
        });
      }
    });
  }



  function updateNhaCungCap(brandId, brandName) {
    // Lấy ra form bằng id của nó
    var form = document.querySelector("#updateForm");

    window.location.href = `FormUpdateNhaCungCap.php?brandId=${brandId}&brandName=${brandName}`

    // Gửi form đi
    form.submit();

  }
</script>