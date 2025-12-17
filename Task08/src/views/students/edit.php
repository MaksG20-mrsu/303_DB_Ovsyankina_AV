<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать студента</title>
    <style>
    body {
        font-family: sans-serif;
        margin: 20px;
    }
    .form-group {
        margin: 10px 0;
    }
    label {
        display: inline-block;
        width: 120px;
        margin-right: 10px;
    }
    input, select, button {
        padding: 4px 6px;
        font-size: 14px;
    }
    .actions a, .btn {
        margin-right: 8px;
        text-decoration: underline;
        color: #000;
    }
    .actions a:hover, .btn:hover {
        text-decoration: none;
    }
    .input_label {
        width: 1;
    }
</style>
</head>
<body>
    <h1>Редактировать студента</h1>

    <form method="POST" action="index.php?action=student_update">
        <input type="hidden" name="id" value="<?= $student['id'] ?>">
        <div class="form-group">
            <label>Группа:</label>
            <select name="group_id" required>
                <?php foreach ($groups as $g): ?>
                    <option value="<?= $g['id'] ?>" <?= $g['id'] == $student['group_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['number']) ?> (<?= htmlspecialchars($g['program']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>ФИО:</label>
            <input type="text" name="full_name" value="<?= htmlspecialchars($student['full_name']) ?>" required>
        </div>
        <div class="form-group">
            <label>Номер зачётки:</label>
            <input type="text" name="student_id" value="<?= htmlspecialchars($student['student_id']) ?>" required>
        </div>
        <div class="form-group">
            <label>Пол:</label>
            <label><input type="radio" name="gender" value="М" <?= $student['gender'] === 'М' ? 'checked' : '' ?>> М</label>
            <label><input type="radio" name="gender" value="Ж" <?= $student['gender'] === 'Ж' ? 'checked' : '' ?>> Ж</label>
        </div>
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="index.php" class="btn btn-secondary">Отмена</a>
    </form>
</body>
</html>
