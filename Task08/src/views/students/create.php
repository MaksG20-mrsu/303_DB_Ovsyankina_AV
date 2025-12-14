<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить студента</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
        }
        .form-group {
            margin: 12px 0;
        }
        label {
            display: inline-block;
            width: 150px;
            vertical-align: top;
        }
        input, select {
            padding: 4px;
            font-size: 14px;
        }
        .radio-group {
            display: inline-block;
        }
        .radio-item {
            margin-right: 15px;
        }
    </style>
</head>
<body>
     <h1>Добавить студента</h1>

    <form method="POST" action="index.php?action=student_store">
        <div class="form-group">
            <label>Группа:</label>
            <select name="group_id" required>
                <?php foreach (Student::getGroupsForSelect() as $g): ?>
                    <option value="<?= $g['id'] ?>">
                        <?= htmlspecialchars($g['number']) ?> (<?= htmlspecialchars($g['program']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>ФИО:</label>
            <input type="text" name="full_name" required>
        </div>
        <div class="form-group">
            <label>Номер зачётки:</label>
            <input type="text" name="student_id" required>
        </div>
        <div class="form-group">
            <label>Пол:</label>
            <label><input type="radio" name="gender" value="М" checked> М</label>
            <label><input type="radio" name="gender" value="Ж"> Ж</label>
        </div>
        <button type="submit">Сохранить</button>
        <a href="index.php">Отмена</a>
    </form>
</body>
</html>
