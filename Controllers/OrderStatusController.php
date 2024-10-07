<?php
require_once __DIR__ . "/../../Models/OrderStatusModel.php";

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
        $response = $this->orderStatusModel->getStatusByOrderId($orderId);
        $this->response($response);
    }

    // Tạo trạng thái đơn hàng lần đầu
    public function createOrderStatusFirstTime()
    {
        // Giả sử dữ liệu được nhận từ POST request
        $form = json_decode(file_get_contents('php://input'));

        // Kiểm tra dữ liệu
        if (empty($form->orderId) || empty($form->status) || empty($form->updateTime)) {
            $this->response((object)[
                "status" => 400,
                "message" => "Invalid input data"
            ]);
            return;
        }

        $response = $this->orderStatusModel->createOrderStatus($form->orderId, $form->status, $form->updateTime);
        $this->response($response);
    }

    // Cập nhật trạng thái đơn hàng
    public function updateOrderStatus()
    {
        $form = json_decode(file_get_contents('php://input'));

        // Kiểm tra dữ liệu
        if (empty($form->orderId) || empty($form->status) || empty($form->updateTime)) {
            $this->response((object)[
                "status" => 400,
                "message" => "Invalid input data"
            ]);
            return;
        }

        $response = $this->orderStatusModel->updateOrderStatus($form->orderId, $form->status, $form->updateTime);
        $this->response($response);
    }

    // Xóa trạng thái đơn hàng
    public function deleteOrderStatus()
    {
        $form = json_decode(file_get_contents('php://input'));

        // Kiểm tra dữ liệu
        if (empty($form->orderId) || empty($form->status)) {
            $this->response((object)[
                "status" => 400,
                "message" => "Invalid input data"
            ]);
            return;
        }

        $response = $this->orderStatusModel->deleteOrderStatus($form->orderId, $form->status);
        $this->response($response);
    }

    // Hàm trả về phản hồi
    private function response($response)
    {
        header('Content-Type: application/json');
        http_response_code($response->status);
        echo json_encode($response);
    }
}
