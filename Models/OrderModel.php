<?php
require_once __DIR__ . "../../Configure/MysqlConfig.php";

class OrderModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Lấy tất cả các đơn hàng với phân trang và tìm kiếm
    public function getAllOrder($pageNumber, $size, $minNgayTao, $maxNgayTao, $status)
    {
        // Xác định phần sụt giảm và điều kiện cho truy vấn
        $offset = ($pageNumber - 1) * $size;
        $query = "
        SELECT o.*, os.Status, u.Fullname, u.Email, u.PhoneNumber, u.Address, u.Birthday, u.Gender
        FROM `Order` o
        LEFT JOIN `OrderStatus` os ON o.Id = os.OrderId
        LEFT JOIN `UserInformation` u ON o.AccountId = u.Id
        WHERE 1=1
    ";

        // Kiểm tra điều kiện ngày bắt đầu và ngày kết thúc
        if (!empty($minNgayTao) && !empty($maxNgayTao)) {
            // Cả 2 ngày đều tồn tại
            $query .= " AND o.OrderTime BETWEEN :from AND :to";
        } elseif (!empty($minNgayTao)) {
            // Chỉ ngày bắt đầu tồn tại
            $query .= " AND o.OrderTime >= :from";
        } elseif (!empty($maxNgayTao)) {
            // Chỉ ngày kết thúc tồn tại
            $query .= " AND o.OrderTime <= :to";
        }

        // Điều kiện trạng thái đơn hàng nếu có
        if (!empty($status)) {
            $query .= " AND os.Status = :status";
        }

        $query .= "
        AND os.UpdateTime = (
            SELECT MAX(os2.UpdateTime)
            FROM `OrderStatus` os2
            WHERE os2.OrderId = o.Id
        )
    ";

        // Tính toán tổng số đơn hàng
        $countQuery = "
        SELECT COUNT(*) AS total
        FROM `Order` o
        LEFT JOIN `OrderStatus` os ON o.Id = os.OrderId
        LEFT JOIN `UserInformation` u ON o.AccountId = u.Id
        WHERE 1=1
    ";

        // Điều kiện ngày bắt đầu và ngày kết thúc cho truy vấn đếm
        if (!empty($minNgayTao) && !empty($maxNgayTao)) {
            $countQuery .= " AND o.OrderTime BETWEEN :from AND :to";
        } elseif (!empty($minNgayTao)) {
            $countQuery .= " AND o.OrderTime >= :from";
        } elseif (!empty($maxNgayTao)) {
            $countQuery .= " AND o.OrderTime <= :to";
        }

        // Điều kiện trạng thái đơn hàng nếu có
        if (!empty($status)) {
            $countQuery .= " AND os.Status = :status";
        }

        $countQuery .= "
        AND os.UpdateTime = (
            SELECT MAX(os2.UpdateTime)
            FROM `OrderStatus` os2
            WHERE os2.OrderId = o.Id
        )
    ";

        // Thực hiện truy vấn đếm
        try {
            $countStatement = $this->connection->prepare($countQuery);
            if (!empty($minNgayTao)) {
                $countStatement->bindValue(':from', $minNgayTao, PDO::PARAM_STR);
            }
            if (!empty($maxNgayTao)) {
                $countStatement->bindValue(':to', $maxNgayTao, PDO::PARAM_STR);
            }
            if (!empty($status)) {
                $countStatement->bindValue(':status', $status, PDO::PARAM_STR);
            }
            $countStatement->execute();
            $totalCount = $countStatement->fetchColumn();
            $totalPages = ceil($totalCount / $size); // Tính tổng số trang

            // Truy vấn lấy dữ liệu đơn hàng
            $query .= " ORDER BY o.OrderTime DESC LIMIT :offset, :size";
            $statement = $this->connection->prepare($query);
            if (!empty($minNgayTao)) {
                $statement->bindValue(':from', $minNgayTao, PDO::PARAM_STR);
            }
            if (!empty($maxNgayTao)) {
                $statement->bindValue(':to', $maxNgayTao, PDO::PARAM_STR);
            }
            if (!empty($status)) {
                $statement->bindValue(':status', $status, PDO::PARAM_STR);
            }
            $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
            $statement->bindValue(':size', $size, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            return (object) [
                "status" => 200,
                "message" => "Orders fetched successfully",
                "totalPages" => $totalPages,
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
