<?php
require_once __DIR__ . '/../Configure/MysqlConfig.php';

class BatchModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Lấy tất cả các lô hàng
    public function getAllBatches()
    {
        $query = "SELECT * FROM `batch`";

        try {
            $statement = $this->connection->prepare($query);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return (object) [
                "status" => 200,
                "message" => "Batches fetched successfully",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Lấy lô hàng theo ID
    public function getBatchById($id)
    {
        $query = "SELECT * FROM `batch` WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            return (object) [
                "status" => 200,
                "message" => "Batch fetched successfully",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Tạo lô hàng mới
    public function createBatch($unitPrice, $quantity, $receivingTime, $productId)
    {
        $query = "INSERT INTO `batch` (`UnitPrice`, `Quantity`, `ReceivingTime`, `ProductId`) 
                  VALUES (:unitPrice, :quantity, :receivingTime, :productId)";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':unitPrice', $unitPrice, PDO::PARAM_INT);
            $statement->bindValue(':quantity', $quantity, PDO::PARAM_INT);
            $statement->bindValue(':receivingTime', $receivingTime, PDO::PARAM_STR);
            $statement->bindValue(':productId', $productId, PDO::PARAM_INT);
            $statement->execute();

            $id = $this->connection->lastInsertId();
            return (object) [
                "status" => 201,
                "message" => "Batch created successfully",
                "data" => $id
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Cập nhật lô hàng
    public function updateBatch($id, $unitPrice, $quantity, $receivingTime, $productId)
    {
        $query = "UPDATE `batch` 
                  SET `UnitPrice` = :unitPrice, `Quantity` = :quantity, `ReceivingTime` = :receivingTime, `ProductId` = :productId 
                  WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->bindValue(':unitPrice', $unitPrice, PDO::PARAM_INT);
            $statement->bindValue(':quantity', $quantity, PDO::PARAM_INT);
            $statement->bindValue(':receivingTime', $receivingTime, PDO::PARAM_STR);
            $statement->bindValue(':productId', $productId, PDO::PARAM_INT);
            $statement->execute();

            if ($statement->rowCount() > 0) {
                return (object) [
                    "status" => 200,
                    "message" => "Batch updated successfully"
                ];
            } else {
                throw new PDOException("No record was updated");
            }
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Xóa lô hàng theo ID
    public function deleteBatch($id)
    {
        $query = "DELETE FROM `batch` WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();

            return (object) [
                "status" => 200,
                "message" => "Batch deleted successfully"
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }
}
