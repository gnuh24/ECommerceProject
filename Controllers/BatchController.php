<?php

require_once __DIR__ . "/../Models/BatchModel.php";

class BatchController
{
    private $batchModel;

    public function __construct()
    {
        $this->batchModel = new BatchModel();
    }

    // Tạo lô hàng mới
    public function createBatch()
    {
        $form = json_decode(file_get_contents('php://input'));

        // Kiểm tra các trường bắt buộc
        if (empty($form->unitPrice) || empty($form->quantity) || empty($form->receivingTime) || empty($form->productId)) {
            $this->respond((object) [
                "status" => 400,
                "message" => "All fields are required"
            ]);
            return;
        }

        $result = $this->batchModel->createBatch($form);
        $this->respond($result);
    }

    // Cập nhật lô hàng
    public function updateBatch()
    {
        $form = json_decode(file_get_contents('php://input'));

        // Kiểm tra các trường bắt buộc
        if (empty($form->id) || empty($form->unitPrice) || empty($form->quantity) || empty($form->receivingTime) || empty($form->productId)) {
            $this->respond((object) [
                "status" => 400,
                "message" => "All fields are required"
            ]);
            return;
        }

        $result = $this->batchModel->updateBatch($form);
        $this->respond($result);
    }

    // Lấy tất cả lô hàng theo ProductId
    public function getAllBatchByProductId($productId)
    {
        if (empty($productId)) {
            $this->respond((object) [
                "status" => 400,
                "message" => "Product ID is required"
            ]);
            return;
        }

        $result = $this->batchModel->getAllBatchByProductId($productId);
        $this->respond($result);
    }

    // Lấy lô hàng hợp lệ
    public function getTheValidBatch($productId)
    {
        if (empty($productId)) {
            $this->respond((object) [
                "status" => 400,
                "message" => "Product ID is required"
            ]);
            return;
        }

        $result = $this->batchModel->getTheValidBatch($productId);
        $this->respond($result);
    }

    // Lấy lô hàng hợp lệ (backup)
    public function getTheValidBatchBackup($productId)
    {
        if (empty($productId)) {
            $this->respond((object) [
                "status" => 400,
                "message" => "Product ID is required"
            ]);
            return;
        }

        $result = $this->batchModel->getTheValidBatchBackup($productId);
        $this->respond($result);
    }

    // Lấy lô hàng theo ID
    public function getBatchById($batchId)
    {
        if (empty($batchId)) {
            $this->respond((object) [
                "status" => 400,
                "message" => "Batch ID is required"
            ]);
            return;
        }

        $result = $this->batchModel->getBatchById($batchId);
        $this->respond($result);
    }

    // Phương thức chung để xử lý phản hồi
    private function respond($result)
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
