<?php
require_once '../includes/config.php';

if (!isAdmin()) {
    redirect('../login.php');
}

$message = '';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    
    if ($product && $product['image'] !== 'default_plant.png') {
        $image_path = '../uploads/products/' . $product['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $message = 'Растение удалено';
}

$products = $conn->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");

include 'header.php';
?>

<h1>Управление растениями</h1>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<p><a href="product_add.php" class="btn">+ Добавить растение</a></p>

<table>
    <tr>
        <th>ID</th>
        <th>Фото</th>
        <th>Название</th>
        <th>Латинское название</th>
        <th>Категория</th>
        <th>Цена</th>
        <th>Остаток</th>
        <th>Сложность</th>
        <th>Действия</th>
    </tr>
    <?php while ($product = $products->fetch_assoc()): ?>
        <tr>
            <td><?php echo $product['id']; ?></td>
            <td><img src="/wildlife/uploads/products/<?php echo htmlspecialchars($product['image']); ?>" width="60" height="60" style="object-fit: cover;"></td>
            <td><?php echo htmlspecialchars($product['name']); ?></td>
            <td><em><?php echo htmlspecialchars($product['scientific_name'] ?? '-'); ?></em></td>
            <td><?php echo htmlspecialchars($product['category_name'] ?? 'Без категории'); ?></td>
            <td><?php echo number_format($product['price'], 0, ',', ' '); ?> ₽</td>
            <td><?php echo $product['stock']; ?></td>
            <td>
                <?php 
                $difficulty_labels = [
                    'easy' => '🌟 Легкая',
                    'medium' => '🌿 Средняя',
                    'hard' => '🔥 Сложная'
                ];
                echo $difficulty_labels[$product['difficulty']] ?? 'Средняя';
                ?>
            </td>
            <td>
                <a href="product_edit.php?id=<?php echo $product['id']; ?>">Редактировать</a> | 
                <a href="products.php?delete=<?php echo $product['id']; ?>" onclick="return confirm('Удалить растение?')">Удалить</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php include 'footer.php'; ?>