<?php
include('db_conn.php');

$id = $_GET['id'];
$entryId = $_GET['entryId'];

$query = "SELECT expense FROM expenses WHERE id = $id";
$result = $conn->query($query);
$deletedAmount = 0;

if ($result && $row = $result->fetch_assoc()) {
    $deletedAmount = $row['expense'];
}

$deleteQuery = "DELETE FROM expenses WHERE id = $id";
if ($conn->query($deleteQuery)) {
    echo json_encode(['success' => true, 'deletedAmount' => $deletedAmount]);
} else {
    echo json_encode(['success' => false]);
}

?>
