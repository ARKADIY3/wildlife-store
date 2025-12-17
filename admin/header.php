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
    <title>Админ-панель - Fixik</title>
</head>
<body>
<header>
    <nav>
        <strong>Админ-панель Fixik</strong> | 
        <a href="/Fixik/admin/index.php">Главная</a> | 
        <a href="/Fixik/admin/products.php">Товары</a> | 
        <a href="/Fixik/admin/categories.php">Категории</a> | 
        <a href="/Fixik/admin/orders.php">Заказы</a> | 
        <a href="/Fixik/admin/users.php">Пользователи</a> | 
        <a href="/Fixik/admin/contacts.php">Сообщения</a> | 
        <a href="/Fixik/index.php">На сайт</a> | 
        <a href="/Fixik/logout.php">Выход</a>
    </nav>
    <hr>
</header>
<main>
