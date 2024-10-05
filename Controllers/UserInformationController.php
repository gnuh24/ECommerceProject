<?php

<?php

require_once __DIR__ . "/../Models/UserInformationModel.php";
require '../vendor/autoload.php';

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
        // Lấy thông tin người dùng hiện tại
        $user = $this->UserInformationModel->getUserInformationById($userId);
        if ($user->status !== 200) {
            return $user; // Nếu không tìm thấy người dùng
        }
        
        // Cập nhật thông tin người dùng
        return $this->UserInformationModel->updateUser($user->data[0], $form);
    }

    // Xóa người dùng theo ID
    public function deleteUser($userId)
    {
        return $this->UserInformationModel->deleteUser($userId);
    }
}
