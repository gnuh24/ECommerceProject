<?php
// Bắt đầu phiên làm việc
session_start();

// Nhận thông tin phản hồi từ VNPAY
$vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? '';
$vnp_TxnRef = $_GET['vnp_TxnRef'] ?? '';
$vnp_Amount = $_GET['vnp_Amount'] ?? '';
$vnp_BankCode = $_GET['vnp_BankCode'] ?? '';
$vnp_TransactionNo = $_GET['vnp_TransactionNo'] ?? '';
$vnp_PayDate = $_GET['vnp_PayDate'] ?? '';

// Kiểm tra nếu giao dịch thành công
if ($vnp_ResponseCode == '00') {
    // Đặt thông tin vào session hoặc biến để sử dụng trong JS
    $_SESSION['vnp_ResponseCode'] = $vnp_ResponseCode;
    $_SESSION['vnp_TxnRef'] = $vnp_TxnRef;
    $_SESSION['vnp_Amount'] = $vnp_Amount;
    $_SESSION['vnp_BankCode'] = $vnp_BankCode;
    $_SESSION['vnp_TransactionNo'] = $vnp_TransactionNo;
    $_SESSION['vnp_PayDate'] = $vnp_PayDate;
} else {
    $_SESSION['vnp_ResponseCode'] = $vnp_ResponseCode;
}

// Đưa thông tin vào file HTML
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Tin Thanh Toán VNPay</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Kiểm tra nếu giao dịch thành công
            var responseCode = "<?php echo $vnp_ResponseCode; ?>";
            if (responseCode === '00') {
                // Lấy dữ liệu từ localStorage
                var formDataString = localStorage.getItem("donHangDangThanhToan");
                console.log(formDataString);
                if (formDataString) {
                    // Phân tích cú pháp chuỗi JSON thành đối tượng
                    var formDataObj = JSON.parse(formDataString);

                    // Tạo một FormData mới từ đối tượng
                    var formData = new FormData();
                    for (let key in formDataObj) {
                        formData.append(key, formDataObj[key]);
                    }


                    // Gửi yêu cầu API
                    $.ajax({
                        url: "../../../Controllers/OrderController.php",
                        method: "POST",
                        data: formData,
                        processData: false, // Ngăn jQuery xử lý dữ liệu
                        contentType: false, // Ngăn jQuery thiết lập tiêu đề `Content-Type`
                        success: function(response) {
                            Swal.fire({
                                title: 'Đặt hàng thành công!',
                                text: 'Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ xử lý đơn hàng của bạn sớm nhất có thể.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result1) => {
                                localStorage.removeItem('cart');
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error("Lỗi:", error);
                        }
                    });
                }
            } else {
                // Nếu không thành công, hiển thị thông báo
                Swal.fire({
                    title: 'Thanh toán không thành công!',
                    text: 'Vui lòng thử lại sau.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });

    </script>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 500px; margin: 50px auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        h2 { color: #4CAF50; }
        .success { color: #4CAF50; font-weight: bold; }
        .error { color: #f44336; font-weight: bold; }
        .info { margin: 10px 0; }
        .back-button { margin-top: 20px; text-align: center; }
        .back-button button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .back-button button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Kết Quả Thanh Toán</h2>
        
        <?php if ($vnp_ResponseCode == '00'): ?>
            <p class="success">Thanh toán thành công!</p>
            <p class="info"><strong>Mã đơn hàng:</strong> <?= htmlspecialchars($vnp_TxnRef) ?></p>
            <p class="info"><strong>Số tiền:</strong> <?= htmlspecialchars(number_format($vnp_Amount / 100, 0, ',', '.')) ?> VND</p>
            <p class="info"><strong>Mã ngân hàng:</strong> <?= htmlspecialchars($vnp_BankCode) ?></p>
            <p class="info"><strong>Số giao dịch:</strong> <?= htmlspecialchars($vnp_TransactionNo) ?></p>
            <p class="info"><strong>Ngày thanh toán:</strong> <?= htmlspecialchars(date('d/m/Y H:i:s', strtotime($vnp_PayDate))) ?></p>
        <?php else: ?>
            <p class="error">Thanh toán không thành công!</p>
            <p class="info"><strong>Mã phản hồi:</strong> <?= htmlspecialchars($vnp_ResponseCode) ?></p>
        <?php endif; ?>
        
        <div class="back-button">
            <button onclick="window.location.href='../HomePage.php'">Trở về trang chủ</button>
        </div>
    </div>
</body>
</html>
