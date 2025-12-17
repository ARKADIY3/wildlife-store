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
            --border: #e5e7eb;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --primary: #2563eb;
            --primary-hover: #1d4ed8;
            --danger: #dc2626;
            --success: #059669;
            --radius: 8px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.5;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        a { color: var(--primary); text-decoration: none; transition: color 0.2s; }
        a:hover { color: var(--primary-hover); }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Header */
        .site-header {
            background: var(--card);
            border-bottom: 1px solid var(--border);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .nav {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .nav strong { font-size: 1.25rem; color: var(--text); }
        .nav-right { margin-left: auto; display: flex; gap: 1rem; align-items: center; }

        /* Main Content */
        main.container {
            padding-top: 2rem;
            padding-bottom: 2rem;
            flex: 1;
        }

        h1, h2, h3 { margin-top: 0; }
        h1 { font-size: 2rem; margin-bottom: 1.5rem; }

        /* Cards */
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            font-weight: 500;
            cursor: pointer;
            border: 1px solid transparent;
            background-color: var(--primary);
            color: white;
            transition: all 0.2s;
        }
        .btn:hover { background-color: var(--primary-hover); color: white; text-decoration: none; }
        .btn-outline { background: transparent; border-color: var(--border); color: var(--text); }
        .btn-outline:hover { border-color: var(--primary); color: var(--primary); }
        .btn-danger { background-color: var(--danger); }
        .btn-danger:hover { background-color: #b91c1c; }
        .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.875rem; }
        .btn-block { width: 100%; }
        
        /* Forms */
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        input[type="text"], input[type="email"], input[type="password"], input[type="number"], input[type="tel"], select, textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 1rem;
            transition: border-color 0.2s;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--card);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            margin-bottom: 1rem;
        }
        th, td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid var(--border); }
        th { background-color: #f9fafb; font-weight: 600; font-size: 0.875rem; text-transform: uppercase; color: var(--muted); }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background-color: #f9fafb; }

        /* Alerts */
        .alert { padding: 1rem; border-radius: var(--radius); margin-bottom: 1rem; }
        .alert-success { background-color: #d1fae5; color: #065f46; }
        .alert-error { background-color: #fee2e2; color: #991b1b; }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        .product-card {
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: transform 0.2s;
        }
        .product-card:hover { transform: translateY(-4px); }
        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: contain;
            background: #fff;
            padding: 1rem;
            border-bottom: 1px solid var(--border);
        }
        .product-content { padding: 1rem; flex: 1; display: flex; flex-direction: column; }
        .product-title { font-weight: 600; margin-bottom: 0.5rem; font-size: 1.1rem; }
        .product-price { font-weight: 700; color: var(--primary); font-size: 1.25rem; margin-top: auto; }
        .product-meta { color: var(--muted); font-size: 0.875rem; margin-bottom: 0.5rem; }

        /* Login Hint */
        .hint-container { position: relative; display: inline-block; margin-top: 1rem; }
        .hint-trigger {
            cursor: help;
            color: var(--muted);
            border-bottom: 1px dashed var(--muted);
        }
        .hint-content {
            display: none;
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #374151;
            color: white;
            padding: 0.75rem;
            border-radius: var(--radius);
            width: max-content;
            max-width: 300px;
            font-size: 0.875rem;
            z-index: 100;
            margin-bottom: 0.5rem;
            box-shadow: var(--shadow);
            text-align: left;
        }
        .hint-content::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: #374151 transparent transparent transparent;
        }
        .hint-container:hover .hint-content { display: block; }
        .hint-content p { margin: 0.25rem 0; }

        /* Catalog Filters */
        .catalog-layout { display: flex; gap: 2rem; align-items: flex-start; }
        .filters-sidebar { width: 250px; flex-shrink: 0; }
        .catalog-content { flex: 1; }
        @media (max-width: 768px) {
            .catalog-layout { flex-direction: column; }
            .filters-sidebar { width: 100%; }
        }

        /* Footer */
        .site-footer {
            background: white;
            border-top: 1px solid var(--border);
            padding: 1.5rem 0;
            margin-top: auto;
            color: var(--muted);
            text-align: center;
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
            
            <div class="nav-right">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/Fixik/cart.php">Корзина</a>
                    <a href="/Fixik/orders.php">Мои заказы</a>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="/Fixik/admin/index.php" class="btn btn-sm btn-outline">Админ-панель</a>
                    <?php endif; ?>
                    <span style="color: var(--muted); font-size: 0.9rem;">Привет, <?php echo htmlspecialchars($_SESSION['username']); ?>!</span>
                    <a href="/Fixik/logout.php" style="color: var(--danger);">Выход</a>
                <?php else: ?>
                    <a href="/Fixik/login.php">Вход</a>
                    <a href="/Fixik/register.php" class="btn btn-sm">Регистрация</a>
                <?php endif; ?>
            </div>
        </nav>
    </div>
</header>
<main class="container">
