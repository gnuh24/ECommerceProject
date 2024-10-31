<?php
require_once __DIR__ . "/../Models/OrderStatusModel.php";

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
        // Validate orderId as a non-empty string and status as non-empty
        if (isset($_POST['orderId'])  && isset($_POST['status'])) {
            $orderId = $_POST['orderId'];
            $status = $_POST['status'];
            $controller->createOrderStatus($orderId, $status);
        } else {
            http_response_code(400);
            echo json_encode([
                "status" => 400,
                "message" => "Order ID (non-empty, alphanumeric) and status (non-empty) are required."
            ]);
        }
        break;


        // Nhận dữ liệu từ PATCH request
        $data = json_decode(file_get_contents('php://input'), true);

        // Kiểm tra xem đã truyền đầy đủ các tham số orderId, status và updateTime hay chưa
        if (isset($_GET['orderId']) && isset($data['status']) && isset($data['updateTime'])) {
            // Truyền tất cả tham số vào controller để cập nhật trạng thái đơn hàng
            $controller->createOrderStatus(
                $_GET['orderId'], // Lấy orderId từ URL
                $data['status'],   // Lấy status từ request body
                $data['updateTime'] // Lấy updateTime từ request body
            );
        } else {
            http_response_code(400);
            echo json_encode(["status" => 400, "message" => "Order ID, status, and update time are required."]);
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

    // Tạo trạng thái mới cho đơn hàng (không phải lần đầu)
    public function createOrderStatus($orderId, $status)
    {
        $response = $this->orderStatusModel->createOrderStatus($orderId, $status);
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

        echo json_encode($response);
    }
}
