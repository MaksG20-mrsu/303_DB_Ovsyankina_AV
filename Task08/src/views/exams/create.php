<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить экзамен</title>
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
    <h1>Добавить экзамен для: <?= htmlspecialchars($student['full_name']) ?></h1>

    <form method="POST" action="index.php?action=exam_store">
        <input type="hidden" name="student_id" value="<?= $student['id'] ?>">
        <div class="form-group">
            <label>Дисциплина:</label>
            <select name="discipline_id" required>
                <?php if (empty($disciplines)): ?>
                    <option value="">Нет доступных дисциплин</option>
                <?php else: ?>
                    <?php foreach ($disciplines as $d): ?>
                        <option value="<?= $d['id'] ?>">
                            Курс <?= $d['course'] ?>: <?= htmlspecialchars($d['name']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Дата экзамена:</label>
            <input type="date" name="exam_date" value="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="form-group">
            <label>Оценка:</label>
            <select name="score" required>
                <option value="2">2 (неудовлетворительно)</option>
                <option value="3">3 (удовлетворительно)</option>
                <option value="4">4 (хорошо)</option>
                <option value="5" selected>5 (отлично)</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="index.php?action=exams_list&student_id=<?= $student['id'] ?>" class="btn btn-secondary">Отмена</a>
    </form>
</body>
</html>
