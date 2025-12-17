<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Студенты</title>
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
        form {
            margin: 10px 0;
        }
        select, button {
            padding: 4px 8px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <h1>Список студентов</h1>

    <form method="GET">
        <label>
            Фильтр по группе:
            <select name="group">
                <option value="">Все действующие</option>
                <?php foreach ($groups as $num): ?>
                    <option value="<?= htmlspecialchars($num) ?>" <?= ($_GET['group'] ?? '') === $num ? 'selected' : '' ?>>
                        <?= htmlspecialchars($num) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit">Применить</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Группа</th>
                <th>ФИО</th>
                <th>Пол</th>
                <th>Студ. билет</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($students)): ?>
                <tr><td colspan="5" style="text-align:center;">Нет студентов</td></tr>
            <?php else: ?>
                <?php foreach ($students as $s): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['group_number']) ?></td>
                        <td><?= htmlspecialchars($s['full_name']) ?></td>
                        <td><?= htmlspecialchars($s['gender']) ?></td>
                        <td><?= htmlspecialchars($s['student_id']) ?></td>
                        <td>
                            <a href="index.php?action=student_edit&id=<?= $s['id'] ?>">Редактировать</a>
                            <a href="index.php?action=student_delete&id=<?= $s['id'] ?>">Удалить</a>
                            <a href="index.php?action=exams_list&student_id=<?= $s['id'] ?>">Экзамены</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="index.php?action=student_create">Добавить студента</a>
</body>
</html>
