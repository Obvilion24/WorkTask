<?php
session_start();
require_once '../config/db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $action = $_POST['action'] ?? '';
    if ($action == 'register') {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $email = trim($_POST['email']) ?: null; 

        if (empty($username) || empty($password)) {
            header('Location: ../register.php?error=Tên đăng nhập và mật khẩu là bắt buộc');
            exit;
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            $sql_check = "SELECT id FROM users WHERE username = :username OR (email IS NOT NULL AND email = :email)";
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->execute(['username' => $username, 'email' => $email]);
            
            if ($stmt_check->fetch()) {
                header('Location: ../register.php?error=Tên đăng nhập hoặc Email đã tồn tại');
                exit;
            }

            $sql = "INSERT INTO users (username, password, email) VALUES (:username, :password, :email)";
            $stmt = $pdo->prepare($sql);
            
            $stmt->execute([
                'username' => $username,
                'password' => $hashed_password,
                'email' => $email
            ]);

            header('Location: ../login.php?success=Đăng ký thành công! Vui lòng đăng nhập.');
            exit;

        } catch (PDOException $e) {
            header('Location: ../register.php?error=Đã xảy ra lỗi, vui lòng thử lại.');
            exit;
        }
    }
    if ($action == 'login') {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if (empty($username) || empty($password)) {
            header('Location: ../login.php?error=Vui lòng nhập đầy đủ thông tin');
            exit;
        }

        try {
            $sql = "SELECT * FROM users WHERE username = :username";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['username' => $username]);
            
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                header('Location: ../menu.php'); // Sửa: ../index.php -> ../menu.php
                exit;
            } else {
                header('Location: ../login.php?error=Tên đăng nhập hoặc mật khẩu không đúng');
                exit;
            }

        } catch (PDOException $e) {
            header('Location: ../login.php?error=Đã xảy ra lỗi: ' . $e->getMessage());
            exit;
        }
    }

} else {
    header('Location: ../menu.php'); 
    exit;
}
?>