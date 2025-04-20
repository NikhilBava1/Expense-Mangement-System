<?php
include('db_conn.php');

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['entryId'], $data['expense'], $data['description'])) {
    $entryId = $data['entryId'];
    $expense = $data['expense'];
    $description = $data['description'];

    $query = "INSERT INTO expenses (ex_id, expense, description) VALUES ('$entryId', '$expense', '$description')";
    if ($conn->query($query) === TRUE) {
        $expenseId = $conn->insert_id; // Get newly inserted expense ID
        echo json_encode(['success' => true, 'expenseId' => $expenseId]);
    } else {
        echo json_encode(['success' => false, 'message' => $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
}
?>
