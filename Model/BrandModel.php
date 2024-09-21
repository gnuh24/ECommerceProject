<?php
require_once __DIR__ . "/../../Configure/MysqlConfig.php";

class BrandModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Lấy tất cả các Brand
    public function getAllBrands()
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
        $query = "INSERT INTO `brand` (`BrandName`) VALUES (:brandName)";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':brandName', $brandName, PDO::PARAM_STR);
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
    public function updateBrand($id, $brandName)
    {
        $query = "UPDATE `brand` SET `BrandName` = :brandName WHERE `Id` = :id";

        try {
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
                throw new PDOException("No record was updated");
            }
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Xóa Brand theo ID
    public function deleteBrand($id)
    {
        $query = "DELETE FROM `brand` WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            return (object) [
                "status" => 200,
                "message" => "Brand deleted successfully"
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }
}
