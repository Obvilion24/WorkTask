<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    die('Bạn phải đăng nhập.');
}

$current_user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';

    try {
        switch ($action) {

            case 'create':
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
                $sql = "INSERT INTO tasks (user_id, group_id, title, description, due_date, status) 
                        VALUES (:user_id, :group_id, :title, :description, :due_date, 'pending')";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'user_id' => $current_user_id, 'group_id' => $group_id,
                    'title' => $title, 'description' => $_POST['description'], 
                    'due_date' => !empty($_POST['due_date']) ? $_POST['due_date'] : null
                ]);
                break;

            case 'update_status':
                $task_id = $_POST['task_id'];
                $new_status = $_POST['status'];

                $stmt_old = $pdo->prepare("SELECT status FROM tasks WHERE id = :tid AND user_id = :uid");
                $stmt_old->execute(['tid' => $task_id, 'uid' => $current_user_id]);
                $old_status = $stmt_old->fetchColumn();

                $sql = "UPDATE tasks SET status = :status WHERE id = :task_id AND user_id = :user_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['status' => $new_status, 'task_id' => $task_id, 'user_id' => $current_user_id]);

                if ($new_status == 'completed' && $old_status != 'completed') {
                    $stmt_reward = $pdo->prepare("UPDATE users SET reward_progress = reward_progress + 1 WHERE id = :user_id");
                    $stmt_reward->execute(['user_id' => $current_user_id]);
                }
                break;

            case 'update_details':
                $task_id = $_POST['task_id'];
                $title = trim($_POST['title']);
                $group_name = trim($_POST['group_name'] ?? '');
                $group_id = null;
                if (!empty($group_name)) {
                    $stmt_check = $pdo->prepare("SELECT id FROM task_groups WHERE name = :name AND user_id = :user_id");
                    $stmt_check->execute(['name' => $group_name, 'user_id' => $current_user_id]);
                    $existing = $stmt_check->fetch();
                    if ($existing) { $group_id = $existing['id']; } 
                    else {
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

            case 'delete':
                $sql = "DELETE FROM tasks WHERE id = :task_id AND user_id = :user_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['task_id' => $_POST['task_id'], 'user_id' => $current_user_id]);
                break;

            case 'delete_group':
                $sql = "DELETE FROM task_groups WHERE id = :group_id AND user_id = :user_id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['group_id' => $_POST['group_id'], 'user_id' => $current_user_id]);
                break;

            case 'claim_reward':

                $stmt_check = $pdo->prepare("SELECT reward_progress FROM users WHERE id = :user_id");
                $stmt_check->execute(['user_id' => $current_user_id]);
                $progress = $stmt_check->fetchColumn();
                if ($progress >= 5) {
                    $stmt_claim = $pdo->prepare("UPDATE users SET reward_progress = reward_progress - 5 WHERE id = :user_id");
                    $stmt_claim->execute(['user_id' => $current_user_id]);
                    header('Location: ../menu.php?reward=1');
                    exit;
                }
                break;
        }
    } catch (PDOException $e) {
        die("Lỗi: " . $e->getMessage());
    }
    header('Location: ../menu.php');
    exit;
}
?>