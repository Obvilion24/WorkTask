<?php 
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: menu.php'); 
    exit;
}
require_once 'includes/header.php'; 
?>

<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h3 class="text-center mb-0">Đăng Ký Tài Khoản</h3>
    </div>
    <div class="card-body">
        
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <form action="chucnang/DKDN.php" method="POST">
            <input type="hidden" name="action" value="register">
            
            <div class="mb-3">
                <label for="username" class="form-label">Tên đăng nhập</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email (Tùy chọn)</label>
                <input type="email" class="form-control" id="email" name="email">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Đăng Ký</button>
            </div>
        </form>
        <hr>
        <p class="text-center">Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a></p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>