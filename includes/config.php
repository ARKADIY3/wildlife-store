<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'wildlife2_bd');

// Сначала подключаемся без указания БД
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

if ($conn->connect_error) {
    die("Ошибка подключения к MySQL: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Создаём БД если не существует
$conn->query("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db(DB_NAME);

// Проверяем, нужно ли создавать таблицы
$tables_exist = $conn->query("SHOW TABLES LIKE 'users'")->num_rows > 0;

if (!$tables_exist) {
    // Создаём таблицы
    $conn->query("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        description TEXT
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(200) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        category_id INT,
        scientific_name VARCHAR(200),
        light_requirement ENUM('low', 'medium', 'high', 'very_high') DEFAULT 'medium',
        water_requirement ENUM('low', 'medium', 'high') DEFAULT 'medium',
        temperature_min DECIMAL(4,1),
        temperature_max DECIMAL(4,1),
        humidity_requirement ENUM('low', 'medium', 'high') DEFAULT 'medium',
        size_height VARCHAR(50),
        difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'medium',
        poisonous ENUM('none', 'mild', 'severe') DEFAULT 'none',
        bloom_period VARCHAR(100),
        image VARCHAR(255) DEFAULT 'default_plant.png',
        stock INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        total_amount DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
        address TEXT,
        phone VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT,
        product_id INT,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS cart (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        product_id INT,
        quantity INT DEFAULT 1,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS contacts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        subject VARCHAR(200) NOT NULL,
        message TEXT NOT NULL,
        is_read TINYINT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Вставляем тестовые данные
    // Пользователи (пароль: 123456)
    $testPasswordHash = password_hash('123456', PASSWORD_DEFAULT);

    $adminUsername = 'admin';
    $adminEmail = 'admin@wildlife.ru';
    $adminRole = 'admin';

    $userUsername = 'user';
    $userEmail = 'user@wildlife.ru';
    $userRole = 'user';

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?), (?, ?, ?, ?)");
    $stmt->bind_param(
        "ssssssss",
        $adminUsername,
        $adminEmail,
        $testPasswordHash,
        $adminRole,
        $userUsername,
        $userEmail,
        $testPasswordHash,
        $userRole
    );
    $stmt->execute();
    $stmt->close();

    // Категории растений
    $conn->query("INSERT INTO categories (name, description) VALUES
        ('Комнатные растения', 'Популярные растения для дома и квартиры'),
        ('Суккуленты и кактусы', 'Неприхотливые растения, запасающие влагу'),
        ('Тропические растения', 'Экзотические растения из тропических лесов'),
        ('Цветущие растения', 'Растения с красивыми цветами'),
        ('Пальмы и крупномеры', 'Крупные растения для озеленения помещений')");

    // Товары (растения) - ВСЕ 12 РАСТЕНИЙ!
    $conn->query("INSERT INTO products (
        name, description, price, category_id, 
        scientific_name, light_requirement, water_requirement, 
        temperature_min, temperature_max, humidity_requirement, 
        size_height, difficulty, poisonous, bloom_period, image, stock
    ) VALUES
        ('Монстера Деликатесная', 'Крупное тропическое растение с резными листьями', 3490.00, 3,
         'Monstera deliciosa', 'medium', 'medium', 18.0, 30.0, 'high',
         '100-150 см', 'easy', 'mild', 'редко', 'monstera.png', 15),
        
        ('Сансевиерия \"Тещин язык\"', 'Неприхотливое растение с жесткими вертикальными листьями', 890.00, 1,
         'Sansevieria trifasciata', 'low', 'low', 15.0, 35.0, 'low',
         '50-80 см', 'easy', 'severe', 'не цветет', 'sansevieria.png', 30),
        
        ('Кактус Эхинокактус Грузони', 'Шаровидный кактус с золотистыми колючками', 1290.00, 2,
         'Echinocactus grusonii', 'high', 'low', 10.0, 35.0, 'low',
         '20-40 см', 'easy', 'mild', 'лето', 'echinocactus.png', 25),
        
        ('Орхидея Фаленопсис', 'Популярная орхидея с длительным цветением', 2490.00, 4,
         'Phalaenopsis', 'medium', 'medium', 18.0, 28.0, 'high',
         '30-50 см', 'medium', 'none', 'до 6 месяцев', 'phalaenopsis.png', 20),
        
        ('Замиокулькас \"Долларовое дерево\"', 'Неприхотливое растение с глянцевыми листьями', 1890.00, 1,
         'Zamioculcas zamiifolia', 'low', 'low', 16.0, 30.0, 'low',
         '60-80 см', 'easy', 'severe', 'редко', 'zamioculcas.png', 18),
        
        ('Фикус Бенджамина', 'Популярное деревце для дома и офиса', 2990.00, 1,
         'Ficus benjamina', 'medium', 'medium', 15.0, 28.0, 'medium',
         '80-120 см', 'medium', 'mild', 'не цветет', 'ficus.png', 22),
        
        ('Алоэ Вера', 'Лечебное растение с мясистыми листьями', 590.00, 2,
         'Aloe vera', 'high', 'low', 10.0, 32.0, 'low',
         '20-30 см', 'easy', 'mild', 'редко', 'aloe.png', 40),
        
        ('Драцена Маргината', 'Пальмообразное растение с тонкими листьями', 1990.00, 5,
         'Dracaena marginata', 'medium', 'medium', 16.0, 28.0, 'medium',
         '100-150 см', 'easy', 'severe', 'не цветет', 'dracaena.png', 12),
        
        ('Хлорофитум хохлатый', 'Ампельное растение, очищающее воздух', 490.00, 1,
         'Chlorophytum comosum', 'medium', 'medium', 12.0, 30.0, 'medium',
         '20-40 см (побеги до 80 см)', 'easy', 'none', 'весна-лето', 'chlorophytum.png', 35),
        
        ('Крассула \"Денежное дерево\"', 'Толстянка с монетовидными листьями', 890.00, 2,
         'Crassula ovata', 'high', 'low', 12.0, 30.0, 'low',
         '30-60 см', 'easy', 'mild', 'зима', 'crassula.png', 28),
        
        ('Спатифиллум \"Женское счастье\"', 'Цветущее растение с белыми цветами', 1590.00, 4,
         'Spathiphyllum', 'medium', 'high', 18.0, 27.0, 'high',
         '40-60 см', 'medium', 'severe', 'весна-лето', 'spathiphyllum.png', 15),
        
        ('Юкка слоновая', 'Крупное растение для просторных помещений', 4990.00, 5,
         'Yucca elephantipes', 'high', 'low', 10.0, 30.0, 'low',
         '150-200 см', 'easy', 'mild', 'лето', 'yucca.png', 8)");
}

// Гарантируем наличие тестовых аккаунтов и исправляем старые тестовые пароли
if ($conn->query("SHOW TABLES LIKE 'users'")->num_rows > 0) {
    $testPassword = '123456';

    $ensureTestUser = function ($username, $email, $role) use ($conn, $testPassword) {
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $targetHash = password_hash($testPassword, PASSWORD_DEFAULT);

        if (!$row) {
            $ins = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
            $ins->bind_param("ssss", $username, $email, $targetHash, $role);
            $ins->execute();
            $ins->close();
            return;
        }

        $storedPassword = (string)($row['password'] ?? '');
        $info = password_get_info($storedPassword);

        $needUpdate = false;

        if (($info['algo'] ?? 0) !== 0) {
            if (!password_verify($testPassword, $storedPassword) && password_verify('password', $storedPassword)) {
                $needUpdate = true;
            }
        } else {
            if (hash_equals($testPassword, $storedPassword)) {
                $needUpdate = true;
            }
        }

        if ($needUpdate) {
            $upd = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $upd->bind_param("si", $targetHash, $row['id']);
            $upd->execute();
            $upd->close();
        }
    };

    $ensureTestUser('admin', 'admin@wildlife.ru', 'admin');
    $ensureTestUser('user', 'user@wildlife.ru', 'user');
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirect($url) {
    header("Location: $url");
    exit();
}
?>