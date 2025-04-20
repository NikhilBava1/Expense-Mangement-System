<?php
include 'db_conn.php';

// Check if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from the POST request
    $id = $_POST['id'];  // ID of the expense
    $column = $_POST['column'];  // Column (expense or description)
    $value = $_POST['value'];  // New value (expense amount or description)

    // Validate column name (to avoid SQL injection)
    $allowed_columns = ['expense', 'description'];  // Allowed columns
    if (!in_array($column, $allowed_columns)) {
        echo "Error: Invalid column";
        exit;
    }

    // Prepare the update query
    if ($column === 'expense') {
        $query = "UPDATE expenses SET $column = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('di', $value, $id);  // Bind double for expense
    } else {
        $query = "UPDATE expenses SET $column = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('si', $value, $id);  // Bind string for description
    }

    // Execute the query and check success
    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
