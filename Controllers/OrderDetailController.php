<?php
require_once __DIR__ . "/../../Models/OrderDetailModel.php";

class OrderDetailController
{
    private $orderDetailModel;

    public function __construct()
    {
        $this->orderDetailModel = new OrderDetailModel();
    }

    // Hàm phản hồi chuẩn hóa
    private function response($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    // Lấy chi tiết đơn hàng theo OrderId
    public function getAllOrderDetailByOrderId($orderId)
    {
        $response = $this->orderDetailModel->getDetailsByOrderId($orderId);
        $this->response($response);
    }

    // Tạo chi tiết đơn hàng mới
    public function createOrderDetail()
    {
        // Lấy dữ liệu từ yêu cầu
        $data = json_decode(file_get_contents("php://input"), true);

        // Kiểm tra xem dữ liệu đã đủ chưa
        if (isset($data['OrderId'], $data['ProductId'], $data['Quantity'], $data['UnitPrice'], $data['Total'])) {
            $orderId = $data['OrderId'];
            $productId = $data['ProductId'];
            $quantity = $data['Quantity'];
            $unitPrice = $data['UnitPrice'];
            $total = $data['Total'];

            // Gọi phương thức tạo chi tiết đơn hàng
            $response = $this->orderDetailModel->createOrderDetail($orderId, $productId, $quantity, $unitPrice, $total);
            $this->response($response);
        } else {
            // Trả về lỗi nếu dữ liệu không đầy đủ
            $this->response((object) [
                "status" => 400,
                "message" => "Missing required fields"
            ]);
        }
    }

    // Cập nhật chi tiết đơn hàng
    public function updateOrderDetail()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($data['OrderId'], $data['ProductId'], $data['Quantity'], $data['UnitPrice'], $data['Total'])) {
            $orderId = $data['OrderId'];
            $productId = $data['ProductId'];
            $quantity = $data['Quantity'];
            $unitPrice = $data['UnitPrice'];
            $total = $data['Total'];

            $response = $this->orderDetailModel->updateOrderDetail($orderId, $productId, $quantity, $unitPrice, $total);
            $this->response($response);
        } else {
            $this->response((object) [
                "status" => 400,
                "message" => "Missing required fields"
            ]);
        }
    }

    // Xóa chi tiết đơn hàng
    public function deleteOrderDetail($orderId, $productId)
    {
        $response = $this->orderDetailModel->deleteOrderDetail($orderId, $productId);
        $this->response($response);
    }
}
