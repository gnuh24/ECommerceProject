<?php

require_once __DIR__ . "/../Models/BrandModel.php";
require '../vendor/autoload.php';
$controller = new BrandController();
// Kiểm tra phương thức HTTP
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Lấy danh sách Brand có phân trang và tìm kiếm
        if (isset($_GET['page'])) {
            $response = $controller->getAllBrand($_GET['page'], $_GET['search']);
            echo $response;
        }
        // Lấy danh sách Brand không phân trang
        else {
            $response = $controller->getAllBrandNoPaging();
            echo $response;
        }
        break;

    case 'PATCH':
        // Đọc dữ liệu thô từ php://input
        $inputData = file_get_contents("php://input");

        // Giải mã dữ liệu JSON
        $data = json_decode($inputData, true); // Chuyển JSON thành mảng

        // Kiểm tra nếu có ID và tên thương hiệu trong request
        if (isset($data['Id']) && isset($data['BrandName'])) {
            $response = $controller->updateBrand($data['Id'], $data['BrandName']);
            echo $response;
        } else {
            // Phản hồi lỗi nếu không có ID hoặc BrandName
            http_response_code(400);
            echo json_encode([
                "status" => 400,
                "message" => "ID và tên thương hiệu là bắt buộc."
            ]);
        }
        break;

    case 'POST':
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

        // Gọi hàm xử lý tạo thương hiệu mới và trả về phản hồi
        $response = $controller->createBrand($decodedData['BrandName']);

        // Trả về dưới dạng JSON
        echo $response;
        break;

    case 'DELETE':
        // Kiểm tra nếu ID đã được gửi trong yêu cầu
        if (isset($_GET['id'])) {
            $id = $_GET['id'];

            // Gọi hàm xử lý xóa thương hiệu
            $response = $controller->deleteBrand($id);

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

class BrandController
{
    private $BrandModel;

    public function __construct()
    {
        $this->BrandModel = new BrandModel();
    }

    // Lấy tất cả các Brand không phân trang
    public function getAllBrandNoPaging()
    {
        $result = $this->BrandModel->getAllBrandNoPaging();
        return $this->respond($result);
    }

    // Lấy tất cả Brand có phân trang và tìm kiếm
    public function getAllBrand($pageable, $search = null)
    {
        $pageSize = isset($_GET['pageSize']) ? intval($_GET['pageSize']) : 5;
        $result = $this->BrandModel->getAllBrand($pageable, $search, $pageSize);
        return $this->respond($result);
    }

    // Lấy Brand theo ID
    public function getBrandById($id)
    {
        $result = $this->BrandModel->getBrandById($id);
        return $this->respond($result);
    }

    // Tạo Brand mới
    public function createBrand($name)
    {
        $result = $this->BrandModel->createBrand($name);
        return $this->respond($result);
    }

    // Cập nhật Brand
    public function updateBrand($id, $name)
    {
        $result = $this->BrandModel->updateBrand($id, $name);
        return $this->respond($result);
    }

    // Xóa Brand theo ID
    public function deleteBrand($brandId)
    {
        $result = $this->BrandModel->deleteBrand($brandId);
        return $this->respond($result);
    }

    // Phương thức chung để xử lý phản hồi
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

        echo json_encode($response);
    }
}
