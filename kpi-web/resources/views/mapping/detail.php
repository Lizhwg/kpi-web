<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đánh Giá - KPI System</title>
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

        .header a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            background: rgba(255,255,255,0.2);
            border-radius: 5px;
            transition: background 0.3s;
        }

        .header a:hover {
            background: rgba(255,255,255,0.3);
        }

        .container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .info-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .info-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 15px;
        }

        .info-item {
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }

        .info-label {
            font-weight: 600;
            color: #667eea;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 16px;
            color: #333;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-top: 5px;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }

        .status-waiting {
            background: #fef3c7;
            color: #92400e;
        }

        .status-pending {
            background: #e0e7ff;
            color: #3730a3;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin: 20px 0 15px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        thead {
            background: #667eea;
            color: white;
        }

        th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        tr:hover {
            background: #f9f9f9;
        }

        .actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
        }

        .btn-primary {
            background: #10b981;
            color: white;
        }

        .btn-primary:hover {
            background: #059669;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #333;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        .score-item {
            padding: 10px;
            background: #f9f9f9;
            border-left: 4px solid #667eea;
            margin-bottom: 10px;
            border-radius: 3px;
        }

        .score-label {
            font-weight: 500;
            color: #333;
            font-size: 14px;
        }

        .score-values {
            display: flex;
            gap: 20px;
            margin-top: 5px;
            font-size: 13px;
        }

        .score-value {
            background: white;
            padding: 5px 10px;
            border-radius: 3px;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #999;
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <h1>📋 CHI TIẾT ĐÁNH GIÁ</h1>
    <div>
        <a href="/KPI/KPI/kpi-web/mapping/list">← Quay lại danh sách</a>
    </div>
</div>

<!-- Main Container -->
<div class="container">

    <?php if (!$evaluation): ?>
        <div class="info-box no-data">
            <p>😕 Không tìm thấy bản ghi đánh giá</p>
        </div>
    <?php else: ?>

        <!-- Thông tin cơ bản -->
        <div class="info-box">
            <div class="info-row">
                <div class="info-item">
                    <div class="info-label">Người được đánh giá</div>
                    <div class="info-value"><?php echo htmlspecialchars($evaluation['member_name'] ?? 'N/A'); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Kỳ đánh giá</div>
                    <div class="info-value"><?php echo htmlspecialchars($evaluation['period_name'] ?? 'N/A'); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Phòng ban</div>
                    <div class="info-value"><?php echo htmlspecialchars($evaluation['bu_name'] ?? 'N/A'); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Trạng thái</div>
                    <div class="info-value">
                        <?php
                            $status = $evaluation['status'] ?? 'Chưa đánh giá';
                            $statusClass = 'status-pending';
                            if ($status === 'Hoàn thành') $statusClass = 'status-completed';
                            elseif ($status === 'Chờ HRBP') $statusClass = 'status-completed';
                            elseif ($status === 'Chờ duyệt') $statusClass = 'status-waiting';
                        ?>
                        <span class="status-badge <?php echo $statusClass; ?>">
                            <?php echo htmlspecialchars($status); ?>
                        </span>
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">Quản lý cấp 1</div>
                    <div class="info-value"><?php echo htmlspecialchars($evaluation['manager_name'] ?? 'N/A'); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Quản lý cấp 2</div>
                    <div class="info-value"><?php echo htmlspecialchars($evaluation['dev_head_name'] ?? 'N/A'); ?></div>
                </div>
            </div>
        </div>

        <!-- Chi tiết điểm số -->
        <div class="info-box">
            <h2 class="section-title">📊 Chi Tiết Điểm Số</h2>

            <?php if (empty($evaluationDetails)): ?>
                <div class="no-data">
                    <p>Chưa có dữ liệu đánh giá</p>
                </div>
            <?php else: ?>
                <?php foreach ($evaluationDetails as $detail): ?>
                    <div class="score-item">
                        <div class="score-label">
                            <?php echo htmlspecialchars($detail['content_description'] ?? 'N/A'); ?>
                        </div>
                        <div class="score-values">
                            <?php if ($detail['manager_score']): ?>
                                <div class="score-value">
                                    <strong>Quản lý:</strong> <?php echo number_format($detail['manager_score'], 1); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($detail['dev_head_score']): ?>
                                <div class="score-value">
                                    <strong>Trưởng phòng:</strong> <?php echo number_format($detail['dev_head_score'], 1); ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($detail['system_score']): ?>
                                <div class="score-value">
                                    <strong>Hệ thống:</strong> <?php echo number_format($detail['system_score'], 1); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if ($detail['head_note']): ?>
                            <div style="margin-top: 8px; font-size: 12px; color: #666; font-style: italic;">
                                Ghi chú: <?php echo htmlspecialchars($detail['head_note']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Hành động -->
        <div class="info-box">
            <div class="actions">
                <a href="/KPI/KPI/kpi-web/mapping/list" class="btn btn-secondary">← Quay lại</a>
                <?php if ($evaluation['status'] !== 'Hoàn thành'): ?>
                    <button class="btn btn-danger" onclick="rejectEvaluation(<?php echo $evaluation['id']; ?>)">
                        ✗ Từ chối
                    </button>
                    <button class="btn btn-primary" onclick="approveEvaluation(<?php echo $evaluation['id']; ?>)">
                        ✓ Phê duyệt
                    </button>
                <?php endif; ?>
            </div>
        </div>

    <?php endif; ?>

</div>

<script>
function approveEvaluation(evaluationId) {
    if (!confirm('Bạn có chắc muốn phê duyệt đánh giá này?')) return;

    fetch('/KPI/KPI/kpi-web/mapping/approve', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id: evaluationId,
            action: 'approve'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Phê duyệt thành công!');
            location.reload();
        } else {
            alert('Lỗi: ' + (data.message || 'Không xác định'));
        }
    })
    .catch(error => alert('Lỗi kết nối: ' + error));
}

function rejectEvaluation(evaluationId) {
    if (!confirm('Bạn có chắc muốn từ chối đánh giá này?')) return;

    fetch('/KPI/KPI/kpi-web/mapping/approve', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id: evaluationId,
            action: 'reject'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Từ chối thành công!');
            location.reload();
        } else {
            alert('Lỗi: ' + (data.message || 'Không xác định'));
        }
    })
    .catch(error => alert('Lỗi kết nối: ' + error));
}
</script>

</body>
</html>
