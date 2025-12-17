<?php
require_once 'includes/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT c.*, p.name, p.price, p.image, p.stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items = $stmt->get_result();

$total = 0;

include 'includes/header.php';
?>

<h1>Корзина</h1>

<?php if (isset($_GET['msg'])): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
<?php endif; ?>

<?php if ($cart_items->num_rows > 0): ?>
    <table>
        <tr>
            <th>Фото</th>
            <th>Название</th>
            <th>Цена</th>
            <th>Количество</th>
            <th>Сумма</th>
            <th>Действия</th>
        </tr>
        <?php while ($item = $cart_items->fetch_assoc()): ?>
            <?php $subtotal = $item['price'] * $item['quantity']; $total += $subtotal; ?>
            <tr>
                <td><img src="/Fixik/uploads/products/<?php echo htmlspecialchars($item['image']); ?>" width="80"></td>
                <td><a href="product.php?id=<?php echo $item['product_id']; ?>"><?php echo htmlspecialchars($item['name']); ?></a></td>
                <td><?php echo number_format($item['price'], 0, ',', ' '); ?> ₽</td>
                <td>
                    <form method="POST" action="cart_update.php" style="display: inline;">
                        <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>" style="width: 60px;">
                        <button type="submit">Обновить</button>
                    </form>
                </td>
                <td><?php echo number_format($subtotal, 0, ',', ' '); ?> ₽</td>
                <td><a href="cart_remove.php?id=<?php echo $item['id']; ?>" onclick="return confirm('Удалить товар из корзины?')">Удалить</a></td>
            </tr>
        <?php endwhile; ?>
        <tr>
            <td colspan="4" align="right"><strong>Итого:</strong></td>
            <td colspan="2"><strong><?php echo number_format($total, 0, ',', ' '); ?> ₽</strong></td>
        </tr>
    </table>
    
    <p>
        <a href="catalog.php">Продолжить покупки</a> | 
        <a href="checkout.php">Оформить заказ</a>
    </p>
<?php else: ?>
    <p>Корзина пуста</p>
    <p><a href="catalog.php">Перейти в каталог</a></p>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
