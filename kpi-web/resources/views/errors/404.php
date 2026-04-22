<?php
/**
 * 404 Error Page
 */
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Trang Không Tìm Thấy</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .error-container {
            background: white;
            padding: 60px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 500px;
        }

        .error-container h1 {
            font-size: 72px;
            color: #e63946;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .error-container p {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
        }

        .error-container a {
            display: inline-block;
            background-color: #667eea;
            color: white;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            transition: background-color 0.3s;
            font-weight: 600;
        }

        .error-container a:hover {
            background-color: #764ba2;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1>404</h1>
        <p>Trang bạn tìm kiếm không tồn tại</p>
        <a href="/">Quay lại Trang chủ</a>
    </div>
</body>
</html>
