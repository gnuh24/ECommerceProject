<?php
require_once __DIR__ . "/../../Configure/MysqlConfig.php";

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
    public function getAllBrand($pageable, $search = null)
    {
        $query = "SELECT * FROM `brand` WHERE `BrandName` LIKE :search LIMIT :offset, :limit";

        try {
            $statement = $this->connection->prepare($query);
            $searchTerm = '%' . $search . '%';
            $statement->bindValue(':search', $searchTerm, PDO::PARAM_STR);
            $statement->bindValue(':offset', $pageable->getOffset(), PDO::PARAM_INT);
            $statement->bindValue(':limit', $pageable->getPageSize(), PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            return (object) [
                "status" => 200,
                "message" => "Brands fetched successfully with pagination",
                "data" => $result
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
    public function createBrand($form)
    {
        // Kiểm tra xem brand với tên đã tồn tại chưa
        $checkQuery = "SELECT COUNT(*) FROM `brand` WHERE `BrandName` = :brandName";
        $query = "INSERT INTO `brand` (`BrandName`, `Description`) VALUES (:brandName, :description)";

        try {
            // Kiểm tra tên brand
            $checkStatement = $this->connection->prepare($checkQuery);
            $checkStatement->bindValue(':brandName', $form->brandName, PDO::PARAM_STR);
            $checkStatement->execute();
            $count = $checkStatement->fetchColumn();

            if ($count > 0) {
                return (object) [
                    "status" => 400,
                    "message" => "Brand name already exists in the system"
                ];
            }

            // Nếu tên brand chưa tồn tại, tiếp tục tạo mới
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':brandName', $form->brandName, PDO::PARAM_STR);
            $statement->bindValue(':description', $form->description, PDO::PARAM_STR);
            $statement->execute();
            $id = $this->connection->lastInsertId();
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
    public function updateBrand($form)
    {
        // Kiểm tra xem brand với tên đã tồn tại chưa (trừ brand hiện tại đang cập nhật)
        $checkQuery = "SELECT COUNT(*) FROM `brand` WHERE `BrandName` = :brandName AND `Id` != :id";
        $query = "UPDATE `brand` SET `BrandName` = :brandName, `Description` = :description WHERE `Id` = :id";

        try {
            // Kiểm tra tên brand
            $checkStatement = $this->connection->prepare($checkQuery);
            $checkStatement->bindValue(':brandName', $form->brandName, PDO::PARAM_STR);
            $checkStatement->bindValue(':id', $form->id, PDO::PARAM_INT);
            $checkStatement->execute();
            $count = $checkStatement->fetchColumn();

            if ($count > 0) {
                return (object) [
                    "status" => 400,
                    "message" => "Brand name already exists in the system"
                ];
            }

            // Nếu tên brand chưa tồn tại, tiếp tục cập nhật
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $form->id, PDO::PARAM_INT);
            $statement->bindValue(':brandName', $form->brandName, PDO::PARAM_STR);
            $statement->bindValue(':description', $form->description, PDO::PARAM_STR);
            $statement->execute();

            if ($statement->rowCount() > 0) {
                return (object) [
                    "status" => 200,
                    "message" => "Brand updated successfully"
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
