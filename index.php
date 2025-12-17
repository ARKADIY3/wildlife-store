<?php
require_once 'includes/config.php';
include 'includes/header.php';

$result = $conn->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT 6");
?>

<h1>Добро пожаловать в Fixik!</h1>
<p>Ваш надежный магазин компьютеров</p>

<h2>Популярные товары</h2>
<div class="products-grid">
<?php while ($product = $result->fetch_assoc()): ?>
    <div class="product-card">
        <img src="/Fixik/uploads/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" width="200">
        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
        <p><strong><?php echo number_format($product['price'], 0, ',', ' '); ?> ₽</strong></p>
        <p>Категория: <?php echo htmlspecialchars($product['category_name'] ?? 'Без категории'); ?></p>
        <p><a href="product.php?id=<?php echo $product['id']; ?>">Подробнее</a></p>
    </div>
<?php endwhile; ?>
</div>

<p><a href="catalog.php">Смотреть весь каталог</a></p>

<?php include 'includes/footer.php'; ?>
