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

    // Hàm response để chuẩn hóa phản hồi
    private function response($status, $message, $data = null)
    {
        header("Content-Type: application/json");
        http_response_code($status);
        $response = ["status" => $status, "message" => $message];
        if ($data) {
            $response["data"] = $data;
        }
        echo json_encode($response);
    }
}
