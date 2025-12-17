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

<h1>Регистрация</h1>

<?php if ($error): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p style="color: green;"><?php echo $success; ?></p>
<?php endif; ?>

<form method="POST">
    <p>
        <label>Имя пользователя:<br>
        <input type="text" name="username" required></label>
    </p>
    <p>
        <label>Email:<br>
        <input type="email" name="email" required></label>
    </p>
    <p>
        <label>Пароль:<br>
        <input type="password" name="password" required></label>
    </p>
    <p>
        <label>Подтвердите пароль:<br>
        <input type="password" name="confirm_password" required></label>
    </p>
    <p><button type="submit">Зарегистрироваться</button></p>
</form>

<p>Уже есть аккаунт? <a href="login.php">Войти</a></p>

<?php include 'includes/footer.php'; ?>
