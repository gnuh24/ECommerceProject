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
    public function getAllProductsCommonUser($brandId = null, $categoryId = null, $search = null, $minPrice = null, $maxPrice = null, $limit = 20, $offset = 0)
    {
        // Lưu trữ điều kiện WHERE
        $conditions = [];
        $params = [];

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
            $params[':searchId'] = $search;  // Thêm tham số cho tìm kiếm theo ID
        }

        if ($minPrice !== null) {
            $conditions[] = "(SELECT MIN(b.UnitPrice) FROM `batch` b WHERE b.ProductId = p.Id AND b.Quantity > 0) >= :minPrice";
            $params[':minPrice'] = $minPrice;
        }

        if ($maxPrice !== null) {
            $conditions[] = "(SELECT MAX(b.UnitPrice) FROM `batch` b WHERE b.ProductId = p.Id AND b.Quantity > 0) <= :maxPrice";
            $params[':maxPrice'] = $maxPrice;
        }

        // Xây dựng điều kiện WHERE nếu có
        $whereClause = '';
        if (!empty($conditions)) {
            $whereClause = 'WHERE ' . implode(' AND ', $conditions);
        }

        // Truy vấn lấy sản phẩm
        $query = "
                    SELECT p.*, 
                        (SELECT b.UnitPrice 
                            FROM `batch` b 
                            WHERE b.ProductId = p.Id 
                            AND b.Quantity > 0 
                            ORDER BY b.ReceivingTime DESC 
                            LIMIT 1) AS UnitPrice
                    FROM `product` p
                    $whereClause
                    LIMIT :limit OFFSET :offset
                ";

        try {
            $statement = $this->connection->prepare($query);

            // Gán giá trị cho tham số LIMIT và OFFSET
            $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
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

            return (object) [
                "status" => 200,
                "message" => "Products fetched successfully",
                "data" => $result,
                "total" => $totalCount
            ];
        } catch (PDOException $e) {
            return (object) [
                "status" => 400,
                "message" => $e->getMessage()
            ];
        }
    }


    // Lấy tất cả sản phẩm của Admin
    public function getAllProductsAdmin($brandId = null, $categoryId = null, $search = null, $minPrice = null, $maxPrice = null, $limit = 20, $page = 1)
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
        if ($minPrice !== null) {
            $conditions[] = "(SELECT MIN(b.UnitPrice) FROM `batch` b WHERE b.ProductId = p.Id AND b.Quantity > 0) >= :minPrice";
            $params[':minPrice'] = $minPrice;
        }
        if ($maxPrice !== null) {
            $conditions[] = "(SELECT MAX(b.UnitPrice) FROM `batch` b WHERE b.ProductId = p.Id AND b.Quantity > 0) <= :maxPrice";
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
            (SELECT b.UnitPrice 
                FROM `batch` b 
                WHERE b.ProductId = p.Id 
                AND b.Quantity > 0 
                ORDER BY b.ReceivingTime DESC 
                LIMIT 1) AS UnitPrice,
            (SELECT b.Quantity 
                FROM `batch` b 
                WHERE b.ProductId = p.Id 
                AND b.Quantity > 0 
                ORDER BY b.ReceivingTime DESC 
                LIMIT 1) AS AvailableQuantity,
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
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
            $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
            foreach ($params as $key => $value) {
                $statement->bindValue($key, $value);
            }
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            $countQuery = "SELECT COUNT(*) AS total FROM `product` p $whereClause";
            $countStatement = $this->connection->prepare($countQuery);
            foreach ($params as $key => $value) {
                $countStatement->bindValue($key, $value);
            }
            $countStatement->execute();
            $totalCount = $countStatement->fetchColumn();
            $totalPages = ceil($totalCount / $limit);
            $content = array_map(function ($item) {
                return [
                    "id" => $item['Id'],
                    "productName" => $item['ProductName'],
                    "status" => $item['Status'],
                    "quantity" => $item['AvailableQuantity'],
                    "price" => $item['UnitPrice'],
                    "image" => $item['Image'],
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
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            $productResult = $statement->fetch(PDO::FETCH_ASSOC);

            if ($productResult) {
                // Lấy thông tin batches
                $batchesQuery = "
                    SELECT b.Id, b.UnitPrice AS price, b.Quantity, b.ReceivingTime 
                    FROM `batch` b 
                    WHERE b.ProductId = :productId 
                    AND b.Quantity > 0
                    ORDER BY b.ReceivingTime DESC
                    ";

                $batchesStatement = $this->connection->prepare($batchesQuery);
                $batchesStatement->bindValue(':productId', $id, PDO::PARAM_INT);
                $batchesStatement->execute();
                $batchesResult = $batchesStatement->fetchAll(PDO::FETCH_ASSOC);

                // Định dạng lại dữ liệu theo yêu cầu
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
                    "batches" => array_map(function ($batch) {
                        return [
                            "id" => $batch['Id'],
                            "price" => $batch['price'],
                            "quantity" => $batch['Quantity'],
                            "receivingTime" => date('H:i:s d/m/Y', strtotime($batch['ReceivingTime']))
                        ];
                    }, $batchesResult),
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
                (SELECT b.UnitPrice 
                    FROM `batch` b 
                    WHERE b.ProductId = p.Id 
                    AND b.Quantity > 0 
                    ORDER BY b.ReceivingTime DESC 
                    LIMIT 1) AS UnitPrice,
                (SELECT b.Quantity 
                    FROM `batch` b 
                    WHERE b.ProductId = p.Id 
                    AND b.Quantity > 0 
                    ORDER BY b.ReceivingTime DESC 
                    LIMIT 1) AS AvailableQuantity,
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
                    "message" => "Product not found or no available batch"
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

    public function updateProduct($id, $productName, $status, $image, $origin, $capacity, $abv, $description, $brandId, $categoryId)
    {
        // Tạo mảng để lưu các phần của câu lệnh SQL
        $fieldsToUpdate = [];
        $params = [];

        // Kiểm tra các trường và thêm vào mảng nếu có giá trị
        if (!empty($productName)) {
            $fieldsToUpdate[] = "`ProductName` = :productName";
            $params[':productName'] = $productName;
        }

        if ($status !== null) {
            $fieldsToUpdate[] = "`Status` = :status";
            $params[':status'] = $status;
        }

        if (!empty($image)) {
            $fieldsToUpdate[] = "`Image` = :image";
            $params[':image'] = $image;
        }

        if (!empty($origin)) {
            $fieldsToUpdate[] = "`Origin` = :origin";
            $params[':origin'] = $origin;
        }

        if (!empty($capacity)) {
            $fieldsToUpdate[] = "`Capacity` = :capacity";
            $params[':capacity'] = $capacity;
        }

        if (!empty($abv)) {
            $fieldsToUpdate[] = "`ABV` = :abv";
            $params[':abv'] = $abv;
        }

        if (!empty($description)) {
            $fieldsToUpdate[] = "`Description` = :description";
            $params[':description'] = $description;
        }

        if (!empty($brandId)) {
            $fieldsToUpdate[] = "`BrandId` = :brandId";
            $params[':brandId'] = $brandId;
        }

        if (!empty($categoryId)) {
            $fieldsToUpdate[] = "`CategoryId` = :categoryId";
            $params[':categoryId'] = $categoryId;
        }

        // Nếu không có trường nào để cập nhật, trả về thông báo lỗi
        if (empty($fieldsToUpdate)) {
            return (object) [
                "status" => 400,
                "message" => "No valid fields to update"
            ];
        }

        // Ghép câu lệnh SQL với các trường cần cập nhật
        $query = "UPDATE `product` SET " . implode(", ", $fieldsToUpdate) . " WHERE `Id` = :id";

        try {
            $statement = $this->connection->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);

            // Gắn các giá trị vào statement
            foreach ($params as $param => $value) {
                $statement->bindValue($param, $value);
            }

            $statement->execute();

            if ($statement->rowCount() > 0) {
                return (object) [
                    "status" => 200,
                    "message" => "Product updated successfully"
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
}
