<?php
require_once __DIR__ . '/../Configure/MysqlConfig.php';

class OrderStatusModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Lấy trạng thái đơn hàng theo OrderId
    public function getStatusByOrderId($orderId)
    {
        $query = "SELECT * FROM `OrderStatus` WHERE `OrderId` = :orderId";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':orderId', $orderId, PDO::PARAM_STR);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return (object) [
                "status" => 200,
                "message" => "Status fetched successfully",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Tạo trạng thái mới cho đơn hàng
    public function createOrderStatus($orderId, $status, $updateTime)
    {
        $query = "INSERT INTO `OrderStatus` (`OrderId`, `Status`, `UpdateTime`) 
                  VALUES (:orderId, :status, :updateTime)";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':orderId', $orderId, PDO::PARAM_STR);
            $statement->bindValue(':status', $status, PDO::PARAM_STR);
            $statement->bindValue(':updateTime', $updateTime, PDO::PARAM_STR);
            $statement->execute();
            return (object) [
                "status" => 201,
                "message" => "Order status created successfully"
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Cập nhật trạng thái của đơn hàng
    public function updateOrderStatus($orderId, $status, $updateTime)
    {
        $query = "UPDATE `OrderStatus` 
                  SET `UpdateTime` = :updateTime 
                  WHERE `OrderId` = :orderId AND `Status` = :status";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':orderId', $orderId, PDO::PARAM_STR);
            $statement->bindValue(':status', $status, PDO::PARAM_STR);
            $statement->bindValue(':updateTime', $updateTime, PDO::PARAM_STR);
            $statement->execute();

            return (object) [
                "status" => 200,
                "message" => "Order status updated successfully"
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Xóa trạng thái của đơn hàng
    public function deleteOrderStatus($orderId, $status)
    {
        $query = "DELETE FROM `OrderStatus` WHERE `OrderId` = :orderId AND `Status` = :status";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':orderId', $orderId, PDO::PARAM_STR);
            $statement->bindValue(':status', $status, PDO::PARAM_STR);
            $statement->execute();

            return (object) [
                "status" => 200,
                "message" => "Order status deleted successfully"
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }
}
