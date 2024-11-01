<?php
require_once __DIR__ . "../../Configure/MysqlConfig.php";

class VoucherModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Check if a voucher code exists, excluding a specific ID if provided
    public function isVoucherCodeExist($code, $id = null)

    {
        if ($id) {
            $query = "SELECT * FROM `Voucher` WHERE `Code` = :code AND `Id` != :id";
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':code', $code, PDO::PARAM_STR);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
        } else {
            $query = "SELECT * FROM `Voucher` WHERE `Code` = :code";
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':code', $code, PDO::PARAM_STR);
        }

        $statement->execute();
        return $statement->rowCount() > 0;
    }

    // Fetch all vouchers without pagination
    public function getAllVouchersNoPaging()
    {
        $query = "SELECT * FROM `Voucher`";

        try {
            $statement = $this->connection->prepare($query);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            return (object) [
                "status" => 200,
                "message" => "Vouchers fetched successfully",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Fetch vouchers with pagination and search by code
    public function getAllVouchers($pageNumber = 1, $size = 10, $minNgayapdung = null, $maxNgayapdung = null, $status = null)
    {
        $offset = ($pageNumber - 1) * $size;
        $conditions = [];
        $params = [];
        // Thêm điều kiện cho minNgayapdung nếu có
        if ($minNgayapdung !== null && $minNgayapdung !== '') {
            $conditions[] = "`ExpirationTime` >= :minNgayapdung";
            $params[':minNgayapdung'] = $minNgayapdung;
        }

        // Thêm điều kiện cho maxNgayapdung nếu có
        if ($maxNgayapdung !== null && $maxNgayapdung !== '') {
            $conditions[] = "`ExpirationTime` <= :maxNgayapdung";
            $params[':maxNgayapdung'] = $maxNgayapdung;
        }

        // Thêm điều kiện cho status nếu có
        if ($status !== null && $status !== '') {
            $conditions[] = "`IsPublic` = :status";
            $params[':status'] = $status;
        }


        // Tạo chuỗi điều kiện cho câu lệnh SQL
        $whereClause = "";
        if (count($conditions) > 0) {
            $whereClause = "WHERE " . implode(" AND ", $conditions);
        }

        $query = "SELECT * FROM `Voucher` $whereClause LIMIT :offset, :size";

        try {
            $statement = $this->connection->prepare($query);
            foreach ($params as $param => $value) {
                $statement->bindValue($param, $value);
            }
            $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
            $statement->bindValue(':size', $size, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            // Lấy tổng số phần tử thỏa mãn điều kiện
            $totalQuery = "SELECT COUNT(*) as total FROM `Voucher` $whereClause";
            $totalStmt = $this->connection->prepare($totalQuery);
            foreach ($params as $param => $value) {
                $totalStmt->bindValue($param, $value);
            }
            $totalStmt->execute();
            $totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);
            $totalElements = $totalResult['total'];
            $totalPages = ceil($totalElements / $size);

            return (object) [
                "status" => 200,
                "message" => "Vouchers fetched successfully",
                "data" => $result,
                "totalPages" => $totalPages,
                "totalElements" => $totalElements,
                "size" => $size,
                "params" => [
                    "pageNumber" => $pageNumber,
                    "size" => $size,
                    "minNgayapdung" => $minNgayapdung,
                    "maxNgayapdung" => $maxNgayapdung,
                    "status" => $status
                ]
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => "An error occurred: " . $e->getMessage()
            ];
        }
    }




    // Fetch a voucher by ID
    public function getVoucherById($id)
    {
        $query = "SELECT * FROM `Voucher` WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            return (object) [
                "status" => 200,
                "message" => "Voucher fetched successfully",
                "data" => $result
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Create a new voucher with code uniqueness check
    public function createVoucher($expirationTime, $code, $condition, $saleAmount, $isPublic = true)
    {
        if ($this->isVoucherCodeExist($code)) {
            return (object) [
                "status" => 409,
                "message" => "Voucher code already exists"
            ];
        }

        $query = "INSERT INTO `Voucher` (`ExpirationTime`, `Code`, `Condition`, `SaleAmount`, `IsPublic`) 
                  VALUES (:expirationTime, :code, :condition, :saleAmount, :isPublic)";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':expirationTime', $expirationTime, PDO::PARAM_STR);
            $statement->bindValue(':code', $code, PDO::PARAM_STR);
            $statement->bindValue(':condition', $condition, PDO::PARAM_INT);
            $statement->bindValue(':saleAmount', $saleAmount, PDO::PARAM_INT);
            $statement->bindValue(':isPublic', $isPublic, PDO::PARAM_BOOL);
            $statement->execute();

            $id = $this->connection->lastInsertId();
            return (object) [
                "status" => 201,
                "message" => "Voucher created successfully",
                "data" => $id
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 500,
                "message" => "An error occurred while creating the voucher: " . $e->getMessage()
            ];
        }
    }
    // Fetch a voucher by ID


    // Update a voucher
    public function updateVoucher($id, $expirationTime = null, $code = null, $condition = null, $saleAmount = null, $isPublic = null)
    {
        // Kiểm tra xem mã code có tồn tại không, bỏ qua nếu không thay đổi
        if ($code !== null && $this->isVoucherCodeExist($code, $id)) {
            return (object) [
                "status" => 409,
                "message" => "Voucher code already exists"
            ];
        }

        // Xây dựng câu truy vấn động
        $query = "UPDATE `Voucher` SET ";
        $params = [];

        if ($expirationTime !== null) {
            $query .= "`ExpirationTime` = :expirationTime, ";
            $params[':expirationTime'] = $expirationTime;
        }
        if ($code !== null) {
            $query .= "`Code` = :code, ";
            $params[':code'] = $code;
        }
        if ($condition !== null) {
            $query .= "`Condition` = :condition, ";
            $params[':condition'] = $condition;
        }
        if ($saleAmount !== null) {
            $query .= "`SaleAmount` = :saleAmount, ";
            $params[':saleAmount'] = $saleAmount;
        }
        if ($isPublic !== null) {
            $query .= "`IsPublic` = :isPublic, ";
            $params[':isPublic'] = $isPublic;
        }

        // Xóa dấu phẩy cuối cùng và thêm điều kiện WHERE
        $query = rtrim($query, ', ') . " WHERE `Id` = :id";
        $params[':id'] = $id;

        try {
            $statement = $this->connection->prepare($query);

            // Gán các tham số vào câu truy vấn
            foreach ($params as $key => $value) {
                $statement->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }

            $statement->execute();

            if ($statement->rowCount() > 0) {
                return (object) [
                    "status" => 200,
                    "message" => "Voucher updated successfully"
                ];
            } else {
                return (object) [
                    "status" => 404,
                    "message" => "Voucher not found"
                ];
            }
        } catch (PDOException $e) {
            return (object) [
                "status" => 500,
                "message" => "An error occurred while updating the voucher: " . $e->getMessage()
            ];
        }
    }


    // Delete a voucher
    public function deleteVoucher($id)
    {
        $query = "DELETE FROM `Voucher` WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();

            return (object) [
                "status" => 200,
                "message" => "Voucher deleted successfully"
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }
}
