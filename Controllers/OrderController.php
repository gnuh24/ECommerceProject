<?php
require_once __DIR__ . "/../Models/OrderModel.php";
require '../vendor/autoload.php';

$controller = new OrderController();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            $response = $controller->getOrderById($_GET['id']);
            echo $response;
        } elseif (isset($_GET['accountId'])) {
            $response = $controller->getOrdersByAccountId($_GET['accountId']);
            echo $response;
        } else {
            $pageNumber = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $size = isset($_GET['size']) ? (int)$_GET['size'] : 10;
            $minNgayTao = isset($_GET['from']) ? urldecode($_GET['from']) : null;
            $maxNgayTao = isset($_GET['to']) ? urldecode($_GET['to']) : null;

            $status = $_GET['status'] ?? null;

            $response = $controller->getAllOrders($pageNumber, $size, $minNgayTao, $maxNgayTao, $status);
            echo $response;
        }
        break;

    case 'POST':
        $response = $controller->createOrder();
        echo $response;
        break;

    case 'PUT':
        if (isset($_GET['orderId'])) {
            $response = $controller->updateOrder($_GET['orderId']);
            echo $response;
        } else {
            http_response_code(400);
            echo json_encode(["status" => 400, "message" => "Order ID is required for update."]);
        }
        break;

    case 'DELETE':
        if (isset($_GET['orderId'])) {
            $response = $controller->deleteOrder($_GET['orderId']);
            echo $response;
        } else {
            http_response_code(400);
            echo json_encode(["status" => 400, "message" => "Order ID is required for deletion."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => 405, "message" => "Method not allowed."]);
        break;
}
class OrderController
{
    private $orderModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
    }
    public function getAllOrders($pageNumber = 1, $size = 10, $minNgayTao = null, $maxNgayTao = null, $status = null)
    {
        $response = $this->orderModel->getAllOrder($pageNumber, $size, $minNgayTao, $maxNgayTao, $status);
        $this->response($response);
    }
    public function getOrderById($orderId)
    {
        $response = $this->orderModel->getOrderById($orderId);
        $this->response($response);
    }

    public function createOrder($data)
    {
        if (!isset($data['totalPrice'], $data['note'], $data['accountId'])) {
            return $this->response((object)[
                "status" => 400,
                "message" => "Invalid input data"
            ]);
        }

        $response = $this->orderModel->createOrder((object)$data);
        $this->response($response);
    }

    // Cập nhật thông tin đơn hàng
    // public function updateOrder($orderId, $data)
    // {
    //     if (!isset($data['totalPrice'], $data['note'])) {
    //         return $this->response((object)[
    //             "status" => 400,
    //             "message" => "Invalid input data"
    //         ]);
    //     }

    //     $response = $this->orderModel->updateOrder($orderId, $data['totalPrice'], $data['note']);
    //     $this->response($response);
    // }

    // Xóa đơn hàng theo Id
    public function deleteOrder($orderId)
    {
        $response = $this->orderModel->deleteOrder($orderId);
        $this->response($response);
    }

    // Lấy tất cả đơn hàng của một tài khoản dựa trên AccountId
    public function getOrdersByAccountId($accountId)
    {
        $response = $this->orderModel->getOrdersByAccountId($accountId);
        $this->response($response);
    }

    // Phương thức xử lý phản hồi
    private function response($result)
    {
        http_response_code($result->status);
        $response = [
            "message" => $result->message,
            "data" => $result->data ?? null
        ];

        if (isset($result->totalPages)) {
            $response['totalPages'] = $result->totalPages;
        }

        echo json_encode($response);
    }
}
