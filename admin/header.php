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
    <title>Админ-панель - Fixik</title>
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
        html { scrollbar-gutter: stable; }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.45;
        }

        body.site {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main.container { flex: 1; }

        img { max-width: 100%; height: auto; display: block; }
        a { color: var(--primary); text-decoration: none; }
        a:hover { color: var(--primary-600); text-decoration: underline; }

        .container {
            width: min(1200px, calc(100% - 32px));
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

        .nav strong { font-size: 16px; }

        main.container { padding: 22px 0 36px; }

        h1 { font-size: 24px; margin: 0 0 14px; }
        h2 { font-size: 18px; margin: 0 0 12px; }
        p { margin: 0 0 12px; }
        ul { margin: 0 0 12px; padding-left: 18px; }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }
        .card-pad { padding: 18px; }
        .card + .card { margin-top: 16px; }

        label { display: block; font-weight: 600; margin-bottom: 6px; }

        input[type="text"],
        input[type="number"],
        input[type="email"],
        textarea,
        select {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 10px 12px;
            outline: none;
            background: #fff;
            color: var(--text);
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
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #374151;
        }

        tr:last-child td { border-bottom: none; }

        @media (max-width: 760px) {
            table { display: block; overflow-x: auto; }
        }

        .site-footer {
            border-top: 1px solid var(--border);
            padding: 18px 0;
            color: var(--muted);
        }

        .site-footer p { margin: 0; text-align: center; }
    </style>
</head>
<body class="site">
<header class="site-header">
    <div class="container">
        <nav class="nav">
            <strong>Админ-панель Fixik</strong>
            <a href="/Fixik/admin/index.php">Главная</a>
            <a href="/Fixik/admin/products.php">Товары</a>
            <a href="/Fixik/admin/categories.php">Категории</a>
            <a href="/Fixik/admin/orders.php">Заказы</a>
            <a href="/Fixik/admin/users.php">Пользователи</a>
            <a href="/Fixik/admin/contacts.php">Сообщения</a>
            <a href="/Fixik/logout.php">Выход</a>
        </nav>
    </div>
</header>
<main class="container">
