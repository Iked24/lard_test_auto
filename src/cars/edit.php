<?php
require_once '../db.php';
require_once '../auth.php';

if (!isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}

$pdo = getDbConnection();
$carId = $_GET['id'] ?? null;
if (!$carId) {
    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ? AND created_by = ?");
$stmt->execute([$carId, $_SESSION['user']['id']]);
$car = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$car) {
    header('Location: list.php');
    exit;
}

$errors = [];
$carData = $_POST['car'] ?? $car;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($carData['number'])) $errors['number'] = 'Номер обязателен';
    if (empty($carData['driver_name'])) $errors['driver_name'] = 'Имя водителя обязательно';

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE cars SET number = ?, driver_name = ? WHERE id = ? AND created_by = ?");
            $stmt->execute([$carData['number'], $carData['driver_name'], $carId, $_SESSION['user']['id']]);
            header('Location: list.php');
            exit;
        } catch (Exception $e) {
            $errors['general'] = 'Ошибка сохранения: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Редактирование машины</title>
</head>
<body>
<h1>Редактирование машины</h1>
<?php if (!empty($errors['general'])) echo "<p style='color:red'>{$errors['general']}</p>"; ?>
<form method="POST">
    <div>
        <label>Номер*:</label>
        <input type="text" name="car[number]" value="<?= htmlspecialchars($carData['number']) ?>">
        <?php if (!empty($errors['number'])) echo "<span style='color:red'>{$errors['number']}</span>"; ?>
    </div>
    <div>
        <label>Имя водителя*:</label>
        <input type="text" name="car[driver_name]" value="<?= htmlspecialchars($carData['driver_name']) ?>">
        <?php if (!empty($errors['driver_name'])) echo "<span style='color:red'>{$errors['driver_name']}</span>"; ?>
    </div>
    <button type="submit">Сохранить</button>
</form>
<a href="list.php">Назад</a>
</body>
</html>