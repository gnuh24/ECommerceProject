<?php
require_once __DIR__ . "/../Models/CategoryModel.php";
require '../vendor/autoload.php';

// Khởi tạo controller
$controller = new CategoryController();

// Kiểm tra phương thức HTTP
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Lấy danh mục có phân trang và tìm kiếm
        if (isset($_GET['page'])) {
            $response = $controller->getAllCategories($_GET['page'], $_GET['search']);
            echo $response;
        }
        // Lấy danh mục không phân trang
        else {
            $response = $controller->getAllCategoriesNoPaging();
            echo $response;
        }
        break;

    case 'PATCH':
        // Đọc dữ liệu thô từ php://input
        $inputData = file_get_contents("php://input");

        // Giải mã dữ liệu JSON
        $data = json_decode($inputData, true); // Chuyển JSON thành mảng

        // Kiểm tra nếu có ID và tên danh mục trong request
        if (isset($data['Id']) && isset($data['CategoryName'])) {
            $response = $controller->updateCategory($data['Id'], $data['CategoryName']);
            echo $response;
        } else {
            // Phản hồi lỗi nếu ID hoặc CategoryName không được cung cấp
            http_response_code(400);
            echo json_encode([
                "status" => 400,
                "message" => "ID và tên danh mục là bắt buộc."
            ]);
        }
        break;

    case 'POST':
        // Lấy dữ liệu JSON từ yêu cầu POST
        $data = file_get_contents('php://input');
        $decodedData = json_decode($data, true);

        if (empty($decodedData)) {
            http_response_code(400); // Bad Request
            echo json_encode([
                "status" => 400,
                "message" => "Dữ liệu không hợp lệ"
            ]);
            exit;
        }

        // Gọi hàm xử lý tạo danh mục mới và trả về phản hồi
        $response = $controller->createCategory($decodedData['CategoryName']);

        // Trả về dưới dạng JSON
        echo $response;
        break;

    case 'DELETE':
        // Kiểm tra nếu ID đã được gửi trong yêu cầu
        if (isset($_GET['id'])) {
            $id = $_GET['id'];

            // Gọi hàm để xử lý việc xóa danh mục
            $response = $controller->deleteCategory($id);

            // Trả về phản hồi dưới dạng JSON
            echo $response;
        } else {
            // Trả về lỗi nếu không có ID trong yêu cầu
            http_response_code(400); // Bad Request
            echo json_encode([
                "status" => 400,
                "message" => "ID is required for deletion."
            ]);
        }
        break;

    default:
        // Phản hồi cho các phương thức không được hỗ trợ
        http_response_code(405); // Method Not Allowed
        echo json_encode([
            "status" => 405,
            "message" => "Method not allowed."
        ]);
        break;
}


class CategoryController
{
    private $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    // Lấy tất cả danh mục không phân trang
    public function getAllCategoriesNoPaging()
    {
        $result = $this->categoryModel->getAllCategoryNoPaging();
        return $this->respond($result);
    }

    // Lấy tất cả danh mục có phân trang và tìm kiếm
    public function getAllCategories($page, $search)
    {
        $pageSize = isset($_GET['pageSize']) ? intval($_GET['pageSize']) : 5;
        $result = $this->categoryModel->getAllCategory($page, $search, $pageSize);
        return $this->respond($result);
    }

    // Lấy danh mục theo ID
    public function getCategoryById($id)
    {
        $result = $this->categoryModel->getCategoryById($id);
        return $this->respond($result);
    }

    // Tạo mới một danh mục
    public function createCategory($categoryName)
    {
        $result = $this->categoryModel->createCategory($categoryName);
        return $this->respond($result);
    }

    // Cập nhật danh mục
    public function updateCategory($id, $categoryName)
    {
        $result = $this->categoryModel->updateCategory($id, $categoryName);
        return $this->respond($result);
    }

    // Xóa danh mục theo ID
    public function deleteCategory($categoryId)
    {
        $result = $this->categoryModel->deleteCategory($categoryId);
        return $this->respond($result);
    }

    // Phương thức xử lý phản hồi chung
    private function respond($result)
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
        // Kiểm tra và thêm totalPages nếu có trong kết quả
        if (isset($result->totalElements)) {
            $response['totalElements'] = $result->totalElements;
        }
        echo json_encode($response);
    }
}
