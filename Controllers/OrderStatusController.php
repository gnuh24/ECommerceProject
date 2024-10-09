<?php
require_once __DIR__ . "/../Controllers/OrderStatusController.php";

$controller = new OrderStatusController();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['orderId'])) {
            // Lấy trạng thái mới nhất của đơn hàng theo OrderId
            $controller->getNewestOrderStatus($_GET['orderId']);
        } else {
            http_response_code(400);
            echo json_encode(["status" => 400, "message" => "Order ID is required for fetching status."]);
        }
        break;

    case 'POST':
        // Nhận dữ liệu từ POST request
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['orderId']) && isset($data['status']) && isset($data['updateTime'])) {
            // Tạo trạng thái đơn hàng lần đầu
            $controller->createOrderStatusFirstTime($data['orderId'], $data['status'], $data['updateTime']);
        } else {
            http_response_code(400);
            echo json_encode(["status" => 400, "message" => "Order ID, status, and update time are required."]);
        }
        break;

    case 'PUT':
        // Nhận dữ liệu từ PUT request
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($_GET['orderId']) && isset($data['status']) && isset($data['updateTime'])) {
            // Cập nhật trạng thái đơn hàng
            $controller->updateOrderStatus($_GET['orderId'], $data['status'], $data['updateTime']);
        } else {
            http_response_code(400);
            echo json_encode(["status" => 400, "message" => "Order ID, status, and update time are required for update."]);
        }
        break;

    case 'DELETE':
        if (isset($_GET['orderId']) && isset($_GET['status'])) {
            // Xóa trạng thái đơn hàng
            $controller->deleteOrderStatus($_GET['orderId'], $_GET['status']);
        } else {
            http_response_code(400);
            echo json_encode(["status" => 400, "message" => "Order ID and status are required for deletion."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => 405, "message" => "Method not allowed."]);
        break;
}

class OrderStatusController
{
    private $orderStatusModel;

    public function __construct()
    {
        $this->orderStatusModel = new OrderStatusModel();
    }

    // Lấy trạng thái mới nhất của đơn hàng
    public function getNewestOrderStatus($orderId)
    {
        $response = $this->orderStatusModel->getNewestOrderStatus($orderId);
        $this->response($response);
    }

    // Tạo trạng thái đơn hàng lần đầu
    public function createOrderStatusFirstTime($orderId, $status, $updateTime)
    {
        $response = $this->orderStatusModel->createOrderStatusFirstTime($orderId, $status, $updateTime);
        $this->response($response);
    }

    // Cập nhật trạng thái đơn hàng
    public function updateOrderStatus($orderId, $status, $updateTime)
    {
        $response = $this->orderStatusModel->updateOrderStatus($orderId, $status, $updateTime);
        $this->response($response);
    }

    // Xóa trạng thái đơn hàng
    public function deleteOrderStatus($orderId, $status)
    {
        $response = $this->orderStatusModel->deleteOrderStatus($orderId, $status);
        $this->response($response);
    }

    // Hàm trả về phản hồi cho các API
    private function response($result)
    {
        http_response_code($result->status);

        // Chuẩn bị phản hồi JSON
        $response = [
            "message" => $result->message,
            "data" => $result->data ?? null
        ];

        // Nếu có số trang (trong trường hợp lấy danh sách)
        if (isset($result->totalPages)) {
            $response['totalPages'] = $result->totalPages;
        }

        echo json_encode($response);
    }
}
