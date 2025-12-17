<?php
require_once 'includes/config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Все поля обязательны для заполнения';
    } elseif ($password !== $confirm_password) {
        $error = 'Пароли не совпадают';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен быть не менее 6 символов';
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Пользователь с таким именем или email уже существует';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            if ($stmt->execute()) {
                $success = 'Регистрация успешна! <a href="login.php">Войти</a>';
            } else {
                $error = 'Ошибка при регистрации';
            }
        }
        $stmt->close();
    }
}

include 'includes/header.php';
?>

<div class="auth-page">
    <h1>Регистрация</h1>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form class="card form-card" method="POST">
        <p>
            <label for="username">Имя пользователя</label>
            <input id="username" type="text" name="username" required>
        </p>
        <p>
            <label for="email">Email</label>
            <input id="email" type="email" name="email" required>
        </p>
        <p>
            <label for="password">Пароль</label>
            <input id="password" type="password" name="password" required>
        </p>
        <p>
            <label for="confirm_password">Подтвердите пароль</label>
            <input id="confirm_password" type="password" name="confirm_password" required>
        </p>
        <p class="auth-button-center"><button type="submit">Зарегистрироваться</button></p>
    </form>

    <p class="auth-links">Уже есть аккаунт? <a href="login.php">Войти</a></p>
</div>

<?php include 'includes/footer.php'; ?>
