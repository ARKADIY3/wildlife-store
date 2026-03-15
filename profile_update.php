<?php
session_start();
require_once 'includes/config.php';

if ($_POST && isset($_POST['user_id'])) {
    $user_id = (int)$_POST['user_id'];
    $email = trim($_POST['email']);
    
    // Обновляем ТОЛЬКО email и password (поля, которые точно есть в БД)
    $sql = "UPDATE users SET email = ?";
    $params = [$email];
    $types = "s";
    
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql .= ", password = ?";
        $params[] = $password;
        $types .= "s";
    }
    
    $sql .= " WHERE id = ?";
    $params[] = $user_id;
    $types .= "i";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $stmt->close();
    
    // Обновляем сессию
    $_SESSION['username'] = $email; // Используем email как имя пользователя
}

header('Location: profile.php?updated=1');
exit;
?>
