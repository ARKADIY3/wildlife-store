<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fixik - Интернет-магазин компьютеров</title>
    <style>
        :root {
            --bg: #f6f7fb;
            --card: #ffffff;
            --text: #111827;
            --muted: #6b7280;
            --border: rgba(17, 24, 39, 0.12);
            --shadow: 0 8px 24px rgba(17, 24, 39, 0.08);
            --primary: #2563eb;
            --primary-600: #1d4ed8;
            --danger: #b91c1c;
            --success: #047857;
            --radius: 14px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.45;
        }

        img { max-width: 100%; height: auto; display: block; }

        a { color: var(--primary); text-decoration: none; }
        a:hover { color: var(--primary-600); text-decoration: underline; }

        .container {
            width: min(1120px, calc(100% - 32px));
            margin-inline: auto;
        }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 10;
            background: rgba(246, 247, 251, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
        }

        .nav {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px 14px;
            padding: 14px 0;
        }

        .nav strong { font-size: 18px; letter-spacing: 0.2px; }

        main.container {
            padding: 22px 0 36px;
        }

        h1 { font-size: 28px; margin: 0 0 14px; }
        h2 { font-size: 20px; margin: 0 0 12px; }
        h3 { font-size: 16px; margin: 0 0 10px; }
        p { margin: 0 0 12px; }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .card-pad { padding: 18px; }

        .form-card {
            width: min(520px, 100%);
            padding: 18px;
            margin: 0 auto;
        }

        label { display: block; font-weight: 600; margin-bottom: 6px; }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="tel"],
        select,
        textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: #fff;
            color: var(--text);
            outline: none;
        }

        textarea { resize: vertical; }

        input:focus, select:focus, textarea:focus {
            border-color: rgba(37, 99, 235, 0.55);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
        }

        button {
            appearance: none;
            border: 1px solid rgba(37, 99, 235, 0.35);
            background: var(--primary);
            color: #fff;
            border-radius: 12px;
            padding: 10px 14px;
            font-weight: 700;
            cursor: pointer;
        }

        button:hover { background: var(--primary-600); }

        fieldset {
            border: 1px solid var(--border);
            border-radius: var(--radius);
            background: var(--card);
            box-shadow: var(--shadow);
            padding: 14px 16px;
        }

        legend { font-weight: 800; padding: 0 8px; }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        @media (max-width: 980px) {
            .products-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 640px) {
            .products-grid { grid-template-columns: 1fr; }
        }

        .product-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            padding: 14px;
            display: grid;
            gap: 10px;
        }

        .product-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: #f3f4f6;
        }

        .product-card p { margin: 0; color: var(--muted); }
        .product-card p strong { color: var(--text); }

        .product-detail {
            display: grid;
            grid-template-columns: 420px 1fr;
            gap: 16px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .product-detail { grid-template-columns: 1fr; }
        }

        .product-image,
        .product-info {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 16px;
        }

        .product-image img {
            border-radius: 12px;
            border: 1px solid var(--border);
            background: #f3f4f6;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        th, td {
            padding: 12px 12px;
            border-bottom: 1px solid var(--border);
            vertical-align: top;
            text-align: left;
        }

        th {
            background: #f3f4f6;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #374151;
        }

        tr:last-child td { border-bottom: none; }

        .alert {
            padding: 12px 14px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: #fff;
        }

        .alert-error {
            border-color: rgba(185, 28, 28, 0.25);
            background: rgba(185, 28, 28, 0.06);
            color: var(--danger);
        }

        .alert-success {
            border-color: rgba(4, 120, 87, 0.25);
            background: rgba(4, 120, 87, 0.07);
            color: var(--success);
        }

        .hint {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            position: relative;
        }

        .hint__badge {
            width: 22px;
            height: 22px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: var(--card);
            color: var(--muted);
            display: grid;
            place-items: center;
            font-weight: 800;
            cursor: default;
            user-select: none;
        }

        .hint__content {
            position: absolute;
            left: 0;
            top: calc(100% + 10px);
            width: max-content;
            max-width: min(520px, calc(100vw - 32px));
            padding: 12px 14px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: var(--shadow);
            color: var(--text);
            display: none;
        }

        .hint:hover .hint__content { display: block; }
        .hint__content div { margin: 6px 0; color: var(--muted); }
        .hint__content strong { color: var(--text); }

        .cards-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .cards-grid { grid-template-columns: 1fr; }
        }

        .site-footer {
            border-top: 1px solid var(--border);
            padding: 18px 0;
            color: var(--muted);
        }
    </style>
</head>
<body class="site">
<header class="site-header">
    <div class="container">
        <nav class="nav">
            <a href="/Fixik/index.php"><strong>Fixik</strong></a>
            <a href="/Fixik/catalog.php">Каталог</a>
            <a href="/Fixik/contact.php">Контакты</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/Fixik/cart.php">Корзина</a>
                <a href="/Fixik/orders.php">Мои заказы</a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="/Fixik/admin/index.php">Админ-панель</a>
                <?php endif; ?>
                <span style="color: var(--muted);">Привет, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                <a href="/Fixik/logout.php">Выход</a>
            <?php else: ?>
                <a href="/Fixik/login.php">Вход</a>
                <a href="/Fixik/register.php">Регистрация</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container">
