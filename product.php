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
<?php if (!empty($product['scientific_name'])): ?>
    <p class="scientific-name"><em><?php echo htmlspecialchars($product['scientific_name']); ?></em></p>
<?php endif; ?>

<div class="product-detail">
    <div class="product-image">
        <img src="/wildlife/uploads/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" width="400" style="max-width: 100%;">
    </div>
    
    <div class="product-info">
        <div class="product-specs-card">
            <h3>Характеристики растения</h3>
            
            <div class="spec-row highlight">
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
                <span class="spec-label">Потребность в свете</span>
                <span class="spec-value">
                    <?php 
                    $light_labels = [
                        'low' => '🌑 Низкая (тень)',
                        'medium' => '🌤️ Средняя (полутень)',
                        'high' => '☀️ Высокая (яркий свет)',
                        'very_high' => '🔥 Очень высокая (прямое солнце)'
                    ];
                    echo $light_labels[$product['light_requirement']] ?? 'Средняя';
                    ?>
                </span>
            </div>
            
            <div class="spec-row">
                <span class="spec-label">Полив</span>
                <span class="spec-value">
                    <?php 
                    $water_labels = [
                        'low' => '💧 Редкий',
                        'medium' => '💧💧 Умеренный',
                        'high' => '💧💧💧 Частый'
                    ];
                    echo $water_labels[$product['water_requirement']] ?? 'Умеренный';
                    ?>
                </span>
            </div>
            
            <div class="spec-row">
                <span class="spec-label">Влажность воздуха</span>
                <span class="spec-value">
                    <?php 
                    $humidity_labels = [
                        'low' => '🏜️ Низкая',
                        'medium' => '🌿 Средняя',
                        'high' => '🌧️ Высокая'
                    ];
                    echo $humidity_labels[$product['humidity_requirement']] ?? 'Средняя';
                    ?>
                </span>
            </div>
            
            <?php if ($product['temperature_min'] || $product['temperature_max']): ?>
            <div class="spec-row">
                <span class="spec-label">Температура</span>
                <span class="spec-value">
                    <?php 
                    if ($product['temperature_min'] && $product['temperature_max']) {
                        echo $product['temperature_min'] . '°C — ' . $product['temperature_max'] . '°C';
                    } elseif ($product['temperature_min']) {
                        echo 'от ' . $product['temperature_min'] . '°C';
                    } elseif ($product['temperature_max']) {
                        echo 'до ' . $product['temperature_max'] . '°C';
                    }
                    ?>
                </span>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($product['size_height'])): ?>
            <div class="spec-row">
                <span class="spec-label">Высота</span>
                <span class="spec-value"><?php echo htmlspecialchars($product['size_height']); ?></span>
            </div>
            <?php endif; ?>
            
            <div class="spec-row">
                <span class="spec-label">Сложность ухода</span>
                <span class="spec-value">
                    <?php 
                    $difficulty_labels = [
                        'easy' => '🌟 Для начинающих',
                        'medium' => '🌿 Средняя',
                        'hard' => '🔥 Для опытных'
                    ];
                    echo $difficulty_labels[$product['difficulty']] ?? 'Средняя';
                    ?>
                </span>
            </div>
            
            <div class="spec-row">
                <span class="spec-label">Ядовитость</span>
                <span class="spec-value">
                    <?php 
                    $poisonous_labels = [
                        'none' => '✅ Не ядовито',
                        'mild' => '⚠️ Слабо ядовито',
                        'severe' => '☠️ Сильно ядовито'
                    ];
                    echo $poisonous_labels[$product['poisonous']] ?? 'Не ядовито';
                    ?>
                </span>
            </div>
            
            <?php if (!empty($product['bloom_period'])): ?>
            <div class="spec-row">
                <span class="spec-label">Период цветения</span>
                <span class="spec-value"><?php echo htmlspecialchars($product['bloom_period']); ?></span>
            </div>
            <?php endif; ?>
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
                <div class="alert alert-error">Растение закончилось</div>
            <?php endif; ?>
        <?php else: ?>
            <p><a href="login.php">Войдите</a>, чтобы добавить растение в корзину</p>
        <?php endif; ?>
    </div>
</div>

<style>
.scientific-name {
    color: #666;
    font-style: italic;
    margin-top: -10px;
    margin-bottom: 20px;
}

.spec-row {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.spec-row.highlight {
    background-color: #f5f5f5;
    padding: 10px;
    border-radius: 4px;
    margin: 10px 0;
    font-size: 1.1em;
}

.spec-label {
    font-weight: 500;
    color: #555;
}

.spec-value {
    text-align: right;
    font-weight: 500;
}

.product-detail {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-top: 20px;
}

.product-image img {
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    max-width: 100%;
    height: auto;
}

.product-specs-card, .product-description-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.product-specs-card h3, .product-description-card h3 {
    margin-top: 0;
    margin-bottom: 15px;
    color: #2c3e50;
    border-bottom: 2px solid #4CAF50;
    padding-bottom: 8px;
}

.product-actions {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.quantity-selector {
    margin-bottom: 15px;
}

.quantity-selector label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

.quantity-selector input {
    width: 80px;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

button[type="submit"] {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s;
}

button[type="submit"]:hover {
    background-color: #45a049;
}

@media (max-width: 768px) {
    .product-detail {
        grid-template-columns: 1fr;
    }
}
</style>

<?php include 'includes/footer.php'; ?>