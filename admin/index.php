<?php
require_once '../includes/config.php';

if (!isAdmin()) {
    redirect('../login.php');
}

$products_count = $conn->query("SELECT COUNT(*) as cnt FROM products")->fetch_assoc()['cnt'];
$users_count = $conn->query("SELECT COUNT(*) as cnt FROM users")->fetch_assoc()['cnt'];
$orders_count = $conn->query("SELECT COUNT(*) as cnt FROM orders")->fetch_assoc()['cnt'];
$contacts_count = $conn->query("SELECT COUNT(*) as cnt FROM contacts WHERE is_read = 0")->fetch_assoc()['cnt'];

include 'header.php';
?>

<h1>Админ-панель Fixik</h1>

<div class="card card-pad">
    <h2>Статистика</h2>
    <ul>
        <li>Товаров: <?php echo $products_count; ?></li>
        <li>Пользователей: <?php echo $users_count; ?></li>
        <li>Заказов: <?php echo $orders_count; ?></li>
        <li>Новых сообщений: <?php echo $contacts_count; ?></li>
    </ul>
</div>

<div class="card card-pad">
    <h2>Управление</h2>
    <ul>
        <li><a href="products.php">Управление товарами</a></li>
        <li><a href="categories.php">Управление категориями</a></li>
        <li><a href="orders.php">Управление заказами</a></li>
        <li><a href="users.php">Управление пользователями</a></li>
        <li><a href="contacts.php">Сообщения обратной связи</a></li>
    </ul>
</div>

<?php include 'footer.php'; ?>
