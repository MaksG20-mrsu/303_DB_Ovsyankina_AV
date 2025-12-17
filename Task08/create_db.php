<?php
require_once __DIR__ . '/src/db.php';

$db = getDatabaseConnection();

$db->exec("
    CREATE TABLE IF NOT EXISTS groups (
        id INTEGER PRIMARY KEY,
        number TEXT NOT NULL,
        program TEXT NOT NULL,
        graduation_year INTEGER NOT NULL
    )
");

$db->exec("
    CREATE TABLE IF NOT EXISTS students (
        id INTEGER PRIMARY KEY,
        group_id INTEGER NOT NULL,
        full_name TEXT NOT NULL,
        gender TEXT NOT NULL CHECK(gender IN ('М', 'Ж')),
        birth_date TEXT NOT NULL,
        student_id TEXT NOT NULL UNIQUE,
        FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE
    )
");

$db->exec("
    CREATE TABLE IF NOT EXISTS disciplines (
        id INTEGER PRIMARY KEY,
        program TEXT NOT NULL,
        course INTEGER NOT NULL,
        name TEXT NOT NULL,
        UNIQUE(program, course, name)
    )
");

$db->exec("
    CREATE TABLE IF NOT EXISTS exams (
        id INTEGER PRIMARY KEY,
        student_id INTEGER NOT NULL,
        discipline_id INTEGER NOT NULL,
        exam_date TEXT NOT NULL,
        score INTEGER NOT NULL CHECK(score BETWEEN 2 AND 5),
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
        FOREIGN KEY (discipline_id) REFERENCES disciplines(id) ON DELETE CASCADE
    )
");

$groups = [
    ['1', 'Прикладная математика и информатика', 2028],
    ['2', 'Прикладная математика и информатика', 2028],
];
$stmt = $db->prepare("INSERT OR IGNORE INTO groups (number, program, graduation_year) VALUES (?, ?, ?)");
foreach ($groups as $g) {
    $stmt->execute($g);
}

$disciplines = [
    ['Прикладная математика и информатика', 1, 'Математический анализ'],
    ['Прикладная математика и информатика', 1, 'Основы программирования'],
    ['Прикладная математика и информатика', 2, 'Алгоритмы и структуры данных'],
    ['Прикладная математика и информатика', 2, 'Экономика'],
    ['Прикладная математика и информатика', 3, 'Базы данных'],
    ['Прикладная математика и информатика', 3, 'Уравнения математической физики'],
];

$stmt = $db->prepare("INSERT OR IGNORE INTO disciplines (program, course, name) VALUES (?, ?, ?)");
foreach ($disciplines as $d) {
    $stmt->execute($d);
}

$students = [
    [1, 'Адеев Ильдар Альбертович', 'М', '2004-05-12', '12345678'],
    [2, 'Акыева Айна', 'Ж', '2003-11-30', '12345679'],
    [2, 'Атаджанова Майса', 'Ж', '2003-11-30', '12345680'],
    [2, 'Бабаханов Даянч', 'М', '2005-02-14', '12345681'],
    [1, 'Вдовин Владислав Владимирович', 'М', '2004-08-22', '12345682'],
    [1, 'Власов Георгий Владиславович', 'М', '2003-07-19', '12345683'],
    [1, 'Голиков Евгений Александрович', 'М', '2004-12-03', '12345684'],
    [1, 'Живаев Максим Александрович', 'М', '2005-01-17', '12345685'],
    [2, 'Зельцер Ксения Александровна', 'Ж', '2004-09-28', '12345686'],
    [2, 'Зинов Никита Александрович', 'М', '2003-04-11', '12345687'],
    [1, 'Игнатьева Татьяна Александровна', 'Ж', '2005-06-05', '12345688'],
    [1, 'Калинин Александр Евгеньевич', 'М', '2004-03-21', '12345689'],
    [1, 'Кечемайкин Дмитрий Максимович', 'М', '2003-10-14', '12345690'],
    [2, 'Куколева Полина Александровна', 'Ж', '2004-07-30', '12345691'],
    [2, 'Леонтьев Руслан Минафизович', 'М', '2005-11-09', '12345692'],
    [1, 'Лосева София Романовна', 'Ж', '2004-01-25', '12345693'],
    [2, 'Моисеев Ян Андреевич', 'М', '2003-12-18', '12345694'],
    [1, 'Мулюгин Александр Дмитриевич', 'М', '2004-04-02', '12345695'],
    [2, 'Овсянкина Александра Владимировна', 'Ж', '2005-08-13', '12345696'],
    [1, 'Розанов Ярослав Дмитриевич', 'М', '2004-10-07', '12345697'],
    [2, 'Сапарова Мивегул Байрамовна', 'Ж', '2003-05-29', '12345698'],
    [1, 'Сковородникова Алёна Николаевна', 'Ж', '2004-06-16', '12345699'],
    [1, 'Ферафонтов Алексей Вадимович', 'М', '2005-09-04', '12345700'],
    [1, 'Чесноков Андрей Павлович', 'М', '2003-02-28', '12345701'],
    [2, 'Шаляева Любовь Евгеньевна', 'Ж', '2004-11-22', '12345702'],
    [2, 'Ямашкина Елизавета Михайловна', 'Ж', '2005-03-11', '12345703'],
];

$stmt = $db->prepare("INSERT OR IGNORE INTO students (group_id, full_name, gender, birth_date, student_id) VALUES (?, ?, ?, ?, ?)");
foreach ($students as $s) {
    $stmt->execute($s);
}
