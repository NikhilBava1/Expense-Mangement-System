<?php
include('db_conn.php');

// Get the entry ID from the GET request
if (isset($_GET['entry_id'])) {
    $entry_id = intval($_GET['entry_id']); // Sanitize input

    // Fetch all expenses for the given entry ID
    $query = "SELECT * FROM expenses WHERE entry_id = $entry_id";
    $result = $conn->query($query);


    // Check if any expenses are found
    if ($result->num_rows > 0) {
        $counter = 1;
        while ($expense = $result->fetch_assoc()) {
            echo "
            <tr>
                <td>Expense #{$counter}</td>
                <td>
                    <input type='number' name='expense_amount[]' value='{$expense['expense']}' class='form-control' required>
                </td>
                <td>
                    <input type='text' name='description[]' value='{$expense['description']}' class='form-control' required>
                </td>
                <input type='hidden' name='expense_id[]' value='{$expense['id']}'>
            </tr>
            ";
            $counter++;
        }
    } else {
        echo "<tr><td colspan='3' class='text-center'>No expenses found for this entry.</td></tr>";
    }
} else {
    echo "<tr><td colspan='3' class='text-center'>Invalid request. No entry ID provided.</td></tr>";
}
?>
