<?php
require_once __DIR__ . "/../Models/OrderDetailModel.php";

class OrderDetailController
{
    private $orderDetailModel;

    public function __construct()
    {
        $this->orderDetailModel = new OrderDetailModel();
    }


    // Lấy chi tiết đơn hàng theo OrderId
    public function getAllOrderDetailByOrderId($orderId)
    {
        $response = $this->orderDetailModel->getAllOrderDetailByOrderId($orderId);
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
