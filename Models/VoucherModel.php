<?php
require_once __DIR__ . "../../Configure/MysqlConfig.php";

class VoucherModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Check if a voucher code exists, excluding a specific ID if provided
    public function isVoucherCodeExist($code, $id = null)
    {
        if ($id) {
            $query = "SELECT * FROM `Voucher` WHERE `Code` = :code AND `Id` != :id";
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':code', $code, PDO::PARAM_STR);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
        } else {
            $query = "SELECT * FROM `Voucher` WHERE `Code` = :code";
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':code', $code, PDO::PARAM_STR);
        }

        $statement->execute();
        return $statement->rowCount() > 0;
    }

    // Fetch all vouchers without pagination
    public function getAllVouchersNoPaging()
    {
        $query = "SELECT * FROM `Voucher`";

        try {
            $statement = $this->connection->prepare($query);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return (object) [
                "status" => 200,
                "message" => "Vouchers fetched successfully",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Fetch vouchers with pagination and search by code
    public function getAllVouchers($pageNumber = 1, $search = null, $pageSize = 5)
    {
        $offset = ($pageNumber - 1) * $pageSize;
        $query = "SELECT * FROM `Voucher` WHERE `Code` LIKE :search LIMIT :offset, :pageSize";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
            $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
            $statement->bindValue(':pageSize', $pageSize, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            $totalQuery = "SELECT COUNT(*) as total FROM `Voucher` WHERE `Code` LIKE :search";
            $totalStmt = $this->connection->prepare($totalQuery);
            $totalStmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
            $totalStmt->execute();
            $totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);
            $totalElements = $totalResult['total'];
            $totalPages = ceil($totalElements / $pageSize);

            return (object) [
                "status" => 200,
                "message" => "Vouchers fetched successfully",
                "data" => $result,
                "totalPages" => $totalPages,
                "totalElements" => $totalElements,
                "size" => $pageSize
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Fetch a voucher by ID
    public function getVoucherById($id)
    {
        $query = "SELECT * FROM `Voucher` WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            return (object) [
                "status" => 200,
                "message" => "Voucher fetched successfully",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Create a new voucher with code uniqueness check
    public function createVoucher($expirationTime, $code, $condition, $saleAmount, $isPublic = true)
    {
        if ($this->isVoucherCodeExist($code)) {
            return (object) [
                "status" => 409,
                "message" => "Voucher code already exists"
            ];
        }

        $query = "INSERT INTO `Voucher` (`ExpirationTime`, `Code`, `Condition`, `SaleAmount`, `IsPublic`) 
                  VALUES (:expirationTime, :code, :condition, :saleAmount, :isPublic)";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':expirationTime', $expirationTime, PDO::PARAM_STR);
            $statement->bindValue(':code', $code, PDO::PARAM_STR);
            $statement->bindValue(':condition', $condition, PDO::PARAM_INT);
            $statement->bindValue(':saleAmount', $saleAmount, PDO::PARAM_INT);
            $statement->bindValue(':isPublic', $isPublic, PDO::PARAM_BOOL);
            $statement->execute();

            $id = $this->connection->lastInsertId();
            return (object) [
                "status" => 201,
                "message" => "Voucher created successfully",
                "data" => $id
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 500,
                "message" => "An error occurred while creating the voucher: " . $e->getMessage()
            ];
        }
    }

    // Update a voucher
    public function updateVoucher($id, $expirationTime, $code, $condition, $saleAmount, $isPublic)
    {
        if ($this->isVoucherCodeExist($code, $id)) {
            return (object) [
                "status" => 409,
                "message" => "Voucher code already exists"
            ];
        }

        $query = "UPDATE `Voucher` 
                  SET `ExpirationTime` = :expirationTime, `Code` = :code, `Condition` = :condition, 
                      `SaleAmount` = :saleAmount, `IsPublic` = :isPublic 
                  WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':expirationTime', $expirationTime, PDO::PARAM_STR);
            $statement->bindValue(':code', $code, PDO::PARAM_STR);
            $statement->bindValue(':condition', $condition, PDO::PARAM_INT);
            $statement->bindValue(':saleAmount', $saleAmount, PDO::PARAM_INT);
            $statement->bindValue(':isPublic', $isPublic, PDO::PARAM_BOOL);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();

            if ($statement->rowCount() > 0) {
                return (object) [
                    "status" => 200,
                    "message" => "Voucher updated successfully"
                ];
            } else {
                return (object) [
                    "status" => 404,
                    "message" => "Voucher not found"
                ];
            }
        } catch (PDOException $e) {
            return (object) [
                "status" => 500,
                "message" => "An error occurred while updating the voucher: " . $e->getMessage()
            ];
        }
    }

    // Delete a voucher
    public function deleteVoucher($id)
    {
        $query = "DELETE FROM `Voucher` WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();

            return (object) [
                "status" => 200,
                "message" => "Voucher deleted successfully"
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }
}
