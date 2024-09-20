<?php
require_once __DIR__ . "/../../Configure/MysqlConfig.php";

class AccountModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Kiểm tra xem tài khoản đã tồn tại hay chưa
    function isAccountExists($userInformationId)
    {
        $query = "SELECT * FROM `account` WHERE `UserInformationId` = :userInformationId";

        try {
            $statement = $this->connection->prepare($query);
            if ($statement !== false) {
                $statement->bindValue(':userInformationId', $userInformationId, PDO::PARAM_INT);
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

    // Lấy thông tin tài khoản theo ID
    function getAccountById($id)
    {
        $query = "SELECT * FROM `account` WHERE `Id` = :id";

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
                "message" => "Unable to fetch account",
            ];
        }
    }

    // Tạo tài khoản mới
    function createAccount($password, $userInformationId, $role = "User", $type = "Standard")
    {
        $query = "INSERT INTO `account` (`Password`, `UserInformationId`, `Role`, `Type`) 
                  VALUES (:password, :userInformationId, :role, :type)";

        try {
            $statement = $this->connection->prepare($query);
            if ($statement !== false) {
                $statement->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
                $statement->bindValue(':userInformationId', $userInformationId, PDO::PARAM_INT);
                $statement->bindValue(':role', $role, PDO::PARAM_STR);
                $statement->bindValue(':type', $type, PDO::PARAM_STR);
                $statement->execute();
                $id = $this->connection->lastInsertId();
                return (object) [
                    "status" => 201,
                    "message" => "Account created successfully",
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

    // Cập nhật thông tin tài khoản
    function updateAccount($id, $password, $status, $active)
    {
        $query = "UPDATE `account` SET 
                    `Password` = :password,
                    `Status` = :status,
                    `Active` = :active
                  WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            if ($statement !== false) {
                $statement->bindValue(':id', $id, PDO::PARAM_INT);
                $statement->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
                $statement->bindValue(':status', $status, PDO::PARAM_BOOL);
                $statement->bindValue(':active', $active, PDO::PARAM_BOOL);
                $statement->execute();

                if ($statement->rowCount() > 0) {
                    return (object) [
                        "status" => 200,
                        "message" => "Account updated successfully",
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
