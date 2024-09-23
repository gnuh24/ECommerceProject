<?php
require_once __DIR__ . "/../../Models/ProductModel.php";

class ProductController
{
    private $productModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
    }

    // Lấy tất cả sản phẩm
    public function getAllProducts()
    {
        $response = $this->productModel->getAllProducts();
        $this->sendResponse($response);
    }

    // Lấy sản phẩm theo ID
    public function getProductById($id)
    {
        $response = $this->productModel->getProductById($id);
        $this->sendResponse($response);
    }

    // Tạo sản phẩm mới
    public function createProduct()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $response = $this->productModel->createProduct(
            $data['ProductName'],
            $data['Status'],
            $data['CreateTime'],
            $data['Image'],
            $data['Origin'],
            $data['Capacity'],
            $data['ABV'],
            $data['Description'],
            $data['BrandId'],
            $data['CategoryId']
        );
        $this->sendResponse($response);
    }

    // Cập nhật sản phẩm
    public function updateProduct($id)
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $response = $this->productModel->updateProduct(
            $id,
            $data['ProductName'],
            $data['Status'],
            $data['CreateTime'],
            $data['Image'],
            $data['Origin'],
            $data['Capacity'],
            $data['ABV'],
            $data['Description'],
            $data['BrandId'],
            $data['CategoryId']
        );
        $this->sendResponse($response);
    }


    // Gửi phản hồi JSON
    private function sendResponse($response)
    {
        header('Content-Type: application/json');
        http_response_code($response->status);
        echo json_encode($response);
        exit();
    }
}
