<?php
// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "regisdb"; // Replace with your database name

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]));
}

// Handle AJAX Requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = $_POST['name'];
        $department = $_POST['department'];
        $role = $_POST['role'];

        if (!empty($name) && !empty($department) && !empty($role)) {
            $stmt = $conn->prepare("INSERT INTO employees (name, department, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $department, $role);
            $success = $stmt->execute();
            $stmt->close();
            echo json_encode(["success" => $success]);
        }
    }

    if ($action === 'edit') {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $department = $_POST['department'];
        $role = $_POST['role'];

        if (!empty($id) && !empty($name) && !empty($department) && !empty($role)) {
            $stmt = $conn->prepare("UPDATE employees SET name=?, department=?, role=? WHERE id=?");
            $stmt->bind_param("sssi", $name, $department, $role, $id);
            $success = $stmt->execute();
            $stmt->close();
            echo json_encode(["success" => $success]);
        }
    }

    if ($action === 'delete') {
        $id = $_POST['id'];

        if (!empty($id)) {
            $stmt = $conn->prepare("DELETE FROM employees WHERE id=?");
            $stmt->bind_param("i", $id);
            $success = $stmt->execute();
            $stmt->close();
            echo json_encode(["success" => $success]);
        }
    }
    exit;
}

// Fetch employees
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['action']) && $_GET['action'] === 'get') {
    $result = $conn->query("SELECT * FROM employees");
    $employees = [];
    while ($row = $result->fetch_assoc()) {
        $employees[] = $row;
    }
    echo json_encode($employees);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Employees</title>
    <style>
        body {  background: #f4f4f4; margin: 0; padding: 20px; }
        h2 { text-align: center; }
        table {width: 100%;border-collapse: collapse;background-color: #ffffff;box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);font-size: 20px;
               border-radius: 8px; /* Rounded table corners */overflow: hidden;}
        th, td { border: 1px solid #dddddd;padding: 12px;text-align: center;}
        th {background-color: #0763c6;color: white;font-weight: bold;}
        tr:nth-child(even) {background-color: #f2f2f2; /* Light gray for even rows */}
        tr:hover {background-color: #d1e0ff; /* Highlight row on hover */transition: 0.3s;}
        .btn { padding: 8px 12px; border: none; cursor: pointer; }
        .edit-btn { background: #28a745; color: white; border-radius:8px;}
        .delete-btn { background: #dc3545; color: white; border-radius:8px;}
        .add-btn { background:  #0763c6; color: white; border-radius:8px;font-size:20px;}
        header{
                background-color:  #0763c6;
                color: #ffffff;
                line-height: 60%;
                height: 100px;

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
            <br>
            <h1 style="text-align: center;">Manage Employees</h1>
            <nav style="text-align: right">
               
                <a class="navi"href="admin.html">Back</a>|
                <a class="navi"href="main.php">Logout</a>
            </nav><br>
    </header><br><br>
    <button class= " btn add-btn" onclick="addEmployee()">Add Employee</button><br><br>
    <table id="employeeTable">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Department</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
    </table>

    <script>
        function loadEmployees() {
            fetch("manageEm.php?action=get")
            .then(response => response.json())
            .then(data => {
                let table = document.getElementById("employeeTable");
                table.innerHTML = `
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                `;

                data.forEach(emp => {
                    let row = table.insertRow();
                    row.innerHTML = `
                        <td>${emp.id}</td>
                        <td>${emp.name}</td>
                        <td>${emp.department}</td>
                        <td>${emp.role}</td>
                        <td>
                            <button class="btn edit-btn" onclick="editEmployee(${emp.id}, '${emp.name}', '${emp.department}', '${emp.role}')">Edit</button>
                            <button class="btn delete-btn" onclick="deleteEmployee(${emp.id})">Delete</button>
                        </td>
                    `;
                });
            })
            .catch(error => console.error("Error loading employees:", error));
        }

        function addEmployee() {
            let name = prompt("Enter Employee Name:");
            let department = prompt("Enter Department:");
            let role = prompt("Enter Role:");

            if (name && department && role) {
                let formData = new FormData();
                formData.append("action", "add");
                formData.append("name", name);
                formData.append("department", department);
                formData.append("role", role);

                fetch("manageEm.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Employee added!");
                        loadEmployees();
                    } else {
                        alert("Error adding employee.");
                    }
                });
            }
        }

        function editEmployee(id, oldName, oldDepartment, oldRole) {
            let name = prompt("Enter new name:", oldName);
            let department = prompt("Enter new department:", oldDepartment);
            let role = prompt("Enter new role:", oldRole);

            if (name && department && role) {
                let formData = new FormData();
                formData.append("action", "edit");
                formData.append("id", id);
                formData.append("name", name);
                formData.append("department", department);
                formData.append("role", role);

                fetch("manageEm.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Employee updated!");
                        loadEmployees();
                    } else {
                        alert("Error updating employee.");
                    }
                });
            }
        }

        function deleteEmployee(id) {
            if (confirm("Are you sure you want to delete this employee?")) {
                let formData = new FormData();
                formData.append("action", "delete");
                formData.append("id", id);

                fetch("manageEm.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Employee deleted!");
                        loadEmployees();
                    } else {
                        alert("Error deleting employee.");
                    }
                });
            }
        }

        document.addEventListener("DOMContentLoaded", loadEmployees);
    </script>

</body>
</html>
