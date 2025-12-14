<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!file_exists(__DIR__ . '/../data/university.db')) {
    require_once __DIR__ . '/../create_db.php';
}

require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/models/Student.php';
require_once __DIR__ . '/../src/models/Exam.php';

$action = $_GET['action'] ?? 'students_list';

$allowed = [
    'students_list', 'student_create', 'student_store', 'student_edit', 'student_update',
    'student_delete', 'student_destroy',
    'exams_list', 'exam_create', 'exam_store', 'exam_edit', 'exam_update',
    'exam_delete', 'exam_destroy'
];

if (!in_array($action, $allowed)) {
    $action = 'students_list';
}

if ($_POST) {
    if ($action === 'student_store') {
        Student::store($_POST);
        header("Location: index.php");
        exit;
    } elseif ($action === 'student_update') {
        Student::update($_POST);
        header("Location: index.php");
        exit;
    } elseif ($action === 'student_destroy') {
        Student::destroy($_POST['id']);
        header("Location: index.php");
        exit;
    } elseif ($action === 'exam_store') {
        Exam::store($_POST);
        header("Location: index.php?action=exams_list&student_id=" . $_POST['student_id']);
        exit;
    } elseif ($action === 'exam_destroy') {
        $exam = Exam::find($_POST['id']);
        $studentId = $exam['student_id'];
        Exam::destroy($_POST['id']);
        header("Location: index.php?action=exams_list&student_id=$studentId");
        exit;
    } elseif ($action === 'exam_update') {
        Exam::update($_POST);
        $studentId = $_POST['student_id'];
        header("Location: index.php?action=exams_list&student_id=$studentId");
        exit;
    }
}

switch ($action) {
    case 'student_create':
        require_once __DIR__ . '/../src/views/students/create.php';
        break;
    case 'student_edit':
        $student = Student::find($_GET['id']);
        $groups = Student::getGroupsForSelect();
        require_once __DIR__ . '/../src/views/students/edit.php';
        break;
    case 'student_delete':
        $student = Student::find($_GET['id']);
        require_once __DIR__ . '/../src/views/students/delete.php';
        break;
    case 'exams_list':
        $student = Student::find($_GET['student_id']);
        $exams = Exam::findByStudent($_GET['student_id']);
        require_once __DIR__ . '/../src/views/exams/list.php';
        break;
    case 'exam_create':
        $student = Student::find($_GET['student_id']);
        $disciplines = Exam::getDisciplinesForStudent($_GET['student_id']);
        require_once __DIR__ . '/../src/views/exams/create.php';
        break;
    case 'exam_delete':
        $exam = Exam::find($_GET['id']);
        $student = Student::find($exam['student_id']);
        require_once __DIR__ . '/../src/views/exams/delete.php';
        break;
    case 'exam_edit':
        $exam = Exam::find($_GET['id']);
        $student = Student::find($exam['student_id']);
        $disciplines = Exam::getDisciplinesForStudent($student['id']);
        require_once __DIR__ . '/../src/views/exams/edit.php';
        break;
    default:
        $students = Student::all($_GET['group'] ?? null);
        $groups = Student::getGroups();
        require_once __DIR__ . '/../src/views/students/list.php';
}
