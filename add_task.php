<?php
include 'db_connection.php';

$data = json_decode(file_get_contents("php://input"), true);
$task = $data['task'];

$sql = "INSERT INTO tasks (task) VALUES (?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $task);

$response = ["success" => false];
if ($stmt->execute()) {
    $response["success"] = true;
}
echo json_encode($response);
$conn->close();
?>
