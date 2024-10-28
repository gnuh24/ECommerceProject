<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="../AdminHome.css" />
  <link rel="stylesheet" href="../QLLoaiSanPham/QLLoaiSanPham.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.5/pagination.css" />
  <!-- PaginationJS CSS -->

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script> <!-- jQuery -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.5/pagination.min.js"></script>
  <!-- PaginationJS -->

  <title>Quản lý loại sản phẩm</title>
</head>
<style>
  .paginationjs {
    display: flex;
    justify-content: center;
    margin: 20px 0;
  }
</style>

<body>
  <div class="App">
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
              <h2>Loại Sản Phẩm</h2>
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
                <a href="./FromCreateLoaiSanPham.php"> Thêm Loại Sản Phẩm</a>
              </button>
            </div>
            <br>
            <div class="boxFeature">
              <div style="position: relative">
                <i class="fa fa-search"></i>
                <input class="Admin_input__LtEE-" placeholder="Tìm kiếm loại sản phẩm" />
                <button id="searchButton" style="cursor: pointer;"><i class="fa fa-search"></i></button>
              </div>


              <div style="margin-left: auto"></div>
            </div>
            <br>
            <table class="Table_table__BWPy">
              <thead class="Table_head__FTUog">
                <tr>
                  <th style="width: 25%" class="Table_th__hCkcg" scope="col">Mã loại sản phẩm</th>
                  <th class="Table_th__hCkcg" scope="col">Loại sản phẩm</th>
                  <th style="width: 15%" class="Table_th__hCkcg" scope="col">Action</th>
                </tr>
              </thead>
              <tbody id="tableBody">

              </tbody>
            </table>
            <!-- </div> -->
            <div id="pagination-container"></div>

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

  function clearTable() {
    var tableBody = document.querySelector('.Table_table__BWPy tbody');
    if (tableBody) {
      tableBody.innerHTML = ''; // Xóa nội dung trong tbody
    } else {
      console.error("Table body not found.");
    }
  }

  function getAllLoaiSanPham(page, search) {
    $.ajax({
      url: "../../../Controllers/CategoryController.php",
      type: "GET",
      dataType: "json",

      data: {
        page: page,
        pageSize: pageSizeGlobal,
        search: search
      },
      success: function(response) {
        var data = response.data;
        var tableBody = document.getElementById("tableBody");
        var tableContent = "";
        // Duyệt qua mảng dữ liệu và tạo các hàng mới cho tbody
        if (data.length > 0) {
          data.forEach(function(record, index) {
            var trClass = (index % 2 !== 0) ? "Table_data_quyen_1" : "Table_data_quyen_2"; // Xác định class của hàng

            var trContent = `
                        <form id="updateForm" method="post" action="FormUpdateLoaiSanPham.php">
                            <tr style="height: 20%"; max-height: 20%;>
                            <td class="${trClass}">${record.Id}</td>
                            <td class="${trClass}">${record.CategoryName}</td>
                            <td class="${trClass}">`;

            if (record.Id == 1) {
              trContent += `Mặc định`;;
            } else {
              trContent += `
                        <button class="edit" onclick="updateLoaiSanPham(${record.Id}, '${record.CategoryName}')">Sửa</button>
                        <button class="delete" onclick="deleteLoaiSanPham(${record.Id}, '${record.CategoryName}')">Xoá</button>`;
            }
            trContent += `</tr></form>`;
            // Nếu chỉ có ít hơn 5 phần tử và đã duyệt đến phần tử cuối cùng, thêm các hàng trống vào
            if (data.length < 5 && index === data.length - 1) {
              for (var i = data.length; i < 5; i++) {
                var emptyTrClass = (i % 2 !== 0) ? "Table_data_quyen_1" : "Table_data_quyen_2"; // Xác định class của hàng trống
                trContent += `
                                <form id="emptyForm" method="post" action="FormUpdateLoaiSanPham.php">
                                    <tr style="height: 20%"; max-height: 20%;>
                                        <td class="${emptyTrClass}" style="width: 130px;"></td>
                                        <td class="${emptyTrClass}"></td>
                                        <td class="${emptyTrClass}"></td>
                                    </tr>
                                </form>`;
              }
            }
            tableContent += trContent; // Thêm nội dung của hàng vào chuỗi tableContent
          });
        } else {
          tableContent = `<tr ><td style="text-align: center;" colspan="7">Không có loại sản phẩm nào thỏa yêu cầu</td></tr>`;
        }

        // Thiết lập lại nội dung của tbody bằng chuỗi tableContent
        tableBody.innerHTML = tableContent;

        // Tạo phân trang
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



  // Hàm để gọi getAllLoaiSanPham và cập nhật dữ liệu và phân trang
  function fetchDataAndUpdateTable(page, search) {
    clearTable();
    // Gọi hàm getAllLoaiSanPham và truyền các giá trị tương ứng
    getAllLoaiSanPham(page, search);
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
    fetchDataAndUpdateTable(currentPage, searchValue);
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

  function deleteLoaiSanPham(id, categoryName) {
    // Sử dụng SweetAlert2 thay vì hộp thoại confirm
    Swal.fire({
      title: `Bạn có muốn xóa ${categoryName} không?`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Đồng ý',
      cancelButtonText: 'Hủy'
    }).then((result) => {
      if (result.isConfirmed) {
        // Gọi Ajax để xóa loại sản phẩm
        $.ajax({
          url: `../../../Controllers/CategoryController.php?id=${id}`, // Chèn đúng ID vào URL
          type: 'DELETE',

          success: function(response) {
            // Hiển thị thông báo thành công bằng SweetAlert2
            Swal.fire('Thành công!', 'Xóa loại sản phẩm thành công !!', 'success').then(() => {
              fetchDataAndUpdateTable(currentPage, ''); // Cập nhật bảng sau khi xóa
            });
          },
          error: function(xhr, status, error) {
            // Hiển thị thông báo lỗi nếu có
            Swal.fire('Lỗi!', 'Đã xảy ra lỗi khi xóa loại sản phẩm.', 'error');
            console.error('Lỗi khi gọi API: ', xhr, status, error);
          }
        });
      }
    });
  }


  function updateLoaiSanPham(id, categoryName) {

    // Lấy ra form bằng id của nó
    var form = document.querySelector("#updateForm");

    window.location.href = `FormUpdateLoaiSanPham.php?id=${id}&categoryName=${categoryName}`;

    // Gửi form đi
    form.submit();

  }
</script>