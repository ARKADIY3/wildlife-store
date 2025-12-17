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
        <div class="product-specs-card">
            <h3>Характеристики</h3>
            <div class="spec-row">
                <span class="spec-label">Цена</span>
                <span class="spec-value"><strong><?php echo number_format($product['price'], 0, ',', ' '); ?> ₽</strong></span>
            </div>
            <div class="spec-row">
                <span class="spec-label">Категория</span>
                <span class="spec-value"><?php echo htmlspecialchars($product['category_name'] ?? 'Без категории'); ?></span>
            </div>
            <div class="spec-row">
                <span class="spec-label">В наличии</span>
                <span class="spec-value"><?php echo $product['stock']; ?> шт.</span>
            </div>
            <div class="spec-row">
                <span class="spec-label">Процессор</span>
                <span class="spec-value"><?php echo htmlspecialchars($product['processor']); ?></span>
            </div>
            <div class="spec-row">
                <span class="spec-label">Оперативная память</span>
                <span class="spec-value"><?php echo htmlspecialchars($product['ram']); ?></span>
            </div>
            <div class="spec-row">
                <span class="spec-label">Накопитель</span>
                <span class="spec-value"><?php echo htmlspecialchars($product['storage']); ?></span>
            </div>
            <div class="spec-row">
                <span class="spec-label">Видеокарта</span>
                <span class="spec-value"><?php echo htmlspecialchars($product['graphics']); ?></span>
            </div>
        </div>
        
        <div class="product-description-card">
            <h3>Описание</h3>
            <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        </div>
        
        <?php if (isLoggedIn()): ?>
            <?php if ($product['stock'] > 0): ?>
                <form class="product-actions" method="POST" action="cart_add.php">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <div class="quantity-selector">
                        <label for="quantity">Количество</label>
                        <input id="quantity" type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                    </div>
                    <button type="submit">Добавить в корзину</button>
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
