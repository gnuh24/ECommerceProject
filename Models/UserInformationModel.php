<?php
require_once __DIR__ . '/../Configure/MysqlConfig.php';

class UserInformationModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Kiểm tra xem email đã tồn tại hay chưa
    function isEmailExists($email)
    {
        $query = "SELECT * FROM `userinformation` WHERE `email` = :email";

        try {
            $statement = $this->connection->prepare($query);
            if ($statement !== false) {
                $statement->bindValue(':email', $email, PDO::PARAM_STR);
                $statement->execute();
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                $isExists = !empty($result);
                return (object) [
                    "status" => 200,
                    "message" => "Query successful",
                    "isExists" => $isExists
                ];
            } else {
                throw new PDOException();
            }
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => "Database query failed",
                "isExists" => false
            ];
        }
    }

    // Lấy thông tin người dùng theo ID
    function getUserInformationById($id)
    {
        $query = "SELECT * FROM `user_information` WHERE `id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            if ($statement !== false) {
                $statement->bindValue(':id', $id, PDO::PARAM_INT);
                $statement->execute();
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                return (object) [
                    "status" => 200,
                    "message" => "Success",
                    "data" => $result,
                ];
            } else {
                throw new PDOException();
            }
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => "Unable to fetch user",
            ];
        }
    }

    function createUserInformation($email)
    {
        $query = "INSERT INTO `UserInformation`(`email`) 
                                    VALUES (:email)";

        try {
            $statement = $this->connection->prepare($query);
            if ($statement !== false) {
                $statement->bindValue(':email', $email, PDO::PARAM_STR);
                $statement->execute();
                $id = $this->connection->lastInsertId();
                return (object) [
                    "status" => 201,
                    "message" => "User created successfully",
                    "data" => $id
                ];
            } else {
                throw new PDOException();
            }
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Cập nhật thông tin người dùng
    function updateUserInformation($id, $fullname, $birthday, $gender, $phone_number, $email, $address)
    {
        $query = "UPDATE `user_information` SET 
                    `fullname` = :fullname,
                    `birthday` = :birthday,
                    `gender` = :gender,
                    `phone_number` = :phone_number,
                    `email` = :email,
                    `address` = :address
                  WHERE `id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            if ($statement !== false) {
                $statement->bindValue(':id', $id, PDO::PARAM_INT);
                $statement->bindValue(':fullname', $fullname, PDO::PARAM_STR);
                $statement->bindValue(':birthday', $birthday, PDO::PARAM_STR);
                $statement->bindValue(':gender', $gender, PDO::PARAM_STR);
                $statement->bindValue(':phone_number', $phone_number, PDO::PARAM_STR);
                $statement->bindValue(':email', $email, PDO::PARAM_STR);
                $statement->bindValue(':address', $address, PDO::PARAM_STR);
                $statement->execute();

                if ($statement->rowCount() > 0) {
                    return (object) [
                        "status" => 200,
                        "message" => "User updated successfully",
                    ];
                } else {
                    throw new PDOException("No record was updated");
                }
            }
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }
}
