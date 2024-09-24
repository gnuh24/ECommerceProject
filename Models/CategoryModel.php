<?php
require_once __DIR__ . "/../../Configure/MysqlConfig.php";

class CategoryModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Lấy danh sách tất cả các Category
    function getAllCategories()
    {
        $query = "SELECT * FROM `category`";

        try {
            $statement = $this->connection->prepare($query);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return (object) [
                "status" => 200,
                "message" => "Categories fetched successfully",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Lấy Category theo ID
    function getCategoryById($id)
    {
        $query = "SELECT * FROM `category` WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            return (object) [
                "status" => 200,
                "message" => "Category fetched successfully",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Tạo Category mới
    function createCategory($categoryName)
    {
        $query = "INSERT INTO `category` (`CategoryName`) VALUES (:categoryName)";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
            $statement->execute();
            $id = $this->connection->lastInsertId();
            return (object) [
                "status" => 201,
                "message" => "Category created successfully",
                "data" => $id
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Cập nhật Category
    function updateCategory($id, $categoryName)
    {
        $query = "UPDATE `category` SET `CategoryName` = :categoryName WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
            $statement->execute();

            if ($statement->rowCount() > 0) {
                return (object) [
                    "status" => 200,
                    "message" => "Category updated successfully"
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

    // Xóa Category theo ID
    function deleteCategory($id)
    {
        $query = "DELETE FROM `category` WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            return (object) [
                "status" => 200,
                "message" => "Category deleted successfully"
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }
}
