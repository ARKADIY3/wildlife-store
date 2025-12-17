<?php
require_once '../includes/config.php';

if (!isAdmin()) {
    redirect('../login.php');
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    redirect('products.php');
}

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    redirect('products.php');
}

$error = '';
$categories = $conn->query("SELECT * FROM categories ORDER BY name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $processor = trim($_POST['processor'] ?? '');
    $ram = trim($_POST['ram'] ?? '');
    $storage = trim($_POST['storage'] ?? '');
    $graphics = trim($_POST['graphics'] ?? '');
    $stock = (int)($_POST['stock'] ?? 0);
    $image = $product['image'];

    if (empty($name) || $price <= 0) {
        $error = 'Название и цена обязательны';
    } else {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'];
            if (in_array($_FILES['image']['type'], $allowed)) {
                if ($product['image'] !== 'default.png') {
                    $old_path = '../uploads/products/' . $product['image'];
                    if (file_exists($old_path)) {
                        unlink($old_path);
                    }
                }
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image = uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/products/' . $image);
            }
        }

        $stmt = $conn->prepare("UPDATE products SET name=?, description=?, price=?, category_id=?, processor=?, ram=?, storage=?, graphics=?, image=?, stock=? WHERE id=?");
        $cat_id = $category_id > 0 ? $category_id : null;
        $stmt->bind_param("ssdisssssii", $name, $description, $price, $cat_id, $processor, $ram, $storage, $graphics, $image, $stock, $id);
        
        if ($stmt->execute()) {
            redirect('products.php');
        } else {
            $error = 'Ошибка при обновлении товара';
        }
    }
}

include 'header.php';
?>

<h1>Редактировать товар</h1>

<p><a href="products.php">&larr; Назад к списку</a></p>

<?php if ($error): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <p><label>Название:<br><input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required></label></p>
    <p><label>Описание:<br><textarea name="description" rows="4" cols="50"><?php echo htmlspecialchars($product['description']); ?></textarea></label></p>
    <p><label>Цена (₽):<br><input type="number" name="price" step="0.01" value="<?php echo $product['price']; ?>" required></label></p>
    <p><label>Категория:<br>
        <select name="category_id">
            <option value="0">Без категории</option>
            <?php while ($cat = $categories->fetch_assoc()): ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo $product['category_id'] == $cat['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
            <?php endwhile; ?>
        </select>
    </label></p>
    <p><label>Процессор:<br><input type="text" name="processor" value="<?php echo htmlspecialchars($product['processor']); ?>"></label></p>
    <p><label>Оперативная память:<br><input type="text" name="ram" value="<?php echo htmlspecialchars($product['ram']); ?>"></label></p>
    <p><label>Накопитель:<br><input type="text" name="storage" value="<?php echo htmlspecialchars($product['storage']); ?>"></label></p>
    <p><label>Видеокарта:<br><input type="text" name="graphics" value="<?php echo htmlspecialchars($product['graphics']); ?>"></label></p>
    <p><label>Остаток на складе:<br><input type="number" name="stock" value="<?php echo $product['stock']; ?>"></label></p>
    <p>
        Текущее фото:<br>
        <img src="/Fixik/uploads/products/<?php echo htmlspecialchars($product['image']); ?>" width="150">
    </p>
    <p><label>Новое фото (оставьте пустым, чтобы не менять):<br><input type="file" name="image" accept="image/*"></label></p>
    <p><button type="submit">Сохранить</button></p>
</form>

<?php include 'footer.php'; ?>
