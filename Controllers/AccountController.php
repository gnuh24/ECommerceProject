<?php
require_once __DIR__ . "/../Models/AccountModel.php";
require_once __DIR__ . "/../Models/UserInformationModel.php";
require_once __DIR__ . '/../Models/TokenModel.php';

require '../vendor/autoload.php'; // Đường dẫn đến autoload.php của Composer
use PHPMailer\PHPMailer\PHPMailer; // Dùng PHP mailer để gửi mail
use PHPMailer\PHPMailer\Exception;


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Login User
    if (isset($_GET['action']) && $_GET['action'] === "isThisEmailExists") {
        // Lấy dữ liệu từ form
        $email = $_GET['email'] ?? null;

        // Kiểm tra dữ liệu hợp lệ
        if ($email) {
            $accountController = new AccountController();

            // Gọi hàm login trong AccountController với email và password
            $response = $accountController->isEmailExists($email);

            // Trả về kết quả cho client (JavaScript)
            echo json_encode([
                'status' => 200,
                'message' => "Kiểm tra thành công !",
                'data' => [
                    'isExists' => $response,
                ]
            ]);
        }
    } else {
        // Trả về lỗi khi không có email hoặc mật khẩu
        echo json_encode([
            'status' => 400,
            'message' => 'Không tìm thấy tham số `action` !'
        ]);
    }
}

// Kiểm tra yêu cầu POST
else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Login User
    if (isset($_POST['action']) && $_POST['action'] === "loginUser") {
        // Lấy dữ liệu từ form
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;

        // Kiểm tra dữ liệu hợp lệ
        if ($email && $password) {
            $accountController = new AccountController();

            // Gọi hàm login trong AccountController với email và password
            $response = $accountController->LoginUser($email, $password);

            // Trả về kết quả cho client (JavaScript)
            if ($response->status === 200) {
                echo json_encode([
                    'status' => $response->status,
                    'message' => $response->message,
                    'data' => [
                        'id' => $response->data['Id'],
                        'role' => $response->data['Role'],
                        'email' => $response->data['Email'],
                        'token' => 'dummyToken', // You can generate a real token here
                        'refreshToken' => 'dummyRefreshToken'
                    ]
                ]);
            } else {
                echo json_encode([
                    'status' => $response->status,
                    'message' => $response->message
                ]);
            }
        } else {
            // Trả về lỗi khi không có email hoặc mật khẩu
            echo json_encode([
                'status' => 400,
                'message' => 'Vui lòng nhập email và mật khẩu!'
            ]);
        }
    }


    // Login User
    if (isset($_POST['action']) && $_POST['action'] === "registration") {
        // Lấy dữ liệu từ form
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;

        // Kiểm tra dữ liệu hợp lệ
        if ($email && $password) {
            $accountController = new AccountController();

            // Gọi hàm login trong AccountController với email và password
            $response = $accountController->registration($email, $password);

            // Trả về kết quả cho client (JavaScript)
            if ($response->status === 201) {
                echo json_encode([
                    'status' => $response->status,
                    'message' => $response->message

                    // 'data' => [
                    //     'id' => $response->data['Id'],
                    //     'role' => $response->data['Role'],
                    //     'email' => $response->data['Email'],
                    //     'token' => 'dummyToken', // You can generate a real token here
                    //     'refreshToken' => 'dummyRefreshToken'
                    // ]
                ]);
            } else {
                echo json_encode([
                    'status' => $response->status,
                    'message' => $response->message
                ]);
            }
        } else {
            // Trả về lỗi khi không có email hoặc mật khẩu
            echo json_encode([
                'status' => 400,
                'message' => 'Vui lòng nhập email và mật khẩu!'
            ]);
        }
    }
}

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     // Lấy dữ liệu từ form
//     $email = $_POST['email'] ?? null;
//     $password = $_POST['password'] ?? null;

//     // Kiểm tra dữ liệu hợp lệ
//     if ($email && $password) {
//         $accountController = new AccountController();

//         // Gọi hàm login trong AccountController với email và password
//         $response = $accountController->LoginAdmin($email, $password);

//         // Trả về kết quả cho client (JavaScript)
//         if ($response->status === 200) {
//             echo json_encode([
//                 'id' => $response->data['Id'],
//                 'token' => 'dummyToken', // Bạn có thể sinh ra token tại đây
//                 'refreshToken' => 'dummyRefreshToken'
//             ]);
//         } else {
//             echo json_encode([
//                 'code' => 8,
//                 'detailMessage' => $response->message
//             ]);
//         }
//     } else {
//         // Trả về lỗi khi không có email hoặc mật khẩu
//         echo json_encode([
//             'code' => 8,
//             'detailMessage' => 'Vui lòng nhập email và mật khẩu!'
//         ]);
//     }
// } else {
//     // Nếu không phải yêu cầu POST, trả về mã lỗi
//     http_response_code(405);
//     echo json_encode([
//         'code' => 405,
//         'detailMessage' => 'Phương thức không được hỗ trợ'
//     ]);
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra xem action có phải là loginAdmin không
    if (isset($_POST['action']) && $_POST['action'] === "loginAdmin") {
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;

        if ($email && $password) {
            $accountController = new AccountController();
            $response = $accountController->LoginAdmin($email, $password);

            // Xử lý phản hồi từ phía controller
            if ($response->status === 200) {
                echo json_encode([
                    'status' => 200,
                    'message' => "Đăng nhập thành công!",
                    'data' => [
                        'id' => $response->data['Id'],
                        'role' => $response->data['Role'],
                        'email' => $response->data['Email'],
                        'token' => 'dummyToken',
                        'refreshToken' => 'dummyRefreshToken'
                    ]
                ]);
            } else {
                echo json_encode([
                    'status' => $response->status,
                    'message' => $response->message
                ]);
            }
        } else {
            echo json_encode([
                'status' => 400,
                'message' => 'Vui lòng nhập email và mật khẩu!'
            ]);
        }
    }
} else {
    echo json_encode([
        'status' => 405,
        'message' => 'Phương thức không được hỗ trợ!'
    ]);

}
// chức năng gửi mail cho mail muốn reset password
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra xem action có phải là resetPassword không
    if (isset($_POST['action']) && $_POST['action'] === "resetPassword") {
        $email = $_POST['email'] ?? null;

        if ($email) {
            $accountController = new AccountController();
            $response = $accountController->resetPasswordRequest($email);

            // Xử lý phản hồi từ phía controller
            if ($response->status === 200) {
                echo json_encode([
                    'status' => 200,
                    'message' => "Email khôi phục mật khẩu đã được gửi!"
                ]);
            } else {
                echo json_encode([
                    'status' => $response->status,
                    'message' => $response->message
                ]);
            }
        } else {
            echo json_encode([
                'status' => 400,
                'message' => 'Vui lòng nhập email!'
            ]);
        }
    }
} else {
    echo json_encode([
        'status' => 405,
        'message' => 'Phương thức không được hỗ trợ!'
    ]);
}

// Xử lý thông tin từ form đổi mật khẩu
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === "setNewPassword") {
        $email = $_POST['email'] ?? null;
        $token = $_POST['token'] ?? null;
        $newPassword = $_POST['new_password'] ?? null;
        $confirmPassword = $_POST['confirm_password'] ?? null;

        // Kiểm tra dữ liệu
        if (!$email || !$token || !$newPassword || !$confirmPassword) {
            echo json_encode([
                'status' => 400,
                'message' => 'Vui lòng điền đầy đủ thông tin!'
            ]);
            exit;
        }

        // Kiểm tra xem mật khẩu xác nhận có khớp không
        if ($newPassword !== $confirmPassword) {
            echo json_encode([
                'status' => 400,
                'message' => 'Mật khẩu xác nhận không khớp.'
            ]);
            exit;
        }

        // Gọi hàm xử lý đặt lại mật khẩu
        $accountController = new AccountController();
        $response = $accountController->processNewPassword($email, $token, $newPassword);

        // Trả về phản hồi JSON
        echo json_encode([
            'status' => $response->status,
            'message' => $response->message
        ]);
        exit;
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Kiểm tra nếu action là "getAccountById"
    if (isset($_GET['action']) && $_GET['action'] === 'getAccountById') {
        // Lấy ID từ tham số GET
        $userInformationId = $_GET['userInformationId'] ?? null;

        // Kiểm tra ID có tồn tại không
        if ($userInformationId) {
            // Khởi tạo controller
            $accountController = new AccountController();

            // Gọi hàm getAccountById từ controller
            $response = $accountController->getAccountById($userInformationId);

            // Trả về phản hồi
            echo $response;
        } else {
            // Trả về lỗi nếu không có ID
            echo json_encode([
                'status' => 400,
                'message' => 'Không tìm thấy tham số ID!'
            ]);
        }
    } else {
        // Trả về lỗi nếu không có action phù hợp
        echo json_encode([
            'status' => 400,
            'message' => 'Không tìm thấy tham số action!'
        ]);
    }
}


class AccountController
{
    private $accountModel;
    private $tokenModel;

    public function __construct()
    {
        $this->accountModel = new AccountModel();
        $this->tokenModel = new TokenModel();
    }

    // Hàm xử lý đăng nhập Admin
    public function LoginAdmin($email, $password)
    {
        // Kiểm tra xem tài khoản có tồn tại không
        $accountExists = $this->accountModel->getAccountByEmail($email);

        if ($accountExists->status === 200 && !empty($accountExists->data)) {
            // Lấy thông tin tài khoản
            $account = $accountExists->data[0];

            // Kiểm tra vai trò của tài khoản có phải là Admin không
            if ($account['Role'] === "Admin") {
                // Kiểm tra mật khẩu
                if (password_verify($password, $account['Password'])) {
                    return (object)[
                        "status" => 200,
                        "message" => "Đăng nhập Admin thành công",
                        "data" => $account
                    ];
                } else {
                    return (object)[
                        "status" => 401,
                        "message" => "Mật khẩu không đúng"
                    ];
                }
            } else {
                return (object)[
                    "status" => 403,
                    "message" => "Tài khoản không phải là Admin"
                ];
            }
        }
        // Nếu không tìm thấy tài khoản
        return (object)[
            "status" => 404,
            "message" => "Không tìm thấy tài khoản Admin"
        ];
    }
    // Hàm xử lý đăng nhập User
    public function LoginUser($email, $password)
    {
        // Kiểm tra xem tài khoản có tồn tại không
        $accountExists = $this->accountModel->getAccountByEmail($email);

        if ($accountExists->status === 200 && !empty($accountExists->data)) {
            // Lấy thông tin tài khoản
            $account = $accountExists->data[0];

            if ($account['Role'] === "User") {
                if ($account['Status'] === 0) {
                    return (object)[
                        "status" => 403,
                        "message" => "Tài khoản đã bị cấm"
                    ];
                }

                if ($account['Active'] === 0) {
                    return (object)[
                        "status" => 403,
                        "message" => "Tài khoản chưa được kích hoạt. Kiểm tra email của bạn: " . $account['Email']
                    ];
                }

                // Kiểm tra mật khẩu
                if (password_verify($password, $account['Password'])) {
                    return (object)[
                        "status" => 200,
                        "message" => "Đăng nhập thành công",
                        "data" => $account
                    ];
                }
            }
        }

        // Nếu không tìm thấy tài khoản hoặc mật khẩu không khớp
        return (object)[
            "status" => 404,
            "message" => "Không tìm thấy tài khoản"
        ];
    }

    function isEmailExists($email)
    {
        return $this->accountModel->isEmailExists($email);
    }

    function registration($email, $password)
    {

        $userInformationModel = new UserInformationModel();

        $id = $userInformationModel->createUserInformation($email);

        if ($id->status === 201) {
            $response = $this->accountModel->createAccount($password, $id->data);

            if ($response->status === 201) {
                return (object)[
                    "status" => 201,
                    "message" => "Tạo tài khoản thành công"
                ];
            }
        }

        return (object)[
            "status" => 400,
            "message" => "Tạo tài khoản thất bại !"
        ];
    }
    public function resetPasswordRequest($email) {
        if (!$this->isEmailExists($email)) {
            return (object) [
                'status' => 404,
                'message' => 'Email không tồn tại.'
            ];
        }
    
        // Lấy thông tin tài khoản theo email
        $accountInfo = $this->accountModel->getAccountByEmail($email);
        $userId = $accountInfo->data[0]['UserInformationId'];
    
        // Xóa tất cả các token cũ liên quan đến email này
        $this->tokenModel->deleteTokenByEmail($email);
    
        // Tạo token mới
        $token = bin2hex(random_bytes(3)); // Mã 6 ký tự
        $expiresAt = (new DateTime())->modify('+2 hours')->format('Y-m-d H:i:s');
    
        // Lưu token vào database
        $this->tokenModel->createToken($token, $expiresAt, 'reset_password', $userId);
    
        // Gửi email chứa token cho người dùng
        $this->sendResetEmail($email, $token);
    
        return (object) [
            'status' => 200,
            'message' => 'Email đã được gửi.'
        ];
    }
    
    private function sendResetEmail($email, $token) {
        $mail = new PHPMailer(true);
        try {
            // Cấu hình máy chủ
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'automaticemail0204@gmail.com'; // Địa chỉ email
            $mail->Password = 'lztz vkly edwe sucl'; // Mật khẩu email
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
    
            // Người gửi và người nhận
            $mail->setFrom('automaticemail0204@gmail.com', 'Your Name');
            $mail->addAddress($email);
    
            // Nội dung email
            $mail->isHTML(true);
            $mail->Subject = 'Khôi phục mật khẩu';
            
            // Truyền thêm tham số email vào URL khôi phục mật khẩu
            $mail->Body = 'Nhấn vào liên kết sau để khôi phục mật khẩu của bạn: <a href="http://localhost/ECommerceProject/Views/MemberUI/Login/ResetPasswordUI.php?token=' . $token . '&email=' . urlencode($email) . '">Khôi phục mật khẩu</a>';
            
    
            // Gửi email
            $mail->send();
            return (object) [
                "status" => 200,
                "message" => "Email khôi phục mật khẩu đã được gửi!"
            ];
        } catch (Exception $e) {
            return (object) [
                "status" => 500,
                "message" => "Không thể gửi email. Lỗi: {$mail->ErrorInfo}"
            ];
        }
    }
    public function processNewPassword($email, $token, $newPassword)
    {
        // Khởi tạo model Account, Token và UserInformation
        $accountModel = new AccountModel();
        $tokenModel = new TokenModel();
        $userInfoModel = new UserInformationModel();
    
        // 1. Kiểm tra xem email và token có hợp lệ không
        $accountResponse = $accountModel->getAccountByEmail($email);
        $tokenResponse = $tokenModel->getTokenByValue($token);
    
        // Kiểm tra xem có tài khoản và token hợp lệ không
        if ($accountResponse->status === 200 && !empty($accountResponse->data) && 
            $tokenResponse->status === 200 && !empty($tokenResponse->data)) {
            
            $account = $accountResponse->data[0];
            $tokenData = $tokenResponse->data;
    
            // Kiểm tra xem token có hết hạn không
            $currentDate = new DateTime();
            $expirationDate = new DateTime($tokenData['Expiration']);
            if ($currentDate > $expirationDate) {
                return (object)[
                    'status' => 400,
                    'message' => 'Token đã hết hạn.'
                ];
            }
    
            // 2. Kiểm tra xem email có tồn tại trong bảng user_information không
            $userInfoResponse = $userInfoModel->isEmailExists($email);
            if (!$userInfoResponse->isExists) {
                return (object)[
                    'status' => 400,
                    'message' => 'Email không tồn tại trong hệ thống.'
                ];
            }
    
            // 3. Cập nhật mật khẩu mới trong CSDL
            $updateResponse = $accountModel->updateAccount($account['Id'], $newPassword, $account['Status'], $account['Active']);
    
            if ($updateResponse->status === 200) {
                // 4. Xoá token sau khi đã sử dụng
                $tokenModel->deleteTokenByEmail($email);
    
                // Phản hồi thành công
                return (object)[
                    'status' => 200,
                    'message' => 'Mật khẩu của bạn đã được cập nhật thành công!'
                ];
            } else {
                return (object)[
                    'status' => 500,
                    'message' => 'Có lỗi xảy ra khi cập nhật mật khẩu. Vui lòng thử lại sau.'
                ];
            }
        } else {
            return (object)[
                'status' => 400,
                'message' => 'Token không hợp lệ hoặc đã hết hạn.'
            ];
        }
    }
    
// Hàm lấy tất cả tài khoản
public function getAccountById($userInformationId, $page, $search, $role, $status)
    {
        // Gọi hàm getAccountById từ model
        $response = $this->accountModel->getAccountById($userInformationId, $page, $search, $role, $status);

        // Kiểm tra kết quả trả về từ model và chuẩn bị phản hồi
        if ($response->status === 200) {
            // Nếu thành công, trả về kết quả dưới dạng JSON
            return json_encode([
                'status' => 200,
                'message' => 'Lấy thông tin tài khoản thành công!',
                'data' => $response->data
            ]);
        } else {
            // Nếu có lỗi, trả về lỗi
            return json_encode([
                'status' => 400,
                'message' => 'Không thể lấy thông tin tài khoản!'
            ]);
        }
    }
    
}
