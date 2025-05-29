<?php
include 'db_connection.php'; // Ensure you have a database connection file

// Fetch employee reports
$sql = "SELECT e.id, e.name, e.role, r.attendance, r.performance 
        FROM employees e 
        LEFT JOIN employee_reports r ON e.id = r.employee_id";
$result = $conn->query($sql);
$employees = [];
while ($row = $result->fetch_assoc()) {
    $employees[] = $row;
}

// Handle report update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $employee_id = $data['employee_id'];
    $attendance = $data['attendance'];
    $performance = $data['performance'];

    $sql = "INSERT INTO employee_reports (employee_id, attendance, performance) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE attendance = VALUES(attendance), performance = VALUES(performance)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $employee_id, $attendance, $performance);

    $response = ["success" => false];
    if ($stmt->execute()) {
        $response["success"] = true;
    }
    echo json_encode($response);
    $conn->close();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Report</title>
    <style>
        /* General Reset */
        body, h1, h2, p, ul, li, table, th, td {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body Styling */
        body {
           
            line-height: 1.6;
            color: #333;
            background: #f4f4f9;
        }
        .navi:hover{
                background-color: #7f95ab;
            }
        .navi{
                color: #ffffff;
                margin:10px;
                font-size:20px;
                text-decoration:none;

                
         }

     

        /* Report Section */
        .report-section {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 1rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .report-section h2 {
            text-align: center;
            margin-bottom: 1rem;
        }

        .report-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }

        th, td {
            text-align: left;
            padding: 0.75rem;
            border: 1px solid #ddd;
        }

        th {
            background: #007bff;
            color: #fff;
        }

        header{
                background-color:  #0763c6;
                color: #ffffff;
                line-height: 60%;
                height: 100px;

            }

        /* Responsive Design */
        @media (max-width: 768px) {
            body { padding: 10px; }
            .btn { padding: 6px 10px; font-size: 16px; }
            .add-btn { font-size: 18px; width: 100%; }
            table { font-size: 16px; }
            th, td { padding: 8px; }
            .table-container { overflow-x: auto; }
        }
    </style>
</head>
<body>
<header>
            <br><br>
            <h1 style="text-align: center;"> Employee's Report</h1><br><br>
            <nav style="text-align: right">
                <a class="navi"href="admin.html">Back</a>|
                <a class="navi"href="main.php">Logout</a>
            </nav><br><br>
    </header>
    <main>
        <section class="report-section">
            <div class="report-container">
                <table id="reportTable">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Attendance</th>
                        <th>Performance</th>
                        <th>Actions</th>
                    </tr>
                    <?php foreach ($employees as $emp): ?>
                    <tr>
                        <td><?= $emp['id'] ?></td>
                        <td><?= $emp['name'] ?></td>
                        <td><?= $emp['role'] ?></td>
                        <td><input type="text" id="att-<?= $emp['id'] ?>" value="<?= $emp['attendance'] ?? '' ?>"></td>
                        <td><input type="text" id="perf-<?= $emp['id'] ?>" value="<?= $emp['performance'] ?? '' ?>"></td>
                        <td><button onclick="updateReport(<?= $emp['id'] ?>)">Save</button></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </section>
    </main>
    
    <script>
        function updateReport(employee_id) {
            let attendance = document.getElementById(`att-${employee_id}`).value;
            let performance = document.getElementById(`perf-${employee_id}`).value;

            fetch("report.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ employee_id, attendance, performance })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Report updated successfully!");
                    location.reload(); // Reload to see updated data
                } else {
                    alert("Error updating report.");
                }
            })
            .catch(error => console.error("Error:", error));
        }
    </script>
</body>
</html>
