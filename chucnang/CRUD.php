<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    die('Bạn phải đăng nhập để thực hiện hành động này.');
}

$current_user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    try {
        switch ($action) {
            // (C) TẠO MỚI
            case 'create':
                $title = trim($_POST['title']);
                $group_name = trim($_POST['group_name'] ?? '');
                $description = trim($_POST['description']) ?: null;
                $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;

                if (empty($title)) die('Tiêu đề là bắt buộc.');

                $group_id = null;
                // Xử lý Nhóm (Bảng task_groups)
                if (!empty($group_name)) {
                    $stmt_check = $pdo->prepare("SELECT id FROM task_groups WHERE name = :name AND user_id = :user_id");
                    $stmt_check->execute(['name' => $group_name, 'user_id' => $current_user_id]);
                    $existing = $stmt_check->fetch();

                    if ($existing) {
                        $group_id = $existing['id'];
                    } else {
                        $stmt_g = $pdo->prepare("INSERT INTO task_groups (user_id, name) VALUES (:u, :n)");
                        $stmt_g->execute(['u' => $current_user_id, 'n' => $group_name]);
                        $group_id = $pdo->lastInsertId();
                    }
                }

                $sql = "INSERT INTO tasks (user_id, group_id, title, description, due_date, status) 
                        VALUES (:user_id, :group_id, :title, :description, :due_date, 'pending')";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'user_id' => $current_user_id, 'group_id' => $group_id,
                    'title' => $title, 'description' => $description, 'due_date' => $due_date
                ]);
                break;

            // (U) CẬP NHẬT TRẠNG THÁI
            case 'update_status':
                $sql = "UPDATE tasks SET status = :status WHERE id = :task_id AND user_id = :user_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['status' => $_POST['status'], 'task_id' => $_POST['task_id'], 'user_id' => $current_user_id]);
                break;

            // (U) CẬP NHẬT CHI TIẾT
            case 'update_details':
                $task_id = $_POST['task_id'];
                $title = trim($_POST['title']);
                $group_name = trim($_POST['group_name'] ?? '');
                
                $group_id = null;
                if (!empty($group_name)) {
                    $stmt_check = $pdo->prepare("SELECT id FROM task_groups WHERE name = :name AND user_id = :user_id");
                    $stmt_check->execute(['name' => $group_name, 'user_id' => $current_user_id]);
                    $existing = $stmt_check->fetch();
                    if ($existing) {
                        $group_id = $existing['id'];
                    } else {
                        $stmt_g = $pdo->prepare("INSERT INTO task_groups (user_id, name) VALUES (:u, :n)");
                        $stmt_g->execute(['u' => $current_user_id, 'n' => $group_name]);
                        $group_id = $pdo->lastInsertId();
                    }
                }

                $sql = "UPDATE tasks SET title = :title, group_id = :group_id, description = :description, due_date = :due_date 
                        WHERE id = :task_id AND user_id = :user_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'title' => $title, 'group_id' => $group_id, 'description' => $_POST['description'], 
                    'due_date' => !empty($_POST['due_date']) ? $_POST['due_date'] : null,
                    'task_id' => $task_id, 'user_id' => $current_user_id
                ]);
                break;

            // (D) XÓA CÔNG VIỆC
            case 'delete':
                $sql = "DELETE FROM tasks WHERE id = :task_id AND user_id = :user_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['task_id' => $_POST['task_id'], 'user_id' => $current_user_id]);
                break;
                
            // (D) XÓA NHÓM
            case 'delete_group':
                $sql = "DELETE FROM task_groups WHERE id = :group_id AND user_id = :user_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['group_id' => $_POST['group_id'], 'user_id' => $current_user_id]);
                break;
        }
    } catch (PDOException $e) {
        die("Lỗi: " . $e->getMessage());
    }
    header('Location: ../menu.php');
    exit;
}
?>