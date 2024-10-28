<?php
require_once __DIR__ . "../../Configure/MysqlConfig.php";

class CategoryModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Kiểm tra nếu tên danh mục mới đã tồn tại trong một danh mục khác
    public function isCategoryNameExist($categoryName, $id = null)
    {
        // Nếu ID được cung cấp, kiểm tra tên trùng nhưng bỏ qua danh mục hiện tại
        if ($id) {
            $query = "SELECT * FROM `category` WHERE `CategoryName` = :categoryName AND `Id` != :id";
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
        } else {
            // Nếu không có ID, kiểm tra toàn bộ danh mục
            $query = "SELECT * FROM `category` WHERE `CategoryName` = :categoryName";
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
        }

        $statement->execute();
        return $statement->rowCount() > 0;
    }

    // Lấy danh sách tất cả các Category không phân trang
    public function getAllCategoryNoPaging()
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

    // Lấy danh sách Category có phân trang và tìm kiếm
    public function getAllCategory($pageNumber = 1, $search = null, $pageSize = 5)
    {
        $offset = ($pageNumber - 1) * $pageSize;
        $query = "SELECT * FROM `category` WHERE `CategoryName` LIKE :search LIMIT :offset, :pageSize";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
            $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
            $statement->bindValue(':pageSize', $pageSize, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            $totalQuery = "SELECT COUNT(*) as total FROM `category` WHERE `CategoryName` LIKE :search";
            $totalStmt = $this->connection->prepare($totalQuery);
            $totalStmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
            $totalStmt->execute();
            $totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);
            $totalElements = $totalResult['total'];
            $totalPages = ceil($totalResult['total'] / $pageSize);

            return (object) [
                "status" => 200,
                "message" => "Categories fetched successfully",
                "data" => $result,
                "totalPages" => $totalPages,
                "totalElements" => $totalElements,
                "size" => $pageSize
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Lấy Category theo ID
    public function getCategoryById($id)
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

    // Tạo Category mới với kiểm tra tên
    public function createCategory($categoryName)
    {
        // Kiểm tra xem tên danh mục đã tồn tại chưa
        if ($this->isCategoryNameExist($categoryName)) {
            return (object) [
                "status" => 409, // Conflict
                "message" => "Category name already exists"
            ];
        }

        $query = "INSERT INTO `category` (`CategoryName`) VALUES (:categoryName)";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
            $statement->execute();

            // Lấy ID của danh mục mới được tạo
            $id = $this->connection->lastInsertId();
            return (object) [
                "status" => 201, // Created
                "message" => "Category created successfully",
                "data" => $id
            ];
        } catch (PDOException $e) {
            // Xử lý các lỗi từ PDO
            return (object) [
                "status" => 500, // Internal Server Error
                "message" => "An error occurred while creating the category: " . $e->getMessage()
            ];
        }
    }


    public function updateCategory($id, $categoryName)
    {
        // Kiểm tra xem tên danh mục mới đã tồn tại trong danh mục khác chưa
        $checkNameQuery = "SELECT `Id` FROM `category` WHERE `CategoryName` = :categoryName AND `Id` != :id";

        $checkStatement = $this->connection->prepare($checkNameQuery);
        $checkStatement->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
        $checkStatement->bindValue(':id', $id, PDO::PARAM_INT);
        $checkStatement->execute();

        // Nếu tìm thấy một danh mục khác với cùng tên, trả về lỗi 409 (Conflict)
        if ($checkStatement->rowCount() > 0) {
            return (object) [
                "status" => 409,
                "message" => "Category name already exists"
            ];
        }

        // Nếu không có xung đột tên, tiếp tục thực hiện cập nhật
        $query = "
        UPDATE `category`
        SET `CategoryName` = :categoryName
        WHERE `Id` = :id
        AND `CategoryName` != :categoryName
        ";
        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
            $statement->execute();

            // Kiểm tra nếu không có dòng nào được cập nhật (tức là tên danh mục không thay đổi)
            if ($statement->rowCount() > 0) {
                return (object) [
                    "status" => 200,
                    "message" => "Category updated successfully"
                ];
            } else {
                // Kiểm tra xem danh mục có tồn tại không
                $checkExistQuery = "SELECT `Id` FROM `category` WHERE `Id` = :id";
                $checkStatement = $this->connection->prepare($checkExistQuery);
                $checkStatement->bindValue(':id', $id, PDO::PARAM_INT);
                $checkStatement->execute();

                if ($checkStatement->rowCount() == 0) {
                    // Nếu danh mục không tồn tại
                    return (object) [
                        "status" => 404,
                        "message" => "Category not found"
                    ];
                } else {
                    // Nếu danh mục tồn tại nhưng không cần cập nhật
                    return (object) [
                        "status" => 200,
                        "message" => "Category updated successfully"
                    ];
                }
            }
        } catch (PDOException $e) {
            return (object) [
                "status" => 500,
                "message" => "An error occurred while updating the category: " . $e->getMessage()
            ];
        }
    }



    // Cập nhật CategoryId của sản phẩm về 1 trước khi xóa
    private function updateProductCategoryToDefault($categoryId)
    {
        $query = "UPDATE `product` SET `CategoryId` = 1 WHERE `CategoryId` = :categoryId";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
            $statement->execute();
        } catch (PDOException $e) {
            throw new Exception("Failed to update products with CategoryId = $categoryId to default");
        }
    }

    // Xóa Category theo ID và cập nhật sản phẩm về CategoryId = 1
    public function deleteCategory($id)
    {
        try {
            $this->updateProductCategoryToDefault($id);

            $query = "DELETE FROM `category` WHERE `Id` = :id";
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
        } catch (Exception $e) {
            return (object) [
                "status" => 500,
                "message" => $e->getMessage()
            ];
        }
    }
}
