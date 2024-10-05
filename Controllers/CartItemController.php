<?php

require_once __DIR__ . "/../Models/CartItemModel.php";
require '../vendor/autoload.php';

class CartItemController
{
    private $CartItemModel;

    public function __construct()
    {
        $this->CartItemModel = new CartItemModel();
    }

    // Phương thức để lấy một mục giỏ hàng theo CartItemId (ProductId và AccountId)
    public function getCartItemById($productId, $accountId)
    {
        $result = $this->CartItemModel->getCartItemById($productId, $accountId);
        $this->respond($result);
    }

    // Phương thức để lấy tất cả mục giỏ hàng theo AccountId
    public function getAllCartItemsByAccountId($accountId)
    {
        $result = $this->CartItemModel->getAllCartItemsByAccountId($accountId);
        $this->respond($result);
    }

    // Phương thức để tạo mới một mục giỏ hàng
    public function createCartItem($cartItem)
    {
        $result = $this->CartItemModel->createCartItem($cartItem);
        $this->respond($result);
    }

    // Phương thức để cập nhật mục giỏ hàng
    public function updateCartItem($cartItem)
    {
        $result = $this->CartItemModel->updateCartItem($cartItem);
        $this->respond($result);
    }

    // Phương thức để xóa một mục giỏ hàng
    public function deleteCartItem($productId, $accountId)
    {
        $result = $this->CartItemModel->deleteCartItem($productId, $accountId);
        $this->respond($result);
    }

    // Phương thức để xóa tất cả mục giỏ hàng theo AccountId
    public function deleteAllCartItems($accountId)
    {
        $result = $this->CartItemModel->deleteAllCartItems($accountId);
        $this->respond($result);
    }

    // Phương thức chung để xử lý phản hồi
    private function respond($result)
    {
        http_response_code($result->status);
        echo json_encode([
            "message" => $result->message,
            "data" => $result->data ?? null
        ]);
    }
}
