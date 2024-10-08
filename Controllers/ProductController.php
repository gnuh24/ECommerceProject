<?php

require_once __DIR__ . "/../Models/ProductModel.php";
require '../vendor/autoload.php';
// Xử lý yêu cầu RESTful cho ProductController
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action']) && $_GET['action'] === 'getAllProductsCommonUser') {
        $productController = new ProductController();
        $productController->getAllProductsCommonUser();
    } elseif (isset($_GET['action']) && $_GET['action'] === 'getAllProductsAdmin') {
        $productController = new ProductController();
        $productController->getAllProductsAdmin();
    } elseif (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $productController = new ProductController();
        $productController->getProductByIdCommonUser($id); // Hoặc getProductByIdAdmin() tùy theo vai trò
    } else {
        echo json_encode(["status" => 400, "message" => "Invalid action"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_GET['action']) && $_GET['action'] === 'createProduct') {
        $productController = new ProductController();
        $productController->createProduct();
    } else {
        echo json_encode(["status" => 400, "message" => "Invalid action"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $productController = new ProductController();
        $productController->updateProduct($id);
    } else {
        echo json_encode(["status" => 400, "message" => "Product ID is required"]);
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
        $brandId = $_GET['brandId'] ?? null;
        $categoryId = $_GET['categoryId'] ?? null;
        $search = $_GET['search'] ?? null;
        $minPrice = $_GET['minPrice'] ?? null;
        $maxPrice = $_GET['maxPrice'] ?? null;
        $limit = $_GET['limit'] ?? 20;
        $offset = $_GET['offset'] ?? 0;

        $result = $this->productModel->getAllProductsCommonUser($brandId, $categoryId, $search, $minPrice, $maxPrice, $limit, $offset);
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
        $limit = $_GET['limit'] ?? 20;
        $offset = $_GET['offset'] ?? 0;

        $result = $this->productModel->getAllProductsAdmin($brandId, $categoryId, $search, $minPrice, $maxPrice, $limit, $offset);
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
    public function createProduct()
    {
        // Kiểm tra nếu trường productName được gửi qua form
        if (isset($_POST['productName'])) {
            $productName = $_POST['productName'];

            // Gọi hàm tạo sản phẩm trong model chỉ với productName
            $result = $this->productModel->createProduct($productName);

            http_response_code(201); // Created
            echo json_encode($result);
        } else {
            http_response_code(400);
            echo json_encode(["status" => 400, "message" => "Invalid input data"]);
        }
    }


    // Cập nhật thông tin sản phẩm - gửi dữ liệu bằng formdata
    public function updateProduct($id)
    {
        $data = $_POST; // Đối với FormData, bạn vẫn có thể dùng $_POST để lấy dữ liệu text và $_FILES cho file

        // Kiểm tra nếu các trường bắt buộc được gửi qua form
        if (
            isset($_POST['productName'], $_POST['status'], $_POST['origin'], $_POST['capacity'], $_POST['abv'], $_POST['description'], $_POST['brandId'], $_POST['categoryId'])
        ) {
            $image = $_FILES['image']; // Lấy file hình ảnh
            // Kiểm tra xem có ảnh được upload hay không
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                // Xử lý file upload (image)
                $image = $_FILES['image']['name'];
                // Di chuyển file đến thư mục mong muốn
                move_uploaded_file($_FILES['image']['tmp_name'], 'path/to/save/' . $image);
            } else {
                // Nếu không có ảnh được upload, có thể giữ nguyên ảnh cũ
                $image = null; // Hoặc lấy giá trị mặc định nếu muốn
            }

            $result = $this->productModel->updateProduct(
                $id,
                $data['productName'],
                $data['status'],
                $image,  // Dùng file ảnh upload
                $data['origin'],
                $data['capacity'],
                $data['abv'],
                $data['description'],
                $data['brandId'],
                $data['categoryId']
            );


            http_response_code(200);
            echo json_encode($result);
        } else {
            http_response_code(400);
            echo json_encode(["status" => 400, "message" => "Invalid input data"]);
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

        echo json_encode($response);
    }
}
