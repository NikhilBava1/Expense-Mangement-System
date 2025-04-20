<?php
include('db.php');

// Fetch all unique entries (entry_id) from the expenses table
$query = "SELECT DISTINCT id FROM expenses";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Manager</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container my-5">
        <h2 class="text-center">Expense Manager</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Entry ID</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($entry = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $entry['id'] ?></td>
                    <td>
                        <button class="btn btn-warning btn-sm edit-expenses" data-id="<?= $entry['id'] ?>">Edit Expenses</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap Modal for Editing Expenses -->
    <div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editExpenseModalLabel">Edit Expenses</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-expenses-form">
                        <!-- Entry ID (Hidden Input) -->
                        <input type="hidden" id="entry-id" name="entry_id">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Expense #</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody id="expense-list">
                                <!-- Expenses will be dynamically populated here -->
                            </tbody>
                        </table>
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript (jQuery and Bootstrap) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Open modal and fetch expenses via AJAX
            $('.edit-expenses').click(function() {
                const entryId = $(this).data('id');
                $('#entry-id').val(entryId); // Set entry_id in hidden input
                $('#expense-list').html(''); // Clear previous data

                // Fetch expenses for this entry_id
                $.ajax({
                    url: 'fetch_expenses.php',
                    method: 'GET',
                    data: { entry_id: entryId },
                    success: function(response) {
                        $('#expense-list').html(response); // Populate the modal
                        $('#editExpenseModal').modal('show'); // Show the modal
                    }
                });
            });

            // Submit edited expenses via AJAX
            $('#edit-expenses-form').submit(function(e) {
                e.preventDefault(); // Prevent form default submission
                const formData = $(this).serialize();

                // Send updated data to server
                $.ajax({
                    url: 'update_expenses.php',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        alert('Expenses updated successfully!');
                        $('#editExpenseModal').modal('hide'); // Close modal
                        location.reload(); // Refresh the page to reflect changes
                    }
                });
            });
        });
    </script>
</body>
</html>
