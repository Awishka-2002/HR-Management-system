<?php
include 'db_connection.php';

$data = json_decode(file_get_contents("php://input"), true);
$task_id = $data['id'];

$sql = "DELETE FROM tasks WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $task_id);

$response = ["success" => false];
if ($stmt->execute()) {
    $response["success"] = true;
}
echo json_encode($response);
$conn->close();
?>
