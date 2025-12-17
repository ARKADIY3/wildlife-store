<?php
require_once 'includes/config.php';

$error = '';
$success = '';

if (!isset($_SESSION['captcha'])) {
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    $_SESSION['captcha'] = $num1 + $num2;
    $_SESSION['captcha_question'] = "$num1 + $num2";
} else {
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $captcha_answer = (int)($_POST['captcha'] ?? 0);

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Все поля обязательны для заполнения';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Некорректный email';
    } elseif ($captcha_answer !== $_SESSION['captcha']) {
        $error = 'Неверный ответ на проверочный вопрос';
    } else {
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);
        if ($stmt->execute()) {
            $success = 'Сообщение успешно отправлено! Мы свяжемся с вами в ближайшее время.';
            $name = $email = $subject = $message = '';
        } else {
            $error = 'Ошибка при отправке сообщения';
        }
        $stmt->close();
    }

    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    $_SESSION['captcha'] = $num1 + $num2;
    $_SESSION['captcha_question'] = "$num1 + $num2";
}

include 'includes/header.php';
?>

<h1>Контакты / Обратная связь</h1>

<h2>Наши контакты</h2>
<ul>
    <li>Адрес: г. Москва, ул. Компьютерная, д. 42</li>
    <li>Телефон: +7 (495) 123-45-67</li>
    <li>Email: info@fixik.ru</li>
    <li>Режим работы: Пн-Пт 9:00-20:00, Сб-Вс 10:00-18:00</li>
</ul>

<h2>Написать нам</h2>

<?php if ($error): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p style="color: green;"><?php echo $success; ?></p>
<?php endif; ?>

<form method="POST">
    <p>
        <label>Ваше имя:<br>
        <input type="text" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required></label>
    </p>
    <p>
        <label>Email:<br>
        <input type="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required></label>
    </p>
    <p>
        <label>Тема:<br>
        <input type="text" name="subject" value="<?php echo htmlspecialchars($subject ?? ''); ?>" required></label>
    </p>
    <p>
        <label>Сообщение:<br>
        <textarea name="message" rows="5" cols="40" required><?php echo htmlspecialchars($message ?? ''); ?></textarea></label>
    </p>
    <p>
        <label>Проверка (<?php echo $_SESSION['captcha_question']; ?> = ?):<br>
        <input type="number" name="captcha" required></label>
    </p>
    <p><button type="submit">Отправить</button></p>
</form>

<?php include 'includes/footer.php'; ?>
