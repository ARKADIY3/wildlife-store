<?php
require_once 'includes/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$cart_id = (int)($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'];

if ($cart_id > 0) {
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
}

redirect('cart.php');
