<?php
require_once __DIR__ . "/../Models/OrderModel.php";
require_once __DIR__ . "/../Models/OrderStatusModel.php";
require_once __DIR__ . "/../Models/OrderDetailModel.php";

require '../vendor/autoload.php';

$controller = new OrderController();

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            $response = $controller->getOrderById($_GET['id']);
            echo $response;
        } elseif (isset($_GET['accountId'])) {
            $response = $controller->getOrdersByAccountId($_GET['accountId']);
            echo $response;
        } elseif (isset($_GET['idOrder'])) {
            $response = $controller->getFullOrderById($_GET['idOrder']);
            echo $response;
        } else {
            $pageNumber = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $pageSize = isset($_GET['pageSize']) ? (int)$_GET['pageSize'] : 5;
            $minNgayTao = isset($_GET['from']) ? urldecode($_GET['from']) : null;
            $maxNgayTao = isset($_GET['to']) ? urldecode($_GET['to']) : null;
            $status = $_GET['status'] ?? null;
            $search = isset($_GET['search']) && $_GET['search'] !== '' ? $_GET['search'] : null;


            $response = $controller->getAllOrders($pageNumber, $pageSize, $minNgayTao, $maxNgayTao, $status, $search);

            echo $response;
        }
        break;

    case 'POST':
        // Kiểm tra dữ liệu nhận được từ client
        var_dump($_POST);

        // Lấy dữ liệu từ request
        $orderId = $_POST['orderId'] ?? null; // Lấy tổng giá trị
        echo "OrderId đây nè: ", $orderId;
        $totalPrice = $_POST['totalPrice'] ?? null; // Lấy tổng giá trị
        $accountId = $_POST['accountId'] ?? null; // Lấy ID tài khoản
        $note = $_POST['note'] ?? null; // Lấy ghi chú
        $listOrderDetail = $_POST['listOrderDetail'] ?? []; // Lấy danh sách chi tiết đơn hàng
        $Payment = $_POST['Payment'] ?? null;
        // Kiểm tra nếu có đủ dữ liệu cần thiết
        if ($totalPrice && $accountId && !empty($listOrderDetail)) {
            // Chuyển đổi danh sách chi tiết đơn hàng thành mảng
            $orderDetails = [];
            foreach ($listOrderDetail as $detail) {
                // Đảm bảo `productId`, `unitPrice`, `quantity`, và `total` đều tồn tại trong mỗi chi tiết đơn hàng
                if (isset($detail['productId'], $detail['unitPrice'], $detail['quantity'], $detail['total'])) {
                    $orderDetails[] = [
                        'productId' => $detail['productId'],
                        'unitPrice' => $detail['unitPrice'],
                        'quantity' => $detail['quantity'],
                        'total' => $detail['total']
                    ];
                } else {
                    echo json_encode([
                        'status' => 400,
                        'message' => 'Chi tiết đơn hàng thiếu dữ liệu cần thiết'
                    ]);
                    exit; // Dừng xử lý nếu dữ liệu chi tiết không hợp lệ
                }
            }

            // Gọi hàm tạo đơn hàng
            $response = $controller->createOrder([
                'orderId' => $orderId,
                'totalPrice' => $totalPrice,
                'accountId' => $accountId,
                'note' => $note,
                'Payment' => $Payment
            ], $orderDetails);

            // Trả về phản hồi dưới dạng JSON
            echo json_encode($response);
        } else {
            // Nếu dữ liệu không hợp lệ
            echo json_encode([
                'status' => 400,
                'message' => 'Dữ liệu không hợp lệ'
            ]);
        }
        break;
        // Kiểm tra dữ liệu nhận được từ client

    case 'PUT':
        if (isset($_GET['orderId'])) {
            // $response = $controller->updateOrder($_GET['orderId']);
            // echo $response;
        } else {
            http_response_code(400);
            echo json_encode(["status" => 400, "message" => "Order ID is required for update."]);
        }
        break;


    default:
        http_response_code(405);
        echo json_encode(["status" => 405, "message" => "Method not allowed."]);
        break;
}
class OrderController
{
    private $orderModel;
    private $orderDetailModel;
    private $orderStatusModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->orderDetailModel = new OrderDetailModel();
        $this->orderStatusModel = new OrderStatusModel();
    }


    public function getAllOrders($pageNumber = 1, $size = 10, $minNgayTao = null, $maxNgayTao = null, $status = null, $search = null)
    {
        $response = $this->orderModel->getAllOrder($pageNumber, $size, $minNgayTao, $maxNgayTao, $status, $search);
        $this->response($response);
    }
    
    public function getOrderById($orderId)
    {
        $response = $this->orderModel->getOrderById($orderId);
        $this->response($response);
    }

    public function getFullOrderById($orderId)
    {
        $response = $this->orderModel->getFullOrderById($orderId);
        $this->response($response);
    }

    public function createOrder($orderData, $orderDetails)
    {
        require_once __DIR__ . "../../Configure/MysqlConfig.php";

        $db = MysqlConfig::getConnection();

        try {
            // Bắt đầu transaction
            $db->beginTransaction();

            // Tạo hóa đơn và lấy `orderId` từ đối tượng trả về
            $orderResult = $this->orderModel->createOrder($orderData);
            $orderId = $orderResult->orderId;

            // Tạo từng chi tiết hóa đơn
            foreach ($orderDetails as $detail) {
                $this->orderDetailModel->createOrderDetail($orderId, $detail);
            }

            $this->orderStatusModel->createOrderStatus($orderId, 'ChoDuyet');

            $db->commit();
            return ['status' => 201, 'message' => 'Hóa đơn tạo thành công', 'orderId' => $orderId];
        } catch (Exception $e) {
            // Rollback transaction nếu có lỗi
            $db->rollBack();
            return ['status' => 500, 'message' => 'Lỗi tạo hóa đơn: ' . $e->getMessage()];
        }
    }

    // Lấy tất cả đơn hàng của một tài khoản dựa trên AccountId
    public function getOrdersByAccountId($accountId)
    {
        $response = $this->orderModel->getOrdersByAccountId($accountId);
        $this->response($response);
    }

    private function response($result)
    {
        http_response_code($result->status);
        $response = [
            "message" => $result->message,
            "data" => $result->data ?? null
        ];

        if (isset($result->totalPages)) {
            $response['totalPages'] = $result->totalPages;
        }

        if (isset($result->totalElements)) {
            $response['totalElements'] = $result->totalElements;
        }

        echo json_encode($response);
    }
}
