<?php
require_once __DIR__ . "/../../Configure/MysqlConfig.php";

class ProductModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Lấy tất cả sản phẩm
    public function getAllProducts()
    {
        $query = "SELECT * FROM `product`";

        try {
            $statement = $this->connection->prepare($query);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return (object) [
                "status" => 200,
                "message" => "Products fetched successfully",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Lấy sản phẩm theo ID
    public function getProductById($id)
    {
        $query = "SELECT * FROM `product` WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            return (object) [
                "status" => 200,
                "message" => "Product fetched successfully",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Tạo sản phẩm mới
    public function createProduct($productName, $status, $createTime, $image, $origin, $capacity, $abv, $description, $brandId, $categoryId)
    {
        $query = "INSERT INTO `product` 
                    (`ProductName`, `Status`, `CreateTime`, `Image`, `Origin`, `Capacity`, `ABV`, `Description`, `BrandId`, `CategoryId`) 
                  VALUES 
                    (:productName, :status, :createTime, :image, :origin, :capacity, :abv, :description, :brandId, :categoryId)";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':productName', $productName, PDO::PARAM_STR);
            $statement->bindValue(':status', $status, PDO::PARAM_BOOL);
            $statement->bindValue(':createTime', $createTime, PDO::PARAM_STR);
            $statement->bindValue(':image', $image, PDO::PARAM_STR);
            $statement->bindValue(':origin', $origin, PDO::PARAM_STR);
            $statement->bindValue(':capacity', $capacity, PDO::PARAM_INT);
            $statement->bindValue(':abv', $abv, PDO::PARAM_INT);
            $statement->bindValue(':description', $description, PDO::PARAM_STR);
            $statement->bindValue(':brandId', $brandId, PDO::PARAM_INT);
            $statement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
            $statement->execute();

            $id = $this->connection->lastInsertId();
            return (object) [
                "status" => 201,
                "message" => "Product created successfully",
                "data" => $id
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Cập nhật sản phẩm
    public function updateProduct($id, $productName, $status, $createTime, $image, $origin, $capacity, $abv, $description, $brandId, $categoryId)
    {
        $query = "UPDATE `product` 
                  SET `ProductName` = :productName, `Status` = :status, `CreateTime` = :createTime, `Image` = :image, 
                      `Origin` = :origin, `Capacity` = :capacity, `ABV` = :abv, `Description` = :description, 
                      `BrandId` = :brandId, `CategoryId` = :categoryId 
                  WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->bindValue(':productName', $productName, PDO::PARAM_STR);
            $statement->bindValue(':status', $status, PDO::PARAM_BOOL);
            $statement->bindValue(':createTime', $createTime, PDO::PARAM_STR);
            $statement->bindValue(':image', $image, PDO::PARAM_STR);
            $statement->bindValue(':origin', $origin, PDO::PARAM_STR);
            $statement->bindValue(':capacity', $capacity, PDO::PARAM_INT);
            $statement->bindValue(':abv', $abv, PDO::PARAM_INT);
            $statement->bindValue(':description', $description, PDO::PARAM_STR);
            $statement->bindValue(':brandId', $brandId, PDO::PARAM_INT);
            $statement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
            $statement->execute();

            if ($statement->rowCount() > 0) {
                return (object) [
                    "status" => 200,
                    "message" => "Product updated successfully"
                ];
            } else {
                throw new PDOException("No record was updated");
            }
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Xóa sản phẩm theo ID
    public function deleteProduct($id)
    {
        $query = "DELETE FROM `product` WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();

            return (object) [
                "status" => 200,
                "message" => "Product deleted successfully"
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }
}
