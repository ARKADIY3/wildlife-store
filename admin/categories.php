<?php
require_once '../includes/config.php';

if (!isAdmin()) {
    redirect('../login.php');
}

$message = '';
$error = '';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $message = 'Категория удалена';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $edit_id = (int)($_POST['edit_id'] ?? 0);

    if (empty($name)) {
        $error = 'Название обязательно';
    } else {
        if ($edit_id > 0) {
            $stmt = $conn->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
            $stmt->bind_param("ssi", $name, $description, $edit_id);
            $message = 'Категория обновлена';
        } else {
            $stmt = $conn->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $description);
            $message = 'Категория добавлена';
        }
        $stmt->execute();
    }
}

$categories = $conn->query("SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id = c.id) as products_count FROM categories c ORDER BY c.name");

$edit_category = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_category = $stmt->get_result()->fetch_assoc();
}

include 'header.php';
?>

<h1>Управление категориями</h1>

<?php if ($message): ?>
    <p style="color: green;"><?php echo $message; ?></p>
<?php endif; ?>

<?php if ($error): ?>
    <p style="color: red;"><?php echo $error; ?></p>
<?php endif; ?>

<h2><?php echo $edit_category ? 'Редактировать категорию' : 'Добавить категорию'; ?></h2>

<form method="POST">
    <?php if ($edit_category): ?>
        <input type="hidden" name="edit_id" value="<?php echo $edit_category['id']; ?>">
    <?php endif; ?>
    <p><label>Название:<br><input type="text" name="name" value="<?php echo htmlspecialchars($edit_category['name'] ?? ''); ?>" required></label></p>
    <p><label>Описание:<br><textarea name="description" rows="3" cols="40"><?php echo htmlspecialchars($edit_category['description'] ?? ''); ?></textarea></label></p>
    <p>
        <button type="submit"><?php echo $edit_category ? 'Сохранить' : 'Добавить'; ?></button>
        <?php if ($edit_category): ?>
            <a href="categories.php">Отмена</a>
        <?php endif; ?>
    </p>
</form>

<h2>Список категорий</h2>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Название</th>
        <th>Описание</th>
        <th>Товаров</th>
        <th>Действия</th>
    </tr>
    <?php while ($cat = $categories->fetch_assoc()): ?>
        <tr>
            <td><?php echo $cat['id']; ?></td>
            <td><?php echo htmlspecialchars($cat['name']); ?></td>
            <td><?php echo htmlspecialchars($cat['description']); ?></td>
            <td><?php echo $cat['products_count']; ?></td>
            <td>
                <a href="categories.php?edit=<?php echo $cat['id']; ?>">Редактировать</a> | 
                <a href="categories.php?delete=<?php echo $cat['id']; ?>" onclick="return confirm('Удалить категорию?')">Удалить</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php include 'footer.php'; ?>
