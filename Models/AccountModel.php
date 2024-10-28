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



    public function getAccountById($userInformationId = null, $filters = [], $page = 1, $search = '', $role = '', $status = null)
    {

        // Xác định trang hiện tại, mặc định là 1 nếu không có giá trị hợp lệ
        $current_page = isset($page) && $page > 0 ? (int)$page : 1;
    
        // Khởi tạo câu truy vấn cơ bản để lấy thông tin tài khoản và thông tin người dùng
        $query = "SELECT a.*, ui.Email, ui.Address, ui.Birthday, ui.Fullname, ui.Gender, ui.PhoneNumber 
                  FROM `account` a
                  JOIN `UserInformation` ui ON a.UserInformationId = ui.Id";
    
        // Mảng chứa các điều kiện WHERE
        $where_conditions = [];
        
        // Kiểm tra xem userInformationId có khác null không
        if ($userInformationId !== null) {
            // Nếu có, thêm điều kiện WHERE để lọc theo UserInformationId
            $query .= " WHERE a.UserInformationId = :userInformationId";
            $where_conditions[':userInformationId'] = $userInformationId; // Thêm tham số vào mảng điều kiện
        } else {
            // Nếu không có userInformationId, bắt đầu với điều kiện WHERE luôn đúng
            $query .= " WHERE 1=1";
        }
    
        // Số phần tử mỗi trang
        $entityPerPage = 10;
    
        // Tổng số trang
        $totalPages = null;
    
        // Thêm điều kiện tìm kiếm (username hoặc email)
        if (!empty($search)) {
            $query .= " AND (ui.`Email` LIKE :search)";
            $where_conditions[':search'] = '%' . $search . '%';
        }
    
        // Thêm các điều kiện lọc
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                $query .= " AND a.`$key` = :$key";
                $where_conditions[":$key"] = $value;
            }
        }
    
        // Thêm điều kiện lọc theo role
        if (!empty($role)) {
            $query .= " AND a.`role` = :role";
            $where_conditions[':role'] = $role;
        }
    
        // Thêm điều kiện lọc theo status
        if ($status === '0' || $status === '1') { // Chỉ kiểm tra nếu status là 0 hoặc 1
            $query .= " AND a.`status` = :status";
            $where_conditions[':status'] = $status; // Gán giá trị cho tham số
        }
    
        // Tính tổng số trang
        if ($totalPages === null) {
            // Câu truy vấn để đếm tổng số hàng
            $query_total_row = "SELECT COUNT(*) 
                                FROM `account` a
                                JOIN `UserInformation` ui ON a.UserInformationId = ui.Id";
    
            // Nếu có $userInformationId, thêm điều kiện WHERE
            if ($userInformationId !== null) {
                $query_total_row .= " WHERE a.UserInformationId = :userInformationId";
            } else {
                $query_total_row .= " WHERE 1=1"; // Để dễ dàng thêm các điều kiện sau
            }
    
            // Thêm các điều kiện khác vào câu truy vấn đếm
            if (!empty($search)) {
                $query_total_row .= " AND (ui.`Email` LIKE :search)";
            }
    
            if (!empty($filters)) {
                foreach ($filters as $key => $value) {
                    $query_total_row .= " AND a.`$key` = :$key";
                }
            }
    
            if (!empty($role)) {
                $query_total_row .= " AND a.`role` = :role";
            }
    
            // Thêm điều kiện cho status
            if ($status === '0' || $status === '1') { 
                $query_total_row .= " AND a.`status` = :status";
            }
    
            // Chạy truy vấn đếm
            $statement_total_row = $this->connection->prepare($query_total_row);
            $statement_total_row->execute($where_conditions);
    
            // Tính tổng số trang
            $totalRows = $statement_total_row->fetchColumn();
            $totalPages = ceil($totalRows / $entityPerPage);
        }
    
        // Tính toán phân trang
        $start_from = ($current_page - 1) * $entityPerPage;
    
        // Thêm điều kiện phân trang vào câu truy vấn
        $query .= " LIMIT :limit OFFSET :offset";
    
        try {
            // Chuẩn bị câu truy vấn
            $statement = $this->connection->prepare($query);
            foreach ($where_conditions as $key => $value) {
                $statement->bindValue($key, $value);
            }
    
            // Gán giá trị cho LIMIT và OFFSET
            $statement->bindValue(':limit', $entityPerPage, PDO::PARAM_INT);
            $statement->bindValue(':offset', $start_from, PDO::PARAM_INT);
    
            // Thực thi câu truy vấn
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    
            // Kiểm tra dữ liệu có tồn tại không
            $isExists = !empty($result);
    
            return (object) [
                "status" => 200,
                "message" => "Truy vấn thành công",
                "data" => $result,
                "totalPages" => $totalPages,
                "isExists" => $query
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
    function updateAccount($id, $password, $status)
    {
        $query = "UPDATE `account` SET 
                    `Password` = :password,
                    `Status` = :status
                  WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            if ($statement !== false) {
                $statement->bindValue(':id', $id, PDO::PARAM_INT);
                $statement->bindValue(':password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
                $statement->bindValue(':status', $status, PDO::PARAM_BOOL);
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

// Phương thức cập nhật quyền cho tài khoản
public function updateRole($id, $newRole)
{
    $query = "UPDATE `account` SET `Role` = :role WHERE `Id` = :id"; 

    try {
        $statement = $this->connection->prepare($query);
        if ($statement !== false) {
            // Liên kết tham số
            $statement->bindValue(':role', $newRole, PDO::PARAM_STR);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            
            // Thực thi câu lệnh
            $statement->execute();

            if ($statement->rowCount() > 0) {
                return (object)[
                    'status' => 200,
                    'message' => 'Cập nhật quyền thành công!'
                ];
            } else {
                return (object)[
                    'status' => 404,
                    'message' => 'Không tìm thấy tài khoản để cập nhật!'
                ];
            }
        } else {
            throw new PDOException();
        }
    } catch (PDOException $e) {
        return (object)[
            'status' => 400,
            'message' => 'Cập nhật quyền thất bại: ' . $e->getMessage()
        ];
    }
}

    
    
}
