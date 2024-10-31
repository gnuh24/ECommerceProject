
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

// // Hàm này dùng để quy đổi tiền trở về dạng số
// function convertPriceToNumber(priceString) {
//     var priceWithoutDot = priceString.replace(/\./g, '');
//     var priceWithoutDong = priceWithoutDot.replace('đ', '');
//     var priceNumber = parseInt(priceWithoutDong);
//     return priceNumber;
// }


// Function to convert dd/MM/yyyy to yyyy-MM-dd
function formatDateToYYYYMMDD(dateString) {
    if (!dateString) return ''; // Return empty if input is invalid
    const [day, month, year] = dateString.split('/'); // Destructure directly
    return (day && month && year) ? `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}` : ''; // Check parts and format
}

// Function to convert yyyy-MM-dd to dd/MM/yyyy
function formatDateToDDMMYYYY(dateString) {
    const [year, month, day] = dateString.split("-"); // Destructure directly
    return (year && month && day) ? `${day}/${month}/${year}` : dateString; // Check parts and format
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

// yyyy-MM-dd HH:mm:ss -> HH:mm:ss dd/MM/yyyy
// Sample: 2024-07-14 09:30:00 -> 09:30:00 14/07/2024
function convertDateTimeFormat(dateString) {
    // Split date and time
    const [datePart, timePart] = dateString.split(" ");
  
    // Split the date part into year, month, day
    const [year, month, day] = datePart.split("-");
  
    // Return the new format
    return `${timePart} ${day}/${month}/${year}`;
}


