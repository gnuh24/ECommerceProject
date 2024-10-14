<?php

require_once __DIR__ . "/../Models/CartItemModel.php";
require '../vendor/autoload.php';
$controller = new CartItemController();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $response = $controller->getAllCartItemsByAccountId($_GET['id']);
        echo $response;

        break;

    case 'POST':
        // Giả sử bạn đã có logic để lấy dữ liệu từ $_POST hoặc $_FILES
        $cartItem = [
            'accountId' => $_POST['accountId'],
            'productId' => $_POST['productId'],
            'unitPrice' => $_POST['unitPrice'],
            'quantity' => $_POST['quantity'],
        ];

        $response = $controller->createCartItem($cartItem);
        echo ($response); // Đảm bảo rằng phản hồi được mã hóa thành JSON
        break;

    case 'PATCH':
        // Lấy dữ liệu từ yêu cầu PATCH
        $data = json_decode(file_get_contents("php://input"), true); // Đọc dữ liệu JSON từ yêu cầu

        $response = $controller->updateCartItem($data); // Gọi hàm với orderId và dữ liệu cập nhật
        echo ($response);
        break;

    case 'DELETE':
        // Kiểm tra có productId và accountId không
        if (isset($_GET['productId']) && isset($_GET['accountId'])) {
            // Gọi hàm deleteCartItem với productId và accountId từ query string
            $response = $controller->deleteCartItem($_GET['productId'], $_GET['accountId']);
            echo ($response); // Trả về phản hồi dưới dạng JSON
        } else {
            http_response_code(400);
            echo json_encode(["status" => 400, "message" => "Product ID and Account ID are required for deletion."]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["status" => 405, "message" => "Method not allowed."]);
        break;
}
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
        // Gọi mô hình để thêm cart item
        $result = $this->CartItemModel->createCartItem($cartItem);

        return $this->respond($result);
    }


    // Phương thức để cập nhật mục giỏ hàng
    public function updateCartItem($cartItem)
    {
        $result = $this->CartItemModel->updateCartItem($cartItem);
        return $this->respond($result);
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
        $response = [
            "message" => $result->message,
            "data" => $result->data ?? null,
            "status" => $result->status
        ];

        // Kiểm tra và thêm totalPages nếu có trong kết quả
        if (isset($result->totalPages)) {
            $response['totalPages'] = $result->totalPages;
        }

        echo json_encode($response);
    }
}
