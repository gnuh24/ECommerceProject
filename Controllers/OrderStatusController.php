<?php
require_once __DIR__ . "/../Models/OrderStatusModel.php";
// require_once __DIR__ . "/../Models/ProductModel.php";
// $productController = new ProductController();
$controller = new OrderStatusController();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':

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
