<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fixik - Интернет-магазин компьютеров</title>
</head>
<body>
<header>
    <nav>
        <a href="/Fixik/index.php"><strong>Fixik</strong></a> | 
        <a href="/Fixik/catalog.php">Каталог</a> | 
        <a href="/Fixik/contact.php">Контакты</a> | 
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/Fixik/cart.php">Корзина</a> | 
            <a href="/Fixik/orders.php">Мои заказы</a> | 
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="/Fixik/admin/index.php">Админ-панель</a> | 
            <?php endif; ?>
            <span>Привет, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span> | 
            <a href="/Fixik/logout.php">Выход</a>
        <?php else: ?>
            <a href="/Fixik/login.php">Вход</a> | 
            <a href="/Fixik/register.php">Регистрация</a>
        <?php endif; ?>
    </nav>
    <hr>
</header>
<main>
