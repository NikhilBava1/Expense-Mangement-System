<?php
include 'db_conn.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Delete from ex_table
    $query = "DELETE FROM ex_table WHERE id = $id";
    $conn->query($query);

    // Optionally delete related expenses
    $conn->query("DELETE FROM expenses WHERE ex_id = $id");

    header("Location: all_entries.php"); // Adjust redirect if needed
    exit;
}
?>
