<?php
require_once __DIR__ . "/../Models/AccountModel.php";
require_once __DIR__ . "/../Models/UserInformationModel.php";


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
class AccountController
{
    private $accountModel;

    public function __construct()
    {
        $this->accountModel = new AccountModel();
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
}
