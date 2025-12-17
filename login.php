<?php
require_once 'includes/config.php';

if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Все поля обязательны для заполнения';
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                redirect('index.php');
            } else {
                $error = 'Неверный логин или пароль';
            }
        } else {
            $error = 'Неверный логин или пароль';
        }
        $stmt->close();
    }
}

include 'includes/header.php';
?>

<h1>Вход</h1>

<?php if ($error): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST">
    <p>
        <label>Логин или Email:<br>
        <input type="text" name="username" required></label>
    </p>
    <p>
        <label>Пароль:<br>
        <input type="password" name="password" required></label>
    </p>
    <p><button type="submit">Войти</button></p>
</form>

<p>Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
<p><small>Тестовые аккаунты: admin/123456 или user/123456</small></p>

<?php include 'includes/footer.php'; ?>
