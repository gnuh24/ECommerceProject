<?php
require_once __DIR__ . "/../../Configure/MysqlConfig.php";

class BatchModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Tạo lô hàng mới
    public function createBatch(BatchCreateForm $form)
    {
        $query = "INSERT INTO `batch` (`UnitPrice`, `Quantity`, `ReceivingTime`, `ProductId`) 
                  VALUES (:unitPrice, :quantity, :receivingTime, :productId)";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':unitPrice', $form->unitPrice, PDO::PARAM_INT);
            $statement->bindValue(':quantity', $form->quantity, PDO::PARAM_INT);
            $statement->bindValue(':receivingTime', $form->receivingTime, PDO::PARAM_STR);
            $statement->bindValue(':productId', $form->productId, PDO::PARAM_INT);
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
    public function updateBatch(BatchUpdateForm $form)
    {
        $query = "UPDATE `batch` 
                  SET `UnitPrice` = :unitPrice, `Quantity` = :quantity, `ReceivingTime` = :receivingTime, `ProductId` = :productId 
                  WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $form->id, PDO::PARAM_INT);
            $statement->bindValue(':unitPrice', $form->unitPrice, PDO::PARAM_INT);
            $statement->bindValue(':quantity', $form->quantity, PDO::PARAM_INT);
            $statement->bindValue(':receivingTime', $form->receivingTime, PDO::PARAM_STR);
            $statement->bindValue(':productId', $form->productId, PDO::PARAM_INT);
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

    // Lấy tất cả lô hàng theo ProductId
    public function getAllBatchByProductId($productId)
    {
        $query = "SELECT * FROM `batch` WHERE `ProductId` = :productId";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':productId', $productId, PDO::PARAM_INT);
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

    // Lấy lô hàng hợp lệ
    public function getTheValidBatch($productId)
    {
        $query = "SELECT * FROM `batch` 
                  WHERE `ProductId` = :productId 
                  AND `Quantity` > 0 
                  ORDER BY `ReceivingTime` ASC 
                  LIMIT 1";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':productId', $productId, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            return (object) [
                "status" => 200,
                "message" => "Valid batch fetched successfully",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Lấy lô hàng hợp lệ (backup)
    public function getTheValidBatchBackup($productId)
    {
        $query = "SELECT * FROM `batch` 
                  WHERE `ProductId` = :productId 
                  AND `Quantity` > 0 
                  ORDER BY `ReceivingTime` DESC 
                  LIMIT 1";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':productId', $productId, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            return (object) [
                "status" => 200,
                "message" => "Backup valid batch fetched successfully",
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
    public function getBatchById($batchId)
    {
        $query = "SELECT * FROM `batch` WHERE `Id` = :batchId";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':batchId', $batchId, PDO::PARAM_INT);
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
}
