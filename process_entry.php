<?php
include('db_conn.php');

$name = $_POST['name'];
$amount_description = $_POST['amount_description'];
$total_amount = isset($_POST['total_amount']) && $_POST['total_amount'] !== '' ? $_POST['total_amount'] : 0;

$query = "INSERT INTO ex_table (name,amount_description, total_amount) VALUES ('$name','$amount_description', '$total_amount')";
if ($conn->query($query) === TRUE) {
    header("Location: index.php");
} else {
    echo "Error: " . $query . "<br>" . $conn->error;
}
$conn->close();
?>
