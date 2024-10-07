<?php
require_once __DIR__ . "/../../Configure/MysqlConfig.php";

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

    // Tạo chi tiết báo cáo tồn kho mới
    public function createInventoryReportDetail($form)
    {
        $query = "INSERT INTO `InventoryReportDetail` 
                  (`InventoryReportId`, `ProductId`, `Quantity`, `UnitPrice`, `Total`, `Profit`) 
                  VALUES (:inventoryReportId, :productId, :quantity, :unitPrice, :total, :profit)";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':inventoryReportId', $form['InventoryReportId'], PDO::PARAM_INT);
            $statement->bindValue(':productId', $form['ProductId'], PDO::PARAM_INT);
            $statement->bindValue(':quantity', $form['Quantity'], PDO::PARAM_INT);
            $statement->bindValue(':unitPrice', $form['UnitPrice'], PDO::PARAM_INT);
            $statement->bindValue(':total', $form['Total'], PDO::PARAM_INT);
            $statement->bindValue(':profit', $form['Profit'], PDO::PARAM_INT);
            $statement->execute();
            $id = $this->connection->lastInsertId();
            return (object) [
                "status" => 201,
                "message" => "Inventory report detail created successfully",
                "data" => $id
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }
}
