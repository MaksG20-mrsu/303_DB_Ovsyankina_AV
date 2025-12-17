<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать экзамен</title>
    <style>
        body { font-family: sans-serif; margin: 20px; }
        .form-group { margin: 10px 0; }
        label { display: inline-block; width: 150px; }
        input, select { padding: 4px 6px; }
    </style>
</head>
<body>
    <h1>Редактировать экзамен</h1>

    <form method="POST" action="index.php?action=exam_update">
        <input type="hidden" name="id" value="<?= $exam['id'] ?>">
        <input type="hidden" name="student_id" value="<?= $student['id'] ?>">

        <div class="form-group">
            <label>Дисциплина:</label>
            <select name="discipline_id" required>
                <?php foreach ($disciplines as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= $d['id'] == $exam['discipline_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($d['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Дата экзамена:</label>
            <input type="date" name="exam_date" value="<?= htmlspecialchars($exam['exam_date']) ?>" required>
        </div>

        <div class="form-group">
            <label>Оценка:</label>
            <select name="score" required>
                <option value="2" <?= $exam['score'] == 2 ? 'selected' : '' ?>>2</option>
                <option value="3" <?= $exam['score'] == 3 ? 'selected' : '' ?>>3</option>
                <option value="4" <?= $exam['score'] == 4 ? 'selected' : '' ?>>4</option>
                <option value="5" <?= $exam['score'] == 5 ? 'selected' : '' ?>>5</option>
            </select>
        </div>

        <button type="submit">Сохранить</button>
        <a href="index.php?action=exams_list&student_id=<?= $student['id'] ?>">Отмена</a>
    </form>
</body>
</html>
