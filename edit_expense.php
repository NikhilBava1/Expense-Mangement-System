<?php
include('db_conn.php');

// Fetch the entry ID to edit
$id = $_GET['id'];

// Fetch all expenses for this entry
$expense_query = "SELECT * FROM expenses WHERE ex_id = ?";
$stmt = $conn->prepare($expense_query);
$stmt->bind_param('i', $id);
$stmt->execute();
$expense_result = $stmt->get_result();

// Fetch the entry's total amount
$entry_query = "SELECT total_amount FROM ex_table WHERE id = ?";
$stmt = $conn->prepare($entry_query);
$stmt->bind_param('i', $id);
$stmt->execute();
$entry_result = $stmt->get_result();
$entry = $entry_result->fetch_assoc();
$total_amount = $entry['total_amount'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $total_expense = 0;

    foreach ($_POST['expense_id'] as $key => $expense_id) {
        $expense_amount = (float)$_POST['expense'][$key];
        $description = $_POST['description'][$key];

        // Update each expense record in the database
        $update_query = "UPDATE expenses SET expense = ?, description = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param('dsi', $expense_amount, $description, $expense_id);
        $stmt->execute();

        $total_expense += $expense_amount;
    }

    // Update total expense and remaining in ex_table
    $remaining_amount = $total_amount - $total_expense;

    $update_totals_query = "UPDATE ex_table SET expense = ?, remaining = ? WHERE id = ?";
    $stmt = $conn->prepare($update_totals_query);
    $stmt->bind_param('dsi', $total_expense, $remaining_amount, $id);
    $stmt->execute();

    // Redirect to index after saving
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Expenses</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .table-editable input {
            border: none;
            width: 100%;
            text-align: center;
        }
        .table-editable input:focus {
            border: 1px solid #0d6efd;
            outline: none;
        }
        .save-btn {
            display: none; /* Hidden by default */
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <h2 class="text-center text-primary">Edit Expenses (Entry ID: <?= htmlspecialchars($id) ?>)</h2>

        <form method="POST" id="edit-form">
            <table class="table table-bordered table-editable">
                <thead>
                    <tr class="table-secondary text-center">
                        <th>#</th>
                        <th>Expense</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $counter = 1;
                    while ($expense = $expense_result->fetch_assoc()): 
                    ?>
                    <tr>
                        <td class="text-center"><?= $counter ?></td>
                        <td>
                            <input type="number" name="expense[]" value="<?= htmlspecialchars($expense['expense']) ?>" class="expense-input" required>
                        </td>
                        <td>
                            <input type="text" name="description[]" value="<?= htmlspecialchars($expense['description']) ?>" class="expense-input" required>
                        </td>
                        <input type="hidden" name="expense_id[]" value="<?= htmlspecialchars($expense['id']) ?>">
                    </tr>
                    <?php 
                    $counter++;
                    endwhile; 
                    ?>
                </tbody>
            </table>
            <div class="text-end">
                <button type="submit" class="btn btn-success save-btn" id="save-btn">Save Changes</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script>
        // Show Save button only if a change is detected
        const inputs = document.querySelectorAll('.expense-input');
        const saveBtn = document.getElementById('save-btn');

        inputs.forEach(input => {
            input.addEventListener('input', () => {
                saveBtn.style.display = 'inline-block'; // Show the Save button
            });
        });
    </script>
</body>
</html>
