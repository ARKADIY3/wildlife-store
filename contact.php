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

<div class="stack-center">
    <div class="card card-pad">
        <h2>Написать нам</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <p>
                <label for="name">Ваше имя</label>
                <input id="name" type="text" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
            </p>
            <p>
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            </p>
            <p>
                <label for="subject">Тема</label>
                <input id="subject" type="text" name="subject" value="<?php echo htmlspecialchars($subject ?? ''); ?>" required>
            </p>
            <p>
                <label for="message">Сообщение</label>
                <textarea id="message" name="message" rows="5" required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
            </p>
            <p>
                <label for="captcha">Проверка (<?php echo $_SESSION['captcha_question']; ?> = ?)</label>
                <input id="captcha" type="number" name="captcha" required>
            </p>
            <p><button type="submit">Отправить</button></p>
        </form>
    </div>

    <div class="card card-pad">
        <h2>Наши контакты</h2>
        <ul>
            <li>Адрес: г. Белореченск, ул. Комсомольска, д. 20</li>
            <li>Телефон: +7 (495) 123-45-67</li>
            <li>Email: info@fixik.ru</li>
            <li>Режим работы: Пн-Пт 9:00-20:00, Сб-Вс 10:00-18:00</li>
        </ul>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
