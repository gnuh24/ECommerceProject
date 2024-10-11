<?php
require_once __DIR__ . "../../Configure/MysqlConfig.php";

class InventoryReportDetailModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Lấy chi tiết báo cáo tồn kho theo InventoryReportDetailId
    public function getInventoryReportDetailById($inventoryReportDetailId)
    {
        $query = "SELECT * FROM `InventoryReportDetail` WHERE `InventoryReportDetailId` = :inventoryReportDetailId";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':inventoryReportDetailId', $inventoryReportDetailId, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            return (object) [
                "status" => 200,
                "message" => "Inventory report detail fetched successfully",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    public function addInventoryReportDetails($inventoryReportId, $inventoryDetails)
    {
        $query = "INSERT INTO `InventoryReportDetail` 
              (`InventoryReportId`, `ProductId`, `Quantity`, `UnitPrice`, `Total`, `Profit`) 
              VALUES (:inventoryReportId, :productId, :quantity, :unitPrice, :total, :profit)";

        try {
            $this->connection->beginTransaction();

            foreach ($inventoryDetails as $detail) {
                $statement = $this->connection->prepare($query);
                $statement->bindValue(':inventoryReportId', $inventoryReportId, PDO::PARAM_INT);
                $statement->bindValue(':productId', $detail['ProductId'], PDO::PARAM_INT);
                $statement->bindValue(':quantity', $detail['Quantity'], PDO::PARAM_INT);
                $statement->bindValue(':unitPrice', $detail['UnitPrice'], PDO::PARAM_INT);
                $statement->bindValue(':total', $detail['Total'], PDO::PARAM_INT);
                $statement->bindValue(':profit', $detail['Profit'], PDO::PARAM_INT);
                $statement->execute();
            }

            $this->connection->commit();
            return (object) [
                "status" => 201,
                "message" => "Inventory report details created successfully"
            ];
        } catch (PDOException $e) {
            $this->connection->rollBack();
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }
}
