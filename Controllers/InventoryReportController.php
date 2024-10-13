<?php
require_once __DIR__ . "/../Models/InventoryReportModel.php";
require '../vendor/autoload.php';
require_once __DIR__ . "/InventoryReportDetailController.php"; // Sử dụng dấu "/" thay vì "\"

$controller = new InventoryReportController();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['Id'])) {
            // Lấy báo cáo tồn kho theo ID
            $response = $controller->getInventoryReportById($_GET['Id']);
            echo $response;
        } else {
            // Lấy tất cả báo cáo tồn kho với phân trang và bộ lọc
            $offset = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $dateFrom = isset($_GET['dateFrom']) ? urldecode($_GET['dateFrom']) : 0;
            $dateTo = isset($_GET['dateTo']) ? urldecode($_GET['dateTo']) : 0;
            $search = isset($_GET['search']) ? urldecode($_GET['search']) : null;

            $response = $controller->getAllInventoryReports($offset, null, $dateFrom, $dateTo, $search);
            echo $response;
        }
        break;

    case 'POST':
        // Đọc dữ liệu JSON từ body của request
        $formData = json_decode(file_get_contents('php://input'), true);

        // Kiểm tra xem dữ liệu có tồn tại không
        if (isset($formData['supplier'], $formData['supplierPhone'], $formData['totalPrice'], $formData['inventoryReportDetailCreateFormList'])) {
            // Lấy dữ liệu từ formData
            $supplier = $formData['supplier'];
            $supplierPhone = $formData['supplierPhone'];
            $totalPrice = $formData['totalPrice'];
            $inventoryDetails = $formData['inventoryReportDetailCreateFormList'];

            // Gọi phương thức tạo báo cáo với dữ liệu chi tiết
            $response = $controller->createInventoryReport($totalPrice, $supplier, $supplierPhone, $inventoryDetails);

            // Trả về phản hồi
            echo json_encode($response);
        } else {
            // Xử lý lỗi nếu không đủ dữ liệu
            echo json_encode(['status' => 400, 'message' => 'Thiếu dữ liệu cần thiết.']);
        }
        break;

    case 'PUT':
        if (isset($_GET['id'])) {
            // Cập nhật báo cáo tồn kho theo ID
            $formData = json_decode(file_get_contents('php://input'), true);
            $formData['id'] = $_GET['id']; // Thêm ID vào form
            $response = $controller->updateInventoryReportById($formData);
            echo $response;
        } else {
            http_response_code(400);
            echo json_encode(["status" => 400, "message" => "Inventory report ID is required for update."]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(["status" => 405, "message" => "Method not allowed."]);
        break;
}

class InventoryReportController
{
    private $inventoryReportModel;
    private $inventoryReportDetailController;

    public function __construct()
    {
        $this->inventoryReportModel = new InventoryReportModel();
        $this->inventoryReportDetailController = new InventoryReportDetailController();
    }

    // Lấy tất cả báo cáo tồn kho với phân trang và bộ lọc
    public function getAllInventoryReports($offset, $limit = 10, $dateFrom = null, $dateTo = null, $search = null)
    {
        $result = $this->inventoryReportModel->getAllInventoryReports($offset, $limit, $dateFrom, $dateTo, $search);
        return $this->response($result);
    }

    // Lấy báo cáo tồn kho theo ID
    public function getInventoryReportById($id)
    {
        $result = $this->inventoryReportModel->getInventoryReportById($id);
        return $this->response($result);
    }

    // Tạo báo cáo tồn kho mới
    public function createInventoryReport($totalPrice, $supplier, $supplierPhone, $inventoryDetails)
    {
        // Tạo báo cáo tồn kho
        $result = $this->inventoryReportModel->createInventoryReport($totalPrice, $supplier, $supplierPhone);

        // Nếu báo cáo tồn kho được tạo thành công
        if ($result->status === 201 && isset($result->data)) {
            // Tạo chi tiết báo cáo tồn kho
            $response = $this->inventoryReportDetailController->createInventoryReportDetail($result->data, $inventoryDetails);
            return $this->response($response);
        } else {
            // Xử lý lỗi nếu tạo báo cáo không thành công
            return $this->response($result);
        }
    }

    // Cập nhật báo cáo tồn kho theo ID
    public function updateInventoryReportById($form)
    {
        $result = $this->inventoryReportModel->updateInventoryReportById($form);
        return $this->response($result);
    }

    private function response($result)
    {
        http_response_code($result->status);
        return json_encode([
            "message" => $result->message,
            "data" => $result->data ?? null,
            "totalPages" => $result->totalPages ?? null
        ]);
    }
}
