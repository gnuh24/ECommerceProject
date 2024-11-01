<?php

require_once __DIR__ . "/../Models/ProductModel.php";
// Xử lý yêu cầu RESTful cho ProductController
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'getAllProductsCommonUser') {
        $productController = new ProductController();
        $productController->getAllProductsCommonUser();
    } elseif (isset($_GET['action']) && $_GET['action'] === 'getAllProductsAdmin') {
        $productController = new ProductController();
        $productController->getAllProductsAdmin();
    } elseif (isset($_GET['Id'])) {
        if (isset($_GET['action']) && $_GET['action'] === 'getProductByIdAdmin') {
            $productController = new ProductController();
            $productController->getProductByIdAdmin($_GET['Id']);
        } else {
            $productController = new ProductController();
            $productController->getProductByIdCommonUser($_GET['Id']);
        }
    } else {
        echo json_encode(["status" => 400, "message" => "Invalid action"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawData = file_get_contents("php://input");

    error_log("Raw Data: " . $rawData); // Ghi nhận dữ liệu thô

    $parsedData = json_decode($rawData, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(["status" => 400, "message" => "Invalid JSON data"]);
        exit;
    }
    $productController = new ProductController();
    $productController->createProduct($parsedData);
} elseif ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    $rawData = file_get_contents("php://input");

    error_log("Raw Data: " . $rawData); // Ghi nhận dữ liệu thô

    $parsedData = json_decode($rawData, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(["status" => 400, "message" => "Invalid JSON data"]);
        exit;
    }

    error_log("Parsed PATCH Data: " . print_r($parsedData, true));

    if (isset($parsedData['id']) && !empty($parsedData['id'])) {
        $id = intval($parsedData['id']);
        $productController = new ProductController();

        if ($parsedData['action'] == "up") {
            $amount = $parsedData['amount'];
            // Gọi hàm updateProduct và truyền dữ liệu cần thiết
            $result = $productController->increaseQuantity($id, $amount);
        } else if ($parsedData['action'] == "down") {
            $amount = $parsedData['amount'];
            // Gọi hàm updateProduct và truyền dữ liệu cần thiết
            $result = $productController->decreaseQuantity($id, $amount);
        } else if ($parsedData['action'] == "update") {
            // Gọi hàm updateProduct và truyền dữ liệu cần thiết
            $result = $productController->updateProduct($id, $parsedData);
        }
    } else {
        http_response_code(400);
        echo json_encode(["status" => 400, "message" => "Product ID is required", "data" => $parsedData]);
    }
} else {
    echo json_encode(["status" => 405, "message" => "Method not allowed"]);
}

class ProductController
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
    }

    // Lấy danh sách tất cả sản phẩm dành cho người dùng thường (CommonUser)
    public function getAllProductsCommonUser()
    {
        // Tham số truy vấn
        $brandId = isset($_GET['brandId']) && !empty($_GET['brandId']) ? $_GET['brandId'] : null;
        $categoryId = isset($_GET['categoryId']) && !empty($_GET['categoryId']) ? $_GET['categoryId'] : null;
        $search = $_GET['search'] ?? null;
        $minPrice = $_GET['minPrice'] ?? null;
        $maxPrice = $_GET['maxPrice'] ?? null;
        $pageSize = $_GET['pageSize'] ?? 12;
        $pageNumber = $_GET['pageNumber'] ?? 1;

        $result = $this->productModel->getAllProductsCommonUser($brandId, $categoryId, $search, $minPrice, $maxPrice, $pageSize, $pageNumber);
        http_response_code(200);
        echo json_encode($result);
    }

    // Lấy danh sách tất cả sản phẩm dành cho Admin
    public function getAllProductsAdmin()
    {
        $brandId = $_GET['brandId'] ?? null;
        $categoryId = $_GET['categoryId'] ?? null;
        $search = $_GET['search'] ?? null;
        $minPrice = $_GET['minPrice'] ?? null;
        $maxPrice = $_GET['maxPrice'] ?? null;
        $pageSize = $_GET['pageSize'] ?? 20;
        $offset = $_GET['page'] ?? 0;
        // Ở phần đầu phương thức, chuyển đổi status sang boolean nếu có:
        $status = null;
        if (isset($_GET['status'])) {
            // Chuyển đổi chuỗi 'true' thành boolean true và 'false' thành boolean false
            if ($_GET['status'] === 'true') {
                $status = true;
            } elseif ($_GET['status'] === 'false') {
                $status = false;
            }
        }

        // Sau đó gọi phương thức getAllProductsAdmin với $status đã được chuyển đổi

        $result = $this->productModel->getAllProductsAdmin($brandId, $categoryId, $search, $status, $minPrice, $maxPrice, $pageSize, $offset);
        http_response_code(200);
        echo json_encode($result);
    }

    // Lấy sản phẩm theo ID dành cho Admin
    public function getProductByIdAdmin($id)
    {
        $result = $this->productModel->getProductByIdAdmin($id);
        if ($result) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(["status" => 404, "message" => "Product not found"]);
        }
    }

    // Lấy sản phẩm theo ID dành cho người dùng thường (CommonUser)
    public function getProductByIdCommonUser($id)
    {
        $result = $this->productModel->getProductByIdCommonUser($id);
        if ($result) {
            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(404);
            echo json_encode(["status" => 404, "message" => "Product not found"]);
        }
    }

    // Tạo sản phẩm mới - truyền bằng formdata
    public function createProduct($parsedData)
    {
        $image = null;

        // Kiểm tra xem có file ảnh được upload hay không
        if (isset($parsedData['image']) && !empty($parsedData['image']['name'])) {
            // Xử lý file upload (image)
            $image = $parsedData['image']['name'];
            // Di chuyển file đến thư mục mong muốn
            move_uploaded_file($parsedData['image']['tmp_name'], 'path/to/save/' . $image);
        }
        // Call the model's createProduct function
        $result = $this->productModel->createProduct(
            isset($parsedData['productName']) && !empty($parsedData['productName']) ? $parsedData['productName'] : null,
            isset($parsedData['price']) && !empty($parsedData['price']) ? $parsedData['price'] : null,
            $image, // Image may be null or an empty string
            isset($parsedData['origin']) && !empty($parsedData['origin']) ? $parsedData['origin'] : null,
            isset($parsedData['capacity']) && !empty($parsedData['capacity']) ? $parsedData['capacity'] : null,
            isset($parsedData['abv']) && !empty($parsedData['abv']) ? $parsedData['abv'] : null,
            isset($parsedData['quanity']) && !empty($parsedData['quanity']) ? $parsedData['quanity'] : null,
            isset($parsedData['description']) && !empty($parsedData['description']) ? $parsedData['description'] : null,
            isset($parsedData['brandId']) ? intval($parsedData['brandId']) : null,
            isset($parsedData['categoryId']) ? intval($parsedData['categoryId']) : null,
            isset($parsedData['voucherId']) ? intval($parsedData['voucherId']) : null,

        );

        // Check the result and set the appropriate HTTP response
        if ($result->status == 201) {
            http_response_code(201); // Created
        } else {
            http_response_code(500); // Internal Server Error
        }

        echo json_encode($result);
    }

    public function updateProduct($id, $parsedData)
    {

        // Khởi tạo biến cho hình ảnh
        $image = null;

        // Kiểm tra xem có file ảnh được upload hay không
        if (isset($parsedData['image']) && !empty($parsedData['image']['name'])) {
            // Xử lý file upload (image)
            $image = $parsedData['image']['name'];
            // Di chuyển file đến thư mục mong muốn
            move_uploaded_file($parsedData['image']['tmp_name'], 'path/to/save/' . $image);
        }

        // Gọi hàm cập nhật sản phẩm trong mô hình
        $result = $this->productModel->updateProduct(
            $id,
            $image, // Trường hợp ảnh có thể là một chuỗi rỗng hoặc null
            isset($parsedData['origin']) && !empty($parsedData['origin']) ? $parsedData['origin'] : null,
            isset($parsedData['capacity']) && !empty($parsedData['capacity']) ? $parsedData['capacity'] : null,
            isset($parsedData['abv']) && !empty($parsedData['abv']) ? $parsedData['abv'] : null,
            isset($parsedData['quanity']) && !empty($parsedData['quanity']) ? $parsedData['quanity'] : null,
            isset($parsedData['description']) && !empty($parsedData['description']) ? $parsedData['description'] : null,
            isset($parsedData['brandId']) ? intval($parsedData['brandId']) : null,
            isset($parsedData['categoryId']) ? intval($parsedData['categoryId']) : null,
            isset($parsedData['status']) ? $parsedData['status'] : null
        );

        // Trả về phản hồi thành công
        return $this->response($result); // Có thể trả về thông tin kết quả hoặc số dòng đã cập nhật

    }

    private function response($result)
    {
        http_response_code($result->status);
        $response = [
            "message" => $result->message,
            "data" => $result->data ?? null
        ];

        // Kiểm tra và thêm totalPages nếu có trong kết quả
        if (isset($result->totalPages)) {
            $response['totalPages'] = $result->totalPages;
        }
        if (isset($result->totalPages)) {
            $response['totalElements'] = $result->totalElements;
        }

        echo json_encode($response);
    }

    // Increase product quantity
    public function increaseQuantity($id, $amount)
    {
        if (!isset($id) || !isset($amount)) {
            return $this->response((object)[
                "status" => 400,
                "message" => "Product ID and amount are required"
            ]);
        }

        $result = $this->productModel->increaseQuantity($id, $amount);
        return $this->response($result);
    }

    // Decrease product quantity
    public function decreaseQuantity($id, $amount)
    {
        if (!isset($id) || !isset($amount)) {
            return $this->response((object)[
                "status" => 400,
                "message" => "Product ID and amount are required"
            ]);
        }

        $result = $this->productModel->decreaseQuantity($id, $amount);
        return $this->response($result);
    }
}
