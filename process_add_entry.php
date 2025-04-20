<?php
include('db_conn.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];

    // Add entry with default total_amount and remaining as 0
    $query = "INSERT INTO ex_table (name,amount_description, total_amount, remaining) VALUES ('$name','$amount_description', 0, 0)";
    if ($conn->query($query) === TRUE) {
        header('Location: index.php');
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
