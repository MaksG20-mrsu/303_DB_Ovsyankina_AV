<?php
class Student {
    public static function all($groupNumber = null) {
        $db = getDatabaseConnection();
        $sql = "
            SELECT s.*, g.number AS group_number, g.program
            FROM students s
            JOIN groups g ON s.group_id = g.id
            WHERE g.graduation_year >= ?
        ";
        $params = [date('Y')];
        if ($groupNumber) {
            $sql .= " AND g.number = ?";
            $params[] = $groupNumber;
        }
        // Убираем сортировку или сортируем по ID (порядку добавления)
        $sql .= " ORDER BY s.id";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getGroups() {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("
            SELECT DISTINCT number
            FROM groups
            WHERE graduation_year >= ?
            ORDER BY number
        ");
        $stmt->execute([date('Y')]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public static function getGroupsForSelect() {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("
            SELECT id, number, program
            FROM groups
            WHERE graduation_year >= ?
            ORDER BY number
        ");
        $stmt->execute([date('Y')]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($id) {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("SELECT * FROM students WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function store($data) {
        $db = getDatabaseConnection();
        $birthDate = $data['birth_date'] ?? date('Y-m-d', strtotime('-20 years'));

        $stmt = $db->prepare("
            INSERT INTO students (group_id, full_name, gender, birth_date, student_id)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['group_id'],
            $data['full_name'],
            $data['gender'],
            $birthDate,
            $data['student_id']
        ]);
    }

    public static function update($data) {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("
            UPDATE students
            SET group_id = ?, full_name = ?, gender = ?, student_id = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $data['group_id'],
            $data['full_name'],
            $data['gender'],
            $data['student_id'],
            $data['id']
        ]);
    }

    public static function destroy($id) {
        $db = getDatabaseConnection();
        $db->prepare("DELETE FROM students WHERE id = ?")->execute([$id]);
    }
}
