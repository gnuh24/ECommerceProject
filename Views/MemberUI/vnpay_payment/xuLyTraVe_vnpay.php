<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Thông tin cấu hình
$vnp_TmnCode = "D1VILURA"; // Mã website của bạn
$vnp_HashSecret = "K5Z6RHOSDXLRRUXXPND6IAKNNAITFU5T"; // Secret key

// Lấy các tham số trả về từ VNPAY
$vnp_SecureHash = $_GET['vnp_SecureHash'];
$vnp_TxnRef = $_GET['vnp_TxnRef'];
$vnp_OrderInfo = $_GET['vnp_OrderInfo'];
$vnp_Amount = $_GET['vnp_Amount'];
$vnp_ResponseCode = $_GET['vnp_ResponseCode'];
$vnp_TransactionNo = $_GET['vnp_TransactionNo'];
$vnp_PayDate = $_GET['vnp_PayDate'];
$vnp_BankCode = $_GET['vnp_BankCode'];
$vnp_CardType = $_GET['vnp_CardType'];
$vnp_TransactionStatus = $_GET['vnp_TransactionStatus'];

// Tạo chuỗi để xác thực
$inputData = array(
    "vnp_TmnCode" => $vnp_TmnCode,
    "vnp_TxnRef" => $vnp_TxnRef,
    "vnp_OrderInfo" => $vnp_OrderInfo,
    "vnp_Amount" => $vnp_Amount,
    "vnp_ResponseCode" => $vnp_ResponseCode,
    "vnp_TransactionNo" => $vnp_TransactionNo,
    "vnp_PayDate" => $vnp_PayDate,
    "vnp_BankCode" => $vnp_BankCode,
    "vnp_CardType" => $vnp_CardType,
    "vnp_TransactionStatus" => $vnp_TransactionStatus
);

// Sắp xếp các tham số
ksort($inputData);
$hashdata = "";
foreach ($inputData as $key => $value) {
    if (!empty($value)) {
        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
    }
}
$hashdata = substr($hashdata, 1); // Xóa ký tự đầu tiên '&'

// Tính toán lại mã băm
$vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);

// // Kiểm tra mã băm
// if ($vnpSecureHash === $vnp_SecureHash) {
    // Xử lý đơn hàng thành công
    if ($vnp_ResponseCode == '00') {
        // Thanh toán thành công
        echo "Thanh toán thành công. Mã đơn hàng: " . $vnp_TxnRef . "<br>";
        echo "Số tiền: " . ($vnp_Amount / 100) . " VND<br>";
        echo "Mã ngân hàng: " . $vnp_BankCode . "<br>";
        echo "Số giao dịch: " . $vnp_TransactionNo . "<br>";
        echo "Ngày thanh toán: " . $vnp_PayDate . "<br>";
    } else {
        // Thanh toán không thành công
        echo "Thanh toán không thành công. Mã phản hồi: " . $vnp_ResponseCode . "<br>";
    }
// } else {
//     echo "Giá trị băm không hợp lệ!";
// }
?>
