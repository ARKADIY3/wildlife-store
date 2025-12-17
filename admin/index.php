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

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card">
        <h2 style="margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 2px solid var(--primary);">📊 Статистика</h2>
        <div style="display: grid; gap: 1rem;">
            <div style="padding: 1rem; background: #f9fafb; border-radius: var(--radius); border-left: 3px solid var(--primary);">
                <div style="font-size: 0.875rem; color: var(--muted); margin-bottom: 0.25rem;">Товаров</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--text);"><?php echo $products_count; ?></div>
            </div>
            <div style="padding: 1rem; background: #f9fafb; border-radius: var(--radius); border-left: 3px solid #10b981;">
                <div style="font-size: 0.875rem; color: var(--muted); margin-bottom: 0.25rem;">Пользователей</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--text);"><?php echo $users_count; ?></div>
            </div>
            <div style="padding: 1rem; background: #f9fafb; border-radius: var(--radius); border-left: 3px solid #f59e0b;">
                <div style="font-size: 0.875rem; color: var(--muted); margin-bottom: 0.25rem;">Заказов</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--text);"><?php echo $orders_count; ?></div>
            </div>
            <div style="padding: 1rem; background: #f9fafb; border-radius: var(--radius); border-left: 3px solid #ef4444;">
                <div style="font-size: 0.875rem; color: var(--muted); margin-bottom: 0.25rem;">Новых сообщений</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: var(--text);"><?php echo $contacts_count; ?></div>
            </div>
        </div>
    </div>

    <div class="card">
        <h2 style="margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 2px solid var(--primary);">⚙️ Управление</h2>
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <a href="products.php" style="display: block; padding: 0.875rem 1rem; background: #f9fafb; border-radius: var(--radius); border-left: 3px solid var(--primary); transition: all 0.2s; text-decoration: none; color: var(--text); font-weight: 500;">
                📦 Управление товарами
            </a>
            <a href="categories.php" style="display: block; padding: 0.875rem 1rem; background: #f9fafb; border-radius: var(--radius); border-left: 3px solid var(--primary); transition: all 0.2s; text-decoration: none; color: var(--text); font-weight: 500;">
                📁 Управление категориями
            </a>
            <a href="orders.php" style="display: block; padding: 0.875rem 1rem; background: #f9fafb; border-radius: var(--radius); border-left: 3px solid var(--primary); transition: all 0.2s; text-decoration: none; color: var(--text); font-weight: 500;">
                🛒 Управление заказами
            </a>
            <a href="users.php" style="display: block; padding: 0.875rem 1rem; background: #f9fafb; border-radius: var(--radius); border-left: 3px solid var(--primary); transition: all 0.2s; text-decoration: none; color: var(--text); font-weight: 500;">
                👥 Управление пользователями
            </a>
            <a href="contacts.php" style="display: block; padding: 0.875rem 1rem; background: #f9fafb; border-radius: var(--radius); border-left: 3px solid var(--primary); transition: all 0.2s; text-decoration: none; color: var(--text); font-weight: 500;">
                ✉️ Сообщения обратной связи
            </a>
        </div>
    </div>
</div>

<style>
.card a:hover {
    background: #eff6ff !important;
    transform: translateX(4px);
}
</style>

<?php include 'footer.php'; ?>
