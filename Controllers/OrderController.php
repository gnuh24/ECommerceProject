<?php
require_once __DIR__ . "/../../Models/OrderModel.php";

class OrderController
{
    private $orderModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
    }

    // Lấy tất cả các đơn hàng
    public function getAllOrders()
    {
        $response = $this->orderModel->getAllOrders();
        $this->response($response);
    }

    // Lấy đơn hàng theo Id
    public function getOrderById($orderId)
    {
        $response = $this->orderModel->getOrderById($orderId);
        $this->response($response);
    }

    // Tạo đơn hàng mới
    public function createOrder()
    {
        // Giả sử bạn đã lấy dữ liệu từ request (POST)
        $data = json_decode(file_get_contents("php://input"), true);

        // Kiểm tra dữ liệu hợp lệ
        if (!isset($data['orderId'], $data['orderTime'], $data['totalPrice'], $data['note'], $data['accountId'])) {
            return $this->response((object)[
                "status" => 400,
                "message" => "Invalid input data"
            ]);
        }

        $response = $this->orderModel->createOrder(
            $data['orderId'],
            $data['orderTime'],
            $data['totalPrice'],
            $data['note'],
            $data['accountId']
        );

        $this->response($response);
    }

    // Cập nhật thông tin đơn hàng
    public function updateOrder($orderId)
    {
        // Giả sử bạn đã lấy dữ liệu từ request (PUT)
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['totalPrice'], $data['note'])) {
            return $this->response((object)[
                "status" => 400,
                "message" => "Invalid input data"
            ]);
        }

        $response = $this->orderModel->updateOrder($orderId, $data['totalPrice'], $data['note']);
        $this->response($response);
    }

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

        // Kiểm tra và thêm totalPages nếu có trong kết quả
        if (isset($result->totalPages)) {
            $response['totalPages'] = $result->totalPages;
        }

        echo json_encode($response);
    }
}
