<?php
require_once '../includes/config.php';

if (!isAdmin()) {
    redirect('../login.php');
}

$order_id = (int)($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT o.*, u.username, u.email FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    redirect('orders.php');
}

$stmt = $conn->prepare("SELECT oi.*, p.name, p.image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result();

$statuses = [
    'pending' => 'Ожидает обработки',
    'processing' => 'В обработке',
    'shipped' => 'Отправлен',
    'delivered' => 'Доставлен',
    'cancelled' => 'Отменен'
];

include 'header.php';
?>

<p><a href="orders.php">&larr; Назад к заказам</a></p>

<h1>Заказ #<?php echo $order['id']; ?></h1>

<h2>Информация о заказе</h2>
<ul>
    <li>Дата: <?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></li>
    <li>Статус: <?php echo $statuses[$order['status']] ?? $order['status']; ?></li>
    <li>Покупатель: <?php echo htmlspecialchars($order['username'] ?? 'Удален'); ?> (<?php echo htmlspecialchars($order['email'] ?? ''); ?>)</li>
    <li>Адрес: <?php echo htmlspecialchars($order['address']); ?></li>
    <li>Телефон: <?php echo htmlspecialchars($order['phone']); ?></li>
</ul>

<h2>Товары</h2>
<table border="1" cellpadding="10">
    <tr>
        <th>Фото</th>
        <th>Товар</th>
        <th>Цена</th>
        <th>Кол-во</th>
        <th>Сумма</th>
    </tr>
    <?php while ($item = $items->fetch_assoc()): ?>
        <tr>
            <td><img src="/Fixik/uploads/products/<?php echo htmlspecialchars($item['image'] ?? 'default.png'); ?>" width="60"></td>
            <td><?php echo htmlspecialchars($item['name'] ?? 'Товар удален'); ?></td>
            <td><?php echo number_format($item['price'], 0, ',', ' '); ?> ₽</td>
            <td><?php echo $item['quantity']; ?></td>
            <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', ' '); ?> ₽</td>
        </tr>
    <?php endwhile; ?>
    <tr>
        <td colspan="4" align="right"><strong>Итого:</strong></td>
        <td><strong><?php echo number_format($order['total_amount'], 0, ',', ' '); ?> ₽</strong></td>
    </tr>
</table>

<?php include 'footer.php'; ?>
