<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Удалить экзамен</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
    </style>
</head>
<body>
    <h1>Удалить экзамен</h1>
    <p>Вы действительно хотите удалить экзамен?</p>
    <p><strong><?= htmlspecialchars($exam['discipline_name']) ?></strong></p>

    <form method="POST" action="index.php?action=exam_destroy">
        <input type="hidden" name="id" value="<?= $exam['id'] ?>">
        <button type="submit">Да, удалить</button>
        <a href="index.php?action=exams_list&student_id=<?= $student['id'] ?>">Отмена</a>
    </form>
</body>
</html>
