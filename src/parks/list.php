<?php
require_once '../db.php';
require_once '../auth.php';

if (!isLoggedIn()) {
    header('Location: ../index.php');
    exit;
}

$pdo = getDbConnection();
$stmt = $pdo->query("SELECT * FROM parks");
$parks = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($parks as &$park) {
    $stmt = $pdo->prepare("SELECT c.number, c.driver_name FROM cars c JOIN park_car pc ON c.id = pc.car_id WHERE pc.park_id = ?");
    $stmt->execute([$park['id']]);
    $park['cars'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Список автопарков</title>
</head>
<body>
<h1>Список автопарков</h1>
<?php if (isManager()): ?>
    <a href="create.php">Создать автопарк</a>
<?php endif; ?>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Название</th>
        <th>Адрес</th>
        <th>График работы</th>
        <th>Машины</th>
        <?php if (isManager()): ?>
            <th>Действия</th>
        <?php endif; ?>
    </tr>
    <?php foreach ($parks as $park): ?>
        <tr>
            <td><?= $park['id'] ?></td>
            <td><?= htmlspecialchars($park['name']) ?></td>
            <td><?= htmlspecialchars($park['address']) ?></td>
            <td><?= htmlspecialchars($park['schedule'] ?? '') ?></td>
            <td>
                <?php foreach ($park['cars'] as $car): ?>
                    <?= htmlspecialchars($car['number'] . ' (' . $car['driver_name'] . ')') ?><br>
                <?php endforeach; ?>
            </td>
            <?php if (isManager()): ?>
                <td>
                    <a href="edit.php?id=<?= $park['id'] ?>">Редактировать</a> |
                    <a href="delete.php?id=<?= $park['id'] ?>" onclick="return confirm('Вы уверены?')">Удалить</a>
                </td>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
</table>
<a href="../index.php">Назад</a>
</body>
</html>