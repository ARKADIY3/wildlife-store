<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'includes/config.php';

// Получаем данные пользователя с ОТЛАДКОЙ
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Если пользователь не найден - ошибка
if (!$user) {
    die("Ошибка: Пользователь с ID $user_id не найден в базе данных. <a href='logout.php'>Выйти</a>");
}

// Получаем заказы пользователя
$stmt = $conn->prepare("
    SELECT o.*, COUNT(oi.id) as items_count, SUM(oi.quantity * oi.price) as order_total 
    FROM orders o 
    LEFT JOIN order_items oi ON o.id = oi.order_id 
    WHERE o.user_id = ? 
    GROUP BY o.id 
    ORDER BY o.created_at DESC 
    LIMIT 10
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$recent_orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Получаем элементы корзины
$stmt = $conn->prepare("
    SELECT c.*, p.name, p.price, p.image 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ? 
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<?php include 'includes/header.php'; ?>

<div class="card">
    <h1>Профиль пользователя: <?php echo htmlspecialchars($user['username']); ?></h1>
    
    <?php if (isset($_GET['updated'])): ?>
        <div class="alert alert-success">Профиль успешно обновлен!</div>
    <?php endif; ?>
    
    <!-- Основная информация -->
    <div class="product-detail">
        <div>
            <h2>Основная информация</h2>
            <div class="product-specs-card">
                <div class="spec-row">
                    <span class="spec-label">Логин:</span>
                    <span class="spec-value"><?php echo htmlspecialchars($user['username'] ?? 'Не указан'); ?></span>
                </div>
                <div class="spec-row">
                    <span class="spec-label">Email:</span>
                    <span class="spec-value"><?php echo htmlspecialchars($user['email'] ?? 'Не указан'); ?></span>
                </div>
                <?php if (isset($user['address']) && $user['address']): ?>
                <div class="spec-row">
                    <span class="spec-label">Адрес:</span>
                    <span class="spec-value"><?php echo htmlspecialchars($user['address']); ?></span>
                </div>
                <?php endif; ?>
                <?php if (isset($user['phone']) && $user['phone']): ?>
                <div class="spec-row">
                    <span class="spec-label">Телефон:</span>
                    <span class="spec-value"><?php echo htmlspecialchars($user['phone']); ?></span>
                </div>
                <?php endif; ?>
                <div class="spec-row">
                    <span class="spec-label">Дата регистрации:</span>
                    <span class="spec-value"><?php echo $user['created_at'] ? date('d.m.Y H:i', strtotime($user['created_at'])) : 'Не указана'; ?></span>
                </div>
                <div class="spec-row">
                    <span class="spec-label">Роль:</span>
                    <span class="spec-value"><?php echo htmlspecialchars(ucfirst($user['role'] ?? 'user')); ?></span>
                </div>
            </div>
            
            <a href="#edit-profile" class="btn btn-sm">Редактировать профиль</a>
        </div>
        
        <!-- Быстрые действия -->
        <div>
            <h2>Быстрые действия</h2>
            <div class="product-actions">
                <a href="cart.php" class="btn btn-block">
                    🛒 Корзина (<?php echo count($cart_items); ?>)
                </a>
                <a href="orders.php" class="btn btn-block btn-outline">
                    📋 Все заказы (<?php echo count($recent_orders); ?>)
                </a>
                <a href="catalog.php" class="btn btn-block btn-outline">
                    🌿 В каталог
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Последние заказы -->
<?php if (!empty($recent_orders)): ?>
<div class="card">
    <h2>Последние заказы</h2>
    <table>
        <thead>
            <tr>
                <th>ID заказа</th>
                <th>Статус</th>
                <th>Товаров</th>
                <th>Сумма</th>
                <th>Дата</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent_orders as $order): ?>
            <tr>
                <td>#<?php echo $order['id']; ?></td>
                <td>
                    <span style="padding: 0.25rem 0.5rem; border-radius: 9999px; background: <?php 
                        echo $order['status'] == 'delivered' ? '#ecfdf5' : 
                             ($order['status'] == 'pending' ? '#fef3c7' : '#dbeafe');
                    ?>; color: <?php 
                        echo $order['status'] == 'delivered' ? '#065f46' : 
                             ($order['status'] == 'pending' ? '#d97706' : '#1e40af');
                    ?>; font-size: 0.8rem; font-weight: 500;">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
                </td>
                <td><?php echo $order['items_count'] ?: 0; ?></td>
                <td><strong><?php echo number_format($order['order_total'] ?? $order['total_amount'] ?? 0, 2, ',', ' '); ?> ₽</strong></td>
                <td><?php echo date('d.m.Y', strtotime($order['created_at'])); ?></td>
                <td><a href="order_detail.php?id=<?php echo $order['id']; ?>">Подробнее</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
<div class="card">
    <h2>Заказы</h2>
    <p>У вас пока нет заказов.</p>
</div>
<?php endif; ?>

<!-- Модальное окно редактирования профиля -->
<div id="edit-profile" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; padding: 2rem;">
    <div class="card" style="max-width: 500px; margin: 0 auto;">
        <h3>Редактировать профиль</h3>
        <form action="profile_update.php" method="POST">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label>Новый пароль (оставьте пустым для сохранения текущего)</label>
                <input type="password" name="password" placeholder="Новый пароль">
            </div>
            
            <div style="display: flex; gap: 1rem;">
                <button type="submit" class="btn">Сохранить</button>
                <a href="#" onclick="document.getElementById('edit-profile').style.display='none'; return false;" class="btn btn-outline btn-sm">Отмена</a>
            </div>
        </form>
    </div>
</div>


<script>
document.querySelector('a[href="#edit-profile"]').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('edit-profile').style.display = 'block';
});
</script>

<?php include 'includes/footer.php'; ?>
