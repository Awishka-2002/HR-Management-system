<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "regisdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve employee data with reports
$sql = "SELECT 
            e.id, 
            e.Name, 
            e.department, 
            e.role, 
            er.attendance, 
            er.performance
        FROM employees e
        LEFT JOIN employee_reports er ON e.id = er.employee_id
        ORDER BY e.id ASC";

$result = $conn->query($sql);
$employees = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Details</title>
    <style>
        body { background-color: #f4f4f4; font-size: 18px;  }
        header { background-color: #007bff; color: white; padding: 15px; text-align: center; }
        table { width: 80%; margin: 20px auto; border-collapse: collapse; background-color: white; box-shadow: 0px 0px 10px gray; }
        th, td { border: 1px solid black; padding: 10px; text-align: center; }
        th { background-color: #007bff; color: white; }
    </style>
</head>
<body>

<header>
    <h1>Employee Details</h1>
</header>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Department</th>
            <th>Role</th>
            <th>Attendance</th>
            <th>Performance</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($employees)): ?>
            <?php foreach ($employees as $emp): ?>
            <tr>
                <td><?= htmlspecialchars($emp['id']) ?></td>
                <td><?= htmlspecialchars($emp['Name']) ?></td>
                <td><?= htmlspecialchars($emp['department']) ?></td>
                <td><?= htmlspecialchars($emp['role']) ?></td>
                <td><?= htmlspecialchars($emp['attendance'] ?? "N/A") ?></td>
                <td><?= htmlspecialchars($emp['performance'] ?? "N/A") ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6">No employees found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
