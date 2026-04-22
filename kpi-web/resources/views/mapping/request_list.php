<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yêu Cầu Đánh Giá - Luvina Software</title>
    <style>
        /* Toàn bộ giao diện bám sát ảnh thiết kế của Hương */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f7f6; display: flex; }

        /* Sidebar xám bên trái */
        .sidebar { width: 80px; background-color: #555; min-height: 100vh; position: fixed; }

        .main-container { flex: 1; margin-left: 80px; padding: 0; }

        /* Header trắng với logo và info */
        .header { 
            background-color: #fff; 
            height: 70px; 
            padding: 0 30px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            border-bottom: 1px solid #ddd;
        }
        .header-left { display: flex; align-items: center; gap: 20px; }
        .header-title { font-size: 22px; font-weight: bold; color: #333; text-transform: uppercase; }
        
        .user-info { display: flex; align-items: center; gap: 20px; }
        .user-name { font-size: 14px; color: #666; }
        
        /* Nút Đăng xuất màu đỏ đô Hương chọn */
        .btn-logout { 
            text-decoration: none; 
            background-color: #A52A2A; 
            color: white; 
            padding: 8px 15px; 
            border-radius: 4px; 
            font-size: 13px;
            transition: 0.3s;
        }
        .btn-logout:hover { background-color: #8B0000; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }

        /* Vùng nội dung chính */
        .content { padding: 30px; }
        
        /* Toolbar chứa Filter và Export */
        .toolbar { display: flex; justify-content: flex-end; gap: 15px; margin-bottom: 20px; }
        .btn-tool { 
            background: #fff; 
            border: 1px solid #ccc; 
            padding: 8px 20px; 
            cursor: pointer; 
            display: flex; 
            align-items: center; 
            gap: 8px; 
            font-size: 14px;
            border-radius: 2px;
        }

        .summary-text { font-size: 13px; color: #666; margin-bottom: 10px; }

        /* Định dạng Table xanh dương chuẩn thiết kế */
        .table-container { background: #fff; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 1000px; }
        
        thead { background-color: #0073a5; color: #fff; }
        th { padding: 12px 10px; border: 1px solid #005a82; font-size: 13px; font-weight: 600; text-align: center; }
        
        td { padding: 12px 10px; border: 1px solid #eee; text-align: center; font-size: 14px; color: #444; }
        tr:nth-child(even) { background-color: #fcfcfc; }
        /* Chỉ áp dụng hover cho các dòng dữ liệu, bỏ qua header */
        tbody tr:hover { 
            background-color: #f1f8ff; 
            transition: 0.2s; /* Thêm chút hiệu ứng mượt mà */
        }

        /* Đảm bảo tiêu đề luôn giữ màu xanh dương Luvina */
        thead tr:hover {
            background-color: #0073a5; 
        }

        /* Link trạng thái click sang màn hình đánh giá */
        .status-link { 
            color: #0073a5; 
            text-decoration: underline; 
            font-weight: bold; 
            cursor: pointer; 
        }
        .status-link:hover { color: #d00; }
    </style>
</head>
<body>

    <div class="sidebar"></div>

    <div class="main-container">
        <header class="header">
            <div class="header-left">
                <div class="header-title">Yêu cầu đánh giá</div>
            </div>
            <div class="user-info">
                <span style="font-size: 18px; cursor: pointer;">🔍</span>
                <span class="user-name">👤 <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></strong></span>
                <a href="/KPI/KPI/kpi-web/logout" class="btn-logout" onclick="return confirm('Bạn có chắc muốn đăng xuất không?')">
                     Đăng xuất
                </a>
            </div>
        </header>

        <div class="content">
            <div class="toolbar">
                <button class="btn-tool"><span>⏳</span> Filter</button>
                <button class="btn-tool"><span>📊</span> Export Excel</button>
            </div>

            <p class="summary-text">Có <strong><?php echo count($evaluations); ?></strong> bản ghi cần đánh giá</p>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 50px;">STT</th>
                            <th>Thời gian<br>đánh giá</th>
                            <th style="text-align: left;">Họ và tên</th>
                            <th>Phòng ban</th>
                            <th>Role</th>
                            <th>Level</th>
                            <th>PM Score</th>
                            <th>TP Score</th>
                            <th>HRBP Score</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($evaluations)): ?>
                            <tr>
                                <td colspan="10" style="padding: 40px; color: #999;">Không có dữ liệu đánh giá nào cho bạn.</td>
                            </tr>
                        <?php else: ?>
                            <?php $stt = 1; foreach ($evaluations as $row): ?>
                                <tr>
                                    <td><?php echo $stt++; ?></td>
                                    <td><?php echo htmlspecialchars($row['period_name']); ?></td>
                                    <td style="text-align: left; font-weight: 500;"><?php echo htmlspecialchars($row['member_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['department'] ?? 'Dev 5'); ?></td>
                                    <td><?php echo $roleMap[$row['role']] ?? 'Member'; ?></td>
                                    <td><?php echo htmlspecialchars($row['level']); ?></td>
                                    <td><?php echo htmlspecialchars($row['pm_score'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['tp_score'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($row['hrbp_score'] ?? ''); ?></td>
                                    <td>
                                        <a href="/KPI/KPI/kpi-web/evaluation/pmForm?mapping_id=<?php echo $row['mapping_id']; ?>" class="status-link">
                                            <?php echo htmlspecialchars($row['status'] ?: 'Chưa đánh giá'); ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>