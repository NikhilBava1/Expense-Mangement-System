<?php
include('db_conn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Securely retrieve and sanitize form data
    $name = $conn->real_escape_string($_POST['name']);
    $total_amount = $conn->real_escape_string($_POST['total_amount']);
    $amount_description = $conn->real_escape_string($_POST['amount_description']);

    // Insert query to include the "amount_description" field
    $query = "INSERT INTO ex_table (name, total_amount, amount_description) 
              VALUES ('$name', '$total_amount', '$amount_description')";

    // Execute the query and handle redirection or error
    if ($conn->query($query)) {
        header('Location: index.php'); // Redirect on success
    } else {
        echo "Error: " . $conn->error; // Display error message
    }
}
?>
