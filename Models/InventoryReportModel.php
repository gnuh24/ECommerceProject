<?php
require_once __DIR__ . '/../Configure/MysqlConfig.php';

class InventoryReportModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Lấy tất cả báo cáo tồn kho
    public function getAllReports()
    {
        $query = "SELECT * FROM `InventoryReport`";

        try {
            $statement = $this->connection->prepare($query);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return (object) [
                "status" => 200,
                "message" => "Reports fetched successfully",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Tạo báo cáo tồn kho mới
    public function createReport($supplier, $supplierPhone, $totalPrice)
    {
        $query = "INSERT INTO `InventoryReport` (`CreateTime`, `Supplier`, `SupplierPhone`, `TotalPrice`) 
                  VALUES (NOW(), :supplier, :supplierPhone, :totalPrice)";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':supplier', $supplier, PDO::PARAM_STR);
            $statement->bindValue(':supplierPhone', $supplierPhone, PDO::PARAM_STR);
            $statement->bindValue(':totalPrice', $totalPrice, PDO::PARAM_INT);
            $statement->execute();
            return (object) [
                "status" => 201,
                "message" => "Inventory report created successfully"
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Cập nhật báo cáo tồn kho
    public function updateReport($id, $supplier, $supplierPhone, $totalPrice)
    {
        $query = "UPDATE `InventoryReport` 
                  SET `Supplier` = :supplier, `SupplierPhone` = :supplierPhone, `TotalPrice` = :totalPrice 
                  WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->bindValue(':supplier', $supplier, PDO::PARAM_STR);
            $statement->bindValue(':supplierPhone', $supplierPhone, PDO::PARAM_STR);
            $statement->bindValue(':totalPrice', $totalPrice, PDO::PARAM_INT);
            $statement->execute();

            return (object) [
                "status" => 200,
                "message" => "Inventory report updated successfully"
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Xóa báo cáo tồn kho
    public function deleteReport($id)
    {
        $query = "DELETE FROM `InventoryReport` WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();

            return (object) [
                "status" => 200,
                "message" => "Inventory report deleted successfully"
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }
}
