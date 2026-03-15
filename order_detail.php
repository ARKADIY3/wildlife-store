<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'includes/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$order_id = (int)($_GET['id'] ?? 0);

if ($order_id <= 0) {
    redirect('orders.php');
}

// Получаем информацию о заказе
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    redirect('orders.php?msg=Заказ+не+найден');
}

// Получаем товары заказа
$stmt = $conn->prepare("
    SELECT oi.*, p.name, p.image 
    FROM order_items oi 
    LEFT JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items_result = $stmt->get_result();
$items = $items_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <div style="margin-bottom: 1rem;">
        <a href="orders.php" class="btn btn-outline btn-sm">&larr; Назад к заказам</a>
    </div>

    <div class="card">
        <h1>Заказ #<?php echo $order['id']; ?></h1>
        
        <div class="product-detail">
            <!-- Информация о заказе -->
            <div>
                <h2>Детали заказа</h2>
                <div class="product-specs-card">
                    <div class="spec-row">
                        <span class="spec-label">Дата создания:</span>
                        <span class="spec-value"><?php echo date('d.m.Y H:i', strtotime($order['created_at'])); ?></span>
                    </div>
                    <div class="spec-row">
                        <span class="spec-label">Статус:</span>
                        <span class="spec-value">
                            <span style="padding: 0.25rem 0.5rem; border-radius: 9999px; background: <?php 
                                echo $order['status'] == 'delivered' ? '#ecfdf5' : 
                                     ($order['status'] == 'pending' ? '#fef3c7' : '#dbeafe');
                            ?>; color: <?php 
                                echo $order['status'] == 'delivered' ? '#065f46' : 
                                     ($order['status'] == 'pending' ? '#d97706' : '#1e40af');
                            ?>; font-weight: 500;">
                                <?php
                                $statuses = [
                                    'pending' => 'Ожидает обработки',
                                    'processing' => 'В обработке',
                                    'shipped' => 'Отправлен',
                                    'delivered' => 'Доставлен',
                                    'cancelled' => 'Отменен'
                                ];
                                echo $statuses[$order['status']] ?? ucfirst($order['status']);
                                ?>
                            </span>
                        </span>
                    </div>
                    <div class="spec-row">
                        <span class="spec-label">Общая сумма:</span>
                        <span class="spec-value"><?php echo number_format($order['total_amount'], 2, ',', ' '); ?> ₽</span>
                    </div>
                    <?php if ($order['address']): ?>
                    <div class="spec-row">
                        <span class="spec-label">Адрес доставки:</span>
                        <span class="spec-value"><?php echo htmlspecialchars($order['address']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($order['phone']): ?>
                    <div class="spec-row">
                        <span class="spec-label">Телефон:</span>
                        <span class="spec-value"><?php echo htmlspecialchars($order['phone']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Товары -->
            <div>
                <h2>Товары в заказе (<?php echo count($items); ?>)</h2>
                <?php if (empty($items)): ?>
                    <p>Товары в заказе недоступны.</p>
                <?php else: ?>
                    <div class="products-grid">
                        <?php foreach ($items as $item): ?>
                        <div class="product-card">
                            <img src="images/<?php echo htmlspecialchars($item['image'] ?? 'default_plant.png'); ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                 style="height: 120px;">
                            <h3><?php echo htmlspecialchars($item['name'] ?? 'Товар удален'); ?></h3>
                            <p><strong><?php echo number_format($item['price'], 2, ',', ' '); ?> ₽</strong> × <?php echo $item['quantity']; ?></p>
                            <p style="margin-top: auto;"><strong><?php echo number_format($item['price'] * $item['quantity'], 2, ',', ' '); ?> ₽</strong></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
