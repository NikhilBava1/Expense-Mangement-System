<?php
include('db_conn.php');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Delete related expenses
    $deleteExpenses = $conn->prepare("DELETE FROM expenses WHERE ex_id = ?");
    $deleteExpenses->bind_param("i", $id);
    $deleteExpenses->execute();

    // Delete the entry
    $deleteEntry = $conn->prepare("DELETE FROM ex_table WHERE id = ?");
    $deleteEntry->bind_param("i", $id);
    $deleteEntry->execute();

    if ($deleteEntry->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>
