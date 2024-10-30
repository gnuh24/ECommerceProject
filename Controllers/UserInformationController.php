<?php
require_once __DIR__ . "/../Models/UserInformationModel.php";
require '../vendor/autoload.php';
if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    $patchData = json_decode(file_get_contents("php://input"), true);

    if (isset($patchData['accountId'])) {
        $userId = $patchData['accountId'];
        $fullname = $patchData['fullname'] ?? null;
        $phone = $patchData['phone'] ?? null;
        $address = $patchData['address'] ?? null;
        $birthday = $patchData['birthday'] ?? null;
        $gender = $patchData['gender'] ?? null;

        if ($fullname && $phone && $address) {
            $form = [
                'fullname' => $fullname,
                'phone' => $phone,
                'birthday' => $birthday,
                'gender' => $gender,
                'address' => $address
            ];

            $controller = new UserInformationController();
            $response = $controller->updateUser($userId, $form);
            echo json_encode(['status' => 200, 'message' => $response]);
        } else {
            echo json_encode(['status' => 400, 'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc.']);
        }
    } else {
        echo json_encode(['status' => 400, 'message' => 'Không tìm thấy tài khoản để cập nhật.']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new UserInformationController();
    echo ($controller->getUserById($_GET['Id']));
}

class UserInformationController
{
    private $UserInformationModel;

    public function __construct()
    {
        $this->UserInformationModel = new UserInformationModel();
    }
    public function getUserById($id)
    {
        return $this->response($this->UserInformationModel->getUserById($id));
    }
    // Lấy thông tin người dùng theo email
    public function getUserByEmail($email)
    {
        return $this->UserInformationModel->getUserByEmail($email);
    }

    // Tạo người dùng mới
    public function createUser($form)
    {
        // Kiểm tra xem email đã tồn tại chưa
        $emailExists = $this->UserInformationModel->isEmailExists($form->email);
        if ($emailExists->isExists) {
            return (object) [
                "status" => 409,
                "message" => "Email already exists"
            ];
        }
        return $this->UserInformationModel->createUser($form);
    }

    // Kiểm tra xem email đã tồn tại hay chưa
    public function isEmailExists($email)
    {
        return $this->UserInformationModel->isEmailExists($email);
    }

    // Cập nhật thông tin người dùng
    public function updateUser($userId, $form)
    {

        // Cập nhật thông tin người dùng
        return $this->response($this->UserInformationModel->updateUser($userId, $form));
    }

    // Xóa người dùng theo ID
    public function deleteUser($userId)
    {
        return $this->UserInformationModel->deleteUser($userId);
    }
    private function response($result)
    {
        http_response_code($result->status);
        $response = [
            "message" => $result->message,
            "status" => $result->status ?? null,
            "data" => $result->data ?? null
        ];

        // Kiểm tra và thêm totalPages nếu có trong kết quả
        if (isset($result->totalPages)) {
            $response['totalPages'] = $result->totalPages;
        }

        echo json_encode($response);
    }
}
