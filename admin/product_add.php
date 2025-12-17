<?php
require_once '../includes/config.php';

if (!isAdmin()) {
    redirect('../login.php');
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
    $image = 'default.png';

    if (empty($name) || $price <= 0) {
        $error = 'Название и цена обязательны';
    } else {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'];
            if (in_array($_FILES['image']['type'], $allowed)) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image = uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/products/' . $image);
            }
        }

        $stmt = $conn->prepare("INSERT INTO products (name, description, price, category_id, processor, ram, storage, graphics, image, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $cat_id = $category_id > 0 ? $category_id : null;
        $stmt->bind_param("ssdisssssi", $name, $description, $price, $cat_id, $processor, $ram, $storage, $graphics, $image, $stock);
        
        if ($stmt->execute()) {
            redirect('products.php');
        } else {
            $error = 'Ошибка при добавлении товара';
        }
    }
}

include 'header.php';
?>

<h1>Добавить товар</h1>

<p><a href="products.php">&larr; Назад к списку</a></p>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<form class="card card-pad" method="POST" enctype="multipart/form-data">
    <p><label>Название:<br><input type="text" name="name" required></label></p>
    <p><label>Описание:<br><textarea name="description" rows="4" cols="50"></textarea></label></p>
    <p><label>Цена (₽):<br><input type="number" name="price" step="0.01" required></label></p>
    <p><label>Категория:<br>
        <select name="category_id">
            <option value="0">Без категории</option>
            <?php while ($cat = $categories->fetch_assoc()): ?>
                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
            <?php endwhile; ?>
        </select>
    </label></p>
    <p><label>Процессор:<br><input type="text" name="processor"></label></p>
    <p><label>Оперативная память:<br><input type="text" name="ram"></label></p>
    <p><label>Накопитель:<br><input type="text" name="storage"></label></p>
    <p><label>Видеокарта:<br><input type="text" name="graphics"></label></p>
    <p><label>Остаток на складе:<br><input type="number" name="stock" value="0"></label></p>
    <p><label>Фото товара:<br><input type="file" name="image" accept="image/*"></label></p>
    <p><button type="submit">Добавить</button></p>
</form>

<?php include 'footer.php'; ?>
