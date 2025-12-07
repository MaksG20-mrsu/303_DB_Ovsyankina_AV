<?php
// Создаем базу данных студентов
$db = new PDO('sqlite:students.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Создаем таблицу групп
$db->exec("CREATE TABLE IF NOT EXISTS groups (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(10) NOT NULL UNIQUE,
    specialization VARCHAR(100) NOT NULL,
    graduation_year INTEGER NOT NULL
)");

// Создаем таблицу студентов
$db->exec("CREATE TABLE IF NOT EXISTS students (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    group_id INTEGER,
    last_name VARCHAR(50) NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    gender VARCHAR(10) NOT NULL CHECK(gender IN ('Мужской', 'Женский')),
    birth_date DATE NOT NULL,
    student_id VARCHAR(20) NOT NULL UNIQUE,
    FOREIGN KEY (group_id) REFERENCES groups(id)
)");

// Очищаем старые данные
$db->exec("DELETE FROM students");
$db->exec("DELETE FROM groups");

// Добавляем группы (текущий год + 4 года обучения)
$currentYear = date('Y');
$groups = [
    ['КБ-404', 'Кибербезопасность', $currentYear + 1],
    ['КТ-101', 'Компьютерные технологии', $currentYear],
    ['ПИ-303', 'Программная инженерия', $currentYear + 2],
    ['ИВ-202', 'Информационные системы', $currentYear - 1], // Архивная (не должна показываться)
    ['БИ-505', 'Бизнес-информатика', $currentYear + 3]
];

$groupIds = [];
foreach ($groups as $group) {
    $stmt = $db->prepare("INSERT INTO groups (name, specialization, graduation_year) VALUES (?, ?, ?)");
    $stmt->execute($group);
    $groupIds[$group[0]] = $db->lastInsertId();
}

// Добавляем студентов
$students = [
    [$groupIds['КБ-404'], 'Федоров', 'Сергей', 'Игоревич', 'Мужской', '2003-12-05', 'КБ-404-001'],
    [$groupIds['КТ-101'], 'Иванов', 'Иван', 'Иванович', 'Мужской', '2003-05-15', 'КТ-101-001'],
    [$groupIds['КТ-101'], 'Петрова', 'Анна', 'Сергеевна', 'Женский', '2004-02-20', 'КТ-101-002'],
    [$groupIds['КТ-101'], 'Сидоров', 'Алексей', 'Дмитриевич', 'Мужской', '2003-11-10', 'КТ-101-003'],
    [$groupIds['ПИ-303'], 'Николаева', 'Ольга', 'Викторовна', 'Женский', '2004-07-12', 'ПИ-303-001'],
    [$groupIds['ПИ-303'], 'Козлов', 'Дмитрий', 'Александрович', 'Мужской', '2004-03-18', 'ПИ-303-002'],
    [$groupIds['ИВ-202'], 'Смирнов', 'Андрей', 'Владимирович', 'Мужской', '2002-09-25', 'ИВ-202-001'], // Архивный студент
    [$groupIds['БИ-505'], 'Волкова', 'Екатерина', 'Михайловна', 'Женский', '2005-01-30', 'БИ-505-001']
];

$stmt = $db->prepare("
    INSERT INTO students (group_id, last_name, first_name, middle_name, gender, birth_date, student_id) 
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

foreach ($students as $student) {
    $stmt->execute($student);
}

echo "✅ База данных успешно создана!\n";
echo "📊 Групп: " . count($groups) . "\n";
echo "👥 Студентов: " . count($students) . "\n";
echo "📁 Файл: students.db\n";
