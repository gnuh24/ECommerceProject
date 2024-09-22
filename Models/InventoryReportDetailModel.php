<?php
require_once __DIR__ . '/../Configure/MysqlConfig.php';

class InventoryReportDetailModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Lấy tất cả chi tiết báo cáo tồn kho theo InventoryReportId
    public function getDetailsByReportId($inventoryReportId)
    {
        $query = "SELECT * FROM `InventoryReportDetail` WHERE `InventoryReportId` = :inventoryReportId";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':inventoryReportId', $inventoryReportId, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return (object) [
                "status" => 200,
                "message" => "Details fetched successfully",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Tạo chi tiết báo cáo tồn kho mới
    public function createDetail($inventoryReportId, $productId, $quantity, $unitPrice, $total, $profit)
    {
        $query = "INSERT INTO `InventoryReportDetail` (`InventoryReportId`, `ProductId`, `Quantity`, `UnitPrice`, `Total`, `Profit`) 
                  VALUES (:inventoryReportId, :productId, :quantity, :unitPrice, :total, :profit)";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':inventoryReportId', $inventoryReportId, PDO::PARAM_INT);
            $statement->bindValue(':productId', $productId, PDO::PARAM_INT);
            $statement->bindValue(':quantity', $quantity, PDO::PARAM_INT);
            $statement->bindValue(':unitPrice', $unitPrice, PDO::PARAM_INT);
            $statement->bindValue(':total', $total, PDO::PARAM_INT);
            $statement->bindValue(':profit', $profit, PDO::PARAM_INT);
            $statement->execute();
            return (object) [
                "status" => 201,
                "message" => "Inventory report detail created successfully"
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Cập nhật chi tiết báo cáo tồn kho
    public function updateDetail($inventoryReportId, $productId, $quantity, $unitPrice, $total, $profit)
    {
        $query = "UPDATE `InventoryReportDetail` 
                  SET `Quantity` = :quantity, `UnitPrice` = :unitPrice, `Total` = :total, `Profit` = :profit 
                  WHERE `InventoryReportId` = :inventoryReportId AND `ProductId` = :productId";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':inventoryReportId', $inventoryReportId, PDO::PARAM_INT);
            $statement->bindValue(':productId', $productId, PDO::PARAM_INT);
            $statement->bindValue(':quantity', $quantity, PDO::PARAM_INT);
            $statement->bindValue(':unitPrice', $unitPrice, PDO::PARAM_INT);
            $statement->bindValue(':total', $total, PDO::PARAM_INT);
            $statement->bindValue(':profit', $profit, PDO::PARAM_INT);
            $statement->execute();

            return (object) [
                "status" => 200,
                "message" => "Inventory report detail updated successfully"
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Xóa chi tiết báo cáo tồn kho
    public function deleteDetail($inventoryReportId, $productId)
    {
        $query = "DELETE FROM `InventoryReportDetail` 
                  WHERE `InventoryReportId` = :inventoryReportId AND `ProductId` = :productId";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':inventoryReportId', $inventoryReportId, PDO::PARAM_INT);
            $statement->bindValue(':productId', $productId, PDO::PARAM_INT);
            $statement->execute();

            return (object) [
                "status" => 200,
                "message" => "Inventory report detail deleted successfully"
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }
}
