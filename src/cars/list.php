<?php
require_once '../db.php';
require_once '../auth.php';

if (!isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}

$pdo = getDbConnection();
if (isDriver()) {
    $stmt = $pdo->prepare("SELECT * FROM cars WHERE created_by = ?");
    $stmt->execute([$_SESSION['user']['id']]);
} else {
    $stmt = $pdo->query("SELECT * FROM cars");
}
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($cars as &$car) {
    $stmt = $pdo->prepare("SELECT p.name FROM parks p JOIN park_car pc ON p.id = pc.park_id WHERE pc.car_id = ?");
    $stmt->execute([$car['id']]);
    $car['parks'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Список машин</title>
</head>
<body>
<h1>Список машин</h1>
<a href="create.php">Создать машину</a>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Номер</th>
        <th>Имя водителя</th>
        <th>Автопарки</th>
        <?php if (!isDriver()): ?>
            <th>Создано пользователем</th>
        <?php endif; ?>
        <th>Действия</th>
    </tr>
    <?php foreach ($cars as $car): ?>
        <tr>
            <td><?= $car['id'] ?></td>
            <td><?= htmlspecialchars($car['number']) ?></td>
            <td><?= htmlspecialchars($car['driver_name']) ?></td>
            <td><?= htmlspecialchars(implode(', ', $car['parks'])) ?></td>
            <?php if (!isDriver()): ?>
                <td><?= $car['created_by'] ?></td>
            <?php endif; ?>
            <td>
                <?php if (isDriver() && $car['created_by'] == $_SESSION['user']['id']): ?>
                    <a href="edit.php?id=<?= $car['id'] ?>">Редактировать</a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<a href="../index.php">Назад</a>
</body>
</html>