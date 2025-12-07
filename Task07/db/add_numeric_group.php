<?php
$db = new PDO('sqlite:students.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Добавляем группу только с цифрами
$currentYear = date('Y');
$stmt = $db->prepare("INSERT OR IGNORE INTO groups (name, specialization, graduation_year) VALUES (?, ?, ?)");
$stmt->execute(['777', 'Тестовая группа', $currentYear + 1]);

// Добавляем студента в эту группу
$group_id = $db->lastInsertId();
if ($group_id) {
    $stmt = $db->prepare("INSERT INTO students (group_id, last_name, first_name, middle_name, gender, birth_date, student_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$group_id, 'Тестов', 'Тест', 'Тестович', 'Мужской', '2004-01-01', '777-001']);
    echo "✅ Добавлена группа '777' с одним студентом\n";
} else {
    echo "⚠️ Группа уже существует\n";
}
