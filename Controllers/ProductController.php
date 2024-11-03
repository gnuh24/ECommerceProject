<?php

require_once __DIR__ . "/../Models/ProductModel.php";
// Xử lý yêu cầu RESTful cho ProductController
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'getAllProductsCommonUser') {
        $productController = new ProductController();
        echo $productController->getAllProductsCommonUser();
    } elseif (isset($_GET['action']) && $_GET['action'] === 'getAllProductsAdmin') {
        $productController = new ProductController();
        echo $productController->getAllProductsAdmin();
    } elseif (isset($_GET['Id'])) {
        if (isset($_GET['action']) && $_GET['action'] === 'getProductByIdAdmin') {
            $productController = new ProductController();
            echo $productController->getProductByIdAdmin($_GET['Id']);
        } else {
            $productController = new ProductController();
            echo $productController->getProductByIdCommonUser($_GET['Id']);
        }
    } else {
        echo json_encode(["status" => 400, "message" => "Invalid action"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Kiểm tra và ghi lại nội dung của $_POST và $_FILES để debug
    error_log("POST Data: " . print_r($_POST, true));
    error_log("FILES Data: " . print_r($_FILES, true));
    if (isset($_POST['update'])) {
        $parsedData = [
            'id' => $_POST['id'] ?? null,
            'productName' => $_POST['productName'] ?? null,
            'categoryId' => $_POST['categoryId'] ?? null,
            'origin' => $_POST['origin'] ?? null,
            'brandId' => isset($_POST['brandId']) ? intval($_POST['brandId']) : null,
            'capacity' => $_POST['capacity'] ?? null,
            'quanity' => $_POST['quanity'] ?? null,
            'abv' => $_POST['abv'] ?? null,
            'description' => $_POST['description'] ?? null,
            'price' => $_POST['price'] ?? null,
            'sale' => $_POST['sale'] ?? null,
        ];

        // Kiểm tra nếu có file ảnh và thêm vào mảng `$parsedData`
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $parsedData['image'] = $_FILES['image'];
        } else {
            $parsedData['image'] = null; // Nếu không có ảnh, để null
        }

        // Khởi tạo đối tượng ProductController và gọi hàm createProduct
        $productController = new ProductController();
        echo $productController->updateProduct($_POST['id'], $parsedData);
    } else {
        // Tạo mảng chứa dữ liệu sau khi xử lý từ $_POST và $_FILES
        $parsedData = [
            'productName' => $_POST['productName'] ?? null,
            'categoryId' => $_POST['categoryId'] ?? null,
            'origin' => $_POST['origin'] ?? null,
            'brandId' => isset($_POST['brandId']) ? intval($_POST['brandId']) : null,
            'capacity' => $_POST['capacity'] ?? null,
            'quanity' => $_POST['quanity'] ?? null,
            'abv' => $_POST['abv'] ?? null,
            'description' => $_POST['description'] ?? null,
            'price' => $_POST['price'] ?? null,
            'sale' => $_POST['sale'] ?? null,
        ];

        // Kiểm tra nếu có file ảnh và thêm vào mảng `$parsedData`
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $parsedData['image'] = $_FILES['image'];
        } else {
            $parsedData['image'] = null; // Nếu không có ảnh, để null
        }

        // Khởi tạo đối tượng ProductController và gọi hàm createProduct
        $productController = new ProductController();
        echo $productController->createProduct($parsedData);
    }
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
        $targetDirectory = '../Views/img/';

        // Kiểm tra xem thư mục đích có tồn tại và có quyền ghi không
        if (!is_dir($targetDirectory) || !is_writable($targetDirectory)) {
            echo json_encode(["error" => "Target directory does not exist or is not writable."]);
            return;
        }

        // Kiểm tra xem có file ảnh trong parsedData không
        if (isset($parsedData['image']) && is_array($parsedData['image'])) {
            if (isset($parsedData['image']['error']) && $parsedData['image']['error'] === UPLOAD_ERR_OK) {
                $image = $parsedData['image']['name'];
                $targetPath = $targetDirectory . basename($image); // Sử dụng basename để loại bỏ đường dẫn không mong muốn

                // Kiểm tra đường dẫn đầy đủ
                echo json_encode(["targetPath" => realpath($targetDirectory) . '/' . $image]);

                // Di chuyển file đến thư mục mong muốn
                if (move_uploaded_file($parsedData['image']['tmp_name'], $targetPath)) {
                    echo json_encode(["success" => "File uploaded successfully to $targetPath"]);
                } else {
                    echo json_encode(["error" => "Failed to move uploaded file.", "details" => error_get_last()]);
                }
            } else {
                echo json_encode(["error" => "Error uploading file: " . ($parsedData['image']['error'] ?? 'No file uploaded or an error occurred.')]);
            }
        } else {
            echo json_encode(["error" => "No image data found."]);
        }

        // Gọi hàm createProduct của model (cần thiết lập thông tin sản phẩm ở đây)
        $result = $this->productModel->createProduct(
            isset($parsedData['productName']) && !empty($parsedData['productName']) ? $parsedData['productName'] : null,
            isset($parsedData['price']) && !empty($parsedData['price']) ? $parsedData['price'] : null,
            $image, // Đường dẫn ảnh (có thể là null hoặc chuỗi rỗng)
            isset($parsedData['origin']) && !empty($parsedData['origin']) ? $parsedData['origin'] : null,
            isset($parsedData['capacity']) && !empty($parsedData['capacity']) ? $parsedData['capacity'] : null,
            isset($parsedData['abv']) && !empty($parsedData['abv']) ? $parsedData['abv'] : null,
            isset($parsedData['quanity']) && !empty($parsedData['quanity']) ? $parsedData['quanity'] : null,
            isset($parsedData['description']) && !empty($parsedData['description']) ? $parsedData['description'] : null,
            isset($parsedData['brandId']) ? intval($parsedData['brandId']) : null,
            isset($parsedData['categoryId']) ? intval($parsedData['categoryId']) : null,
            isset($parsedData['sale']) ? intval($parsedData['sale']) : null
        );

        if ($result->status == 201) {
            http_response_code(201); // Đã tạo thành công
        } else {
            http_response_code(500); // Lỗi máy chủ nội bộ
        }

        echo json_encode($result);
    }



    public function updateProduct($id, $parsedData)
    {
        // Khởi tạo biến cho hình ảnh
        $image = null;
        $targetDirectory = '../Views/img/';

        // Kiểm tra xem thư mục đích có tồn tại và có quyền ghi không
        if (!is_dir($targetDirectory) || !is_writable($targetDirectory)) {
            echo json_encode(["error" => "Target directory does not exist or is not writable."]);
            return;
        }

        // Kiểm tra xem có file ảnh được upload hay không
        if (isset($parsedData['image']) && is_array($parsedData['image'])) {
            if (isset($parsedData['image']['error']) && $parsedData['image']['error'] === UPLOAD_ERR_OK) {
                // Xử lý file upload (image)
                $image = $parsedData['image']['name'];
                $targetPath = $targetDirectory . basename($image); // Sử dụng basename để loại bỏ đường dẫn không mong muốn

                // Di chuyển file đến thư mục mong muốn
                if (!move_uploaded_file($parsedData['image']['tmp_name'], $targetPath)) {
                    echo json_encode(["error" => "Failed to move uploaded file.", "details" => error_get_last()]);
                    return;
                }
            } else {
                echo json_encode(["error" => "Error uploading file: " . ($parsedData['image']['error'] ?? 'No file uploaded or an error occurred.')]);
                return;
            }
        }

        echo "Trạng thái controller: " . $parsedData['status'];
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
            isset($parsedData['status']) ? intval($parsedData['status']) : null, // Chuyển đổi status sang kiểu số nếu cần
            isset($parsedData['sale']) ? intval($parsedData['sale']) : null // Chuyển đổi sale sang kiểu số nếu cần
        );

        // Kiểm tra kết quả và trả về phản hồi thành công
        if ($result->status == 200) {
            http_response_code(200); // Cập nhật thành công
            echo json_encode(["success" => "Product updated successfully", "data" => $result]);
        } else {
            http_response_code(500); // Lỗi máy chủ nội bộ
            echo json_encode(["error" => "Failed to update product", "details" => $result]);
        }
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
