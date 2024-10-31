<?php
require_once __DIR__ . "../../Configure/MysqlConfig.php";

class OrderStatusModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }


    public function createOrderStatus($orderId, $status)
    {
        $query = "INSERT INTO `OrderStatus` (`OrderId`, `Status`) 
                  VALUES (:orderId, :status)";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':orderId', $orderId, PDO::PARAM_STR);
            $statement->bindValue(':status', $status, PDO::PARAM_STR);
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
