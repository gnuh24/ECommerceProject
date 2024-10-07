<?php
require_once __DIR__ . "/../Models/CategoryModel.php";
require '../vendor/autoload.php';

class CategoryController
{
    private $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    // Lấy tất cả danh mục không phân trang
    public function getAllCategoriesNoPaging()
    {
        $result = $this->categoryModel->getAllCategoryNoPaging();
        return $this->respond($result);
    }

    // Lấy tất cả danh mục có phân trang và tìm kiếm
    public function getAllCategories($pageable, $search)
    {
        $result = $this->categoryModel->getAllCategory($pageable, $search);
        return $this->respond($result);
    }

    // Lấy danh mục theo ID
    public function getCategoryById($id)
    {
        $result = $this->categoryModel->getCategoryById($id);
        return $this->respond($result);
    }

    // Tạo mới một danh mục
    public function createCategory($categoryCreateForm)
    {
        $result = $this->categoryModel->createCategory($categoryCreateForm['CategoryName']);
        return $this->respond($result);
    }

    // Cập nhật danh mục
    public function updateCategory($categoryUpdateForm)
    {
        $id = $categoryUpdateForm['Id'];
        $categoryName = $categoryUpdateForm['CategoryName'];
        $result = $this->categoryModel->updateCategory($id, $categoryName);
        return $this->respond($result);
    }

    // Xóa danh mục theo ID
    public function deleteCategory($categoryId)
    {
        $result = $this->categoryModel->deleteCategory($categoryId);
        return $this->respond($result);
    }

    // Phương thức xử lý phản hồi chung
    private function respond($result)
    {
        http_response_code($result->status);
        echo json_encode([
            "message" => $result->message,
            "data" => $result->data ?? null
        ]);
    }
}
