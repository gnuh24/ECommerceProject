<?php
require_once __DIR__ . "../../Configure/MysqlConfig.php";

class CartItemModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Lấy một mục giỏ hàng cụ thể theo CartItemId (ProductId + AccountId)
    public function getCartItemById($productId, $accountId)
    {
        $query = "SELECT * FROM `cartitem` WHERE `ProductId` = :productId AND `AccountId` = :accountId";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':productId', $productId, PDO::PARAM_INT);
            $statement->bindValue(':accountId', $accountId, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            return (object) [
                "status" => 200,
                "message" => "Cart item fetched successfully",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Lấy tất cả các mục giỏ hàng theo AccountId
    public function getAllCartItemsByAccountId($accountId)
    {
        $query = "
                    SELECT ci.*, p.ProductName, p.Image, p.Description 
                    FROM `CartItem` ci
                    LEFT JOIN `Product` p ON ci.ProductId = p.Id
                    WHERE ci.AccountId = :accountId
                ";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':accountId', $accountId, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            return (object) [
                "status" => 200,
                "message" => "Cart items fetched successfully",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }


    // Thêm một mục vào giỏ hàng (tạo mới)
    public function createCartItem($cartItem)
    {
        // Kiểm tra số lượng hàng tồn kho từ batch xa nhất
        $stockCheckQuery = "
                                SELECT b.Quantity 
                                FROM `Batch` b
                                WHERE b.ProductId = :productId 
                                AND b.Quantity > 0 
                                ORDER BY b.ReceivingTime DESC 
                                LIMIT 1
                            ";

        // Lấy số lượng hiện tại trong giỏ hàng
        $currentQuantityQuery = "
                                    SELECT SUM(ci.Quantity) AS currentQuantity 
                                    FROM `CartItem` ci 
                                    WHERE ci.AccountId = :accountId 
                                    AND ci.ProductId = :productId
                                ";

        try {
            // Kiểm tra hàng tồn kho
            $stockCheckStatement = $this->connection->prepare($stockCheckQuery);
            $stockCheckStatement->bindValue(':productId', $cartItem['productId'], PDO::PARAM_INT);
            $stockCheckStatement->execute();
            $stock = $stockCheckStatement->fetch(PDO::FETCH_ASSOC);

            // Nếu không có batch nào tồn tại
            if (!$stock) {
                return (object) [
                    "status" => 400,
                    "message" => "No stock available for this product."
                ];
            }

            // Lấy số lượng hiện tại trong giỏ hàng
            $currentQuantityStatement = $this->connection->prepare($currentQuantityQuery);
            $currentQuantityStatement->bindValue(':accountId', $cartItem['accountId'], PDO::PARAM_INT);
            $currentQuantityStatement->bindValue(':productId', $cartItem['productId'], PDO::PARAM_INT);
            $currentQuantityStatement->execute();
            $currentQuantity = $currentQuantityStatement->fetch(PDO::FETCH_ASSOC)['currentQuantity'] ?? 0;

            // Tính tổng số lượng nếu thêm sản phẩm này
            $totalQuantity = $currentQuantity + $cartItem['quantity'];

            // Nếu tổng số lượng vượt quá số lượng tồn kho
            if ($totalQuantity > $stock['Quantity']) {
                return (object) [
                    "status" => 400,
                    "message" => "Cannot add more products than available in stock."
                ];
            }

            // Nếu đủ hàng, tính tổng
            $total = $cartItem['quantity'] * $cartItem['unitPrice'];
            $query = "INSERT INTO `CartItem` (`ProductId`, `AccountId`, `Quantity`, `UnitPrice`, `Total`)
                  VALUES (:productId, :accountId, :quantity, :unitPrice, :total)
                  ON DUPLICATE KEY UPDATE 
                  `Quantity` = `Quantity` + VALUES(`Quantity`), 
                  `Total` = `Quantity` * `UnitPrice`";

            $statement = $this->connection->prepare($query);
            $statement->bindValue(':productId', $cartItem['productId'], PDO::PARAM_INT);
            $statement->bindValue(':accountId', $cartItem['accountId'], PDO::PARAM_INT);
            $statement->bindValue(':quantity', $cartItem['quantity'], PDO::PARAM_INT);
            $statement->bindValue(':unitPrice', $cartItem['unitPrice'], PDO::PARAM_INT);
            $statement->bindValue(':total', $total, PDO::PARAM_INT);
            $statement->execute();

            return (object) [
                "status" => 201,
                "message" => "Cart item updated successfully"
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }



    public function updateCartItem($cartItem)
    {
        // Kiểm tra số lượng hàng tồn kho từ batch xa nhất
        $stockCheckQuery = "
                                SELECT b.Quantity 
                                FROM `Batch` b
                                WHERE b.ProductId = :productId 
                                AND b.Quantity > 0 
                                ORDER BY b.ReceivingTime DESC 
                                LIMIT 1
                            ";

        // Lấy số lượng hiện tại trong giỏ hàng
        $currentQuantityQuery = "
                                    SELECT Quantity 
                                    FROM `CartItem` 
                                    WHERE AccountId = :accountId 
                                    AND ProductId = :productId
                                ";

        try {
            // Kiểm tra hàng tồn kho
            $stockCheckStatement = $this->connection->prepare($stockCheckQuery);
            $stockCheckStatement->bindValue(':productId', $cartItem['productId'], PDO::PARAM_INT);
            $stockCheckStatement->execute();
            $stock = $stockCheckStatement->fetch(PDO::FETCH_ASSOC);

            // Nếu không có batch nào tồn tại
            if (!$stock) {
                return (object) [
                    "status" => 400,
                    "message" => "No stock available for this product."
                ];
            }

            // Lấy số lượng hiện tại trong giỏ hàng
            $currentQuantityStatement = $this->connection->prepare($currentQuantityQuery);
            $currentQuantityStatement->bindValue(':accountId', $cartItem['accountId'], PDO::PARAM_INT);
            $currentQuantityStatement->bindValue(':productId', $cartItem['productId'], PDO::PARAM_INT);
            $currentQuantityStatement->execute();
            $currentQuantity = $currentQuantityStatement->fetch(PDO::FETCH_ASSOC)['Quantity'] ?? 0;

            // Tính tổng số lượng nếu cập nhật sản phẩm này
            $totalQuantity = $cartItem['quantity']; // số lượng mới từ yêu cầu
            $newTotalQuantity =  $totalQuantity;

            // Nếu tổng số lượng vượt quá số lượng tồn kho
            if ($newTotalQuantity > $stock['Quantity']) {
                return (object) [
                    "status" => 400,
                    "message" => "Cannot update quantity to exceed available stock."
                ];
            }

            // Tính tổng
            $total = $newTotalQuantity * $cartItem['unitPrice'];
            $query = "UPDATE `CartItem` 
                  SET `Quantity` = :quantity, `UnitPrice` = :unitPrice, `Total` = :total
                  WHERE `ProductId` = :productId AND `AccountId` = :accountId";

            $statement = $this->connection->prepare($query);
            $statement->bindValue(':productId', $cartItem['productId'], PDO::PARAM_INT);
            $statement->bindValue(':accountId', $cartItem['accountId'], PDO::PARAM_INT);
            $statement->bindValue(':quantity', $newTotalQuantity, PDO::PARAM_INT);
            $statement->bindValue(':unitPrice', $cartItem['unitPrice'], PDO::PARAM_INT);
            $statement->bindValue(':total', $total, PDO::PARAM_INT);
            $statement->execute();

            return (object) [
                "status" => 200,
                "message" => "Cart item updated successfully"
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }


    public function deleteCartItem($productId, $accountId)
    {
        // Bước 1: Xóa sản phẩm khỏi giỏ hàng
        $query = "DELETE FROM `cartitem` WHERE `ProductId` = :productId AND `AccountId` = :accountId";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':productId', $productId, PDO::PARAM_INT);
            $statement->bindValue(':accountId', $accountId, PDO::PARAM_INT);
            $statement->execute();

            // Bước 2: Tính toán lại tổng giá trị của giỏ hàng
            $totalQuery = "
            SELECT SUM(Total) AS totalAmount
            FROM `cartitem`
            WHERE `AccountId` = :accountId
        ";
            $totalStatement = $this->connection->prepare($totalQuery);
            $totalStatement->bindValue(':accountId', $accountId, PDO::PARAM_INT);
            $totalStatement->execute();
            $totalAmount = $totalStatement->fetch(PDO::FETCH_ASSOC)['totalAmount'] ?? 0;

            // Bước 3: Trả về thông tin
            return (object) [
                "status" => 200,
                "message" => "Cart item deleted successfully",
                "data" => $totalAmount // Trả về tổng giá trị mới
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }


    // Xóa tất cả các mục giỏ hàng theo AccountId
    public function deleteAllCartItems($accountId)
    {
        $query = "DELETE FROM `cartitem` WHERE `AccountId` = :accountId";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':accountId', $accountId, PDO::PARAM_INT);
            $statement->execute();
            return (object) [
                "status" => 200,
                "message" => "All cart items deleted successfully"
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }
}
