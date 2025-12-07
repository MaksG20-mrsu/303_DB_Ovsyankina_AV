<?php
if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    system('chcp 65001 > nul');
}

class StudentConsoleApp {
    private PDO $db;
    
    public function __construct() {
        try {
            $dbPath = 'C:/Users/Александра Овсянкина/303_DB_Ovsyankina_AV/Task07/db/students.db';
            $this->db = new PDO('sqlite:' . $dbPath);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->exec('PRAGMA encoding = "UTF-8"');
        } catch (PDOException $e) {
            die("❌ Database error: " . $e->getMessage() . "\n");
        }
    }
    
    private function getActiveGroups(): array {
        $currentYear = (int) date('Y');
        $stmt = $this->db->prepare("SELECT name FROM groups WHERE graduation_year >= ? ORDER BY name");
        $stmt->execute([$currentYear]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    private function getStudents(?string $groupName = null): array {
        $currentYear = (int) date('Y');
        $sql = "SELECT g.name as group_name, g.specialization,
                       s.last_name || ' ' || s.first_name || ' ' || COALESCE(s.middle_name, '') as full_name,
                       s.gender, s.birth_date, s.student_id
                FROM students s
                JOIN groups g ON s.group_id = g.id
                WHERE g.graduation_year >= :year";
        
        $params = ['year' => $currentYear];
        
        if ($groupName !== null && $groupName !== '') {
            $sql .= " AND g.name = :group_name";
            $params['group_name'] = $groupName;
        }
        
        $sql .= " ORDER BY g.name, s.last_name, s.first_name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function drawTable(array $data): void {
        if (empty($data)) {
            echo "📭 No data to display\n";
            return;
        }
        
        $headers = ['Group', 'Specialization', 'Full Name', 'Gender', 'Birth Date', 'Student ID'];
        $widths = [8, 25, 30, 8, 15, 15];
        
        echo "┌";
        foreach ($widths as $i => $w) {
            echo str_repeat("─", $w);
            echo $i < count($widths) - 1 ? "┬" : "┐";
        }
        echo "\n";
        
        echo "│";
        foreach ($headers as $i => $header) {
            printf(" %-".($widths[$i]-1)."s│", $header);
        }
        echo "\n";
        
        echo "├";
        foreach ($widths as $i => $w) {
            echo str_repeat("─", $w);
            echo $i < count($widths) - 1 ? "┼" : "┤";
        }
        echo "\n";
        
        foreach ($data as $row) {
            echo "│";
            foreach ($row as $i => $value) {
                printf(" %-".($widths[$i]-1)."s│", $value);
            }
            echo "\n";
        }
        
        echo "└";
        foreach ($widths as $i => $w) {
            echo str_repeat("─", $w);
            echo $i < count($widths) - 1 ? "┴" : "┘";
        }
        echo "\n";
    }
    
    public function run(): void {
        if (ob_get_level()) ob_end_clean();
        
        echo "═══════════════════════════════════════════════════\n";
        echo "          STUDENT MANAGEMENT SYSTEM\n";
        echo "═══════════════════════════════════════════════════\n\n";
        
        $activeGroups = $this->getActiveGroups();
        
        if (empty($activeGroups)) {
            echo "📭 No active groups found\n";
            return;
        }
        
        echo "📊 Available groups: " . implode(', ', $activeGroups) . "\n\n";
        
        // FIX: Show alternative input options due to PowerShell encoding issues
        echo "Note: Due to PowerShell encoding issues, you may need to:\n";
        echo "  - Enter group without first letter (e.g., 'И-505' for 'БИ-505')\n";
        echo "  - Or use only numbers (e.g., '505' for 'БИ-505')\n";
        echo "  - Or press Enter for all students\n\n";
        
        echo "Enter group name for filtering or press Enter for all groups:\n";
        echo "> ";
        
        $userInput = trim(fgets(STDIN));
        
        // FIX: Handle PowerShell encoding issue
        if ($userInput !== '') {
            $foundGroup = null;
            
            // Try exact match first
            if (in_array($userInput, $activeGroups, true)) {
                $foundGroup = $userInput;
            } else {
                // Try to find by suffix (e.g., "505" for "БИ-505")
                foreach ($activeGroups as $group) {
                    if (strpos($group, $userInput) !== false) {
                        $foundGroup = $group;
                        break;
                    }
                }
                
                // Try without first character (PowerShell issue)
                if ($foundGroup === null && strlen($userInput) > 2) {
                    foreach ($activeGroups as $group) {
                        if (strpos($group, substr($userInput, 1)) !== false) {
                            $foundGroup = $group;
                            break;
                        }
                    }
                }
            }
            
            if ($foundGroup === null) {
                echo "\n❌ Error: Group '$userInput' not found!\n";
                echo "   Available groups: " . implode(', ', $activeGroups) . "\n";
                return;
            }
            
            // Use the found full group name
            echo "   Using group: $foundGroup\n";
            $userInput = $foundGroup;
        }
        
        $studentData = $this->getStudents($userInput !== '' ? $userInput : null);
        
        echo "\n";
        
        if (empty($studentData)) {
            echo "📭 No students found\n";
            return;
        }
        
        $tableRows = [];
        foreach ($studentData as $student) {
            $tableRows[] = [
                $student['group_name'],
                $student['specialization'],
                $student['full_name'],
                $student['gender'],
                $student['birth_date'],
                $student['student_id']
            ];
        }
        
        $this->drawTable($tableRows);
        echo "📈 Total students: " . count($studentData) . "\n";
        if ($userInput !== '') {
            echo "   Filtered by group: $userInput\n";
        }
    }
}

$app = new StudentConsoleApp();
$app->run();
