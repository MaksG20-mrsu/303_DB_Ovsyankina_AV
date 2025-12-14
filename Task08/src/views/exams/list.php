<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Экзамены студента</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
            background: white;
            color: black;
        }
        h1 {
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 8px 10px;
            text-align: left;
            border: 1px solid #000;
        }
        th {
            font-weight: bold;
        }
        a {
            text-decoration: underline;
            color: #000;
            margin-right: 10px;
        }
        a:hover {
            text-decoration: none;
        }
    </style>
</head>
<body>
    <h1>Экзамены студента: <?= htmlspecialchars($student['full_name']) ?></h1>

    <a href="index.php?action=exam_create&student_id=<?= $student['id'] ?>">Добавить экзамен</a>
    <a href="index.php">← Назад к списку</a>

    <table>
        <thead>
            <tr>
                <th>Дата</th>
                <th>Курс</th>
                <th>Дисциплина</th>
                <th>Оценка</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($exams)): ?>
                <tr><td colspan="5" style="text-align:center;">Нет экзаменов</td></tr>
            <?php else: ?>
                <?php foreach ($exams as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['exam_date']) ?></td>
                        <td><?= htmlspecialchars($e['course']) ?></td>
                        <td><?= htmlspecialchars($e['discipline_name']) ?></td>
                        <td><?= htmlspecialchars($e['score']) ?></td>
                        <td>
                            <a href="index.php?action=exam_edit&id=<?= $e['id'] ?>">Редактировать</a>
                            <a href="index.php?action=exam_delete&id=<?= $e['id'] ?>">Удалить</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
