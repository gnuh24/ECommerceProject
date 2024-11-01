<?php
require_once __DIR__ . "/../Models/VoucherModel.php";

// Khởi tạo controller
$controller = new VoucherController();

// Kiểm tra phương thức HTTP
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['Id'])) {
            $response = $controller->getVoucherById($_GET['Id']);
            echo $response;
        } elseif (isset($_GET['page'])) {
            $pageNumber = $_GET['page'];
            $pageSize = isset($_GET['pageSize']) ? $_GET['pageSize'] : 10;
            $from = isset($_GET['from']) ? $_GET['from'] : null;
            $to = isset($_GET['to']) ? $_GET['to'] : null;
            $status = isset($_GET['status']) ? $_GET['status'] : null;

            // Gọi hàm trong controller
            $response = $controller->getAllVouchers(
                $pageNumber,
                $pageSize,
                $from,
                $to,
                $status
            );

            // Trả về JSON hợp lệ
            echo ($response);
        }

        // Lấy danh sách voucher không phân trang
        else {
            $response = $controller->getAllVouchersNoPaging();
            echo $response;
        }
        break;

    case 'PATCH':
        // Đọc dữ liệu thô từ php://input
        $inputData = file_get_contents("php://input");

        // Giải mã dữ liệu JSON
        $data = json_decode($inputData, true);

        // Kiểm tra nếu có ID và tên voucher trong request
        if (isset($data['id'])) {
            if (isset($data['action'])) {
                $response =  $controller->updateVoucher(
                    $data['id'],
                    isset($data['ExpirationTime']) ? $data['ExpirationTime'] : null,
                    isset($data['Code']) ? $data['Code'] : null,
                    isset($data['maCondition']) ? $data['maCondition'] : null,
                    isset($data['SaleAmount']) ? $data['SaleAmount'] : null,
                    isset($data['IsPublic']) ? $data['IsPublic'] : null
                );
                echo $response;
            } else {
                $response = $controller->updateVoucher($data['id'], null, null, null, null, $data['isPublic']);
                echo $response;
            }
        } else {
            // Phản hồi lỗi nếu ID hoặc VoucherName không được cung cấp
            http_response_code(400);
            echo json_encode([
                "status" => 400,
                "message" => "ID và tên voucher là bắt buộc."
            ]);
        }
        break;

    case 'POST':
        // Lấy dữ liệu JSON từ yêu cầu POST
        $data = file_get_contents('php://input');
        $decodedData = json_decode($data, true);

        if (empty($decodedData)) {
            http_response_code(400); // Bad Request
            echo json_encode([
                "status" => 400,
                "message" => "Dữ liệu không hợp lệ"
            ]);
            exit;
        }

        // Gọi hàm xử lý tạo voucher mới và trả về phản hồi
        $response = $controller->createVoucher($decodedData['ExpirationTime'], $decodedData['Code'], $decodedData['maCondition'], $decodedData['SaleAmount'], $decodedData['IsPublic']);

        // Trả về dưới dạng JSON
        echo $response;
        break;

    case 'DELETE':
        // Kiểm tra nếu ID đã được gửi trong yêu cầu
        if (isset($_GET['id'])) {
            $id = $_GET['id'];

            // Gọi hàm để xử lý việc xóa voucher
            $response = $controller->deleteVoucher($id);

            // Trả về phản hồi dưới dạng JSON
            echo $response;
        } else {
            // Trả về lỗi nếu không có ID trong yêu cầu
            http_response_code(400); // Bad Request
            echo json_encode([
                "status" => 400,
                "message" => "ID is required for deletion."
            ]);
        }
        break;

    default:
        // Phản hồi cho các phương thức không được hỗ trợ
        http_response_code(405); // Method Not Allowed
        echo json_encode([
            "status" => 405,
            "message" => "Method not allowed."
        ]);
        break;
}

class VoucherController
{
    private $voucherModel;

    public function __construct()
    {
        $this->voucherModel = new VoucherModel();
    }
    public function getVoucherById($id)
    {
        $result = $this->voucherModel->getVoucherById($id);
        return $this->respond($result);
    }
    // Lấy tất cả voucher không phân trang
    public function getAllVouchersNoPaging()
    {
        $result = $this->voucherModel->getAllVouchersNoPaging();
        return $this->respond($result);
    }

    // Lấy tất cả voucher có phân trang và tìm kiếm
    public function getAllVouchers($pageNumber = 1, $size = 10, $minNgayTao = null, $maxNgayTao = null, $status = null)
    {
        $result = $this->voucherModel->getAllVouchers($pageNumber, $size, $minNgayTao, $maxNgayTao, $status);
        return $this->respond($result);
    }

    // Tạo mới một voucher
    public function createVoucher($expirationTime, $code, $condition, $saleAmount, $isPublic = true)
    {
        $result = $this->voucherModel->createVoucher($expirationTime, $code, $condition, $saleAmount, $isPublic);
        return $this->respond($result);
    }

    // Cập nhật voucher
    public function updateVoucher($id, $expirationTime, $code, $condition, $saleAmount, $isPublic)
    {
        $result = $this->voucherModel->updateVoucher($id, $expirationTime, $code, $condition, $saleAmount, $isPublic);
        return $this->respond($result);
    }

    // Xóa voucher theo ID
    public function deleteVoucher($voucherId)
    {
        $result = $this->voucherModel->deleteVoucher($voucherId);
        return $this->respond($result);
    }

    // Phương thức xử lý phản hồi chung
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
        // Kiểm tra và thêm totalElements nếu có trong kết quả
        if (isset($result->totalElements)) {
            $response['totalElements'] = $result->totalElements;
        }
        echo json_encode($response);
    }
}
