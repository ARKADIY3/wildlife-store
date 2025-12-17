<?php
require_once 'includes/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();

include 'includes/header.php';
?>

<h1>Мои заказы</h1>

<?php if (isset($_GET['msg'])): ?>
    <p style="color: green;"><?php echo htmlspecialchars($_GET['msg']); ?></p>
<?php endif; ?>

<?php if ($orders->num_rows > 0): ?>
    <table border="1" cellpadding="10">
        <tr>
            <th>№ заказа</th>
            <th>Дата</th>
            <th>Сумма</th>
            <th>Статус</th>
            <th>Действия</th>
        </tr>
        <?php while ($order = $orders->fetch_assoc()): ?>
            <tr>
                <td>#<?php echo $order['id']; ?></td>
                <td><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></td>
                <td><?php echo number_format($order['total_amount'], 0, ',', ' '); ?> ₽</td>
                <td>
                    <?php
                    $statuses = [
                        'pending' => 'Ожидает обработки',
                        'processing' => 'В обработке',
                        'shipped' => 'Отправлен',
                        'delivered' => 'Доставлен',
                        'cancelled' => 'Отменен'
                    ];
                    echo $statuses[$order['status']] ?? $order['status'];
                    ?>
                </td>
                <td><a href="order_detail.php?id=<?php echo $order['id']; ?>">Подробнее</a></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>У вас пока нет заказов</p>
    <p><a href="catalog.php">Перейти в каталог</a></p>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
