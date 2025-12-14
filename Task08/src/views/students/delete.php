<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Удалить студента</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
    </style>
</head>
<body>
    <h1>Удалить студента</h1>
    <p>Вы действительно хотите удалить студента?</p>
    <p><strong><?= htmlspecialchars($student['full_name']) ?></strong></p>

    <form method="POST" action="index.php?action=student_destroy">
        <input type="hidden" name="id" value="<?= $student['id'] ?>">
        <button type="submit">Да, удалить</button>
        <a href="index.php">Отмена</a>
    </form>
</body>
</html>
