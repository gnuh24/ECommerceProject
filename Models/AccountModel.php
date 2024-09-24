<?php
require_once __DIR__ . '/../Configure/MysqlConfig.php';

class AccountModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Kiểm tra xem tài khoản đã tồn tại hay chưa
    function isAccountExists($userInformationId)
    {
        $query = "SELECT * FROM `account` WHERE `UserInformationId` = :userInformationId";

        try {
            $statement = $this->connection->prepare($query);
            if ($statement !== false) {
                $statement->bindValue(':userInformationId', $userInformationId, PDO::PARAM_INT);
                $statement->execute();
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                $isExists = !empty($result);
                return (object) [
                    "status" => 200,
                    "message" => "Truy vấn thành công",
                    "isExists" => $isExists
                ];
            } else {
                throw new PDOException();
            }
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => "Truy vấn cơ sở dữ liệu thất bại",
                "isExists" => false
            ];
        }
    }



    // Lấy thông tin tài khoản theo ID
    public function getAccountById($userInformationId, $filters = [], $page = 1, $search = '', $role = '', $status = '')
    {
       

        $query = "SELECT * FROM `account` WHERE `UserInformationId` = :userInformationId";
        
        // Mảng chứa điều kiện
        $where_conditions = [':userInformationId' => $userInformationId];
        
        // Số phần tử mỗi trang
        $entityPerPage = 10;
        
        // Tổng số trang
        $totalPages = null;

        // Tìm kiếm theo từ khóa (username hoặc email)
        if (!empty($search)) {
            $query .= " AND (`username` LIKE :search OR `email` LIKE :search)";
            $where_conditions[':search'] = '%' . $search . '%';
        }

        // Lọc theo các filters truyền vào
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                $query .= " AND `$key` = :$key";
                $where_conditions[":$key"] = $value;
            }
        }

        // Lọc theo role
        if (!empty($role)) {
            $query .= " AND `role` = :role";
            $where_conditions[':role'] = $role;
        }

        // Lọc theo status
        if (!empty($status)) {
            $query .= " AND `status` = :status";
            $where_conditions[':status'] = $status;
        }


        // Tính toán tổng số trang
        if ($totalPages === null) {
            // Câu truy vấn để đếm tổng số hàng
            $query_total_row = "SELECT COUNT(*) FROM `account` WHERE `userInformationId` = :userInformationId";
            
            // Cộng các điều kiện khác (search, filter, role, status) vào câu truy vấn đếm
            if (!empty($search)) {
                $query_total_row .= " AND (`username` LIKE :search OR `email` LIKE :search)";
            }
            if (!empty($filters)) {
                foreach ($filters as $key => $value) {
                    $query_total_row .= " AND `$key` = :$key";
                }
            }
            if (!empty($role)) {
                $query_total_row .= " AND `role` = :role";
            }
            if (!empty($status)) {
                $query_total_row .= " AND `status` = :status";
            }

            // Chạy truy vấn đếm
            $statement_total_row = $this->connection->prepare($query_total_row);
            $statement_total_row->execute($where_conditions);
            
            // Tính tổng số trang
            $totalRows = $statement_total_row->fetchColumn();
            $totalPages = ceil($totalRows / $entityPerPage);
        }

        // Phân trang
        $current_page = (int)$page; // Ép kiểu $page thành số nguyên
        $start_from = ($current_page - 1) * $entityPerPage;

        // Thêm điều kiện phân trang vào câu truy vấn
        $query .= " LIMIT :limit OFFSET :offset";

        try {
            // Chuẩn bị câu truy vấn
            $statement = $this->connection->prepare($query);
            foreach ($where_conditions as $key => $value) {
                $statement->bindValue($key, $value);
            }
            $statement->bindValue(':limit', $entityPerPage, PDO::PARAM_INT);
            $statement->bindValue(':offset', $start_from, PDO::PARAM_INT);

            // Thực thi truy vấn
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            // Kiểm tra dữ liệu có tồn tại không
            $isExists = !empty($result);
            
            return (object) [
                "status" => 200,
                "message" => "Truy vấn thành công",
                "data" => $result,
                "totalPages" => $totalPages,
                "isExists" => $isExists
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => "Truy vấn cơ sở dữ liệu thất bại: " . $e->getMessage(),
                "isExists" => false
            ];
        }
    }

    
    

    // Kiểm tra xem email đã tồn tại hay chưa
    function isEmailExists($email)
    {
        // Gọi hàm getAccountByEmail để lấy thông tin tài khoản theo email
        $response = $this->getAccountByEmail($email);

        // Kiểm tra trạng thái và dữ liệu trả về
        if ($response->status === 200 && !empty($response->data)) {
            // Email đã tồn tại trong cơ sở dữ liệu
            return true;
        } else {
            // Email không tồn tại hoặc có lỗi xảy ra
            return false;
        }
    }


    // Lấy thông tin tài khoản theo email từ UserInformation
    function getAccountByEmail($email)
    {
        $query = "
            SELECT a.*, u.* 
            FROM `Account` a
            JOIN `UserInformation` u 
            ON a.UserInformationId = u.Id
            WHERE u.Email = :email
        ";

        try {
            $statement = $this->connection->prepare($query);
            if ($statement !== false) {
                $statement->bindValue(':email', $email, PDO::PARAM_STR);
                $statement->execute();
                $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                return (object) [
                    "status" => 200,
                    "message" => "Thành công",
                    "data" => $result,
                ];
            } else {
                throw new PDOException();
            }
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => "Không thể lấy thông tin tài khoản",
            ];
        }
    }

    // Tạo tài khoản mới
    function createAccount($password, $userInformationId,  $type = "Standard")
    {

        $query = "INSERT INTO `account` (`Password`, `UserInformationId`,  `Type`) 
                  VALUES (:password, :userInformationId,  :type)";

        try {
            $statement = $this->connection->prepare($query);
            if ($statement !== false) {
                $statement->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
                $statement->bindValue(':userInformationId', $userInformationId, PDO::PARAM_INT);
                $statement->bindValue(':type', $type, PDO::PARAM_STR);
                $statement->execute();
                $id = $this->connection->lastInsertId();
                return (object) [
                    "status" => 201,
                    "message" => "Tài khoản đã được tạo thành công",
                    "data" => $id
                ];
            } else {
                throw new PDOException();
            }
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    // Cập nhật thông tin tài khoản
    function updateAccount($id, $password, $status, $active)
    {
        $query = "UPDATE `account` SET 
                    `Password` = :password,
                    `Status` = :status,
                    `Active` = :active
                  WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            if ($statement !== false) {
                $statement->bindValue(':id', $id, PDO::PARAM_INT);
                $statement->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
                $statement->bindValue(':status', $status, PDO::PARAM_BOOL);
                $statement->bindValue(':active', $active, PDO::PARAM_BOOL);
                $statement->execute();

                if ($statement->rowCount() > 0) {
                    return (object) [
                        "status" => 200,
                        "message" => "Tài khoản đã được cập nhật thành công",
                    ];
                } else {
                    throw new PDOException("Không có bản ghi nào được cập nhật");
                }
            }
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }
}
