<?php
require_once 'includes/check.php'; 
require_once 'config/db.php';

$stmt_groups = $pdo->prepare("SELECT * FROM task_groups WHERE user_id = :uid ORDER BY created_at DESC");
$stmt_groups->execute(['uid' => $current_user_id]);
$groups = $stmt_groups->fetchAll();

$sort = $_GET['sort'] ?? 'due_date';
$filter_status = $_GET['filter_status'] ?? 'all';

$sql = "SELECT tasks.*, task_groups.name as group_name 
        FROM tasks 
        LEFT JOIN task_groups ON tasks.group_id = task_groups.id 
        WHERE tasks.user_id = :user_id";
$params = ['user_id' => $current_user_id];

if ($filter_status != 'all') {
    $sql .= " AND tasks.status = :status";
    $params['status'] = $filter_status;
}
$sql .= " ORDER BY $sort ASC"; 

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$all_tasks = $stmt->fetchAll();

$count_pending = 0;
$count_in_progress = 0;
$urgent_tasks = []; 

$today = date('Y-m-d');
$tomorrow = date('Y-m-d', strtotime('+1 day'));
$yesterday = date('Y-m-d', strtotime('-1 day')); 

$tasks_by_group = [];
$tasks_uncategorized = [];

foreach ($all_tasks as $task) {
    if ($task['group_id']) {
        $tasks_by_group[$task['group_id']][] = $task;
    } else {
        $tasks_uncategorized[] = $task;
    }

    if ($task['status'] == 'pending') {
        $count_pending++;
    } elseif ($task['status'] == 'in_progress') {
        $count_in_progress++;
    }

    if ($task['status'] != 'completed' && !empty($task['due_date'])) {
        if ($task['due_date'] == $yesterday) {
            $urgent_tasks[] = "Vừa quá hạn hôm qua: " . $task['title'];
        } 
        elseif ($task['due_date'] == $today) {
            $urgent_tasks[] = "Hạn chót hôm nay: " . $task['title'];
        } 
        elseif ($task['due_date'] == $tomorrow) {
            $urgent_tasks[] = "Hạn ngày mai: " . $task['title'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Công việc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    
    <style>
        body {
            background-image: url('assets/BK.jpg'); 
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-position: center center;
        }
        .card, .modal-content, .list-group-item, .accordion-item {
            background-color: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(5px);
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .accordion-button:not(.collapsed) {
            background-color: #e7f1ff;
            color: #0c63e4;
            box-shadow: none;
        }
        input::-webkit-calendar-picker-indicator {
            opacity: 1 !important;
            display: block !important;
            cursor: pointer;
        }
        .top-banner-image {
            width: 100%;
            height: 500px; 
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            background-color: #f8f9fa; 
            text-align: center; 
        }
        .top-banner-image img {
            height: 100%;       
            width: auto;        
            max-width: 100%;    
            object-fit: contain; 
        }

        .dashboard-banner {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
            border-left: 5px solid #0d6efd;
        }
        .clock-box {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            text-align: center;
            border-right: 1px solid #ddd;
        }
        .time-text { font-size: 2.5rem; font-weight: bold; color: #333; line-height: 1; }
        .date-text { font-size: 1.1rem; color: #666; margin-top: 5px;}
        
        .status-bar {
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .bar-pending { background-color: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
        .bar-progress { background-color: #fff3cd; color: #664d03; border: 1px solid #ffecb5; }
        
        .urgent-box {
            background-color: #fff;
            border: 1px dashed #dc3545;
            border-radius: 8px;
            padding: 10px;
            font-size: 0.9rem;
            max-height: 100px;
            overflow-y: auto;
        }
        .urgent-item { color: #dc3545; font-weight: bold; display: block; margin-bottom: 4px; }

        @media (max-width: 768px) {
            .clock-box { border-right: none; border-bottom: 1px solid #ddd; padding-bottom: 15px; margin-bottom: 15px; }
        }
    </style>
</head>
<body class="bg-light" onload="startTime()">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand" href="menu.php">ToDo App</a>
        <div class="d-flex align-items-center">
            <span class="navbar-text me-3 text-white d-none d-sm-block">
                Hi, <strong><?= htmlspecialchars($current_username) ?></strong>
            </span>
            <a class="btn btn-sm btn-danger" href="logout.php">Thoát</a>
        </div>
    </div>
</nav>

<div class="container mt-4">

   <div class="top-banner-image">
        <img src="assets/A1.jpg" alt="Banner Poster">
    </div>

    <div class="dashboard-banner">
        <div class="row align-items-center">
            <div class="col-md-4 clock-box">
                <div id="clock" class="time-text">--:--:--</div>
                <div id="date" class="date-text">Đang tải...</div>
            </div>
            <div class="col-md-4">
                <div class="status-bar bar-pending">
                    <span><i class="bi bi-hourglass-split me-2"></i> Công việc chưa làm</span>
                    <span class="badge bg-danger rounded-pill"><?= $count_pending ?></span>
                </div>
                <div class="status-bar bar-progress">
                    <span><i class="bi bi-gear-wide-connected me-2"></i> Đang thực hiện</span>
                    <span class="badge bg-warning text-dark rounded-pill"><?= $count_in_progress ?></span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="fw-bold mb-2 text-danger"><i class="bi bi-alarm"></i> Cần chú ý:</div>
                <div class="urgent-box">
                    <?php if (empty($urgent_tasks)): ?>
                        <span class="text-success"><i class="bi bi-check-circle"></i> Không có việc gấp!</span>
                    <?php else: ?>
                        <?php foreach ($urgent_tasks as $msg): ?>
                            <span class="urgent-item"><i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($msg) ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row align-items-center mb-4">
        <div class="col-md-6 text-md-start text-center mb-3 mb-md-0">
            <button type="button" class="btn btn-primary btn-lg shadow-sm px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                <i class="bi bi-plus-lg me-1"></i> Thêm Mới
            </button>
        </div>
        <div class="col-md-6 d-flex justify-content-md-end justify-content-center">
            <form method="GET" action="menu.php" class="d-flex gap-2 align-items-center bg-white p-2 rounded shadow-sm" style="opacity: 0.9;">
                <label class="small fw-bold">Lọc:</label>
                <select name="filter_status" class="form-select form-select-sm border-0 bg-light" onchange="this.form.submit()">
                    <option value="all" <?= $filter_status == 'all' ? 'selected' : '' ?>>Tất cả</option>
                    <option value="pending" <?= $filter_status == 'pending' ? 'selected' : '' ?>>Chưa làm</option>
                    <option value="in_progress" <?= $filter_status == 'in_progress' ? 'selected' : '' ?>>Đang làm</option>
                    <option value="completed" <?= $filter_status == 'completed' ? 'selected' : '' ?>>Xong</option>
                </select>
            </form>
        </div>
    </div>

    <div class="modal fade" id="addTaskModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">Thêm Công Việc</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="chucnang/CRUD.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="create">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Tiêu đề (*)</label>
                            <input type="text" class="form-control" name="title" required placeholder="Nhập tên công việc...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nhóm / Dự án</label>
                            <input class="form-control" list="groupOptions" name="group_name" placeholder="Chọn hoặc gõ tên nhóm mới...">
                            <datalist id="groupOptions">
                                <?php foreach ($groups as $g): ?>
                                    <option value="<?= htmlspecialchars($g['name']) ?>">
                                <?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Hạn chót</label>
                            <input type="date" class="form-control" name="due_date">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Mô tả chi tiết</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary px-4">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="accordion pb-5" id="taskAccordion">
        <?php foreach ($groups as $group): ?>
            <?php 
                $g_id = $group['id'];
                $tasks_in_group = $tasks_by_group[$g_id] ?? [];
                $count = count($tasks_in_group);
            ?>
            <div class="accordion-item mb-3 shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $g_id ?>">
                        <i class="bi bi-folder2-open me-2 text-warning"></i> <?= htmlspecialchars($group['name']) ?> 
                        <span class="badge bg-light text-dark ms-2 border"><?= $count ?></span>
                    </button>
                </h2>
                <div id="collapse<?= $g_id ?>" class="accordion-collapse collapse" data-bs-parent="#taskAccordion">
                    <div class="accordion-body p-0">
                        <div class="text-end p-2 bg-light border-bottom">
                            <form action="chucnang/CRUD.php" method="POST" onsubmit="return confirm('Xóa nhóm này?');">
                                <input type="hidden" name="action" value="delete_group">
                                <input type="hidden" name="group_id" value="<?= $g_id ?>">
                                <button class="btn btn-xs btn-outline-danger border-0 small">Xóa nhóm</button>
                            </form>
                        </div>
                        <?php if (empty($tasks_in_group)): ?>
                            <div class="p-3 text-center text-muted small">Trống</div>
                        <?php else: ?>
                            <?php renderTasks($tasks_in_group); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (!empty($tasks_uncategorized)): ?>
            <div class="accordion-item mb-3 shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUncat">
                        <i class="bi bi-list-task me-2 text-primary"></i> Công việc chung
                        <span class="badge bg-light text-dark ms-2 border"><?= count($tasks_uncategorized) ?></span>
                    </button>
                </h2>
                <div id="collapseUncat" class="accordion-collapse collapse show" data-bs-parent="#taskAccordion">
                    <div class="accordion-body p-0">
                        <?php renderTasks($tasks_uncategorized); ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div> 

<?php function renderTasks($list) { ?>
    <div class="list-group list-group-flush">
        <?php foreach ($list as $task): ?>
            <?php
            $bg = '';
            if ($task['status'] == 'pending') $bg = 'text-bg-danger';
            elseif ($task['status'] == 'in_progress') $bg = 'text-bg-warning';
            elseif ($task['status'] == 'completed') $bg = 'text-bg-success';
            ?>
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-truncate pe-2">
                        <div class="fw-bold"><?= htmlspecialchars($task['title']) ?></div>
                        <?php if($task['due_date']): ?>
                            <small class="text-muted"><i class="bi bi-clock"></i> <?= date('d/m', strtotime($task['due_date'])) ?></small>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <form action="chucnang/CRUD.php" method="POST">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                            <select name="status" class="form-select form-select-sm <?= $bg ?>" style="width:auto; font-size:0.8rem; font-weight:bold;" onchange="this.form.submit()">
                                <option value="pending" style="background:white;color:black" <?= $task['status']=='pending'?'selected':'' ?>>Chưa làm</option>
                                <option value="in_progress" style="background:white;color:black" <?= $task['status']=='in_progress'?'selected':'' ?>>Đang làm</option>
                                <option value="completed" style="background:white;color:black" <?= $task['status']=='completed'?'selected':'' ?>>Xong</option>
                            </select>
                        </form>
                        <button class="btn btn-sm btn-link text-secondary" data-bs-toggle="modal" data-bs-target="#editTaskModal<?= $task['id'] ?>">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="chucnang/CRUD.php" method="POST" onsubmit="return confirm('Xóa?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                            <button class="btn btn-sm btn-link text-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php } ?>

<?php foreach ($all_tasks as $task): ?>
    <div class="modal fade" id="editTaskModal<?= $task['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning-subtle">
                    <h6 class="modal-title">Sửa: <?= htmlspecialchars($task['title']) ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="chucnang/CRUD.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_details">
                        <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Tiêu đề</label>
                            <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($task['title']) ?>" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Nhóm</label>
                            <input class="form-control" list="groupListEdit<?= $task['id'] ?>" name="group_name" value="<?= htmlspecialchars($task['group_name'] ?? '') ?>">
                            <datalist id="groupListEdit<?= $task['id'] ?>">
                                <?php foreach ($groups as $g): ?><option value="<?= htmlspecialchars($g['name']) ?>"><?php endforeach; ?>
                            </datalist>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Hạn</label>
                            <input type="date" class="form-control" name="due_date" value="<?= $task['due_date'] ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-bold">Mô tả</label>
                            <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($task['description']) ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function startTime() {
        const today = new Date();
        let h = today.getHours();
        let m = today.getMinutes();
        let s = today.getSeconds();
        let day = today.getDate();
        let month = today.getMonth() + 1;
        let year = today.getFullYear();
        m = checkTime(m);
        s = checkTime(s);
        day = checkTime(day);
        month = checkTime(month);
        document.getElementById('clock').innerHTML =  h + ":" + m + ":" + s;
        document.getElementById('date').innerHTML = "Ngày " + day + "/" + month + "/" + year;
        setTimeout(startTime, 1000);
    }
    function checkTime(i) { if (i < 10) {i = "0" + i}; return i; }
</script>
</body>
</html>