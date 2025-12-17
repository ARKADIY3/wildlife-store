<?php
require_once 'includes/config.php';
include 'includes/header.php';

$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 0;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

$sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];
$types = "";

if ($category_id > 0) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_id;
    $types .= "i";
}

if (!empty($search)) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.processor LIKE ? OR p.graphics LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ssss";
}

if ($min_price > 0) {
    $sql .= " AND p.price >= ?";
    $params[] = $min_price;
    $types .= "d";
}

if ($max_price > 0) {
    $sql .= " AND p.price <= ?";
    $params[] = $max_price;
    $types .= "d";
}

switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY p.price DESC";
        break;
    case 'name':
        $sql .= " ORDER BY p.name ASC";
        break;
    default:
        $sql .= " ORDER BY p.created_at DESC";
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result();

$categories = $conn->query("SELECT * FROM categories ORDER BY name");
?>

<h1>Каталог компьютеров</h1>

<form method="GET" class="filters-form">
    <fieldset>
        <legend>Фильтры и поиск</legend>
          <div class="filters-grid">
              <div class="filter-field">
                  <label>Поиск</label>
                  <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Название, процессор...">
              </div>
              
              <div class="filter-field">
                  <label>Категория</label>
                  <select name="category">
                      <option value="0">Все категории</option>
                      <?php while ($cat = $categories->fetch_assoc()): ?>
                          <option value="<?php echo $cat['id']; ?>" <?php echo $category_id == $cat['id'] ? 'selected' : ''; ?>>
                              <?php echo htmlspecialchars($cat['name']); ?>
                          </option>
                      <?php endwhile; ?>
                  </select>
              </div>

              <div class="filter-field">
                  <label>Цена от</label>
                  <input type="number" name="min_price" value="<?php echo $min_price > 0 ? $min_price : ''; ?>" placeholder="0">
              </div>

              <div class="filter-field">
                  <label>до</label>
                  <input type="number" name="max_price" value="<?php echo $max_price > 0 ? $max_price : ''; ?>" placeholder="Max">
              </div>

              <div class="filter-field">
                  <label>Сортировка</label>
                  <select name="sort">
                      <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Сначала новые</option>
                      <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Цена: по возрастанию</option>
                      <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Цена: по убыванию</option>
                      <option value="name" <?php echo $sort === 'name' ? 'selected' : ''; ?>>По названию</option>
                  </select>
              </div>

              <div class="filters-actions">
                  <button type="submit">Применить</button>
                  <a href="catalog.php" class="btn" style="background: var(--muted); border-color: var(--muted);">Сбросить</a>
              </div>
          </div>
    </fieldset>
</form>

<h2>Товары (<?php echo $products->num_rows; ?>)</h2>

<div class="products-grid">
<?php if ($products->num_rows > 0): ?>
    <?php while ($product = $products->fetch_assoc()): ?>
        <div class="product-card">
            <img src="/Fixik/uploads/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" width="200">
            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
            <p><strong><?php echo number_format($product['price'], 0, ',', ' '); ?> ₽</strong></p>
            <p>Категория: <?php echo htmlspecialchars($product['category_name'] ?? 'Без категории'); ?></p>
            <p>В наличии: <?php echo $product['stock']; ?> шт.</p>
            <p>
                <a href="product.php?id=<?php echo $product['id']; ?>">Подробнее</a>
                <?php if (isLoggedIn()): ?>
                    | <a href="cart_add.php?id=<?php echo $product['id']; ?>">В корзину</a>
                <?php endif; ?>
            </p>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>Товары не найдены</p>
<?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
