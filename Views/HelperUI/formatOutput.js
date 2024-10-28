
/*
    FILE NÀY CHỨA TẤT CẢ CÁC HÀM FORMAT CẤU TRÚC ĐẦU RA NHƯ: NGÀY THÁNG NĂM, TIỀN TỆ

*/

// Hàm format tiền tệ sang Vnđ
function formatCurrency(number) {
    // Chuyển đổi số thành chuỗi và đảm bảo nó là số nguyên
    number = parseInt(number);

    // Sử dụng hàm toLocaleString() để định dạng số tiền và thêm đơn vị tiền tệ "đ" vào cuối chuỗi
    return number.toLocaleString('vi-VN', {
        style: 'currency',
        currency: 'VND'
    });
}

// // Hàm format ngày về dạng yyyy-MM-dd
// function formatDateToYYYYMMDD(dateString) {
//     var date = new Date(dateString);
//     var year = date.getFullYear();
//     var month = (date.getMonth() + 1).toString().padStart(2, '0');
//     var day = date.getDate().toString().padStart(2, '0');
//     return `${year}-${month}-${day}`;
// }

function formatDateToYYYYMMDD(dateString) {
    // Kiểm tra nếu dữ liệu đầu vào rỗng hoặc không hợp lệ
    if (!dateString || dateString.trim() === '') {
        return ''; // Trả về rỗng nếu đầu vào không hợp lệ
    }

    var parts = dateString.split('/'); // Tách chuỗi theo dấu '/'

    // Kiểm tra nếu định dạng ngày không đúng (không đủ 3 phần: ngày, tháng, năm)
    if (parts.length !== 3) {
        return ''; // Trả về rỗng nếu không đúng định dạng
    }

    var day = parts[0]; // Ngày
    var month = parts[1]; // Tháng
    var year = parts[2]; // Năm

    // Trả về định dạng yyyy-MM-dd
    return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
}

// Hàm format ngày từ yyyy-MM-dd về dạng dd/MM/yyyy để sử dụng trong input type="date"
function formatDateToDDMMYYYY(dateString) {
    // Tách chuỗi theo dấu gạch ngang "-"
    var parts = dateString.split("-");

    // Đảm bảo chuỗi có đủ các phần (năm, tháng, ngày)
    if (parts.length === 3) {
        var year = parts[0];
        var month = parts[1];
        var day = parts[2];

        // Trả về định dạng dd/mm/yyyy
        return `${day}/${month}/${year}`;
    } else {
        // Nếu chuỗi không hợp lệ, trả về giá trị ban đầu
        return dateString;
    }
}


// Chuyển từ Enum -> Text (Có dấu tiếng Việt)
function fromEnumStatusToText(status) {
    switch (status) {
        case 'ChoDuyet':
            return 'Chờ Duyệt';
        case 'DaDuyet':
            return 'Đã duyệt';
        case 'Huy':
            return 'Đã Hủy';
        case 'DangGiao':
            return 'Đang Giao';
        case 'GiaoThanhCong':
            return 'Giao thành công';
        default:
            return status;
    }
}

// Lấy thao tác cho trạng thái tiếp theo trong nút
function fromCurrentStatusToNextStatusText(status) {
    switch (status) {
        case 'ChoDuyet':
            return 'Duyệt đơn';
        case 'DaDuyet':
            return 'Giao cho shipper';
        case 'DangGiao':
            return 'Hoàn tất đơn hàng';
    }
}

// Lấy trạng thái tiếp theo trong quy trình mua hàng
function fromCurrentStatusToNextStatus(status) {
    switch (status) {
        case 'ChoDuyet':
            return 'DaDuyet';
        case 'DaDuyet':
            return 'DangGiao';
        case 'DangGiao':
            return 'GiaoThanhCong';
    }
}