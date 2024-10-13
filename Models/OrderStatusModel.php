<?php
require_once __DIR__ . "../../Configure/MysqlConfig.php";

class OrderStatusModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Lấy trạng thái mới nhất của đơn hàng theo OrderId
    public function getNewestOrderStatus($orderId)
    {
        $query = "SELECT * FROM `OrderStatus` 
                  WHERE `OrderId` = :orderId 
                  ORDER BY `UpdateTime` DESC 
                  LIMIT 1";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':orderId', $orderId, PDO::PARAM_STR);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            return (object) [
                "status" => 200,
                "message" => "Newest order status fetched successfully",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Tạo trạng thái đơn hàng lần đầu tiên
    public function createOrderStatusFirstTime($form)
    {
        $query = "INSERT INTO `OrderStatus` (`OrderId`, `Status`, `UpdateTime`) 
                  VALUES (:orderId, :status, :updateTime)";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':orderId', $form->orderId, PDO::PARAM_STR);
            $statement->bindValue(':status', $form->status, PDO::PARAM_STR);
            $statement->bindValue(':updateTime', $form->updateTime, PDO::PARAM_STR);
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

    // Tạo trạng thái mới cho đơn hàng (không phải lần đầu)
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
}
