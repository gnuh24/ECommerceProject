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
        $query = "SELECT * FROM `cartitem` WHERE `AccountId` = :accountId";

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
        $total = $cartItem->quantity * $cartItem->unitPrice;
        $query = "INSERT INTO `cartitem` (`ProductId`, `AccountId`, `Quantity`, `UnitPrice`, `Total`)
                  VALUES (:productId, :accountId, :quantity, :unitPrice, :total)
                  ON DUPLICATE KEY UPDATE 
                  `Quantity` = `Quantity` + VALUES(`Quantity`), 
                  `Total` = `Quantity` * `UnitPrice`";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':productId', $cartItem->productId, PDO::PARAM_INT);
            $statement->bindValue(':accountId', $cartItem->accountId, PDO::PARAM_INT);
            $statement->bindValue(':quantity', $cartItem->quantity, PDO::PARAM_INT);
            $statement->bindValue(':unitPrice', $cartItem->unitPrice, PDO::PARAM_INT);
            $statement->bindValue(':total', $total, PDO::PARAM_INT);
            $statement->execute();
            return (object) [
                "status" => 201,
                "message" => "Cart item added/updated successfully"
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Cập nhật mục giỏ hàng
    public function updateCartItem($cartItem)
    {
        $total = $cartItem->quantity * $cartItem->unitPrice;
        $query = "UPDATE `cartitem` 
                  SET `Quantity` = :quantity, `UnitPrice` = :unitPrice, `Total` = :total
                  WHERE `ProductId` = :productId AND `AccountId` = :accountId";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':productId', $cartItem->productId, PDO::PARAM_INT);
            $statement->bindValue(':accountId', $cartItem->accountId, PDO::PARAM_INT);
            $statement->bindValue(':quantity', $cartItem->quantity, PDO::PARAM_INT);
            $statement->bindValue(':unitPrice', $cartItem->unitPrice, PDO::PARAM_INT);
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

    // Xóa một mục giỏ hàng
    public function deleteCartItem($productId, $accountId)
    {
        $query = "DELETE FROM `cartitem` WHERE `ProductId` = :productId AND `AccountId` = :accountId";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':productId', $productId, PDO::PARAM_INT);
            $statement->bindValue(':accountId', $accountId, PDO::PARAM_INT);
            $statement->execute();
            return (object) [
                "status" => 200,
                "message" => "Cart item deleted successfully"
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
