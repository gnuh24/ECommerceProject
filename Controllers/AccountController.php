<?php
require_once __DIR__ . "/../Models/AccountModel.php";

class AccountController
{
    private $accountModel;

    public function __construct()
    {
        $this->accountModel = new AccountModel();
    }

    // Hàm xử lý đăng nhập
    public function LoginAdmin($userInformationId, $password)
    {
        // Kiểm tra xem tài khoản có tồn tại không
        $accountExists = $this->accountModel->isAccountExists($userInformationId);
        
        if ($accountExists->status === 200 && $accountExists->isExists) {
            // Lấy thông tin tài khoản
            $accountData = $this->accountModel->getAccountById($userInformationId);
            if ($accountData->status === 200 && !empty($accountData->data)) {
                $account = $accountData->data[0];

                // Kiểm tra mật khẩu
                if (password_verify($password, $account['Password'])) {
                    return (object)[
                        "status" => 200,
                        "message" => "Login successful",
                        "data" => $account
                    ];
                } else {
                    return (object)[
                        "status" => 401,
                        "message" => "Invalid password"
                    ];
                }
            } else {
                return (object)[
                    "status" => 404,
                    "message" => "Account not found"
                ];
            }
        } else {
            return (object)[
                "status" => 404,
                "message" => "Account does not exist"
            ];
        }
    }

}