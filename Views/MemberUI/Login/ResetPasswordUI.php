<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="LoginUI.css"> <!-- Đường dẫn đến tệp CSS của bạn -->
    <title>Đặt lại mật khẩu</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa; /* Màu nền */
        }

        #container {
            max-width: 400px; /* Độ rộng tối đa của form */
            width: 100%; /* Chiếm toàn bộ chiều rộng */
            padding: 20px;
            border-radius: 8px;
            background-color: white; /* Màu nền cho form */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Đổ bóng cho form */
        }

        .form-container {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container" id="container">
        <div class="form-container reset-password">
            <form id="resetPasswordForm" method="POST" action="../../../Controllers/AccountController.php">
                <h2>Đặt lại mật khẩu</h2>
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>">

                <div class="mb-3">
                    <label for="new_password" class="form-label">Mật khẩu mới:</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới:</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>
                <div>
                    <button type="submit" class="btn btn-danger">Đặt lại mật khẩu</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#resetPasswordForm').on('submit', function(event) {
                event.preventDefault(); // Ngăn chặn việc gửi form mặc định

                // Lấy dữ liệu từ form
                var email = $('input[name="email"]').val();
                var token = $('input[name="token"]').val();
                var newPassword = $('#new_password').val();
                var confirmPassword = $('#confirm_password').val();

                // Kiểm tra xem mật khẩu có khớp không
                if (newPassword !== confirmPassword) {
                    Swal.fire({
                        title: 'Lỗi!',
                        text: 'Mật khẩu không khớp.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // Gửi dữ liệu lên server
                $.ajax({
                    url: '../../../Controllers/AccountController.php', // Đường dẫn đến file xử lý
                    type: 'POST',
                    data: {
                        email: email,
                        token: token,
                        new_password: newPassword,
                        confirm_password: confirmPassword,
                        "action": "setNewPassword" 
                    },
                    success: function(response) {
                        // In ra phản hồi từ server
                        console.log(response);

                        // Xử lý phản hồi từ server
                        try {
                        var result = JSON.parse(response); // Parse JSON
                        if (result.status === 200) {
                       Swal.fire({
                      title: 'Thành công!',
                     text: 'Đặt lại mật khẩu thành công!',
                     icon: 'success',
                     confirmButtonText: 'OK'
            }).then(() => {
                // Chuyển hướng về trang login
                window.location.href = 'LoginUI.php'; // Thay đổi đường dẫn này
            });
                                
                            }
                        } catch (e) {
                            console.error('JSON parse error:', e); // In lỗi nếu JSON không hợp lệ
                            Swal.fire({
                                title: 'Lỗi!',
                                text: 'Lỗi không thể phân tích cú pháp JSON!',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Lỗi!',
                            text: 'Đã xảy ra lỗi khi gửi yêu cầu!',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>
