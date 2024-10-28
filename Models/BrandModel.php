<?php
require_once __DIR__ . "../../Configure/MysqlConfig.php";

class BrandModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Lấy tất cả các Brand (không phân trang)
    public function getAllBrandNoPaging()
    {
        $query = "SELECT * FROM `brand`";

        try {
            $statement = $this->connection->prepare($query);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return (object) [
                "status" => 200,
                "message" => "Brands fetched successfully",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Lấy tất cả Brand có phân trang và tìm kiếm
    public function getAllBrand($page, $search, $pageSize)
    {
        // Tính toán offset
        $offset = ($page - 1) * $pageSize;

        // Chuẩn bị truy vấn để lấy tất cả các thương hiệu với phân trang và tìm kiếm
        $query = "SELECT * FROM `brand` WHERE `BrandName` LIKE :search LIMIT :offset, :pageSize";

        try {
            // Chuẩn bị và thực hiện truy vấn để lấy thương hiệu
            $statement = $this->connection->prepare($query);
            $searchTerm = '%' . $search . '%';
            $statement->bindValue(':search', $searchTerm, PDO::PARAM_STR);
            $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
            $statement->bindValue(':pageSize', $pageSize, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            // Truy vấn để lấy tổng số thương hiệu
            $totalQuery = "SELECT COUNT(*) as total FROM `brand` WHERE `BrandName` LIKE :search";
            $totalStmt = $this->connection->prepare($totalQuery);
            $totalStmt->bindValue(':search', $searchTerm, PDO::PARAM_STR);
            $totalStmt->execute();
            $totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);

            // Tính toán tổng số trang
            $totalPages = $totalResult['total'] > 0 ? ceil($totalResult['total'] / $pageSize) : 1;

            return (object) [
                "status" => 200,
                "message" => "Brands fetched successfully",
                "data" => $result,
                "totalPages" => $totalPages,
                "totalElements" => $totalResult['total']
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }



    // Lấy Brand theo ID
    public function getBrandById($id)
    {
        $query = "SELECT * FROM `brand` WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            return (object) [
                "status" => 200,
                "message" => "Brand fetched successfully",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Tạo Brand mới
    public function createBrand($brandName)
    {
        // Kiểm tra xem brand với tên đã tồn tại chưa
        $checkQuery = "SELECT COUNT(*) FROM `brand` WHERE `BrandName` = :brandName"; // Sửa tên bảng thành 'brand'
        $query = "INSERT INTO `brand` (`BrandName`) VALUES (:brandName)"; // Xóa dấu phẩy thừa

        try {
            // Kiểm tra tên brand
            $checkStatement = $this->connection->prepare($checkQuery);
            $checkStatement->bindValue(':brandName', $brandName, PDO::PARAM_STR);
            $checkStatement->execute();
            $count = $checkStatement->fetchColumn();

            if ($count > 0) {
                return (object) [
                    "status" => 409,
                    "message" => "Brand name already exists in the system"
                ];
            }

            // Nếu tên brand chưa tồn tại, tiếp tục tạo mới
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':brandName', $brandName, PDO::PARAM_STR);
            $statement->execute();
            $id = $this->connection->lastInsertId(); // Lấy ID của thương hiệu vừa tạo
            return (object) [
                "status" => 201,
                "message" => "Brand created successfully",
                "data" => $id
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Cập nhật Brand
    public function updateBrand($id, $brandName)
    {
        // Kiểm tra xem tên thương hiệu đã tồn tại trong danh sách khác chưa
        $checkQuery = "SELECT `Id` FROM `brand` WHERE `BrandName` = :brandName AND `Id` != :id";

        try {
            // Kiểm tra tên thương hiệu
            $checkStatement = $this->connection->prepare($checkQuery);
            $checkStatement->bindValue(':brandName', $brandName, PDO::PARAM_STR);
            $checkStatement->bindValue(':id', $id, PDO::PARAM_INT);
            $checkStatement->execute();

            // Nếu tìm thấy thương hiệu khác với cùng tên, trả về lỗi 409 (Conflict)
            if ($checkStatement->rowCount() > 0) {
                return (object) [
                    "status" => 409,
                    "message" => "Brand name already exists"
                ];
            }

            // Nếu không có xung đột tên, tiếp tục thực hiện cập nhật
            $query = "
                    UPDATE `brand`
                    SET `BrandName` = :brandName
                    WHERE `Id` = :id
                    ";
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->bindValue(':brandName', $brandName, PDO::PARAM_STR);
            $statement->execute();

            if ($statement->rowCount() > 0) {
                return (object) [
                    "status" => 200,
                    "message" => "Brand updated successfully"
                ];
            } else {
                // Kiểm tra xem thương hiệu có tồn tại không
                $checkExistQuery = "SELECT `Id` FROM `brand` WHERE `Id` = :id";
                $checkExistStatement = $this->connection->prepare($checkExistQuery);
                $checkExistStatement->bindValue(':id', $id, PDO::PARAM_INT);
                $checkExistStatement->execute();

                if ($checkExistStatement->rowCount() == 0) {
                    // Nếu thương hiệu không tồn tại
                    return (object) [
                        "status" => 404,
                        "message" => "Brand not found"
                    ];
                } else {
                    // Nếu thương hiệu tồn tại nhưng không cần cập nhật
                    return (object) [
                        "status" => 200,
                        "message" => "Brand updated successfully"
                    ];
                }
            }
        } catch (PDOException $e) {
            return (object) [
                "status" => 500,
                "message" => "An error occurred while updating the brand: " . $e->getMessage()
            ];
        }
    }



    // Xóa Brand theo ID sau khi chuyển sản phẩm sang BrandId 1 (trừ khi BrandId = 1)
    public function deleteBrand($brandId)
    {
        // Kiểm tra nếu brandId = 1 thì trả về lỗi không cho phép xóa brand mặc định
        if ($brandId == 1) {
            return (object) [
                "status" => 400,
                "message" => "Không thể xóa brand mặc định"
            ];
        }

        try {
            // Bắt đầu transaction
            $this->connection->beginTransaction();

            // Cập nhật tất cả sản phẩm có BrandId = $brandId thành BrandId = 1
            $updateProductQuery = "UPDATE `product` SET `BrandId` = 1 WHERE `BrandId` = :brandId";
            $updateProductStatement = $this->connection->prepare($updateProductQuery);
            $updateProductStatement->bindValue(':brandId', $brandId, PDO::PARAM_INT);
            $updateProductStatement->execute();

            // Xóa brand
            $deleteBrandQuery = "DELETE FROM `brand` WHERE `Id` = :brandId";
            $deleteBrandStatement = $this->connection->prepare($deleteBrandQuery);
            $deleteBrandStatement->bindValue(':brandId', $brandId, PDO::PARAM_INT);
            $deleteBrandStatement->execute();

            // Commit transaction
            $this->connection->commit();

            return (object) [
                "status" => 200,
                "message" => "Brand and related products updated successfully"
            ];
        } catch (PDOException $e) {
            // Rollback transaction in case of error
            $this->connection->rollBack();
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }
}
