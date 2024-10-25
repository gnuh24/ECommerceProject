<?php

require_once __DIR__ . "/../Models/StatisticModel.php";
require '../vendor/autoload.php';

$controller = new StatisticController();

// Kiểm tra phương thức HTTP
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['type'])) {
            // Lấy tổng quan trạng thái đơn hàng
            if ($_GET['type'] === 'orderStatusSummary') {
                $response = $controller->getOrderStatusSummary($_GET['minDate'], $_GET['maxDate']);
                echo $response;
            }
            // Lấy danh sách sản phẩm bán chạy
            else if ($_GET['type'] === 'bestSellingProducts') {
                $response = $controller->getBestSellingProducts($_GET['startDate'], $_GET['endDate'], $_GET['topProducts']);
                echo $response;
            } else {
                // Phản hồi lỗi nếu không tìm thấy loại yêu cầu phù hợp
                http_response_code(400); // Bad Request
                echo json_encode([
                    "status" => 400,
                    "message" => "Yêu cầu không hợp lệ."
                ]);
            }
        } else {
            // Phản hồi lỗi nếu thiếu tham số 'type'
            http_response_code(400); // Bad Request
            echo json_encode([
                "status" => 400,
                "message" => "'type' là bắt buộc."
            ]);
        }
        break;

    default:
        // Phản hồi cho các phương thức không được hỗ trợ
        http_response_code(405); // Method Not Allowed
        echo json_encode([
            "status" => 405,
            "message" => "Phương thức không được hỗ trợ."
        ]);
        break;
}

class StatisticController
{
    private $StatisticModel;

    public function __construct()
    {
        $this->StatisticModel = new StatisticModel();
    }

    // Lấy tổng quan trạng thái đơn hàng
    public function getOrderStatusSummary($minDate, $maxDate)
    {
        $result = $this->StatisticModel->findOrderStatusSummary($minDate, $maxDate);
        return $this->respond($result);
    }

    // Lấy danh sách sản phẩm bán chạy nhất
    public function getBestSellingProducts($startDate, $endDate, $topProducts)
    {
        $result = $this->StatisticModel->findBestSellingProducts($startDate, $endDate, $topProducts);
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

        echo json_encode($response);
    }
}
