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
    <title>Админ-панель - Wildlife</title>
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
            .nav a { color: var(--primary); text-decoration: none; transition: color 0.2s; }
            .nav a:hover { color: var(--primary-hover); }

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
          
          .card-pad { padding: 1.5rem; }
          .card + .card { margin-top: 1.5rem; }

          /* Forms */
          label { display: block; font-weight: 500; margin-bottom: 0.5rem; }

          input[type="text"],
          input[type="number"],
          input[type="email"],
          input[type="password"],
          textarea,
          select {
              width: 100%;
              border: 1px solid var(--border);
              border-radius: var(--radius);
              padding: 0.5rem 0.75rem;
              outline: none;
              background: #fff;
              color: var(--text);
              transition: border-color 0.2s, box-shadow 0.2s;
          }

          textarea { resize: vertical; min-height: 100px; }

          input:focus, select:focus, textarea:focus {
              border-color: var(--primary);
              box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
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
              text-decoration: none;
              transition: all 0.2s;
              background: var(--primary);
              color: #fff;
          }

          button:hover, .btn:hover { 
              background: var(--primary-hover); 
              text-decoration: none;
          }

          /* Alerts */
          .alert {
              padding: 0.75rem 1rem;
              border-radius: var(--radius);
              border: 1px solid var(--border);
              margin-bottom: 1rem;
          }

          .alert-error {
              border-color: #fecaca;
              background: #fef2f2;
              color: var(--danger);
          }

          .alert-success {
              border-color: #bbf7d0;
              background: #f0fdf4;
              color: var(--success);
          }

          /* Tables */
          table {
              width: 100%;
              border-collapse: collapse;
              background: var(--card);
              border: 1px solid var(--border);
              border-radius: var(--radius);
              box-shadow: var(--shadow);
              overflow: hidden;
          }

          th, td {
              padding: 0.75rem 1rem;
              border-bottom: 1px solid var(--border);
              text-align: left;
          }

          th {
              background: #f9fafb;
              font-weight: 600;
              font-size: 0.875rem;
              text-transform: uppercase;
              letter-spacing: 0.05em;
              color: var(--muted);
          }

          tr:last-child td { border-bottom: none; }

          @media (max-width: 760px) {
              table { display: block; overflow-x: auto; }
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



          /* Природная палитра */
:root {
  --green-primary: #27AE60;
  --green-dark: #1B5E20;
}

/* ЛОГОТИП Wildlife - ТОЛСТЫЙ рукописный стиль */
.site-header a[href="/wildlife/index.php"] {
  display: inline-block;
  font-family: cursive, 'Comic Sans MS', 'Brush Script MT', serif;
  font-size: 1.6em;
  font-weight: 900; /* Максимально толстый */
  color: var(--green-dark) !important;
  text-decoration: none;
  padding: 4px 0;
  margin-right: 25px;
  transition: color 0.3s ease;
  line-height: 1.3;
  font-style: italic;
  text-shadow: 0 1px 1px rgba(0,0,0,0.1);
}

.site-header a[href="/wildlife/index.php"] strong {
  font-weight: 900;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.site-header a[href="/wildlife/index.php"]:hover {
  color: var(--green-primary);
}

    </style>
</head>
<body class="site">
<header class="site-header">
    <div class="container">
        <nav class="nav">
            <a href="/wildlife/index.php"><strong>Wildlife</strong></a>
            <a href="/wildlife/admin/index.php">Главная</a>
            <a href="/wildlife/admin/products.php">Товары</a>
            <a href="/wildlife/admin/categories.php">Категории</a>
            <a href="/wildlife/admin/orders.php">Заказы</a>
            <a href="/wildlife/admin/users.php">Пользователи</a>
            <a href="/wildlife/admin/contacts.php">Сообщения</a>
            <a href="/wildlife/logout.php">Выход</a>
        </nav>
    </div>
</header>
<main class="container">
