<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 1.2em;
            opacity: 0.9;
        }
        
        .content {
            padding: 40px;
        }
        
        .filter-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-section label {
            font-weight: 600;
            color: #495057;
            font-size: 1.1em;
        }
        
        .filter-section select {
            flex: 1;
            min-width: 250px;
            padding: 12px 20px;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            font-size: 1em;
            transition: all 0.3s;
            background: white;
            cursor: pointer;
        }
        
        .filter-section select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 35px;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .btn-reset {
            background: #6c757d;
        }
        
        .students-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .students-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .students-table th {
            padding: 18px 15px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9em;
        }
        
        .students-table tbody tr {
            border-bottom: 1px solid #e9ecef;
            transition: background 0.2s;
        }
        
        .students-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .students-table td {
            padding: 16px 15px;
            color: #495057;
        }
        
        .stats {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .stats div {
            font-size: 1.1em;
            color: #495057;
        }
        
        .stats strong {
            color: #212529;
            font-size: 1.2em;
        }
        
        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
            font-size: 1.2em;
        }
        
        @media (max-width: 768px) {
            .content {
                padding: 20px;
            }
            
            .header {
                padding: 30px 20px;
            }
            
            .header h1 {
                font-size: 2em;
            }
            
            .students-table {
                display: block;
                overflow-x: auto;
            }
            
            .filter-section {
                flex-direction: column;
                align-items: stretch;
            }
            
            .filter-section select {
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>🎓 Student Management System</h1>
            <p>View and manage student information</p>
        </header>
        
        <main class="content">
            <!-- Filter form -->
            <div class="filter-section">
                <form method="GET">
                    <label for="group">Filter by group:</label>
                    <select name="group" id="group">
                        <option value="">All groups</option>
                        <?php
                        // Database connection using PDO with prepared statements
                        try {
                            $database = new PDO('sqlite:../db/students.db');
                            $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            
                            // Get active groups (current year or later)
                            $currentYear = date('Y');
                            $groupQuery = $database->prepare("
                                SELECT name, specialization 
                                FROM groups 
                                WHERE graduation_year >= :year 
                                ORDER BY specialization, name
                            ");
                            $groupQuery->execute(['year' => $currentYear]);
                            $groupList = $groupQuery->fetchAll(PDO::FETCH_ASSOC);
                            
                            // Get selected group from URL
                            $selectedGroup = $_GET['group'] ?? '';
                            
                            // Populate dropdown options
                            foreach ($groupList as $groupItem) {
                                $isSelected = ($selectedGroup === $groupItem['name']) ? 'selected' : '';
                                echo "<option value=\"{$groupItem['name']}\" $isSelected>";
                                echo htmlspecialchars($groupItem['name']) . ' - ' . htmlspecialchars($groupItem['specialization']);
                                echo "</option>";
                            }
                            
                            // Get student data with optional filter
                            $studentQuery = "SELECT g.name as group_name, g.specialization,
                                                    s.last_name, s.first_name, s.middle_name,
                                                    s.gender, s.birth_date, s.student_id
                                             FROM students s
                                             JOIN groups g ON s.group_id = g.id
                                             WHERE g.graduation_year >= :year";
                            
                            $queryParams = ['year' => $currentYear];
                            
                            if (!empty($selectedGroup)) {
                                $studentQuery .= " AND g.name = :group_name";
                                $queryParams['group_name'] = $selectedGroup;
                            }
                            
                            $studentQuery .= " ORDER BY g.name, s.last_name, s.first_name";
                            
                            $studentStatement = $database->prepare($studentQuery);
                            $studentStatement->execute($queryParams);
                            $studentRecords = $studentStatement->fetchAll(PDO::FETCH_ASSOC);
                            
                        } catch (PDOException $error) {
                            die("Database connection error: " . $error->getMessage());
                        }
                        ?>
                    </select>
                    <button type="submit" class="btn">Apply Filter</button>
                    <a href="?" class="btn btn-reset">Reset</a>
                </form>
            </div>
            
            <!-- Students table -->
            <?php if (!empty($studentRecords)): ?>
                <table class="students-table">
                    <thead>
                        <tr>
                            <th>Group</th>
                            <th>Specialization</th>
                            <th>Last Name</th>
                            <th>First Name</th>
                            <th>Middle Name</th>
                            <th>Gender</th>
                            <th>Birth Date</th>
                            <th>Student ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($studentRecords as $student): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['group_name']) ?></td>
                            <td><?= htmlspecialchars($student['specialization']) ?></td>
                            <td><?= htmlspecialchars($student['last_name']) ?></td>
                            <td><?= htmlspecialchars($student['first_name']) ?></td>
                            <td><?= htmlspecialchars($student['middle_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($student['gender']) ?></td>
                            <td><?= htmlspecialchars($student['birth_date']) ?></td>
                            <td><?= htmlspecialchars($student['student_id']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- Statistics -->
                <div class="stats">
                    <div>
                        <strong>Total students:</strong> <?= count($studentRecords) ?>
                    </div>
                    <div>
                        <?php if (!empty($selectedGroup)): ?>
                            <strong>Filtered by group:</strong> <?= htmlspecialchars($selectedGroup) ?>
                        <?php else: ?>
                            Showing all active groups
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="no-data">
                    <div>📭</div>
                    <p>No students found</p>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
