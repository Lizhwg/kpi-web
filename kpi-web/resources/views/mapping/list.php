<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xét Duyệt Người Được Đánh Giá - KPI System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .header h1 {
            font-size: 28px;
            font-weight: 600;
        }

        .header .user-info {
            text-align: right;
        }

        .header .username {
            font-size: 14px;
            opacity: 0.9;
        }

        .header a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            padding: 8px 15px;
            background: rgba(255,255,255,0.2);
            border-radius: 5px;
            transition: background 0.3s;
        }

        .header a:hover {
            background: rgba(255,255,255,0.3);
        }

        .container {
            max-width: 1400px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .controls {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: center;
        }

        .control-group {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .control-group label {
            font-weight: 500;
            white-space: nowrap;
        }

        select, input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        select {
            min-width: 150px;
        }

        .btn {
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
        }

        .btn-filter {
            background: #667eea;
            color: white;
        }

        .btn-filter:hover {
            background: #5568d3;
        }

        .btn-reset {
            background: #f0f0f0;
            color: #333;
        }

        .btn-reset:hover {
            background: #e0e0e0;
        }

        .btn-action {
            background: #10b981;
            color: white;
            padding: 6px 12px;
            font-size: 12px;
        }

        .btn-action:hover {
            background: #059669;
        }

        .btn-action.reject {
            background: #ef4444;
        }

        .btn-action.reject:hover {
            background: #dc2626;
        }

        .table-wrapper {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .actions-bar {
            padding: 15px 20px;
            background: #f9f9f9;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .actions-dropdown {
            min-width: 150px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #667eea;
            color: white;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            border-right: 1px solid #5568d3;
        }

        th:last-child {
            border-right: none;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        tr:hover {
            background: #f9f9f9;
        }

        .checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .status-waiting {
            background: #fef3c7;
            color: #92400e;
        }

        .status-approved {
            background: #d1fae5;
            color: #065f46;
        }

        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-pending {
            background: #e0e7ff;
            color: #3730a3;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
            padding: 15px;
            background: white;
            border-radius: 10px;
        }

        .pagination button, .pagination a {
            padding: 8px 12px;
            border: 1px solid #ddd;
            background: white;
            color: #333;
            cursor: pointer;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .pagination button:hover, .pagination a:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .pagination .active {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .quick-action-btn {
            padding: 6px 12px;
            font-size: 12px;
            margin: 0 3px;
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <h1>🎯 XÉT DUYỆT NGƯỜI ĐƯỢC ĐÁNH GIÁ</h1>
    <div class="user-info">
        <div class="username">Xin chào, <strong><?php echo htmlspecialchars($user['username'] ?? 'Guest'); ?></strong> (Thư ký)</div>
        <a href="/KPI/KPI/kpi-web/dashboard">Quay lại</a>
        <a href="/KPI/KPI/kpi-web/logout">Đăng xuất</a>
    </div>
</div>

<!-- Main Container -->
<div class="container">

    <!-- Controls -->
    <div class="controls">
        <form method="GET" action="/KPI/KPI/kpi-web/mapping/list" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center; flex: 1;">
            <div class="control-group">
                <label>Kỳ đánh giá:</label>
                <select name="period" onchange="this.form.submit()">
                    <option value="">-- Tất cả --</option>
                    <?php foreach ($periods as $period): ?>
                        <option value="<?php echo $period['id']; ?>" <?php echo ($periodFilter == $period['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($period['period_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="control-group">
                <label>Trạng thái:</label>
                <select name="status" onchange="this.form.submit()">
                    <option value="">-- Tất cả --</option>
                    <?php foreach ($statusList as $status): ?>
                        <option value="<?php echo $status; ?>" <?php echo ($statusFilter === $status) ? 'selected' : ''; ?>>
                            <?php echo $status; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-filter">🔍 Lọc</button>
            <a href="/KPI/KPI/kpi-web/mapping/list" class="btn btn-reset">⟲ Reset</a>
        </form>
    </div>

    <!-- Table -->
    <div class="table-wrapper">
        <div class="actions-bar">
            <select class="actions-dropdown">
                <option>-- Chọn hành động --</option>
                <option value="approve">Phê duyệt</option>
                <option value="reject">Từ chối</option>
                <option value="send_back">Trả lại sửa</option>
            </select>
            <span style="font-size: 12px; color: #999;">
                Hiển thị <strong><?php echo count($evaluations); ?></strong> / <strong><?php echo $totalRecords; ?></strong> bản ghi
            </span>
        </div>

        <?php if (empty($evaluations)): ?>
            <div class="no-data">
                <p>😕 Không có dữ liệu để hiển thị</p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" class="checkbox" onclick="toggleAllCheckboxes(this)"></th>
                        <th>ID</th>
                        <th>Họ và tên</th>
                        <th>Phòng ban</th>
                        <th>Role</th>
                        <th>Level</th>
                        <th>Quản lý cấp 1</th>
                        <th>Quản lý cấp 2</th>
                        <th>Người đánh giá</th>
                        <th>Trạng thái đánh giá</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($evaluations as $eval): ?>
                        <tr>
                            <td><input type="checkbox" class="checkbox row-checkbox" value="<?php echo $eval['id']; ?>"></td>
                            <td><?php echo $eval['id']; ?></td>
                            <td>
                                <a href="/KPI/KPI/kpi-web/mapping/detail?id=<?php echo $eval['id']; ?>" style="color: #667eea; text-decoration: none; font-weight: 500;">
                                    <?php echo htmlspecialchars($eval['member_name'] ?? 'N/A'); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($eval['business_unit'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($eval['role_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($eval['level']); ?></td>
                            <td><?php echo htmlspecialchars($eval['manager_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($eval['dev_head_name'] ?? 'N/A'); ?></td>
                            <td>-</td>
                            <td>
                                <?php
                                    $status = $eval['status'] ?? 'Chưa đánh giá';
                                    $statusClass = 'status-pending';
                                    if ($status === 'Hoàn thành') $statusClass = 'status-completed';
                                    elseif ($status === 'Chờ HRBP') $statusClass = 'status-approved';
                                    elseif ($status === 'Chờ duyệt') $statusClass = 'status-waiting';
                                    elseif ($status === 'Đánh giá lại') $statusClass = 'status-rejected';
                                ?>
                                <span class="status-badge <?php echo $statusClass; ?>">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=1<?php echo $statusFilter ? "&status=$statusFilter" : ''; ?><?php echo $periodFilter ? "&period=$periodFilter" : ''; ?>">« Đầu</a>
            <a href="?page=<?php echo $page - 1; ?><?php echo $statusFilter ? "&status=$statusFilter" : ''; ?><?php echo $periodFilter ? "&period=$periodFilter" : ''; ?>">‹ Trước</a>
        <?php endif; ?>

        <span>Trang <strong><?php echo $page; ?></strong> / <strong><?php echo $totalPages ?: 1; ?></strong></span>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?php echo $page + 1; ?><?php echo $statusFilter ? "&status=$statusFilter" : ''; ?><?php echo $periodFilter ? "&period=$periodFilter" : ''; ?>">Sau ›</a>
            <a href="?page=<?php echo $totalPages; ?><?php echo $statusFilter ? "&status=$statusFilter" : ''; ?><?php echo $periodFilter ? "&period=$periodFilter" : ''; ?>">Cuối »</a>
        <?php endif; ?>
    </div>

</div>

<script>
function toggleAllCheckboxes(masterCheckbox) {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);
}
</script>

</body>
</html>
