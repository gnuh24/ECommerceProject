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
        $this->response($response->status, $response->message, $response->data ?? null);
    }

    // Lấy đơn hàng theo Id
    public function getOrderById($orderId)
    {
        $response = $this->orderModel->getOrderById($orderId);
        $this->response($response->status, $response->message, $response->data ?? null);
    }

    // Tạo đơn hàng mới
    public function createOrder()
    {
        // Giả sử bạn đã lấy dữ liệu từ request (POST)
        $data = json_decode(file_get_contents("php://input"), true);

        // Kiểm tra dữ liệu hợp lệ
        if (!isset($data['orderId'], $data['orderTime'], $data['totalPrice'], $data['note'], $data['accountId'])) {
            return $this->response(400, "Invalid input data");
        }

        $response = $this->orderModel->createOrder(
            $data['orderId'],
            $data['orderTime'],
            $data['totalPrice'],
            $data['note'],
            $data['accountId']
        );

        $this->response($response->status, $response->message);
    }

    // Cập nhật thông tin đơn hàng
    public function updateOrder($orderId)
    {
        // Giả sử bạn đã lấy dữ liệu từ request (PUT)
        $data = json_decode(file_get_contents("php://input"), true);

        $response = $this->orderModel->updateOrder($orderId, $data['totalPrice'], $data['note']);
        $this->response($response->status, $response->message);
    }

    // Xóa đơn hàng theo Id
    public function deleteOrder($orderId)
    {
        $response = $this->orderModel->deleteOrder($orderId);
        $this->response($response->status, $response->message);
    }

    // Lấy tất cả đơn hàng của một tài khoản dựa trên AccountId
    public function getOrdersByAccountId($accountId)
    {
        $response = $this->orderModel->getOrdersByAccountId($accountId);
        $this->response($response->status, $response->message, $response->data ?? null);
    }

    // Hàm response
    private function response($status, $message, $data = null)
    {
        // Thiết lập mã trạng thái HTTP
        http_response_code($status);

        // Thiết lập tiêu đề nội dung là JSON
        header('Content-Type: application/json');

        // Tạo mảng phản hồi
        $response = [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];

        // Gửi phản hồi JSON
        echo json_encode($response);

        // Dừng thực thi script
        exit;
    }
}
