<?php
require_once __DIR__ . "../../Configure/MysqlConfig.php";

class ProductModel
{
    private $connection;

    public function __construct()
    {
        $this->connection = MysqlConfig::getConnection();
    }

    // Lấy tất cả sản phẩm của CommonUser
    public function getAllProductsCommonUser(
        $brandId = null,
        $categoryId = null,
        $search = null,
        $minPrice = null,
        $maxPrice = null,
        $limit = 12,
        $pageNumber = 0
    ) {
        // Lưu trữ điều kiện WHERE
        $conditions = [];
        $params = [];

        if ($brandId !== null) {
            $conditions[] = "p.brandId = :brandId";
            $params[':brandId'] = $brandId;
        }

        if ($categoryId !== null) {
            $conditions[] = "p.categoryId = :categoryId";
            $params[':categoryId'] = $categoryId;
        }

        if ($search !== null) {
            $conditions[] = "(p.ProductName LIKE :search OR p.Id = :searchId)";
            $params[':search'] = '%' . $search . '%';
            $params[':searchId'] = $search;  // Thêm tham số cho tìm kiếm theo ID
        }

        if ($minPrice !== null) {
            $conditions[] = "p.UnitPrice >= :minPrice";
            $params[':minPrice'] = $minPrice;
        }

        if ($maxPrice !== null) {
            $conditions[] = "p.UnitPrice <= :maxPrice";
            $params[':maxPrice'] = $maxPrice;
        }

        // Xây dựng điều kiện WHERE nếu có
        $whereClause = '';
        if (!empty($conditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $conditions);
        }

        // Truy vấn lấy sản phẩm
        $query = "
                SELECT p.* FROM `Product` p
                $whereClause
                LIMIT :limit OFFSET :offset
            ";

        try {
            $statement = $this->connection->prepare($query);

            // Gán giá trị cho tham số LIMIT và OFFSET
            $statement->bindValue(':limit', $limit, PDO::PARAM_INT);

            $offset = ($pageNumber - 1) * $limit;
            $statement->bindValue(':offset', $offset, PDO::PARAM_INT);

            // Gán các tham số khác nếu có
            foreach ($params as $key => $value) {
                $statement->bindValue($key, $value);
            }

            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            // Lấy tổng số sản phẩm để hỗ trợ phân trang
            $countQuery = "SELECT COUNT(*) AS total FROM `product` p $whereClause";
            $countStatement = $this->connection->prepare($countQuery);
            foreach ($params as $key => $value) {
                $countStatement->bindValue($key, $value);
            }
            $countStatement->execute();
            $totalCount = $countStatement->fetchColumn();

            // Tính số trang
            $totalPages = ceil($totalCount / $limit);

            return (object) [
                "status" => 200,
                "message" => "Products fetched successfully",
                "data" => $result,
                "totalPages" => $totalPages,
                "totalElements" => $totalCount,
                "size" => $limit
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }



    // Lấy tất cả sản phẩm của Admin
    public function getAllProductsAdmin($brandId = null, $categoryId = null, $search = null, $trangthai = null, $minPrice = null, $maxPrice = null, $limit = 20, $page = 1)
    {
        $conditions = [];
        $params = [];

        // Các điều kiện lọc
        if ($brandId !== null) {
            $conditions[] = "p.BrandId = :brandId";
            $params[':brandId'] = $brandId;
        }
        if ($categoryId !== null) {
            $conditions[] = "p.CategoryId = :categoryId";
            $params[':categoryId'] = $categoryId;
        }
        if ($search !== null) {
            $conditions[] = "(p.ProductName LIKE :search OR p.Id = :searchId)";
            $params[':search'] = '%' . $search . '%';
            $params[':searchId'] = $search;
        }
        // Điều kiện cho trạng thái (boolean true/false)
        if ($trangthai !== null) {
            $conditions[] = "p.Status = :trangthai";
            $params[':trangthai'] = $trangthai ? TRUE : FALSE; // true = 1, false = 0
        }

        if ($minPrice !== null) {
            $conditions[] = "p.UnitPrice >= :minPrice";
            $params[':minPrice'] = $minPrice;
        }

        if ($maxPrice !== null) {
            $conditions[] = "p.UnitPrice <= :maxPrice";
            $params[':maxPrice'] = $maxPrice;
        }

        $whereClause = '';
        if (!empty($conditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $conditions);
        }

        // Tính toán offset
        $offset = ($page - 1) * $limit;

        $query = "
            SELECT p.*,
                (SELECT br.BrandName 
                    FROM `brand` br 
                    WHERE br.Id = p.BrandId) AS BrandName,
                (SELECT c.CategoryName 
                    FROM `category` c 
                    WHERE c.Id = p.CategoryId) AS CategoryName
            FROM `product` p
            $whereClause
            LIMIT :limit OFFSET :offset
        ";

        try {

            //Setup tham số
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
            $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
            foreach ($params as $key => $value) {
                $statement->bindValue($key, $value);
            }
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);


            //Xử lý để lấy tổng số lượng sản phẩm
            $countQuery = "SELECT COUNT(*) AS total FROM `product` p $whereClause";
            $countStatement = $this->connection->prepare($countQuery);
            foreach ($params as $key => $value) {
                $countStatement->bindValue($key, $value);
            }
            $countStatement->execute();
            $totalCount = $countStatement->fetchColumn();

            //Tổng số trang
            $totalPages = ceil($totalCount / $limit);

            $content = array_map(function ($item) {
                return [
                    "id" => $item['Id'],
                    "productName" => $item['ProductName'],
                    "status" => $item['Status'],
                    "quantity" => $item['AvailableQuantity'],
                    "price" => $item['UnitPrice'],
                    "image" => $item['Image'],
                    "sale" => $item['Sale'],
                    "createTime" => date('H:i:s d/m/Y', strtotime($item['CreateTime'])),
                    "brand" => [
                        "id" => $item['BrandId'],
                        "brandName" => $item['BrandName']
                    ],
                    "category" => [
                        "id" => $item['CategoryId'],
                        "categoryName" => $item['CategoryName']
                    ]
                ];
            }, $result);

            return (object) [
                "status" => 200,
                "message" => "Products fetched successfully",
                "totalPages" => $totalPages,
                "totalElements" => $totalCount,
                "size" => $limit,
                "content" => $content
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }


    // Lấy sản phẩm theo ID của Admin
    public function getProductByIdAdmin($id)
    {
        // Truy vấn sản phẩm dựa trên ID
        $query = "
            SELECT p.*, 
                (SELECT br.BrandName 
                    FROM `brand` br 
                    WHERE br.Id = p.BrandId) AS BrandName,
                (SELECT c.CategoryName 
                    FROM `category` c 
                    WHERE c.Id = p.CategoryId) AS CategoryName
            FROM `product` p
            WHERE p.Id = :id
        ";

        try {
            // Chuẩn bị và thực thi truy vấn sản phẩm
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            $productResult = $statement->fetch(PDO::FETCH_ASSOC);

            // Kiểm tra xem sản phẩm có tồn tại hay không
            if ($productResult) {
                // Định dạng dữ liệu trả về
                $response = [
                    "id" => $productResult['Id'],
                    "productName" => $productResult['ProductName'],
                    "status" => $productResult['Status'],
                    "createTime" => date('H:i:s d/m/Y', strtotime($productResult['CreateTime'])),
                    "image" => $productResult['Image'],
                    "description" => $productResult['Description'],
                    "origin" => $productResult['Origin'],
                    "capacity" => $productResult['Capacity'],
                    "abv" => $productResult['ABV'],
                    "unitPrice" => $productResult['UnitPrice'],
                    "quantity" => $productResult['Quantity'],
                    "sale" => $productResult['Sale'],
                    "brand" => [
                        "id" => $productResult['BrandId'],
                        "brandName" => $productResult['BrandName']
                    ],
                    "category" => [
                        "id" => $productResult['CategoryId'],
                        "categoryName" => $productResult['CategoryName']
                    ]
                ];

                return (object) [
                    "status" => 200,
                    "message" => "Product fetched successfully",
                    "data" => $response
                ];
            } else {
                return (object) [
                    "status" => 404,
                    "message" => "Product not found"
                ];
            }
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }


    // Lấy sản phẩm theo ID của CommonUser
    public function getProductByIdCommonUser($id)
    {
        $query = "
            SELECT p.*,
                p.UnitPrice,
                p.Quantity AS AvailableQuantity,
                (SELECT br.BrandName 
                    FROM `brand` br 
                    WHERE br.Id = p.BrandId) AS BrandName,
                (SELECT c.CategoryName 
                    FROM `category` c 
                    WHERE c.Id = p.CategoryId) AS CategoryName
            FROM `product` p
            WHERE p.Id = :id
            ";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                // Định dạng lại dữ liệu theo yêu cầu
                $response = [
                    "id" => $result['Id'],
                    "productName" => $result['ProductName'],
                    "price" => $result['UnitPrice'],
                    "image" => $result['Image'],
                    "description" => $result['Description'],
                    "origin" => $result['Origin'],
                    "capacity" => $result['Capacity'],
                    "abv" => $result['ABV'],
                    "quantity" => $result['AvailableQuantity'],
                    "sale" => $result['Sale'],
                    "brand" => [
                        "id" => $result['BrandId'],
                        "brandName" => $result['BrandName']
                    ],
                    "category" => [
                        "id" => $result['CategoryId'],
                        "categoryName" => $result['CategoryName']
                    ]
                ];

                return (object) [
                    "status" => 200,
                    "message" => "Product fetched successfully",
                    "data" => $response
                ];
            } else {
                return (object) [
                    "status" => 404,
                    "message" => "Product not found or no available"
                ];
            }
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    public function createProduct($productName)
    {
        // Lấy thời gian hiện tại ở định dạng 'Y-m-d H:i:s'
        $currentDateTime = date('Y-m-d H:i:s');

        // Status mặc định là true (hoặc 1)
        $status = true;

        $query = "
        INSERT INTO `Product` 
        (`ProductName`, `Status`, `CreateTime`, `BrandId`, `CategoryId`) 
        VALUES (:productName, :status, :createTime, 1, 1)
        ";

        try {
            $statement = $this->connection->prepare($query);

            // Gán giá trị cho các tham số
            $statement->bindValue(':productName', $productName, PDO::PARAM_STR);
            $statement->bindValue(':status', $status, PDO::PARAM_BOOL);
            $statement->bindValue(':createTime', $currentDateTime, PDO::PARAM_STR);

            $statement->execute();

            return (object)[
                "status" => 201,
                "message" => "Product created successfully",
                "productId" => $this->connection->lastInsertId() // Lấy ID của sản phẩm vừa được tạo
            ];
        } catch (PDOException $e) {
            return (object)[
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }

    public function updateProduct($id, $image, $origin, $capacity, $abv, $description, $brandId, $categoryId, $status)
    {
        $fieldsToUpdate = [];
        $params = [];

        // Xây dựng các trường cần cập nhật
        if ($image !== null && $image !== '') {
            $fieldsToUpdate[] = "`Image` = :image";
            $params[':image'] = $image;
        }

        if ($origin !== null && $origin !== '') {
            $fieldsToUpdate[] = "`Origin` = :origin";
            $params[':origin'] = $origin;
        }

        if ($capacity !== null) {
            $fieldsToUpdate[] = "`Capacity` = :capacity";
            $params[':capacity'] = $capacity;
        }

        if ($abv !== null) {
            $fieldsToUpdate[] = "`ABV` = :abv";
            $params[':abv'] = $abv;
        }

        if ($description !== null && $description !== '') {
            $fieldsToUpdate[] = "`Description` = :description";
            $params[':description'] = $description;
        }

        if ($brandId !== null) {
            $fieldsToUpdate[] = "`BrandId` = :brandId";
            $params[':brandId'] = $brandId;
        }

        if ($categoryId !== null) {
            $fieldsToUpdate[] = "`CategoryId` = :categoryId";
            $params[':categoryId'] = $categoryId;
        }

        // Thêm logic cập nhật cho status
        if ($status !== null) {
            $fieldsToUpdate[] = "`Status` = :status";
            $params[':status'] = $status;
        }

        // Kiểm tra nếu không có trường nào hợp lệ để cập nhật
        if (empty($fieldsToUpdate)) {
            return (object)[
                "status" => 400,
                "message" => "No valid fields to update"
            ];
        }

        // Tạo câu lệnh SQL
        $query = "UPDATE `product` SET " . implode(", ", $fieldsToUpdate) . " WHERE `Id` = :id";
        $params[':id'] = intval($id);

        // Tạo câu SQL có chứa các giá trị đã được thay thế
        $queryWithParams = $this->getSQLWithParams($query, $params);

        try {
            // Thực thi câu lệnh
            $statement = $this->connection->prepare($query);

            // Gán giá trị cho các tham số
            foreach ($params as $param => $value) {
                $statement->bindValue($param, $value);
            }

            // Thực thi câu lệnh và kiểm tra số dòng bị ảnh hưởng
            $statement->execute();
            $rowCount = $statement->rowCount();

            // Trả về câu lệnh SQL và trạng thái cập nhật
            return (object)[
                "status" => 200,
                "message" => $rowCount > 0 ? "Product updated successfully" : "No record was updated",
                "query" => $queryWithParams, // Trả về câu lệnh SQL đã thay thế
                "rowCount" => $rowCount
            ];
        } catch (PDOException $e) {
            // Ghi log lỗi và trả về lỗi
            error_log($e->getMessage());
            return (object)[
                "status" => 500,
                "message" => "Update failed: " . $e->getMessage(),
                "query" => $queryWithParams
            ];
        }
    }

    private function getSQLWithParams($query, $params)
    {
        foreach ($params as $param => $value) {
            $escapedValue = $this->escapeForSQL($value); // Sử dụng hàm escape giá trị
            $query = str_replace($param, $escapedValue, $query);
        }
        return $query;
    }

    private function escapeForSQL($value)
    {
        // Hàm escape giá trị cho câu lệnh SQL
        if (is_string($value)) {
            return "'" . addslashes($value) . "'";
        } elseif (is_numeric($value)) {
            return $value;
        } else {
            return 'NULL';
        }
    }
}
