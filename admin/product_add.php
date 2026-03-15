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
    
    // Новые поля для растений
    $scientific_name = trim($_POST['scientific_name'] ?? '');
    $light_requirement = $_POST['light_requirement'] ?? 'medium';
    $water_requirement = $_POST['water_requirement'] ?? 'medium';
    $temperature_min = !empty($_POST['temperature_min']) ? (float)$_POST['temperature_min'] : null;
    $temperature_max = !empty($_POST['temperature_max']) ? (float)$_POST['temperature_max'] : null;
    $humidity_requirement = $_POST['humidity_requirement'] ?? 'medium';
    $size_height = trim($_POST['size_height'] ?? '');
    $difficulty = $_POST['difficulty'] ?? 'medium';
    $poisonous = $_POST['poisonous'] ?? 'none';
    $bloom_period = trim($_POST['bloom_period'] ?? '');
    $stock = (int)($_POST['stock'] ?? 0);
    $image = 'default_plant.png';

    if (empty($name) || $price <= 0) {
        $error = 'Название и цена обязательны';
    } else {
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp'];
            if (in_array($_FILES['image']['type'], $allowed)) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image = uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], '../uploads/products/' . $image);
            }
        }

        $stmt = $conn->prepare("INSERT INTO products (
            name, description, price, category_id, 
            scientific_name, light_requirement, water_requirement, 
            temperature_min, temperature_max, humidity_requirement, 
            size_height, difficulty, poisonous, bloom_period, image, stock
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $cat_id = $category_id > 0 ? $category_id : null;
        
        $stmt->bind_param(
            "ssdissdssssssssi",
            $name, $description, $price, $cat_id,
            $scientific_name, $light_requirement, $water_requirement,
            $temperature_min, $temperature_max, $humidity_requirement,
            $size_height, $difficulty, $poisonous, $bloom_period,
            $image, $stock
        );
        
        if ($stmt->execute()) {
            redirect('products.php');
        } else {
            $error = 'Ошибка при добавлении растения: ' . $conn->error;
        }
    }
}

include 'header.php';
?>

<h1>Добавить растение</h1>

<p><a href="products.php">&larr; Назад к списку</a></p>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>

<form class="card card-pad" method="POST" enctype="multipart/form-data">
    <h2>Основная информация</h2>
    
    <p><label>Название растения:<br><input type="text" name="name" required></label></p>
    
    <p><label>Латинское название (научное):<br><input type="text" name="scientific_name" placeholder="например: Monstera deliciosa"></label></p>
    
    <p><label>Описание:<br><textarea name="description" rows="4" cols="50" placeholder="Описание растения, особенности, уход..."></textarea></label></p>
    
    <p><label>Цена (₽):<br><input type="number" name="price" step="0.01" required></label></p>
    
    <p><label>Категория:<br>
        <select name="category_id">
            <option value="0">Без категории</option>
            <?php while ($cat = $categories->fetch_assoc()): ?>
                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
            <?php endwhile; ?>
        </select>
    </label></p>
    
    <h2>Требования к освещению</h2>
    
    <p><label>Потребность в свете:<br>
        <select name="light_requirement">
            <option value="low">Низкая (тень)</option>
            <option value="medium" selected>Средняя (полутень)</option>
            <option value="high">Высокая (яркий свет)</option>
            <option value="very_high">Очень высокая (прямое солнце)</option>
        </select>
    </label></p>
    
    <h2>Полив и влажность</h2>
    
    <p><label>Потребность в воде:<br>
        <select name="water_requirement">
            <option value="low">Низкая (редкий полив)</option>
            <option value="medium" selected>Средняя (умеренный полив)</option>
            <option value="high">Высокая (частый полив)</option>
        </select>
    </label></p>
    
    <p><label>Требования к влажности воздуха:<br>
        <select name="humidity_requirement">
            <option value="low">Низкая (сухой воздух)</option>
            <option value="medium" selected>Средняя (обычная влажность)</option>
            <option value="high">Высокая (нужно опрыскивание)</option>
        </select>
    </label></p>
    
    <h2>Температура</h2>
    
    <p>
        <label>Мин. температура (°C):<br><input type="number" name="temperature_min" step="0.1" placeholder="например: 15"></label>
        &nbsp;&nbsp;
        <label>Макс. температура (°C):<br><input type="number" name="temperature_max" step="0.1" placeholder="например: 30"></label>
    </p>
    
    <h2>Размер и сложность</h2>
    
    <p><label>Высота растения:<br><input type="text" name="size_height" placeholder="например: 50-80 см"></label></p>
    
    <p><label>Сложность ухода:<br>
        <select name="difficulty">
            <option value="easy">Лёгкая (для начинающих)</option>
            <option value="medium" selected>Средняя</option>
            <option value="hard">Сложная (требует опыта)</option>
        </select>
    </label></p>
    
    <h2>Дополнительная информация</h2>
    
    <p><label>Ядовитость:<br>
        <select name="poisonous">
            <option value="none" selected>Не ядовито</option>
            <option value="mild">Слабо ядовито</option>
            <option value="severe">Сильно ядовито (опасно для детей/животных)</option>
        </select>
    </label></p>
    
    <p><label>Период цветения:<br><input type="text" name="bloom_period" placeholder="например: весна-лето, или 'не цветет'"></label></p>
    
    <p><label>Остаток на складе:<br><input type="number" name="stock" value="0" min="0"></label></p>
    
    <p><label>Фото растения:<br><input type="file" name="image" accept="image/*"></label></p>
    
    <p><button type="submit">Добавить растение</button></p>
</form>

<?php include 'footer.php'; ?>