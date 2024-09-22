<?php
require_once __DIR__ . '/../Configure/MysqlConfig.php';

class CartItemModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Lấy tất cả các mục giỏ hàng theo AccountId
    public function getCartItemsByAccountId($accountId)
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

    // Lấy một mục giỏ hàng cụ thể theo ProductId và AccountId
    public function getCartItem($productId, $accountId)
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

    // Thêm một mục vào giỏ hàng
    public function addCartItem($productId, $accountId, $quantity, $unitPrice)
    {
        $total = $quantity * $unitPrice;
        $query = "INSERT INTO `cartitem` (`ProductId`, `AccountId`, `Quantity`, `UnitPrice`, `Total`)
                  VALUES (:productId, :accountId, :quantity, :unitPrice, :total)
                  ON DUPLICATE KEY UPDATE 
                  `Quantity` = `Quantity` + VALUES(`Quantity`), 
                  `Total` = `Quantity` * `UnitPrice`";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':productId', $productId, PDO::PARAM_INT);
            $statement->bindValue(':accountId', $accountId, PDO::PARAM_INT);
            $statement->bindValue(':quantity', $quantity, PDO::PARAM_INT);
            $statement->bindValue(':unitPrice', $unitPrice, PDO::PARAM_INT);
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

    // Cập nhật số lượng và đơn giá của mục giỏ hàng
    public function updateCartItem($productId, $accountId, $quantity, $unitPrice)
    {
        $total = $quantity * $unitPrice;
        $query = "UPDATE `cartitem` 
                  SET `Quantity` = :quantity, `UnitPrice` = :unitPrice, `Total` = :total
                  WHERE `ProductId` = :productId AND `AccountId` = :accountId";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':productId', $productId, PDO::PARAM_INT);
            $statement->bindValue(':accountId', $accountId, PDO::PARAM_INT);
            $statement->bindValue(':quantity', $quantity, PDO::PARAM_INT);
            $statement->bindValue(':unitPrice', $unitPrice, PDO::PARAM_INT);
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
    public function clearCart($accountId)
    {
        $query = "DELETE FROM `cartitem` WHERE `AccountId` = :accountId";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':accountId', $accountId, PDO::PARAM_INT);
            $statement->execute();
            return (object) [
                "status" => 200,
                "message" => "Cart cleared successfully"
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }
}
