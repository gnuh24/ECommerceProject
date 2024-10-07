<?php
require_once __DIR__ . "/../../Configure/MysqlConfig.php";

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

    // Tạo trạng thái mới cho đơn hàng lần đầu
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

    // Tạo trạng thái đơn hàng (có kiểm tra tồn kho)
    public function createOrderStatus($token, $form)
    {
        // Kiểm tra tồn kho ở đây
        // Nếu không đủ hàng tồn kho, ném exception NotEnoughInventory
        // ...

        return $this->createOrderStatusFirstTime($form);
    }

    // Tạo trạng thái đơn hàng (có xác thực quyền truy cập)
    public function createOrderStatusWithAccess($token, $form)
    {
        // Kiểm tra quyền truy cập ở đây
        // Nếu không có quyền, ném exception AccessDeniedException
        // ...

        return $this->createOrderStatus($token, $form);
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
