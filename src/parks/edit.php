<?php
require_once '../db.php';
require_once '../auth.php';

if (!isManager()) {
    header('Location: ../index.php');
    exit;
}

$pdo = getDbConnection();
$parkId = $_GET['id'] ?? null;
if (!$parkId) {
    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM parks WHERE id = ?");
$stmt->execute([$parkId]);
$park = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$park) {
    header('Location: list.php');
    exit;
}

$stmt = $pdo->prepare("SELECT c.* FROM cars c JOIN park_car pc ON c.id = pc.car_id WHERE pc.park_id = ?");
$stmt->execute([$parkId]);
$existingCars = $stmt->fetchAll(PDO::FETCH_ASSOC);

$errors = [];
$parkData = $_POST['park'] ?? $park;
$carsData = $_POST['cars'] ?? $existingCars;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($parkData['name'])) $errors['name'] = 'Название обязательно';
    if (empty($parkData['address'])) $errors['address'] = 'Адрес обязателен';

    if (empty($errors)) {
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("UPDATE parks SET name = ?, address = ?, schedule = ? WHERE id = ?");
            $stmt->execute([$parkData['name'], $parkData['address'], $parkData['schedule'] ?? null, $parkId]);

            $stmt = $pdo->prepare("DELETE FROM park_car WHERE park_id = ?");
            $stmt->execute([$parkId]);

            foreach ($carsData as $car) {
                if (empty($car['number']) || empty($car['driver_name'])) {
                    continue;
                }
                $stmt = $pdo->prepare("SELECT id FROM cars WHERE number = ? AND driver_name = ?");
                $stmt->execute([$car['number'], $car['driver_name']]);
                $carId = $stmt->fetchColumn();

                if (!$carId) {
                    $stmt = $pdo->prepare("INSERT INTO cars (number, driver_name) VALUES (?, ?)");
                    $stmt->execute([$car['number'], $car['driver_name']]);
                    $carId = $pdo->lastInsertId('cars_id_seq');
                }

                $stmt = $pdo->prepare("INSERT INTO park_car (park_id, car_id) VALUES (?, ?) ON CONFLICT DO NOTHING");
                $stmt->execute([$parkId, $carId]);
            }

            $pdo->commit();
            header('Location: list.php');
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors['general'] = 'Ошибка сохранения: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Редактирование автопарка</title>
    <script src="../assets/script.js"></script>
</head>
<body>
<h1>Редактирование автопарка</h1>
<?php if (!empty($errors['general'])) echo "<p style='color:red'>{$errors['general']}</p>"; ?>
<form method="POST">
    <div>
        <label>Название*:</label>
        <input type="text" name="park[name]" value="<?= htmlspecialchars($parkData['name']) ?>">
        <?php if (!empty($errors['name'])) echo "<span style='color:red'>{$errors['name']}</span>"; ?>
    </div>
    <div>
        <label>Адрес*:</label>
        <input type="text" name="park[address]" value="<?= htmlspecialchars($parkData['address']) ?>">
        <?php if (!empty($errors['address'])) echo "<span style='color:red'>{$errors['address']}</span>"; ?>
    </div>
    <div>
        <label>График работы:</label>
        <input type="text" name="park[schedule]" value="<?= htmlspecialchars($parkData['schedule'] ?? '') ?>">
    </div>
    <h3>Машины</h3>
    <div id="cars-container">
        <?php foreach ($carsData as $i => $car): ?>
            <div class="car-row">
                <input type="text" name="cars[<?= $i ?>][number]" value="<?= htmlspecialchars($car['number']) ?>" placeholder="Номер*">
                <input type="text" name="cars[<?= $i ?>][driver_name]" value="<?= htmlspecialchars($car['driver_name']) ?>" placeholder="Имя водителя*">
                <button type="button" onclick="removeCar(this)">Удалить</button>
            </div>
        <?php endforeach; ?>
    </div>
    <button type="button" onclick="addCar()">Добавить машину</button>
    <button type="submit">Сохранить</button>
</form>
<a href="list.php">Назад</a>
</body>
</html>