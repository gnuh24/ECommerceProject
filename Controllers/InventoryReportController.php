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

    // Lấy tất cả báo cáo tồn kho với phân trang và bộ lọc
    public function getAllInventoryReports($offset, $limit, $filterForm, $search = null)
    {
        $result = $this->inventoryReportModel->getAllInventoryReports($offset, $limit, $filterForm, $search);
        if ($result->status === 200) {
            $this->response(200, $result->message, $result->data);
        } else {
            $this->response(400, $result->message);
        }
    }

    // Lấy báo cáo tồn kho theo ID
    public function getInventoryReportById($id)
    {
        $result = $this->inventoryReportModel->getInventoryReportById($id);
        if ($result->status === 200) {
            $this->response(200, $result->message, $result->data);
        } else {
            $this->response(400, $result->message);
        }
    }

    // Tạo báo cáo tồn kho mới
    public function createInventoryReport($form)
    {
        $result = $this->inventoryReportModel->createInventoryReport($form);
        if ($result->status === 201) {
            $this->response(201, $result->message, $result->data);
        } else {
            $this->response(400, $result->message);
        }
    }

    // Cập nhật báo cáo tồn kho theo ID
    public function updateInventoryReportById($form)
    {
        $result = $this->inventoryReportModel->updateInventoryReportById($form);
        if ($result->status === 200) {
            $this->response(200, $result->message);
        } else {
            $this->response(400, $result->message);
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
