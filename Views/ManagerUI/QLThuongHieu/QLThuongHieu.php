<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../AdminHome.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="QLThuongHieu.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

  <!-- Include Pagination.js -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> <!-- jQuery -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.5/pagination.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.5/pagination.css" />

  <title>Quản lý thương hiệu</title>
</head>
<style>
  .paginationjs {
    display: flex;
    justify-content: center;
    margin: 20px 0;
  }
</style>

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
              <a href="./FormCreateThuongHieu.php"> Thêm Thương Hiệu</a>
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
                  <th style="width: 25%" class="Table_th__hCkcg">Mã thương hiệu</th>
                  <th class="Table_th__hCkcg">Thương hiệu</th>
                  <!-- <th class="Table_th__hCkcg">Email</th>
                            <th class="Table_th__hCkcg">Số điện thoại</th> -->
                  <th style="width: 15%" class="Table_th__hCkcg">Action</th>
                </tr>
              </thead>
              <tbody id="tableBody">

              </tbody>
            </table>
          </div>
          <div id="pagination-container">
          </div>
        </div>
      </div>
    </div>
  </div>

</body>


</html>


<script>
  // Khởi tạo trang hiện tại
  fetchDataAndUpdateTable(currentPage, '');
  var currentPage = 1;
  var pageSizeGlobal = 5;
  var search = "";

  // Hàm để xóa hết các dòng trong bảng
  function clearTable() {
    var tableBody = document.querySelector('.Table_table__BWPy tbody');
    tableBody.innerHTML = ''; // Xóa nội dung trong tbody
  }

  var currentPage = 1;
  var pageSizeGlobal = 5;
  var search = "";

  function getAllThuongHieu(page, search) {
    $.ajax({
      url: '../../../Controllers/BrandController.php',
      type: 'GET',
      dataType: "json",
      data: {
        page: page,
        pageSize: pageSizeGlobal,
        search: search
      },
      success: function(response) {
        var data = response.data; // Lấy mảng dữ liệu từ API
        var tableBody = document.getElementById("tableBody"); // Lấy thẻ tbody của bảng
        var tableContent = ""; // Chuỗi chứa nội dung mới của tbody

        // Duyệt qua mảng dữ liệu và tạo các hàng mới cho tbody
        if (data.length > 0) {
          data.forEach(function(record, index) {
            var trClass = (index % 2 !== 0) ? "Table_data_quyen_1" : "Table_data_quyen_2"; // Xác định class của hàng
            var trContent = `
                            <tr style="height: 20%"; max-height: 20%;>
                              <td class="${trClass}">${record.Id}</td>
                              <td class="${trClass}">${record.BrandName}</td>
                              <td class="${trClass}">`;

            if (record.Id == 1) {
              trContent += `Mặc định`;;
            } else {
              trContent += `
                        <button class="edit" onclick="updateThuongHieu(${record.Id}, '${record.BrandName}')">Sửa</button>
                        <button class="delete" onclick="deleteThuongHieu(${record.Id}, '${record.BrandName}')">Xoá</button>`;
            }
            trContent += `</tr>`;
            // Nếu chỉ có ít hơn 10 phần tử và đã duyệt đến phần tử cuối cùng, thêm các hàng trống vào
            if (data.length < 5 && index === data.length - 1) {
              for (var i = data.length; i < 5; i++) {
                var emptyTrClass = (i % 2 !== 0) ? "Table_data_quyen_1" : "Table_data_quyen_2"; // Xác định class của hàng trống
                trContent += `
                                <form id="emptyForm" method="post" action="FormUpdateThuongHieu.php">
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
          tableContent = `<tr ><td style="text-align: center;" colspan="7">Không có thương hiệu nào thỏa yêu cầu</td></tr>`;
        }

        // Thiết lập lại nội dung của tbody bằng chuỗi tableContent
        tableBody.innerHTML = tableContent;

        // Tạo phân trang (giả sử `createPagination` là hàm bạn đã định nghĩa để tạo phân trang)
        setupPagination(response.totalElements, page);
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




  // Hàm để gọi getAllThuongHieu và cập nhật dữ liệu và phân trang
  function fetchDataAndUpdateTable(page, search) {
    //Clear dữ liệu cũ
    clearTable();

    // Gọi hàm getAllTaiKhoan và truyền các giá trị tương ứng
    getAllThuongHieu(page, search);
  }

  // Hàm tạo nút phân trang
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
          currentPage = pagination.pageNumber; // Cập nhật trang hiện tại
          fetchDataAndUpdateTable(currentPage, search); // Tải dữ liệu mới cho trang
        }
      }
    });
  }


  // Hàm xử lý sự kiện khi nút tìm kiếm được click
  document.getElementById('searchButton').addEventListener('click', function() {
    var searchValue = document.querySelector('.Admin_input__LtEE-').value;

    // Truyền giá trị của biến currentPage vào hàm fetchDataAndUpdateTable
    fetchDataAndUpdateTable(currentPage, searchValue, '');
  });

  // Bắt sự kiện khi người dùng ấn phím Enter trong ô tìm kiếm
  document.querySelector('.Admin_input__LtEE-').addEventListener('keypress', function(event) {
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

  function deleteThuongHieu(brandId, brandName) {
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

          success: function(response) {
            Swal.fire({
              icon: 'success',
              title: 'Thành công!',
              text: 'Xóa thương hiệu thành công !!',
            }).then(function() {
              fetchDataAndUpdateTable(currentPage, '');
            });
          },
          error: function(xhr, status, error) {
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



  function updateThuongHieu(brandId, brandName) {
    window.location.href = `FormUpdateThuongHieu.php?brandId=${brandId}&brandName=${brandName}`
  }
</script>