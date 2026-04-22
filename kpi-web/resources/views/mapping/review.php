<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xét Duyệt Người Đánh Giá - KPI System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 20px;
        }

        h1 {
            color: #333;
            margin-bottom: 30px;
            font-size: 24px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }

        .toolbar {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        .toolbar select, .toolbar input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .toolbar select {
            cursor: pointer;
            background: white;
        }

        .toolbar button {
            padding: 8px 16px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .toolbar button:hover {
            background: #764ba2;
        }

        .toolbar button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table thead {
            background: #667eea;
            color: white;
        }

        table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            border: 1px solid #ddd;
        }

        table td {
            padding: 12px;
            border: 1px solid #ddd;
            font-size: 13px;
        }

        table tbody tr {
            transition: background 0.2s;
        }

        table tbody tr:hover {
            background: #f9f9f9;
        }

        table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .checkbox {
            cursor: pointer;
            width: 18px;
            height: 18px;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-waiting {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }

        .pagination input {
            width: 50px;
            text-align: center;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 8px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }

        .modal h2 {
            margin-bottom: 20px;
            color: #333;
            font-size: 18px;
        }

        .modal-body {
            margin-bottom: 20px;
        }

        .modal-body p {
            line-height: 1.6;
            color: #666;
            margin-bottom: 12px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 13px;
        }

        .form-group select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
        }

        .modal-footer {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .modal-footer button {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s;
        }

        .btn-cancel {
            background: #e9ecef;
            color: #333;
        }

        .btn-cancel:hover {
            background: #dee2e6;
        }

        .btn-confirm {
            background: #667eea;
            color: white;
        }

        .btn-confirm:hover {
            background: #764ba2;
        }

        .btn-nav {
            padding: 8px 16px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s;
        }

        .btn-nav:hover {
            background: #764ba2;
        }
    </style>
</head>
<body>
    <header style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; margin: -20px -20px 20px -20px;">
        <h1 style="margin: 0; font-size: 20px;">HỆ THỐNG LULINK</h1>
        <div style="display: flex; align-items: center; gap: 15px;">
            <span>Xin chào, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
            <a href="/KPI/KPI/kpi-web/logout" style="color: white; text-decoration: none; padding: 8px 15px; background-color: rgba(255, 255, 255, 0.2); border-radius: 4px;">Đăng xuất</a>
        </div>
    </header>
    <div class="container">
        <h1>XÉT DUYỆT NGƯỜI ĐÁNH GIÁ</h1>

        <?php
// Initialize variables
if (!isset($periods)) $periods = [];
if (!isset($evaluators)) $evaluators = [];
if (!isset($evaluations)) $evaluations = [];
if (!isset($total_pages)) $total_pages = 1;
if (!isset($success_message)) $success_message = null;
if (!isset($error_message)) $error_message = null;

// Check session messages
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
?>

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                ✓ <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-error">
                ✗ <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" id="reviewForm" style="display: none;">
            <input type="hidden" name="action" id="actionInput">
            <input type="hidden" name="selected_ids" id="selectedIds">
            <input type="hidden" name="evaluator_id" id="evaluatorId">
        </form>

        <!-- Toolbar -->
        <div class="toolbar">
            <select id="actionSelect" onchange="handleActionChange()">
                <option value="">-- Chọn hành động --</option>
                <option value="approve">Phê duyệt</option>
                <option value="edit">Chỉnh sửa người đánh giá</option>
            </select>
            <button type="button" onclick="executeAction()" id="actionBtn" disabled>
                Thực hiện
            </button>
            <select id="periodFilter" onchange="filterByPeriod()" style="margin-left: auto;">
                <option value="0">-- Tất cả kỳ đánh giá --</option>
                <?php foreach ($periods as $period): ?>
                    <option value="<?php echo $period['id']; ?>" <?php echo (isset($selectedPeriodId) && $selectedPeriodId == $period['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($period['period_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Table -->
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 40px;">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="checkbox">
                        </th>
                        <th>ID</th>
                        <th>Họ và tên</th>
                        <th>Phòng ban</th>
                        <th>Quản lý cấp 1</th>
                        <th>Kỳ đánh giá</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php foreach ($evaluations as $eval): ?>
                        <tr data-eval-id="<?php echo $eval['id']; ?>">
                            <td>
                                <input type="checkbox" class="checkbox row-checkbox" 
                                    value="<?php echo $eval['id']; ?>" 
                                    onchange="updateSelectAll()">
                            </td>
                            <td><?php echo htmlspecialchars($eval['id']); ?></td>
                            <td><?php echo htmlspecialchars($eval['member_name']); ?></td>
                            <td><?php echo htmlspecialchars($eval['business_unit'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($eval['manager_name'] ?? 'Chưa gán'); ?></td>
                            <td><?php echo htmlspecialchars($eval['period_name'] ?? 'N/A'); ?></td>
                           <td>
                            <?php 
                                $statusMap = [
                                    // Khi chưa duyệt -> Để trống hoàn toàn
                                    'Chưa đánh giá' => ['label' => '', 'class' => ''], 

                                    // Sau khi Thư ký duyệt (status đã đổi thành Chờ duyệt trong DB) -> Hiện chữ
                                    'Chờ duyệt'     => ['label' => 'Chờ PM đánh giá', 'class' => 'status-waiting'],

                                    'Đánh giá lại'  => ['label' => 'Yêu cầu đánh giá lại', 'class' => 'status-rejected'],
                                    'Chờ HRBP'      => ['label' => 'Chờ HRBP phê duyệt', 'class' => 'status-approved'],
                                    'Hoàn thành'    => ['label' => 'Đã hoàn thành', 'class' => 'status-approved']
                                ];

                                $currentStatus = $eval['status'];
                                $displayInfo = $statusMap[$currentStatus] ?? ['label' => $currentStatus, 'class' => ''];
                                
                                if (!empty($displayInfo['label'])): 
                            ?>
                                <span class="status-badge <?php echo $displayInfo['class']; ?>">
                                    <?php echo htmlspecialchars($displayInfo['label']); ?>
                                </span>
                            <?php endif; ?>
                        </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
       <div class="pagination" style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
            <div>
                <select onchange="changePageSize(this.value)" style="width: auto; padding: 5px; border-radius: 4px;">
                    <?php 
                        $sizes = [20, 50, 100];
                        foreach ($sizes as $size): 
                    ?>
                        <option value="<?php echo $size; ?>" <?php echo ($perPage == $size) ? 'selected' : ''; ?>>
                            <?php echo $size; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span> bản ghi / trang</span>
            </div>

            <div style="font-size: 14px; color: #333;">
                Trang <strong><?php echo $page; ?></strong> 
                / <span id="totalPages"><?php echo $total_pages; ?></span>
            </div>
            <div style="display: flex; gap: 10px;">
                <button onclick="changePage(<?php echo $page - 1; ?>)" 
                        class="btn-nav" 
                        <?php echo ($page <= 1) ? 'disabled style="background:#ccc; cursor: not-allowed;"' : ''; ?>>
                    ← Trước
                </button>

                <button onclick="changePage(<?php echo $page + 1; ?>)" 
                        class="btn-nav" 
                        <?php echo ($page >= $total_pages) ? 'disabled style="background:#ccc; cursor: not-allowed;"' : ''; ?>>
                    Sau →
                </button>
            </div>
        </div>

    <!-- Modal Approve -->
    <div id="approveModal" class="modal">
        <div class="modal-content">
            <h2>Phê duyệt đánh giá</h2>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn phê duyệt <strong id="approveCount">0</strong> nhân viên này?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('approveModal')">Hủy</button>
                <button type="button" class="btn-confirm" onclick="confirmApprove()">OK</button>
            </div>
        </div>
    </div>

    <!-- Modal Edit Evaluator -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>Chỉnh sửa người đánh giá</h2>
            <div class="modal-body">
                <p>Chọn người đánh giá mới cho <strong id="editCount">0</strong> nhân viên:</p>
                <div class="form-group">
                    <label for="evaluatorSelect">Người đánh giá:</label>
                    <select id="evaluatorSelect">
                        <option value="">-- Chọn người đánh giá --</option>
                        <?php foreach ($evaluators as $evaluator): ?>
                            <option value="<?php echo $evaluator['id']; ?>">
                                <?php echo htmlspecialchars($evaluator['username']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('editModal')">Hủy</button>
                <button type="button" class="btn-confirm" onclick="confirmEdit()">Áp dụng</button>
            </div>
        </div>
    </div>

    <script>
        function handleActionChange() {
            const action = document.getElementById('actionSelect').value;
            const btn = document.getElementById('actionBtn');
            btn.disabled = !action;
        }

        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll').checked;
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.checked = selectAll;
            });
        }

        function updateSelectAll() {
            const allChecked = document.querySelectorAll('.row-checkbox').length > 0 &&
                             Array.from(document.querySelectorAll('.row-checkbox')).every(cb => cb.checked);
            document.getElementById('selectAll').checked = allChecked;
        }

        function getSelectedIds() {
            return Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
        }

        function executeAction() {
            const action = document.getElementById('actionSelect').value;
            const selectedIds = getSelectedIds();

            if (selectedIds.length === 0) {
                alert('Vui lòng chọn ít nhất 1 nhân viên');
                return;
            }

            if (action === 'approve') {
                document.getElementById('approveCount').textContent = selectedIds.length;
                openModal('approveModal');
            } else if (action === 'edit') {
                document.getElementById('editCount').textContent = selectedIds.length;
                openModal('editModal');
            }
        }

        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        function confirmApprove() {
        const selectedIds = getSelectedIds();
        document.getElementById('actionInput').value = 'approve'; // Phải có dòng này!
        document.getElementById('selectedIds').value = JSON.stringify(selectedIds);
        submitForm();
}

        function confirmEdit() {
            const evaluatorId = document.getElementById('evaluatorSelect').value;
            if (!evaluatorId) {
                alert('Vui lòng chọn người đánh giá');
                return;
            }

            const selectedIds = getSelectedIds();
            document.getElementById('actionInput').value = 'edit';
            document.getElementById('selectedIds').value = JSON.stringify(selectedIds);
            document.getElementById('evaluatorId').value = evaluatorId;
            submitForm();
        }

        function submitForm() {
            const form = document.getElementById('reviewForm');
            form.method = 'POST';
            form.action = 'review-approve';
            form.submit();
        }

        function filterByPeriod() {
            const periodId = document.getElementById('periodFilter').value;
            // Sử dụng URLSearchParams để giữ nguyên cấu trúc URL hiện tại và chỉ đổi param period_id
            const url = new URL(window.location.href);
            if (periodId && periodId !== "0") {
                url.searchParams.set('period_id', periodId);
            } else {
                url.searchParams.delete('period_id');
            }
            window.location.href = url.toString();
        }

        function changePageSize(size) {
            // 1. Lấy tất cả các tham số hiện có trên URL (để không làm mất bộ lọc)
            const urlParams = new URLSearchParams(window.location.search);
            
            // 2. Cập nhật tham số 'per_page' bằng giá trị 'size' mới
            urlParams.set('per_page', size);
            
            // 3. Quan trọng: Khi đổi số lượng bản ghi, phải đưa về trang 1
            urlParams.set('page', 1);
            
            // 4. Chuyển hướng trang với bộ tham số mới
            window.location.href = window.location.pathname + '?' + urlParams.toString();
        }

       function changePage(pageNumber) {
            // Lấy URL hiện tại
            const url = new URL(window.location.href);
            
            // Cập nhật tham số page
            url.searchParams.set('page', pageNumber);
            
            // Giữ lại các bộ lọc khác (như period_id) nếu có
            // Chuyển hướng trang
            window.location.href = url.toString();
        }

        // Cập nhật lại các hàm cũ để gọi về hàm mới (nếu em lỡ dùng onclick cũ)
        function prevPage() { 
            const curPage = parseInt(document.getElementById('currentPage').value);
            if(curPage > 1) changePage(curPage - 1);
        }

        function nextPage() {
            const curPage = parseInt(document.getElementById('currentPage').value);
            // Lưu ý: total_pages phải được truyền từ PHP ra một biến JS hoặc lấy từ giao diện
            const totalPages = <?php echo $total_pages; ?>; 
            if(curPage < totalPages) changePage(curPage + 1);
        }
        // Close modals on outside click
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.remove('active');
            }
        }
    </script>
</body>
</html>
