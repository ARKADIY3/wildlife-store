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
            --bg: #f3f4f6;
            --card: #ffffff;
            --text: #1f2937;
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
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .nav {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .nav strong { font-size: 1.25rem; color: var(--text); font-weight: 800; }
        .nav-right { margin-left: auto; display: flex; gap: 1rem; align-items: center; }

        /* Main Content */
        main.container {
            padding-top: 2rem;
            padding-bottom: 3rem;
            flex: 1;
        }

        h1, h2, h3 { margin-top: 0; color: #111827; }
        h1 { font-size: 2rem; margin-bottom: 1.5rem; font-weight: 700; }
        h2 { font-size: 1.5rem; margin-bottom: 1rem; font-weight: 600; }

        /* Cards */
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
        }

        /* Buttons */
        .btn, button {
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
            font-size: 0.95rem;
            line-height: 1.25rem;
            text-decoration: none;
        }
        .btn:hover, button:hover { background-color: var(--primary-hover); color: white; text-decoration: none; }
        
        .btn-outline { background: transparent; border-color: var(--border); color: var(--text); }
        .btn-outline:hover { border-color: var(--primary); background: transparent; color: var(--primary); }
        
        .btn-danger { background-color: var(--danger); }
        .btn-danger:hover { background-color: #b91c1c; }
        
        .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.875rem; }
        .btn-block { width: 100%; display: flex; }

        /* Forms */
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 500; color: #374151; }
        input[type="text"], input[type="email"], input[type="password"], input[type="number"], input[type="tel"], select, textarea {
            width: 100%;
            padding: 0.625rem;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: #fff;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        /* Catalog Filters */
        .filters-form {
            background: var(--card);
            padding: 1.5rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
            border: 1px solid var(--border);
        }
        .filters-form fieldset { border: none; padding: 0; margin: 0; }
        .filters-form legend { font-weight: 600; font-size: 1.1rem; margin-bottom: 1rem; border-bottom: 1px solid var(--border); width: 100%; padding-bottom: 0.5rem; }
        
          .filters-grid {
              display: grid;
              grid-template-columns: repeat(5, 1fr);
              gap: 1rem;
              align-items: end;
          }
          
          .filter-field {
              display: flex;
              flex-direction: column;
          }
          
          .filter-field label {
              margin-bottom: 0.5rem;
          }
          
          .filter-field input,
          .filter-field select {
              width: 100%;
              height: 42px;
          }

          .filters-actions { 
              display: flex; 
              gap: 1rem; 
              grid-column: 1 / -1;
              margin-top: 0.5rem;
          }
          .filters-actions button, .filters-actions .btn { height: 42px; flex: 1; }
          
          @media (max-width: 1024px) {
              .filters-grid {
                  grid-template-columns: repeat(2, 1fr);
              }
          }
          
          @media (max-width: 640px) {
              .filters-grid {
                  grid-template-columns: 1fr;
              }
          }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--card);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            margin-bottom: 1.5rem;
        }
        th, td { padding: 0.75rem 1rem; text-align: left; border-bottom: 1px solid var(--border); }
        th { background-color: #f9fafb; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; color: var(--muted); letter-spacing: 0.05em; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background-color: #f9fafb; }

        /* Products Grid */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .product-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.2s;
            height: 100%;
        }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: contain;
            background: #fff;
            padding: 1rem;
            border-bottom: 1px solid var(--border);
        }
        .product-card h3 { padding: 1rem 1rem 0.5rem; margin: 0; font-size: 1.1rem; }
        .product-card p { padding: 0 1rem; margin: 0 0 0.5rem; color: var(--muted); font-size: 0.9rem; }
        .product-card p strong { color: var(--text); font-size: 1.25rem; }
        .product-card p:last-child { padding-bottom: 1rem; margin-top: auto; display: flex; gap: 0.5rem; }
        
          /* Auth Page */
          .auth-page { max-width: 400px; margin: 2rem auto; text-align: center; }
          .auth-links { text-align: center; margin-top: 1rem; font-size: 0.9rem; }
          .form-card { text-align: left; }
          .auth-button-center { text-align: center; }

          /* Stack Center (Contact) */
          .stack-center {
              display: flex;
              flex-direction: column;
              gap: 2rem;
              max-width: 600px;
              margin: 0 auto;
          }
          
          .text-center {
              text-align: center;
          }
        .hint {
            position: relative;
            display: inline-flex;
            align-items: center;
            cursor: help;
            gap: 0.5rem;
            margin: 0 0 1.5rem 0;
            font-size: 0.9rem;
            color: var(--muted);
        }
        .hint__badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--muted);
            color: white;
            font-size: 11px;
            font-weight: bold;
        }
        .hint__content {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: #1f2937;
            color: white;
            padding: 1rem;
            border-radius: var(--radius);
            width: max-content;
            min-width: 200px;
            z-index: 100;
            margin-top: 0.5rem;
            box-shadow: var(--shadow);
            font-size: 0.85rem;
        }
        .hint__content div { margin-bottom: 0.25rem; }
        .hint:hover .hint__content { display: block; }

        /* Alerts */
        .alert { padding: 1rem; border-radius: var(--radius); margin-bottom: 1rem; border: 1px solid transparent; }
        .alert-success { background-color: #ecfdf5; color: #065f46; border-color: #a7f3d0; }
        .alert-error { background-color: #fef2f2; color: #991b1b; border-color: #fecaca; }

          /* Product Detail Page */
          .product-detail {
              display: grid;
              grid-template-columns: 1fr 1fr;
              gap: 2rem;
              margin-top: 2rem;
          }
          
          .product-image img {
              width: 100%;
              height: auto;
              border-radius: var(--radius);
              border: 1px solid var(--border);
              padding: 1rem;
              background: #fff;
          }
          
          .product-specs-card,
          .product-description-card {
              background: var(--card);
              border: 1px solid var(--border);
              border-radius: var(--radius);
              padding: 1.5rem;
              box-shadow: var(--shadow);
              margin-bottom: 1.5rem;
          }
          
          .product-specs-card h3,
          .product-description-card h3 {
              margin-top: 0;
              margin-bottom: 1rem;
              padding-bottom: 0.75rem;
              border-bottom: 2px solid var(--primary);
              color: var(--text);
          }
          
          .spec-row {
              display: flex;
              justify-content: space-between;
              padding: 0.75rem 0;
              border-bottom: 1px solid var(--border);
          }
          
          .spec-row:last-child {
              border-bottom: none;
          }
          
          .spec-label {
              font-weight: 500;
              color: var(--muted);
          }
          
          .spec-value {
              text-align: right;
              color: var(--text);
          }
          
          .product-actions {
              background: var(--card);
              border: 1px solid var(--border);
              border-radius: var(--radius);
              padding: 1.5rem;
              box-shadow: var(--shadow);
          }
          
          .quantity-selector {
              margin-bottom: 1rem;
          }
          
          .quantity-selector label {
              display: block;
              margin-bottom: 0.5rem;
          }
          
          .quantity-selector input {
              width: 100%;
          }
          
          .product-actions button {
              width: 100%;
          }
          
          @media (max-width: 768px) {
              .product-detail {
                  grid-template-columns: 1fr;
              }
          }

          /* Footer */
          .site-footer {
              background: var(--card);
              border-top: 1px solid var(--border);
              padding: 2rem 0;
              margin-top: auto;
              color: var(--muted);
              text-align: center;
              font-size: 0.9rem;
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
