<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thanh toán VNPAY</title>
</head>
<body>

    <h2>Thanh toán qua VNPAY</h2>
    <form id="paymentForm" action="./xuLyThanhToan_vnpay.php" method="POST">
        <label for="order_id">Mã đơn hàng:</label>
        <input type="text" id="order_id" name="order_id" required><br><br>

        <label for="order_desc">Mô tả đơn hàng:</label>
        <input type="text" id="order_desc" name="order_desc" required><br><br>

        <label for="order_type">Loại đơn hàng:</label>
        <input type="text" id="order_type" name="order_type" required><br><br>

        <label for="amount">Số tiền (VND):</label>
        <input type="number" value="10000" id="amount" name="amount" required><br><br>

        <label for="language">Ngôn ngữ:</label>
        <select id="language" name="language">
            <option value="vn">Tiếng Việt</option>
            <option value="en">English</option>
        </select><br><br>

        <label for="txtexpire">Ngày hết hạn (yyyymmddhhmmss):</label>
        <input type="text" id="txtexpire" name="txtexpire" required><br><br>

        <button name="redirect" type="submit">Thanh toán</button>
    </form>

</body>
</html>
