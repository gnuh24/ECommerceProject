<?php
require_once __DIR__ . "../../Configure/MysqlConfig.php";

class OrderModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Lấy tất cả các đơn hàng với phân trang và filter
    public function getAllOrder($pageNumber, $pageSize, $minNgayTao, $maxNgayTao, $status)
    {
        // Xác định phần sụt giảm và điều kiện cho truy vấn
        $offset = ($pageNumber - 1) * $pageSize;
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
            $query .= " AND DATE(o.OrderTime) BETWEEN :from AND :to";
        } elseif (!empty($minNgayTao)) {
            // Chỉ ngày bắt đầu tồn tại
            $query .= " AND DATE(o.OrderTime) >= :from";
        } elseif (!empty($maxNgayTao)) {
            // Chỉ ngày kết thúc tồn tại
            $query .= " AND DATE(o.OrderTime) <= :to";
        }

        // Điều kiện trạng thái đơn hàng nếu có
        if (!empty($status)) {
            $query .= " AND os.Status = :status";
        }

        // Lọc ra đúng nh
        $query .= "
                    AND os.UpdateTime = (
                        SELECT MAX(os2.UpdateTime)
                        FROM `OrderStatus` os2
                        WHERE os2.OrderId = o.Id
                    )
                ";

        //Tính tổng phần tử
        $totalElementsQuery = "
                        SELECT COUNT(*) AS total
                        FROM `Order` o
                        LEFT JOIN `OrderStatus` os ON o.Id = os.OrderId
                        LEFT JOIN `UserInformation` u ON o.AccountId = u.Id
                        WHERE 1=1
                    ";

        // Điều kiện ngày bắt đầu và ngày kết thúc cho truy vấn đếm
        if (!empty($minNgayTao) && !empty($maxNgayTao)) {
            $totalElementsQuery .= " AND o.OrderTime BETWEEN :from AND :to";
        } elseif (!empty($minNgayTao)) {
            $totalElementsQuery .= " AND DATE(o.OrderTime) >= :from";
        } elseif (!empty($maxNgayTao)) {
            $totalElementsQuery .= " AND DATE(o.OrderTime) <= :to";
        }

        // Điều kiện trạng thái đơn hàng nếu có
        if (!empty($status)) {
            $totalElementsQuery .= " AND os.Status = :status";
        }

        $totalElementsQuery .=  "
                                    AND os.UpdateTime = (
                                        SELECT MAX(os2.UpdateTime)
                                        FROM `OrderStatus` os2
                                        WHERE os2.OrderId = o.Id
                                    )
                                ";

        // Thực hiện truy vấn đếm
        try {


            // Tính totalElements của truy vấn
            $totalElementsStatement = $this->connection->prepare($totalElementsQuery);
            if (!empty($minNgayTao)) {
                $totalElementsStatement->bindValue(':from', $minNgayTao, PDO::PARAM_STR);
            }
            if (!empty($maxNgayTao)) {
                $totalElementsStatement->bindValue(':to', $maxNgayTao, PDO::PARAM_STR);
            }
            if (!empty($status)) {
                $totalElementsStatement->bindValue(':status', $status, PDO::PARAM_STR);
            }
            $totalElementsStatement->execute();
            $totalElements = $totalElementsStatement->fetchColumn();




            // Truy vấn lấy dữ liệu đơn hàng
            $query .= " ORDER BY o.OrderTime DESC LIMIT :offset, :pageSize";
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
            $statement->bindValue(':pageSize', $pageSize, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            return (object) [
                "status" => 200,
                "message" => "Orders fetched successfully",
                "totalElements" => $totalElements,
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    public function getOrderById($orderId)
    {
        // Truy vấn chính lấy thông tin đơn hàng và chi tiết sản phẩm
        $orderQuery = "
                        SELECT o.*, 
                            od.ProductId, 
                            od.Quantity, 
                            od.UnitPrice, 
                            od.Total,
                            ui.Email, 
                            ui.Address, 
                            ui.Birthday, 
                            ui.Fullname, 
                            ui.Gender, 
                            ui.PhoneNumber,
                            p.Image,
                            p.ProductName
                        FROM `Order` o
                        LEFT JOIN `OrderDetail` od ON o.Id = od.OrderId
                        LEFT JOIN `UserInformation` ui ON o.AccountId = ui.Id
                        LEFT JOIN `Product` p ON od.ProductId = p.Id
                        WHERE o.Id = :orderId
                    ";

        // Truy vấn lấy toàn bộ trạng thái đơn hàng
        $statusQuery = "
                        SELECT os.Status, os.UpdateTime
                        FROM `OrderStatus` os
                        WHERE os.OrderId = :orderId
                        ORDER BY os.UpdateTime ASC
                    ";

        try {
            // Thực hiện truy vấn cho chi tiết đơn hàng
            $statement = $this->connection->prepare($orderQuery);
            $statement->bindValue(':orderId', $orderId, PDO::PARAM_STR);
            $statement->execute();
            $orderResults = $statement->fetchAll(PDO::FETCH_ASSOC);

            // Kiểm tra nếu không có kết quả
            if (empty($orderResults)) {
                return (object) [
                    "status" => 404,
                    "message" => "Order not found"
                ];
            }

            // Tách biệt thông tin chính và chi tiết sản phẩm
            $info = [
                "OrderTime" => $orderResults[0]['OrderTime'],
                "TotalPrice" => $orderResults[0]['TotalPrice'],
                "Note" => $orderResults[0]['Note'],
                "Address" => $orderResults[0]['Address'],
                "Fullname" => $orderResults[0]['Fullname'],
                "PhoneNumber" => $orderResults[0]['PhoneNumber'],
                "Payment" => $orderResults[0]['Payment'],
                "isPaid" => $orderResults[0]['isPaid']
            ];

            // Tạo danh sách chi tiết sản phẩm (loại bỏ trùng lặp)
            $details = [];
            foreach ($orderResults as $row) {
                if (!isset($details[$row['ProductId']])) {
                    $details[$row['ProductId']] = [
                        "ProductId" => $row['ProductId'],
                        "ProductName" => $row['ProductName'],
                        "Quantity" => $row['Quantity'],
                        "UnitPrice" => $row['UnitPrice'],
                        "Total" => $row['Total'],
                        "Image" => $row['Image']
                    ];
                }
            }

            // Thực hiện truy vấn cho trạng thái đơn hàng
            $statusStatement = $this->connection->prepare($statusQuery);
            $statusStatement->bindValue(':orderId', $orderId, PDO::PARAM_STR);
            $statusStatement->execute();
            $statusResults = $statusStatement->fetchAll(PDO::FETCH_ASSOC);

            // Tạo danh sách trạng thái đơn hàng
            $orderStatuses = [];
            foreach ($statusResults as $statusRow) {
                $orderStatuses[] = [
                    "Status" => $statusRow['Status'],
                    "UpdateTime" => $statusRow['UpdateTime']
                ];
            }

            // Trả về dữ liệu theo cấu trúc mới
            return (object) [
                "status" => 200,
                "message" => "Order fetched successfully",
                "data" => [
                    "info" => $info,  // Thông tin đơn hàng chính và người dùng
                    "details" => array_values($details), // Các chi tiết sản phẩm
                    "orderStatuses" => $orderStatuses // Danh sách các trạng thái đơn hàng
                ]
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    public function getFullOrderById($orderId)
    {
        // Truy vấn chính lấy thông tin đơn hàng, chi tiết sản phẩm, thông tin người dùng và thông tin sản phẩm
        $orderQuery = "
                    SELECT o.*, 
                        od.ProductId, 
                        od.Quantity, 
                        od.UnitPrice, 
                        od.Total,
                        ui.Email, 
                        ui.Address, 
                        ui.Birthday, 
                        ui.Fullname, 
                        ui.Gender, 
                        ui.PhoneNumber,
                        p.Image, 
                        p.ProductName
                    FROM `Order` o
                    LEFT JOIN `OrderDetail` od ON o.Id = od.OrderId
                    LEFT JOIN `UserInformation` ui ON o.AccountId = ui.Id
                    LEFT JOIN `Product` p ON od.ProductId = p.Id
                    WHERE o.Id = :orderId
                ";

        // Truy vấn lấy toàn bộ trạng thái đơn hàng
        $statusQuery = "
                    SELECT os.Status, os.UpdateTime
                    FROM `OrderStatus` os
                    WHERE os.OrderId = :orderId
                    ORDER BY os.UpdateTime ASC
                ";

        try {
            // Thực hiện truy vấn cho thông tin đơn hàng
            $statement = $this->connection->prepare($orderQuery);
            $statement->bindValue(':orderId', $orderId, PDO::PARAM_STR);
            $statement->execute();
            $orderResults = $statement->fetchAll(PDO::FETCH_ASSOC);

            // Kiểm tra nếu không có kết quả
            if (empty($orderResults)) {
                return (object) [
                    "status" => 404,
                    "message" => "Order not found"
                ];
            }

            // Tách biệt thông tin chính của đơn hàng
            $info = [
                "OrderId" => $orderResults[0]['Id'],
                "OrderTime" => $orderResults[0]['OrderTime'],
                "TotalPrice" => $orderResults[0]['TotalPrice'],
                "Note" => $orderResults[0]['Note'],
                "Address" => $orderResults[0]['Address'],
                "Fullname" => $orderResults[0]['Fullname'],
                "PhoneNumber" => $orderResults[0]['PhoneNumber'],
                "Payment" => $orderResults[0]['Payment'],
                "isPaid" => $orderResults[0]['isPaid']
            ];

            // Tạo danh sách chi tiết sản phẩm (loại bỏ trùng lặp)
            $details = [];
            foreach ($orderResults as $row) {
                if (!isset($details[$row['ProductId']])) {
                    $details[$row['ProductId']] = [
                        "ProductId" => $row['ProductId'],
                        "ProductName" => $row['ProductName'], // Thêm tên sản phẩm
                        "Quantity" => $row['Quantity'],
                        "UnitPrice" => $row['UnitPrice'],
                        "Total" => $row['Total'],
                        "Image" => $row['Image'] // Thêm hình ảnh sản phẩm
                    ];
                }
            }

            // Thực hiện truy vấn cho trạng thái đơn hàng
            $statusStatement = $this->connection->prepare($statusQuery);
            $statusStatement->bindValue(':orderId', $orderId, PDO::PARAM_STR);
            $statusStatement->execute();
            $statusResults = $statusStatement->fetchAll(PDO::FETCH_ASSOC);

            // Tạo danh sách trạng thái đơn hàng
            $orderStatuses = [];
            foreach ($statusResults as $statusRow) {
                $orderStatuses[] = [
                    "Status" => $statusRow['Status'],
                    "UpdateTime" => $statusRow['UpdateTime']
                ];
            }

            // Trả về dữ liệu theo cấu trúc mới
            return (object) [
                "status" => 200,
                "message" => "Order fetched successfully",
                "data" => [
                    "info" => $info,  // Thông tin đơn hàng chính và người dùng
                    "details" => array_values($details), // Các chi tiết sản phẩm
                    "orderStatuses" => $orderStatuses // Danh sách tất cả các trạng thái đơn hàng
                ]
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }


    public function createOrder($orderData)
    {
        $orderId = $this->generateOrderId(); // Gọi hàm để tạo ID

        $query = "INSERT INTO `Order` (`Id`, `OrderTime`, `TotalPrice`, `Note`, `AccountId`, `Payment`, `isPaid`, `VoucherId`)
              VALUES (:orderId, :orderTime, :totalPrice, :note, :accountId, :payment, :isPaid, :voucherId)";

        try {
            $statement = $this->connection->prepare($query);

            // Lấy thời gian hiện tại cho `orderTime`
            $currentDateTime = date("Y-m-d H:i:s");

            // Gán giá trị và kiểm tra nếu trống thì để là NULL
            $statement->bindValue(':orderId', $orderId, PDO::PARAM_STR);
            $statement->bindValue(':orderTime', $currentDateTime, PDO::PARAM_STR);
            $statement->bindValue(':totalPrice', $orderData['totalPrice'] ?? null, PDO::PARAM_INT);
            $statement->bindValue(':note', !empty($orderData['note']) ? $orderData['note'] : null, PDO::PARAM_STR);
            $statement->bindValue(':accountId', $orderData['accountId'] ?? null, PDO::PARAM_INT);
            $statement->bindValue(':payment', !empty($orderData['Payment']) ? $orderData['Payment'] : 'COD', PDO::PARAM_STR);
            $statement->bindValue(':isPaid', $orderData['isPaid'] ?? false, PDO::PARAM_BOOL);
            $statement->bindValue(':voucherId', !empty($orderData['voucherId']) ? $orderData['voucherId'] : null, PDO::PARAM_INT);

            $statement->execute();

            return (object) [
                "status" => 201,
                "message" => "Order created successfully",
                "orderId" => $orderId
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    private function generateOrderId()
    {
        // Lấy số lượng đơn hàng hiện tại
        $query = "SELECT COUNT(*) as count FROM `Order`";
        $statement = $this->connection->prepare($query);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);

        $count = $result['count'] + 1; // Tăng số đếm lên 1
        return 'ORD' . str_pad($count, 8, '0', STR_PAD_LEFT); // Tạo ID theo định dạng ORD00000030
    }






    // Lấy tất cả đơn hàng của một tài khoản dựa trên AccountId
    public function getOrdersByAccountId($accountId)
    {
        $query = "
                    SELECT 
                        o.Id AS OrderId,
                        o.OrderTime,
                        o.TotalPrice,
                        o.Note,
                        os.Status,
                        os.UpdateTime
                    FROM 
                        `order` o
                    LEFT JOIN 
                        (
                            SELECT 
                                os.OrderId,       
                                os.Status,
                                os.UpdateTime
                            FROM 
                                OrderStatus os
                            JOIN 
                                (
                                    SELECT 
                                        OrderId, 
                                        MAX(UpdateTime) AS LatestUpdate
                                    FROM 
                                        OrderStatus
                                    GROUP BY 
                                        OrderId
                                ) latest ON os.OrderId = latest.OrderId AND os.UpdateTime = latest.LatestUpdate
                        ) os ON o.Id = os.OrderId
                    WHERE 
                        o.AccountId = :accountId
                ";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':accountId', $accountId, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            return (object) [
                "status" => 200,
                "message" => "Orders with latest statuses fetched successfully",
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
