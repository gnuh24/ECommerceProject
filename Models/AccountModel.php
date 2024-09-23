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
    function getAccountById($userInformationId, $page, $search, $role, $status)
    {
        $query = "SELECT * FROM `account` WHERE `UserInformationId` = :userInformationId";

        //Mảng chứa điều kiện
        $where_conditions=[];

        //Số phần tử mỗi trang
        $entityPerPage = 10;

        //Tổng số trang
        $totalPages = null;

        if (!empty($search)) {
            $empty = false;
            $where_conditions[] .= "(`TenDangNhap`  LIKE '%" . $search . "%' OR `Email`         LIKE '%" . $search . "%')";
        }

        if (!empty($role)) {
            $where_conditions[] = "`Quyen` = '$quyen' ";
        }

        if (!empty($status)) {
            $where_conditions[] = "`TrangThai` = $trangThai";
        }

        if (!empty($where_conditions)) {
            $query .= " WHERE " . implode(" AND ", $where_conditions);
        }

        if ($totalPages === null) {
            //fetchColumn ( <Cột thứ n> ) : Lấy row đầu tiên của cột thứ n - 1
            $query_total_row = substr_replace($query, "COUNT(*)", 7, 1);

            // Chạy lệnh Query để lấy ra tổng trang
            $statement_total_row = $connection->prepare($query_total_row);
            $statement_total_row->execute();
    
            //Làm tròn lên -> Tính ra tổng số trang
            $totalPages = ceil($statement_total_row->fetchColumn() / $entityPerPage);
        }

        $current_page = isset($page) ? $page : 1;
        $start_from = ($current_page - 1) * $entityPerPage;
    
        $query .= " LIMIT $entityPerPage OFFSET $start_from";

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
                    "data" => $result,
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
