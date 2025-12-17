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
            $storedPassword = (string)($user['password'] ?? '');
            $info = password_get_info($storedPassword);

            $isValid = false;

            // Поддержка тестовых данных/старых импортов: если пароль не хэшированный — сравниваем как обычную строку
            if (($info['algo'] ?? 0) !== 0) {
                $isValid = password_verify($password, $storedPassword);
            } else {
                $isValid = hash_equals($storedPassword, $password);
            }

            if ($isValid) {
                // Если пароль был не в формате password_hash — обновляем на нормальный хэш при первом входе
                if (($info['algo'] ?? 0) === 0) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $upd->bind_param("si", $newHash, $user['id']);
                    $upd->execute();
                    $upd->close();
                }

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

<div class="auth-page">
    <h1>Вход</h1>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form class="card form-card" method="POST">
        <p>
            <label for="username">Логин или Email</label>
            <input id="username" type="text" name="username" required>
        </p>
        <p>
            <label for="password">Пароль</label>
            <input id="password" type="password" name="password" required>
        </p>
        <p class="auth-button-center"><button type="submit">Войти</button></p>
    </form>

    <p class="auth-links">Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>
</div>

<?php include 'includes/footer.php'; ?>
