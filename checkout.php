<?php
require_once 'includes/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

$stmt = $conn->prepare("SELECT c.*, p.name, p.price, p.stock FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items = $stmt->get_result();

if ($cart_items->num_rows === 0) {
    redirect('cart.php');
}

$items = [];
$total = 0;
while ($item = $cart_items->fetch_assoc()) {
    $items[] = $item;
    $total += $item['price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (empty($address) || empty($phone)) {
        $error = 'Все поля обязательны для заполнения';
    } else {
        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, address, phone) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("idss", $user_id, $total, $address, $phone);
            $stmt->execute();
            $order_id = $conn->insert_id;

            $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($items as $item) {
                $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
                $stmt->execute();

                $update_stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $update_stmt->bind_param("ii", $item['quantity'], $item['product_id']);
                $update_stmt->execute();
            }

            $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            $conn->commit();
            redirect('orders.php?msg=Заказ успешно оформлен!');
        } catch (Exception $e) {
            $conn->rollback();
            $error = 'Ошибка при оформлении заказа';
        }
    }
}

include 'includes/header.php';
?>

<h1>Оформление заказа</h1>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<h2>Ваш заказ:</h2>
<table>
    <tr>
        <th>Товар</th>
        <th>Цена</th>
        <th>Кол-во</th>
        <th>Сумма</th>
    </tr>
    <?php foreach ($items as $item): ?>
        <tr>
            <td><?php echo htmlspecialchars($item['name']); ?></td>
            <td><?php echo number_format($item['price'], 0, ',', ' '); ?> ₽</td>
            <td><?php echo $item['quantity']; ?></td>
            <td><?php echo number_format($item['price'] * $item['quantity'], 0, ',', ' '); ?> ₽</td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <td colspan="3" align="right"><strong>Итого:</strong></td>
        <td><strong><?php echo number_format($total, 0, ',', ' '); ?> ₽</strong></td>
    </tr>
</table>

<h2>Данные для доставки:</h2>
<form method="POST">
    <p>
        <label>Адрес доставки:<br>
        <textarea name="address" rows="3" cols="40" required></textarea></label>
    </p>
    <p>
        <label>Телефон:<br>
        <input type="tel" name="phone" required></label>
    </p>
    <p>
        <button type="submit">Подтвердить заказ</button>
        <a href="cart.php">Вернуться в корзину</a>
    </p>
</form>

<?php include 'includes/footer.php'; ?>
