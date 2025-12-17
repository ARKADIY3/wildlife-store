<?php
require_once '../includes/config.php';

if (!isAdmin()) {
    redirect('../login.php');
}

$message = '';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM contacts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $message = 'Сообщение удалено';
}

if (isset($_GET['read'])) {
    $id = (int)$_GET['read'];
    $stmt = $conn->prepare("UPDATE contacts SET is_read = 1 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

$contacts = $conn->query("SELECT * FROM contacts ORDER BY is_read ASC, created_at DESC");

include 'header.php';
?>

<h1>Сообщения обратной связи</h1>

<?php if ($message): ?>
    <p style="color: green;"><?php echo $message; ?></p>
<?php endif; ?>

<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Дата</th>
        <th>Имя</th>
        <th>Email</th>
        <th>Тема</th>
        <th>Сообщение</th>
        <th>Статус</th>
        <th>Действия</th>
    </tr>
    <?php while ($contact = $contacts->fetch_assoc()): ?>
        <tr style="<?php echo $contact['is_read'] ? '' : 'background: #ffffd0;'; ?>">
            <td><?php echo $contact['id']; ?></td>
            <td><?php echo date('d.m.Y H:i', strtotime($contact['created_at'])); ?></td>
            <td><?php echo htmlspecialchars($contact['name']); ?></td>
            <td><a href="mailto:<?php echo htmlspecialchars($contact['email']); ?>"><?php echo htmlspecialchars($contact['email']); ?></a></td>
            <td><?php echo htmlspecialchars($contact['subject']); ?></td>
            <td><?php echo nl2br(htmlspecialchars($contact['message'])); ?></td>
            <td><?php echo $contact['is_read'] ? 'Прочитано' : '<strong>Новое</strong>'; ?></td>
            <td>
                <?php if (!$contact['is_read']): ?>
                    <a href="contacts.php?read=<?php echo $contact['id']; ?>">Прочитано</a> | 
                <?php endif; ?>
                <a href="contacts.php?delete=<?php echo $contact['id']; ?>" onclick="return confirm('Удалить сообщение?')">Удалить</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<?php include 'footer.php'; ?>
