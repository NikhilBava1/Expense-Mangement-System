<?php
include 'db_conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $searchTerm = $_GET['query'] ?? '';
    $searchTerm = "%" . $searchTerm . "%";

    $query = "SELECT * FROM expenses WHERE name LIKE ? OR description LIKE ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $expenses = [];
    while ($row = $result->fetch_assoc()) {
        $expenses[] = $row;
    }

    echo json_encode($expenses);
}
?>
