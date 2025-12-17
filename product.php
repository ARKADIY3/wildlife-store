<?php
require_once 'includes/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    redirect('catalog.php');
}

$stmt = $conn->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    redirect('catalog.php');
}

include 'includes/header.php';
?>

<p><a href="catalog.php">&larr; Назад в каталог</a></p>

<h1><?php echo htmlspecialchars($product['name']); ?></h1>

<div class="product-detail">
    <div class="product-image">
        <img src="/Fixik/uploads/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" width="400">
    </div>
    
    <div class="product-info">
        <p><strong>Цена: <?php echo number_format($product['price'], 0, ',', ' '); ?> ₽</strong></p>
        <p>Категория: <?php echo htmlspecialchars($product['category_name'] ?? 'Без категории'); ?></p>
        <p>В наличии: <?php echo $product['stock']; ?> шт.</p>
        
        <h3>Характеристики:</h3>
        <ul>
            <li>Процессор: <?php echo htmlspecialchars($product['processor']); ?></li>
            <li>Оперативная память: <?php echo htmlspecialchars($product['ram']); ?></li>
            <li>Накопитель: <?php echo htmlspecialchars($product['storage']); ?></li>
            <li>Видеокарта: <?php echo htmlspecialchars($product['graphics']); ?></li>
        </ul>
        
        <h3>Описание:</h3>
        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        
        <?php if (isLoggedIn()): ?>
            <?php if ($product['stock'] > 0): ?>
                <form class="product-actions" method="POST" action="cart_add.php">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <p>
                        <label for="quantity">Количество</label>
                        <input id="quantity" type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                    </p>
                    <p><button type="submit">Добавить в корзину</button></p>
                </form>
            <?php else: ?>
                <div class="alert alert-error">Товар закончился</div>
            <?php endif; ?>
        <?php else: ?>
            <p><a href="login.php">Войдите</a>, чтобы добавить товар в корзину</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
