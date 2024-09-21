<?php
require_once __DIR__ . "/../../Controllers/AccountController.php";

// Kiểm tra yêu cầu POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $email = $_POST['email'] ?? null;
    $password = $_POST['password'] ?? null;

    // Kiểm tra dữ liệu hợp lệ
    if ($email && $password) {
        $accountController = new AccountController();

        // Gọi hàm login trong AccountController với email và password
        $response = $accountController->LoginAdmin($email, $password);

        // Trả về kết quả cho client (JavaScript)
        if ($response->status === 200) {
            echo json_encode([
                'id' => $response->data['Id'],
                'token' => 'dummyToken', // Bạn có thể sinh ra token tại đây
                'refreshToken' => 'dummyRefreshToken'
            ]);
        } else {
            echo json_encode([
                'code' => 8,
                'detailMessage' => $response->message
            ]);
        }
    } else {
        // Trả về lỗi khi không có email hoặc mật khẩu
        echo json_encode([
            'code' => 8,
            'detailMessage' => 'Vui lòng nhập email và mật khẩu!'
        ]);
    }
} else {
    // Nếu không phải yêu cầu POST, trả về mã lỗi
    http_response_code(405);
    echo json_encode([
        'code' => 405,
        'detailMessage' => 'Phương thức không được hỗ trợ'
    ]);
}
?>
