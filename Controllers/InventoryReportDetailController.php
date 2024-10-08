<?php
require_once __DIR__ . "/../Models/InventoryReportModel.php";
require '../vendor/autoload.php';

class InventoryReportController
{
    private $inventoryReportModel;

    public function __construct()
    {
        $this->inventoryReportModel = new InventoryReportModel();
    }

    // Lấy tất cả báo cáo tồn kho
    public function getAllReports()
    {
        $response = $this->inventoryReportModel->getAllReports();
        echo json_encode($response);
    }

    // Lấy báo cáo tồn kho theo ID
    public function getReportById($id)
    {
        $response = $this->inventoryReportModel->getReportById($id);
        echo json_encode($response);
    }

    // Tạo báo cáo tồn kho mới
    public function createReport()
    {
        // Giả định dữ liệu được gửi qua POST
        $data = json_decode(file_get_contents("php://input"), true);
        $supplier = $data['supplier'] ?? null;
        $supplierPhone = $data['supplierPhone'] ?? null;
        $totalPrice = $data['totalPrice'] ?? null;

        if ($supplier && $supplierPhone && $totalPrice) {
            $response = $this->inventoryReportModel->createReport($supplier, $supplierPhone, $totalPrice);
            echo json_encode($response);
        } else {
            echo json_encode([
                "status" => 400,
                "message" => "Invalid input data"
            ]);
        }
    }

    // Cập nhật báo cáo tồn kho
    public function updateReport($id)
    {
        // Giả định dữ liệu được gửi qua POST
        $data = json_decode(file_get_contents("php://input"), true);
        $supplier = $data['supplier'] ?? null;
        $supplierPhone = $data['supplierPhone'] ?? null;
        $totalPrice = $data['totalPrice'] ?? null;

        if ($supplier && $supplierPhone && $totalPrice) {
            $response = $this->inventoryReportModel->updateReport($id, $supplier, $supplierPhone, $totalPrice);
            echo json_encode($response);
        } else {
            echo json_encode([
                "status" => 400,
                "message" => "Invalid input data"
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
