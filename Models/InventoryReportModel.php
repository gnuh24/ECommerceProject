<?php
require_once __DIR__ . "../../Configure/MysqlConfig.php";

class InventoryReportModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Lấy báo cáo tồn kho theo ID
    public function getInventoryReportById($id)
    {
        $query = "SELECT * FROM `InventoryReport` WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            return (object) [
                "status" => 200,
                "message" => "Inventory report fetched successfully",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Lấy tất cả báo cáo tồn kho với phân trang và bộ lọc
    public function getAllInventoryReports($offset, $limit, $filterForm, $search = null)
    {
        $query = "SELECT * FROM `InventoryReport` WHERE 1=1";

        // Thêm điều kiện bộ lọc
        if (isset($filterForm['Supplier'])) {
            $query .= " AND `Supplier` = :supplier";
        }
        if (isset($filterForm['DateFrom']) && isset($filterForm['DateTo'])) {
            $query .= " AND `CreateTime` BETWEEN :dateFrom AND :dateTo";
        }
        if ($search) {
            $query .= " AND (`Supplier` LIKE :search OR `SupplierPhone` LIKE :search)";
        }

        $query .= " LIMIT :offset, :limit";

        try {
            $statement = $this->connection->prepare($query);

            // Ràng buộc giá trị tham số
            if (isset($filterForm['Supplier'])) {
                $statement->bindValue(':supplier', $filterForm['Supplier'], PDO::PARAM_STR);
            }
            if (isset($filterForm['DateFrom']) && isset($filterForm['DateTo'])) {
                $statement->bindValue(':dateFrom', $filterForm['DateFrom'], PDO::PARAM_STR);
                $statement->bindValue(':dateTo', $filterForm['DateTo'], PDO::PARAM_STR);
            }
            if ($search) {
                $searchParam = "%$search%";
                $statement->bindValue(':search', $searchParam, PDO::PARAM_STR);
            }

            $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
            $statement->bindValue(':limit', $limit, PDO::PARAM_INT);

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
    public function createInventoryReport($form)
    {
        $query = "INSERT INTO `InventoryReport` (`CreateTime`, `Supplier`, `SupplierPhone`, `TotalPrice`) 
                  VALUES (NOW(), :supplier, :supplierPhone, :totalPrice)";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':supplier', $form['Supplier'], PDO::PARAM_STR);
            $statement->bindValue(':supplierPhone', $form['SupplierPhone'], PDO::PARAM_STR);
            $statement->bindValue(':totalPrice', $form['TotalPrice'], PDO::PARAM_INT);
            $statement->execute();

            $id = $this->connection->lastInsertId();
            return (object) [
                "status" => 201,
                "message" => "Inventory report created successfully",
                "data" => $id
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Cập nhật báo cáo tồn kho
    public function updateInventoryReportById($form)
    {
        $query = "UPDATE `InventoryReport` 
                  SET `Supplier` = :supplier, `SupplierPhone` = :supplierPhone, `TotalPrice` = :totalPrice 
                  WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $form['Id'], PDO::PARAM_INT);
            $statement->bindValue(':supplier', $form['Supplier'], PDO::PARAM_STR);
            $statement->bindValue(':supplierPhone', $form['SupplierPhone'], PDO::PARAM_STR);
            $statement->bindValue(':totalPrice', $form['TotalPrice'], PDO::PARAM_INT);
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
}
