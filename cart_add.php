<?php
require_once 'includes/config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];
$product_id = 0;
$quantity = 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);
} else {
    $product_id = (int)($_GET['id'] ?? 0);
}

if ($product_id <= 0) {
    redirect('catalog.php');
}

if ($quantity < 1) $quantity = 1;

$stmt = $conn->prepare("SELECT id FROM cart WHERE user_id = ? AND product_id = ?");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$existing = $stmt->get_result()->fetch_assoc();

if ($existing) {
    $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + ? WHERE id = ?");
    $stmt->bind_param("ii", $quantity, $existing['id']);
} else {
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $user_id, $product_id, $quantity);
}

$stmt->execute();
redirect('cart.php?msg=Товар добавлен в корзину');
