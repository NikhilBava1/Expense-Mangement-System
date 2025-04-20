<?php
include 'db_conn.php';

// Fetch the total expenses for the current entry
$total_expenses_query = "SELECT SUM(expense) AS total_expenses FROM expenses WHERE ex_id = ?";
$stmt = $conn->prepare($total_expenses_query);
$stmt->bind_param('i', $_POST['ex_id']);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$total_expenses = $data['total_expenses'];
$remaining_expenses = $total_amount - $total_expenses;  // Remaining is total amount minus total expenses

// Return the updated total and remaining expenses as JSON
echo json_encode([
    'total_expenses' => $total_expenses,
    'remaining_expenses' => $remaining_expenses
]);
?>
