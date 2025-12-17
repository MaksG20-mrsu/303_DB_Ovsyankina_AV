<?php
class Exam {
    public static function findByStudent($studentId) {
        $db = getDatabaseConnection();
        $sql = "
            SELECT e.*, d.name AS discipline_name, d.course
            FROM exams e
            JOIN disciplines d ON e.discipline_id = d.id
            WHERE e.student_id = ?
            ORDER BY e.exam_date
        ";
        $stmt = $db->prepare($sql);
        $stmt->execute([$studentId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getDisciplinesForStudent($studentId) {
        $db = getDatabaseConnection();
        $currentYear = (int)date('Y');

        $stmt = $db->prepare("
            SELECT g.program, g.graduation_year
            FROM students s
            JOIN groups g ON s.group_id = g.id
            WHERE s.id = ?
        ");
        $stmt->execute([$studentId]);
        $group = $stmt->fetch();
        if (!$group) return [];

        $program = $group['program'];
        $gradYear = $group['graduation_year'];
        $currentCourse = $gradYear - $currentYear + 1;
        $currentCourse = max(1, min(4, $currentCourse));

        $stmt = $db->prepare("
            SELECT id, name, course
            FROM disciplines
            WHERE program = ? AND course <= ?
            ORDER BY course, name
        ");
        $stmt->execute([$program, $currentCourse]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function store($data) {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("
            INSERT INTO exams (student_id, discipline_id, exam_date, score)
            VALUES (?, ?, ?, ?)
        ");
        $result = $stmt->execute([
            $data['student_id'],
            $data['discipline_id'],
            $data['exam_date'],
            $data['score']
        ]);
    }

    public static function destroy($id) {
        $db = getDatabaseConnection();
        $db->prepare("DELETE FROM exams WHERE id = ?")->execute([$id]);
    }

    public static function find($id) {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("SELECT * FROM exams WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function update($data) {
        $db = getDatabaseConnection();
        $stmt = $db->prepare("
            UPDATE exams
            SET discipline_id = ?, exam_date = ?, score = ?
            WHERE id = ?
        ");

        try {
            $stmt->execute([
                $data['discipline_id'],
                $data['exam_date'],
                $data['score'],
                (int)$data['id']
            ]);
        } catch (PDOException $e) {
            die("Ошибка при обновлении экзамена: " . $e->getMessage());
        }
    }
}
