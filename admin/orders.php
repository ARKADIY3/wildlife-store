<?php
require_once '../includes/config.php';

if (!isAdmin()) {
    redirect('../login.php');
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $allowed_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    
    if (in_array($status, $allowed_statuses)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
        $stmt->execute();
        $message = 'Статус заказа обновлен';
    }
}

$orders = $conn->query("SELECT o.*, u.username FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");

$statuses = [
    'pending' => 'Ожидает обработки',
    'processing' => 'В обработке',
    'shipped' => 'Отправлен',
    'delivered' => 'Доставлен',
    'cancelled' => 'Отменен'
];

include 'header.php';
?>

<h1>Управление заказами</h1>

<?php if ($message): ?>
    <p style="color: green;"><?php echo $message; ?></p>
<?php endif; ?>

<table border="1" cellpadding="10">
    <tr>
        <th>№</th>
        <th>Дата</th>
        <th>Покупатель</th>
        <th>Сумма</th>
        <th>Адрес</th>
        <th>Телефон</th>
        <th>Статус</th>
        <th>Действия</th>
    </tr>
    <?php while ($order = $orders->fetch_assoc()): ?>
        <tr>
            <td>#<?php echo $order['id']; ?></td>
            <td><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></td>
            <td><?php echo htmlspecialchars($order['username'] ?? 'Удален'); ?></td>
            <td><?php echo number_format($order['total_amount'], 0, ',', ' '); ?> ₽</td>
            <td><?php echo htmlspecialchars($order['address']); ?></td>
            <td><?php echo htmlspecialchars($order['phone']); ?></td>
            <td>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <select name="status" onchange="this.form.submit()">
                        <?php foreach ($statuses as $key => $value): ?>
                            <option value="<?php echo $key; ?>" <?php echo $order['status'] === $key ? 'selected' : ''; ?>><?php echo $value; ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </td>
            <td><a href="order_detail.php?id=<?php echo $order['id']; ?>">Подробнее</a></td>
        </tr>
    <?php endwhile; ?>
</table>

<?php include 'footer.php'; ?>
