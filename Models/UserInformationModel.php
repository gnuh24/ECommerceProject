<?php
class UserInformationModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Lấy thông tin người dùng theo email
    function getUserByEmail($email)
    {
        $query = "SELECT * FROM `UserInformation` WHERE `Email` = :email";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':email', $email, PDO::PARAM_STR);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return (object) [
                    "status" => 200,
                    "message" => "User found",
                    "data" => $result,
                ];
            } else {
                return (object) [
                    "status" => 404,
                    "message" => "User not found",
                ];
            }
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => "Database query failed",
            ];
        }
    }

    // Tạo người dùng mới
    function createUser($form)
    {
        $query = "INSERT INTO `UserInformation` (`Email`, `Address`, `Birthday`, `Fullname`, `Gender`, `PhoneNumber`) 
                  VALUES (:email, :address, :birthday, :fullname, :gender, :phone_number)";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':email', $form->email, PDO::PARAM_STR);
            $statement->bindValue(':address', $form->address, PDO::PARAM_STR);
            $statement->bindValue(':birthday', $form->birthday, PDO::PARAM_STR);
            $statement->bindValue(':fullname', $form->fullname, PDO::PARAM_STR);
            $statement->bindValue(':gender', $form->gender, PDO::PARAM_STR);
            $statement->bindValue(':phone_number', $form->phone_number, PDO::PARAM_STR);
            $statement->execute();
            $id = $this->connection->lastInsertId();
            return (object) [
                "status" => 201,
                "message" => "User created successfully",
                "data" => $id
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Kiểm tra xem email đã tồn tại hay chưa
    function isEmailExists($email)
    {
        $query = "SELECT COUNT(*) FROM `UserInformation` WHERE `Email` = :email";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':email', $email, PDO::PARAM_STR);
            $statement->execute();
            $count = $statement->fetchColumn();

            return (object) [
                "status" => 200,
                "message" => "Query successful",
                "isExists" => $count > 0
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => "Database query failed",
                "isExists" => false
            ];
        }
    }

    // Cập nhật thông tin người dùng
    function updateUser($user, $form)
    {
        $query = "UPDATE `UserInformation` SET 
                    `Email` = :email,
                    `Address` = :address,
                    `Birthday` = :birthday,
                    `Fullname` = :fullname,
                    `Gender` = :gender,
                    `PhoneNumber` = :phone_number
                  WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $user->Id, PDO::PARAM_INT);
            $statement->bindValue(':email', $form->email, PDO::PARAM_STR);
            $statement->bindValue(':address', $form->address, PDO::PARAM_STR);
            $statement->bindValue(':birthday', $form->birthday, PDO::PARAM_STR);
            $statement->bindValue(':fullname', $form->fullname, PDO::PARAM_STR);
            $statement->bindValue(':gender', $form->gender, PDO::PARAM_STR);
            $statement->bindValue(':phone_number', $form->phone_number, PDO::PARAM_STR);
            $statement->execute();

            if ($statement->rowCount() > 0) {
                return (object) [
                    "status" => 200,
                    "message" => "User updated successfully",
                ];
            } else {
                return (object) [
                    "status" => 404,
                    "message" => "No record was updated",
                ];
            }
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Cập nhật thông tin người dùng (khác)
    function updateUserWithForm($form)
    {
        return $this->updateUser($this->getUserByEmail($form->email), $form);
    }

    // Xóa người dùng theo ID
    function deleteUser($userId)
    {
        $query = "DELETE FROM `UserInformation` WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $userId, PDO::PARAM_INT);
            $statement->execute();

            if ($statement->rowCount() > 0) {
                return (object) [
                    "status" => 200,
                    "message" => "User deleted successfully",
                ];
            } else {
                return (object) [
                    "status" => 404,
                    "message" => "No record was deleted",
                ];
            }
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }
}
