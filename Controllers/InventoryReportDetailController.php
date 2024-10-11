<?php
require_once __DIR__ . "/../Models/InventoryReportDetailModel.php";
require '../vendor/autoload.php';

class InventoryReportDetailController
{
    private $inventoryReportDetailModel;

    public function __construct()
    {
        $this->inventoryReportDetailModel = new InventoryReportDetailModel(); // Chỉnh sửa tên lớp thành InventoryReportDetailModel
    }
    // Lấy chi tiết báo cáo tồn kho theo ID
    public function getInventoryReportDetailById($id)
    {
        $response = $this->inventoryReportDetailModel->getInventoryReportDetailById($id); // Gọi đến phương thức trong model
        echo json_encode($response);
    }

    // Tạo chi tiết báo cáo tồn kho
    public function createInventoryReportDetail($inventoryReportId, $inventoryDetails)
    {
        if (!empty($inventoryDetails)) {
            // Gọi phương thức để thêm chi tiết vào model
            $response = $this->inventoryReportDetailModel->addInventoryReportDetails($inventoryReportId, $inventoryDetails);
            return $response;
        } else {
            return (object) ["status" => 400, "message" => "No inventory details provided."];
        }
    }
}
