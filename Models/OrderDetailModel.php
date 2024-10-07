<?php
require_once __DIR__ . "/../../Configure/MysqlConfig.php";

class OrderDetailModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Lấy chi tiết đơn hàng theo OrderId
    public function getAllOrderDetailByOrderId($orderId)
    {
        $query = "SELECT * FROM `OrderDetail` WHERE `OrderId` = :orderId";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':orderId', $orderId, PDO::PARAM_STR);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return (object) [
                "status" => 200,
                "message" => "Order details fetched successfully",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Tạo chi tiết đơn hàng mới
    public function createOrderDetail($orderId, $productId, $quantity, $unitPrice, $total)
    {
        $query = "INSERT INTO `OrderDetail` (`OrderId`, `ProductId`, `Quantity`, `UnitPrice`, `Total`) 
                  VALUES (:orderId, :productId, :quantity, :unitPrice, :total)";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':orderId', $orderId, PDO::PARAM_STR);
            $statement->bindValue(':productId', $productId, PDO::PARAM_INT);
            $statement->bindValue(':quantity', $quantity, PDO::PARAM_INT);
            $statement->bindValue(':unitPrice', $unitPrice, PDO::PARAM_INT);
            $statement->bindValue(':total', $total, PDO::PARAM_INT);
            $statement->execute();
            return (object) [
                "status" => 201,
                "message" => "Order detail created successfully"
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Cập nhật chi tiết đơn hàng
    public function updateOrderDetail($orderId, $productId, $quantity, $unitPrice, $total)
    {
        $query = "UPDATE `OrderDetail` 
                  SET `Quantity` = :quantity, `UnitPrice` = :unitPrice, `Total` = :total 
                  WHERE `OrderId` = :orderId AND `ProductId` = :productId";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':orderId', $orderId, PDO::PARAM_STR);
            $statement->bindValue(':productId', $productId, PDO::PARAM_INT);
            $statement->bindValue(':quantity', $quantity, PDO::PARAM_INT);
            $statement->bindValue(':unitPrice', $unitPrice, PDO::PARAM_INT);
            $statement->bindValue(':total', $total, PDO::PARAM_INT);
            $statement->execute();

            return (object) [
                "status" => 200,
                "message" => "Order detail updated successfully"
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Xóa chi tiết đơn hàng (nếu cần)
    public function deleteOrderDetail($orderId, $productId)
    {
        $query = "DELETE FROM `OrderDetail` WHERE `OrderId` = :orderId AND `ProductId` = :productId";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':orderId', $orderId, PDO::PARAM_STR);
            $statement->bindValue(':productId', $productId, PDO::PARAM_INT);
            $statement->execute();

            return (object) [
                "status" => 200,
                "message" => "Order detail deleted successfully"
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }
}
