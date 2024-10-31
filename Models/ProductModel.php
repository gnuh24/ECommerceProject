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
                    "quantity" => $item['Quantity'],
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

    public function createProduct($productName, $unitPrice, $image, $origin = null, $capacity = null, $quantity, $abv = null, $description = null, $brandId = null, $categoryId = null)
    {
        $status = 1; // Default status
        $createTime = date('Y-m-d H:i:s'); // Current date and time

        $fields = ["`ProductName`", "`Status`", "`CreateTime`", "`Quantity`", "`UnitPrice`"];
        $placeholders = [":productName", ":status", ":createTime", ":quantity", ":unitPrice"];
        $params = [
            ':productName' => $productName,
            ':status' => $status,
            ':createTime' => $createTime,
            ':quantity' => $quantity,
            ':unitPrice' => $unitPrice
        ];

        // Check and add optional fields
        if (!empty($image)) {
            $fields[] = "`Image`";
            $placeholders[] = ":image";
            $params[':image'] = $image;
        }

        if (!empty($sale)) {
            $fields[] = "`Sale`";
            $placeholders[] = ":sale";
            $params[':sale'] = $sale;
        }

        if (!empty($origin)) {
            $fields[] = "`Origin`";
            $placeholders[] = ":origin";
            $params[':origin'] = $origin;
        }

        if ($capacity !== null) {
            $fields[] = "`Capacity`";
            $placeholders[] = ":capacity";
            $params[':capacity'] = $capacity;
        }

        if ($abv !== null) {
            $fields[] = "`ABV`";
            $placeholders[] = ":abv";
            $params[':abv'] = $abv;
        }

        if (!empty($description)) {
            $fields[] = "`Description`";
            $placeholders[] = ":description";
            $params[':description'] = $description;
        }

        if ($brandId !== null) {
            $fields[] = "`BrandId`";
            $placeholders[] = ":brandId";
            $params[':brandId'] = $brandId;
        }

        if ($categoryId !== null) {
            $fields[] = "`CategoryId`";
            $placeholders[] = ":categoryId";
            $params[':categoryId'] = $categoryId;
        }

        // Create SQL query
        $query = "INSERT INTO `Product` (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $placeholders) . ")";
        $queryWithParams = $this->getSQLWithParams($query, $params);

        try {
            // Prepare and execute statement
            $statement = $this->connection->prepare($query);

            // Bind values to parameters
            foreach ($params as $param => $value) {
                $statement->bindValue($param, $value);
            }

            // Execute statement
            $statement->execute();
            $lastInsertId = $this->connection->lastInsertId();

            // Return success status
            return (object)[
                "status" => 201,
                "message" => "Product created successfully",
            ];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return (object)[
                "status" => 500,
                "message" => "Insert failed: " . $e->getMessage(),
                "query" => $queryWithParams
            ];
        }
    }

    public function updateProduct($id, $image, $origin, $capacity, $abv, $quantity, $description, $brandId, $categoryId, $status)
    {
        $fieldsToUpdate = [];
        $params = [];

        // Add fields and parameters dynamically if they are not null or empty
        if (!empty($image)) {
            $fieldsToUpdate[] = "`Image` = :image";
            $params[':image'] = $image;
        }

        if (!empty($origin)) {
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

        if ($quantity !== null) {
            $fieldsToUpdate[] = "`Quantity` = :quantity";
            $params[':quantity'] = $quantity;
        }

        if (!empty($description)) {
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

        if ($status !== null) {
            $fieldsToUpdate[] = "`Status` = :status";
            $params[':status'] = $status;
        }

        // Ensure there are fields to update
        if (empty($fieldsToUpdate)) {
            return (object)[
                "status" => 400,
                "message" => "No valid fields to update"
            ];
        }

        // Build the SQL query
        $query = "UPDATE `product` SET " . implode(", ", $fieldsToUpdate) . " WHERE `Id` = :id";
        $params[':id'] = intval($id);

        // Replace parameters in SQL for logging
        $queryWithParams = $this->getSQLWithParams($query, $params);

        try {
            // Prepare and bind parameters for execution
            $statement = $this->connection->prepare($query);
            foreach ($params as $param => $value) {
                $statement->bindValue($param, $value);
            }

            // Execute statement and check affected rows
            $statement->execute();
            $rowCount = $statement->rowCount();

            return (object)[
                "status" => 200,
                "message" => $rowCount > 0 ? "Product updated successfully" : "No record was updated",
                "query" => $queryWithParams,
                "rowCount" => $rowCount
            ];
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return (object)[
                "status" => 500,
                "message" => "Update failed: " . $e->getMessage(),
                "query" => $queryWithParams
            ];
        }
    }

    public function increaseQuantity($id, $amount)
    {
        if ($amount <= 0) {
            return (object)[
                "status" => 400,
                "message" => "Increase amount must be positive"
            ];
        }

        // Fetch current quantity and product name
        $productData = $this->getProductData($id);
        if ($productData === null) {
            return (object)[
                "status" => 404,
                "message" => "Product not found"
            ];
        }

        $currentQuantity = $productData['Quantity'];
        $newQuantity = $currentQuantity + $amount;

        // Update quantity
        return $this->updateProduct($id, null, null, null, null, $newQuantity, null, null, null, null);
    }

    public function decreaseQuantity($id, $amount)
    {
        if ($amount <= 0) {
            return (object)[
                "status" => 400,
                "message" => "Decrease amount must be positive"
            ];
        }

        // Fetch current quantity and product name
        $productData = $this->getProductData($id);
        if ($productData === null) {
            return (object)[
                "status" => 404,
                "message" => "Product not found"
            ];
        }

        $currentQuantity = $productData['Quantity'];
        $productName = $productData['Name'];

        // Check if there is enough quantity in stock
        if ($currentQuantity < $amount) {
            return (object)[
                "status" => 400,
                "message" => "Not enough quantity in inventory for product: $productName"
            ];
        }

        $newQuantity = $currentQuantity - $amount;

        // Update quantity
        return $this->updateProduct($id, null, null, null, null, $newQuantity, null, null, null, null);
    }

    // Helper function to fetch product data including Quantity and Name
    private function getProductData($id)
    {
        $query = "SELECT `Quantity`, `Name` FROM `product` WHERE `Id` = :id";
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':id', intval($id), PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
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
