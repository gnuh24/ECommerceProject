<?php
require_once __DIR__ . "/../../Configure/MysqlConfig.php";

class TokenModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Lấy token theo ID
    function getTokenById($id)
    {
        $query = "SELECT * FROM `token` WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            if ($statement !== false) {
                $statement->bindValue(':id', $id, PDO::PARAM_INT);
                $statement->execute();
                $result = $statement->fetch(PDO::FETCH_ASSOC);
                return (object) [
                    "status" => 200,
                    "message" => "Success",
                    "data" => $result
                ];
            } else {
                throw new PDOException();
            }
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => "Unable to fetch token",
            ];
        }
    }

    // Tạo token mới
    function createToken($token, $expiration, $type, $accountId)
    {
        $query = "INSERT INTO `token` (`Token`, `Expiration`, `Type`, `AccountId`) 
                  VALUES (:token, :expiration, :type, :accountId)";

        try {
            $statement = $this->connection->prepare($query);
            if ($statement !== false) {
                $statement->bindValue(':token', $token, PDO::PARAM_STR);
                $statement->bindValue(':expiration', $expiration, PDO::PARAM_STR);
                $statement->bindValue(':type', $type, PDO::PARAM_STR);
                $statement->bindValue(':accountId', $accountId, PDO::PARAM_INT);
                $statement->execute();
                $id = $this->connection->lastInsertId();
                return (object) [
                    "status" => 201,
                    "message" => "Token created successfully",
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

    // Cập nhật token
    function updateToken($id, $token, $expiration)
    {
        $query = "UPDATE `token` SET 
                    `Token` = :token,
                    `Expiration` = :expiration
                  WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            if ($statement !== false) {
                $statement->bindValue(':id', $id, PDO::PARAM_INT);
                $statement->bindValue(':token', $token, PDO::PARAM_STR);
                $statement->bindValue(':expiration', $expiration, PDO::PARAM_STR);
                $statement->execute();

                if ($statement->rowCount() > 0) {
                    return (object) [
                        "status" => 200,
                        "message" => "Token updated successfully"
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

    // Xoá token theo ID
    function deleteTokenById($id)
    {
        $query = "DELETE FROM `token` WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            if ($statement !== false) {
                $statement->bindValue(':id', $id, PDO::PARAM_INT);
                $statement->execute();
                return (object) [
                    "status" => 200,
                    "message" => "Token deleted successfully"
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

    // Lấy token theo giá trị token
    function getTokenByValue($token)
    {
        $query = "SELECT * FROM `token` WHERE `Token` = :token";

        try {
            $statement = $this->connection->prepare($query);
            if ($statement !== false) {
                $statement->bindValue(':token', $token, PDO::PARAM_STR);
                $statement->execute();
                $result = $statement->fetch(PDO::FETCH_ASSOC);
                return (object) [
                    "status" => 200,
                    "message" => "Success",
                    "data" => $result
                ];
            } else {
                throw new PDOException();
            }
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => "Unable to fetch token"
            ];
        }
    }
}
