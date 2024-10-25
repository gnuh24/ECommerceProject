<?php
require_once __DIR__ . "/../Models/UserInformationModel.php";
require '../vendor/autoload.php';
if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    // Đọc dữ liệu từ body của request
    parse_str(file_get_contents("php://input"), $patchData);

    // Kiểm tra xem accountId có tồn tại trong dữ liệu không
    if (isset($patchData['accountId'])) {
        $userId = $patchData['accountId'];

        // Lấy các giá trị cần cập nhật từ dữ liệu nhận được
        $fullname = isset($patchData['fullname']) ? $patchData['fullname'] : null;
        $phone = isset($patchData['phone']) ? $patchData['phone'] : null;
        $birthday = isset($patchData['birthday']) ? $patchData['birthday'] : null;
        $gender = isset($patchData['gender']) ? $patchData['gender'] : null;
        $address = isset($patchData['address']) ? $patchData['address'] : null;

        // Kiểm tra các trường bắt buộc
        if ($fullname && $phone && $birthday && $address) {
            // Tạo mảng chứa dữ liệu để cập nhật
            $form = [
                'fullname' => $fullname,
                'phone' => $phone,
                'birthday' => $birthday,
                'gender' => $gender,
                'address' => $address
            ];

            // Gọi hàm updateUser từ controller để cập nhật dữ liệu
            $controller = new UserInformationController();
            $response = $controller->updateUser($userId, $form);

            // Trả về phản hồi cho client
            echo ($response);
        } else {
            // Thiếu dữ liệu cần thiết
            echo json_encode([
                'status' => 400,
                'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc.'
            ]);
        }
    } else {
        // Nếu không có accountId trong yêu cầu
        echo json_encode([
            'status' => 400,
            'message' => 'Không tìm thấy tài khoản để cập nhật.'
        ]);
    }
}

class UserInformationController
{
    private $UserInformationModel;

    public function __construct()
    {
        $this->UserInformationModel = new UserInformationModel();
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
            "status" => $result->status ?? null
        ];

        // Kiểm tra và thêm totalPages nếu có trong kết quả
        if (isset($result->totalPages)) {
            $response['totalPages'] = $result->totalPages;
        }

        echo json_encode($response);
    }
}
