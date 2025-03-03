<?php
require_once 'auth.php';

if (!isLoggedIn()) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        if (login($username, $password)) {
            header('Location: index.php');
            exit;
        } else {
            $error = "Неверный логин или пароль";
        }
    }
} else {
    if (isset($_GET['logout'])) {
        logout();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Управление автопарками</title>
</head>
<body>
<?php if (!isLoggedIn()): ?>
    <h1>Вход</h1>
    <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    <form method="POST">
        <div>
            <label>Логин:</label>
            <input type="text" name="username" required>
        </div>
        <div>
            <label>Пароль:</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">Войти</button>
    </form>
<?php else: ?>
    <h1>Добро пожаловать, <?= htmlspecialchars($_SESSION['user']['username']) ?></h1>
    <p>Роль: <?= $_SESSION['user']['role'] ?></p>
    <a href="parks/list.php">Список автопарков</a> |
    <a href="cars/list.php">Список машин</a> |
    <a href="?logout=1">Выйти</a>
<?php endif; ?>
</body>
</html>