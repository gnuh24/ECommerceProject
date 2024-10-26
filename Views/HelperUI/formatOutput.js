
/*
    FILE NÀY CHỨA TẤT CẢ CÁC HÀM FORMAT CẤU TRÚC ĐẦU RA NHƯ: NGÀY THÁNG NĂM, TIỀN TỆ

*/

function formatCurrency(number) {
    // Chuyển đổi số thành chuỗi và đảm bảo nó là số nguyên
    number = parseInt(number);

    // Sử dụng hàm toLocaleString() để định dạng số tiền
    // và thêm đơn vị tiền tệ "đ" vào cuối chuỗi
    return number.toLocaleString('vi-VN', {
        style: 'currency',
        currency: 'VND'
    });
}