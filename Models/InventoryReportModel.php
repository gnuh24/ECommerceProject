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
        // Truy vấn lấy thông tin chính từ bảng InventoryReport
        $query = "SELECT * FROM `InventoryReport` WHERE `Id` = :id";
        // Truy vấn lấy chi tiết từ bảng InventoryReportDetail và nối với bảng Product
        $detailsQuery = "
        SELECT d.*, p.ProductName
        FROM `InventoryReportDetail` d
        JOIN `Product` p ON d.ProductId = p.Id
        WHERE d.InventoryReportId = :id
    ";

        try {
            // Lấy thông tin báo cáo
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            $info = $statement->fetch(PDO::FETCH_ASSOC); // Lưu thông tin vào biến info

            // Lấy chi tiết báo cáo với ProductName
            $detailsStatement = $this->connection->prepare($detailsQuery);
            $detailsStatement->bindValue(':id', $id, PDO::PARAM_INT);
            $detailsStatement->execute();
            $details = $detailsStatement->fetchAll(PDO::FETCH_ASSOC); // Lưu chi tiết vào biến details

            // Trả về kết quả với cả thông tin và chi tiết
            return (object) [
                "status" => 200,
                "message" => "Inventory report fetched successfully",
                "data" => [
                    "infor" => $info,
                    "details" => $details
                ]
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }



    public function getAllInventoryReports($pageNumber, $size, $dateFrom = null, $dateTo = null, $search = null)
    {
        if ($size <= 0) {
            $size = 10; // Giá trị mặc định cho $size nếu nó bị 0 hoặc nhỏ hơn
        }
        // Xác định phần sụt giảm và thiết lập giá trị mặc định cho phân trang
        $offset = ($pageNumber - 1) * $size;

        $query = "
                    SELECT * 
                    FROM `InventoryReport` 
                    WHERE 1=1
                ";

        $countQuery = "
                            SELECT COUNT(*) as total 
                            FROM `InventoryReport` 
                            WHERE 1=1
                        ";


        // Thêm điều kiện lọc theo ngày bắt đầu và ngày kết thúc nếu có
        if (!empty($dateFrom) && !empty($dateTo)) {
            $query .= " AND `CreateTime` BETWEEN :dateFrom AND :dateTo";
            $countQuery .= " AND `CreateTime` BETWEEN :dateFrom AND :dateTo";
        } elseif (!empty($dateFrom)) {
            $query .= " AND `CreateTime` >= :dateFrom";
            $countQuery .= " AND `CreateTime` >= :dateFrom";
        } elseif (!empty($dateTo)) {
            $query .= " AND `CreateTime` <= :dateTo";
            $countQuery .= " AND `CreateTime` <= :dateTo";
        }

        // Thêm điều kiện tìm kiếm nếu có
        if (!empty($search)) {
            $query .= " AND (`Supplier` LIKE :search OR `SupplierPhone` LIKE :search)";
            $countQuery .= " AND (`Supplier` LIKE :search OR `SupplierPhone` LIKE :search)";
        }

        // Áp dụng phân trang
        $query .= " ORDER BY `CreateTime` DESC LIMIT :offset, :size";

        try {
            // Chuẩn bị truy vấn đếm
            $countStatement = $this->connection->prepare($countQuery);


            if (!empty($dateFrom)) {
                $countStatement->bindValue(':dateFrom', $dateFrom, PDO::PARAM_STR);
            }
            if (!empty($dateTo)) {
                $countStatement->bindValue(':dateTo', $dateTo, PDO::PARAM_STR);
            }
            if (!empty($search)) {
                $searchParam = "%$search%";
                $countStatement->bindValue(':search', $searchParam, PDO::PARAM_STR);
            }

            // Thực thi truy vấn đếm
            $countStatement->execute();
            $totalItems = $countStatement->fetchColumn();
            $totalPages = ceil($totalItems / $size); // Tính tổng số trang

            // Chuẩn bị truy vấn chính để lấy dữ liệu
            $statement = $this->connection->prepare($query);


            if (!empty($dateFrom)) {
                $statement->bindValue(':dateFrom', $dateFrom, PDO::PARAM_STR);
            }
            if (!empty($dateTo)) {
                $statement->bindValue(':dateTo', $dateTo, PDO::PARAM_STR);
            }
            if (!empty($search)) {
                $statement->bindValue(':search', $searchParam, PDO::PARAM_STR);
            }
            $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
            $statement->bindValue(':size', $size, PDO::PARAM_INT);

            // Thực thi truy vấn chính
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            // Trả về kết quả
            return (object) [
                "status" => 200,
                "message" => "Reports fetched successfully",
                "data" => $result,
                "totalPages" => $totalPages
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }



    // Tạo báo cáo tồn kho mới
    public function createInventoryReport($totalPrice, $supplier, $supplierPhone)
    {
        $query = "INSERT INTO `InventoryReport` (`CreateTime`, `Supplier`, `SupplierPhone`, `TotalPrice`) 
                  VALUES (NOW(), :supplier, :supplierPhone, :totalPrice)";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':supplier', $supplier, PDO::PARAM_STR);
            $statement->bindValue(':supplierPhone', $supplierPhone, PDO::PARAM_STR);
            $statement->bindValue(':totalPrice', $totalPrice, PDO::PARAM_INT);
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
