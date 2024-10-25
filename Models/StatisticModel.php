<?php
require_once __DIR__ . '/../Configure/MysqlConfig.php';

class StatisticModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    public function findOrderStatusSummary($minDate, $maxDate)
    {
        // Câu truy vấn để lấy tóm tắt trạng thái đơn hàng
        $query = "SELECT ot.Status AS status, COUNT(ot.Status) AS quantity, DATE(ot.UpdateTime) AS updateDate
              FROM `Order` od
              JOIN `OrderStatus` ot ON od.Id = ot.OrderId
              WHERE ot.UpdateTime = (
                  SELECT MAX(ot2.UpdateTime) FROM `OrderStatus` ot2 WHERE ot2.OrderId = od.Id
              )
              AND DATE(ot.UpdateTime) BETWEEN COALESCE(:minDate, '2010-01-01') AND COALESCE(:maxDate, CURRENT_DATE())
              GROUP BY ot.Status, DATE(ot.UpdateTime)
              ORDER BY DATE(ot.UpdateTime)";

        try {
            $stmt = $this->connection->prepare($query);
            $stmt->bindValue(':minDate', $minDate ?: '2010-01-01');
            $stmt->bindValue(':maxDate', $maxDate ?: date('Y-m-d'));

            // Thực thi câu truy vấn
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Tạo câu truy vấn đã thay thế tham số
            $finalQuery = $this->replacePlaceholders($query, [
                ':minDate' => $minDate ?: '2010-01-01',
                ':maxDate' => $maxDate ?: date('Y-m-d'),
            ]);

            return (object)[
                "status" => 200,
                "message" => "Truy vấn thành công",
                "data" => $result,  // Trả về dữ liệu truy vấn
                "query" => $finalQuery,  // Trả về câu truy vấn đã thay thế tham số
            ];
        } catch (PDOException $e) {
            return (object)[
                "status" => 400,
                "message" => "Truy vấn cơ sở dữ liệu thất bại: " . $e->getMessage(),
                "query" => $query,  // Trả về câu truy vấn gốc
            ];
        }
    }

    // Hàm để thay thế tham số trong câu truy vấn
    private function replacePlaceholders($query, $params)
    {
        foreach ($params as $key => $value) {
            // Thay thế tham số bằng giá trị tương ứng
            $escapedValue = is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
            $query = str_replace($key, $escapedValue, $query);
        }
        return $query;
    }



    public function findBestSellingProducts($startDate, $endDate, $topProducts)
    {
        // Truy vấn SQL để tìm sản phẩm bán chạy nhất với tổng số lượng và tổng giá trị
        $query = "
                    SELECT p.Id, p.ProductName, 
                        SUM(od.Quantity) AS totalQuantity, 
                        SUM(od.Quantity * od.UnitPrice) AS totalValue
                    FROM `Product` p
                    JOIN `OrderDetail` od ON p.Id = od.ProductId
                    JOIN `Order` o ON od.OrderId = o.Id
                    JOIN `OrderStatus` os ON o.Id = os.OrderId
                    WHERE os.Status IN ('DaDuyet', 'DangGiao', 'GiaoThanhCong')
                    AND DATE(os.UpdateTime) BETWEEN COALESCE(:startDate, '2010-01-01') AND COALESCE(:endDate, CURRENT_DATE())
                    GROUP BY p.Id, p.ProductName
                    ORDER BY totalQuantity DESC
                    LIMIT :topProducts
                ";

        try {
            // Chuẩn bị câu truy vấn
            $statement = $this->connection->prepare($query);

            // Gán giá trị cho các tham số
            $statement->bindValue(':startDate', $startDate ?: '2010-01-01', PDO::PARAM_STR);
            $statement->bindValue(':endDate', $endDate ?: date('Y-m-d'), PDO::PARAM_STR);
            $statement->bindValue(':topProducts', (int)$topProducts, PDO::PARAM_INT);

            // Thực thi câu truy vấn
            $statement->execute();

            // Lấy kết quả
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            // Trả về kết quả
            return (object) [
                "status" => 200,
                "message" => "Truy vấn thành công",
                "data" => $result
            ];
        } catch (PDOException $e) {
            // Xử lý lỗi
            return (object) [
                "status" => 400,
                "message" => "Truy vấn thất bại: " . $e->getMessage()
            ];
        }
    }
}
