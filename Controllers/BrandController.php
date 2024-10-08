<?php

require_once __DIR__ . "/../Models/BrandModel.php";
require '../vendor/autoload.php';

class BrandController
{
    private $BrandModel;

    public function __construct()
    {
        $this->BrandModel = new BrandModel();
    }

    // Lấy tất cả các Brand không phân trang
    public function getAllBrandNoPaging()
    {
        $result = $this->BrandModel->getAllBrandNoPaging();
        return $this->respond($result);
    }

    // Lấy tất cả Brand có phân trang và tìm kiếm
    public function getAllBrand($pageable, $search = null)
    {
        $result = $this->BrandModel->getAllBrand($pageable, $search);
        return $this->respond($result);
    }

    // Lấy Brand theo ID
    public function getBrandById($id)
    {
        $result = $this->BrandModel->getBrandById($id);
        return $this->respond($result);
    }

    // Tạo Brand mới
    public function createBrand($form)
    {
        $result = $this->BrandModel->createBrand($form);
        return $this->respond($result);
    }

    // Cập nhật Brand
    public function updateBrand($form)
    {
        $result = $this->BrandModel->updateBrand($form);
        return $this->respond($result);
    }

    // Xóa Brand theo ID
    public function deleteBrand($brandId)
    {
        $result = $this->BrandModel->deleteBrand($brandId);
        return $this->respond($result);
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
