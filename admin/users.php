<?php
require_once '../includes/config.php';

if (!isAdmin()) {
    redirect('../login.php');
}

$message = '';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id != $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $message = 'Пользователь удален';
    } else {
        $message = 'Нельзя удалить свой аккаунт';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && isset($_POST['role'])) {
    $user_id = (int)$_POST['user_id'];
    $role = $_POST['role'];
    
    if (in_array($role, ['user', 'admin'])) {
        $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $role, $user_id);
        $stmt->execute();
        $message = 'Роль пользователя обновлена';
    }
}

$users = $conn->query("SELECT u.*, (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as orders_count FROM users u ORDER BY u.id");

include 'header.php';
?>

<h1>Управление пользователями</h1>

<?php if ($message): ?>
    <p style="color: green;"><?php echo $message; ?></p>
<?php endif; ?>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Логин</th>
        <th>Email</th>
        <th>Роль</th>
        <th>Заказов</th>
        <th>Регистрация</th>
        <th>Действия</th>
    </tr>
    <?php while ($user = $users->fetch_assoc()): ?>
        <tr>
            <td><?php echo $user['id']; ?></td>
            <td><?php echo htmlspecialchars($user['username']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                    <select name="role" onchange="this.form.submit()">
                        <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>Пользователь</option>
                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Администратор</option>
                    </select>
                </form>
            </td>
            <td><?php echo $user['orders_count']; ?></td>
            <td><?php echo date('d.m.Y', strtotime($user['created_at'])); ?></td>
            <td>
                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                    <a href="users.php?delete=<?php echo $user['id']; ?>" onclick="return confirm('Удалить пользователя?')">Удалить</a>
                <?php else: ?>
                    <em>Вы</em>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php include 'footer.php'; ?>
