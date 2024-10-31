<?php
require_once __DIR__ . "../../Configure/MysqlConfig.php";

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
    public function createOrderDetail($orderId, $detail)
    {
        $query = "INSERT INTO `OrderDetail` (`OrderId`, `ProductId`, `Quantity`, `UnitPrice`, `Total`) 
              VALUES (:orderId, :productId, :quantity, :unitPrice, :total)";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':orderId', $orderId, PDO::PARAM_STR);
            $statement->bindValue(':productId', $detail['productId'], PDO::PARAM_INT);
            $statement->bindValue(':quantity', $detail['quantity'], PDO::PARAM_INT);
            $statement->bindValue(':unitPrice', $detail['unitPrice'], PDO::PARAM_INT);
            $statement->bindValue(':total', $detail['unitPrice'] * $detail['quantity'], PDO::PARAM_INT);

            $statement->execute();

            return [
                'status' => 201,
                'message' => 'Order detail created successfully'
            ];
        } catch (PDOException $e) {
            return [
                'status' => 400,
                'message' => 'Error creating order detail: ' . $e->getMessage()
            ];
        }
    }
}
