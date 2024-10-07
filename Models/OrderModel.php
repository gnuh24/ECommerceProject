<?php
require_once __DIR__ . "/../../Configure/MysqlConfig.php";

class OrderModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Lấy tất cả các đơn hàng với phân trang và tìm kiếm
    public function getAllOrder($page, $size, $search)
    {
        $query = "SELECT * FROM `order` WHERE `Note` LIKE :search LIMIT :offset, :size";
        $offset = ($page - 1) * $size;

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':search', "%$search%", PDO::PARAM_STR);
            $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
            $statement->bindValue(':size', $size, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            return (object) [
                "status" => 200,
                "message" => "Orders fetched successfully",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Kiểm tra xem đơn hàng có thuộc về ID người dùng không
    public function isOrderBelongToThisId($userInformationId, $orderId)
    {
        $query = "SELECT COUNT(*) FROM `order` WHERE `Id` = :orderId AND `AccountId` = :userId";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':orderId', $orderId, PDO::PARAM_STR);
            $statement->bindValue(':userId', $userInformationId, PDO::PARAM_INT);
            $statement->execute();
            $count = $statement->fetchColumn();
            return $count > 0; // Trả về true nếu có đơn hàng thuộc về người dùng
        } catch (PDOException $e) {
            return false; // Trả về false nếu có lỗi
        }
    }

    // Lấy tất cả đơn hàng của một người dùng dựa trên token
    public function getAllOrderByUser($token)
    {
        // Giả định rằng bạn đã xác thực token và lấy được AccountId từ nó
        $accountId = $this->getAccountIdFromToken($token);

        return $this->getOrdersByAccountId($accountId);
    }

    // Lấy đơn hàng theo Id
    public function getOrderById($orderId)
    {
        return $this->getOrder($orderId);
    }

    // Lấy đơn hàng theo Id với xác thực token
    public function getOrderByIdWithToken($token, $orderId)
    {
        // Giả định rằng bạn đã xác thực token và lấy được AccountId từ nó
        $accountId = $this->getAccountIdFromToken($token);

        if ($this->isOrderBelongToThisId($accountId, $orderId)) {
            return $this->getOrder($orderId);
        } else {
            return (object) [
                "status" => 403,
                "message" => "Access denied"
            ];
        }
    }

    // Tạo đơn hàng mới
    public function createOrder($form)
    {
        $orderId = uniqid(); // Tạo ID cho đơn hàng mới
        $orderTime = date('Y-m-d H:i:s'); // Lấy thời gian hiện tại
        $totalPrice = $form->totalPrice;
        $note = $form->note;
        $accountId = $form->accountId; // Có thể lấy từ token

        return $this->createNewOrder($orderId, $orderTime, $totalPrice, $note, $accountId);
    }

    // Hàm lấy AccountId từ token (giả định)
    private function getAccountIdFromToken($token)
    {
        // Giả định bạn có một hàm để lấy AccountId từ token
        // Ví dụ: giải mã JWT hoặc xác thực token
        return 1; // Thay đổi tùy theo logic thực tế của bạn
    }

    // Phương thức riêng để lấy đơn hàng theo Id
    private function getOrder($orderId)
    {
        $query = "SELECT * FROM `order` WHERE `Id` = :orderId";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':orderId', $orderId, PDO::PARAM_STR);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            return (object) [
                "status" => 200,
                "message" => "Order fetched successfully",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Tạo đơn hàng mới
    private function createNewOrder($orderId, $orderTime, $totalPrice, $note, $accountId)
    {
        $query = "INSERT INTO `order` (`Id`, `OrderTime`, `TotalPrice`, `Note`, `AccountId`)
                  VALUES (:orderId, :orderTime, :totalPrice, :note, :accountId)";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':orderId', $orderId, PDO::PARAM_STR);
            $statement->bindValue(':orderTime', $orderTime, PDO::PARAM_STR);
            $statement->bindValue(':totalPrice', $totalPrice, PDO::PARAM_INT);
            $statement->bindValue(':note', $note, PDO::PARAM_STR);
            $statement->bindValue(':accountId', $accountId, PDO::PARAM_INT);
            $statement->execute();
            return (object) [
                "status" => 201,
                "message" => "Order created successfully"
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Xóa đơn hàng theo Id
    public function deleteOrder($orderId)
    {
        $query = "DELETE FROM `order` WHERE `Id` = :orderId";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':orderId', $orderId, PDO::PARAM_STR);
            $statement->execute();

            return (object) [
                "status" => 200,
                "message" => "Order deleted successfully"
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Lấy tất cả đơn hàng của một tài khoản dựa trên AccountId
    public function getOrdersByAccountId($accountId)
    {
        $query = "SELECT * FROM `order` WHERE `AccountId` = :accountId";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':accountId', $accountId, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            return (object) [
                "status" => 200,
                "message" => "Orders fetched successfully",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }
}
