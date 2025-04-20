<?php
include 'db_conn.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? '';

    if ($action === 'add') {
        $ex_id = intval($data['ex_id']);
        $expense = floatval($data['expense']);
        $description = $data['description'];

        $insert_query = "INSERT INTO expenses (ex_id, expense, description) VALUES ($ex_id, $expense, '$description')";
        $result = $conn->query($insert_query);

        // Return success or failure message
        echo json_encode(['success' => $result, 'expense_id' => $conn->insert_id]);
        exit;
    }

    if ($action === 'delete') {
        $expense_id = intval($data['id']);

        $delete_query = "DELETE FROM expenses WHERE id = $expense_id";
        $result = $conn->query($delete_query);

        echo json_encode(['success' => $result]);
        exit;
    }

    if ($action === 'update') {
        $id = intval($data['id']);
        $column = $data['column'];
        $value = $data['value'];

        $update_query = "UPDATE expenses SET $column = '$value' WHERE id = $id";
        $result = $conn->query($update_query);

        echo json_encode(['success' => $result]);
        exit;
    }
}

// Fetch entry details
$entry_query = "SELECT name, total_amount FROM ex_table WHERE id = $id";
$entry_result = $conn->query($entry_query);
$entry = $entry_result->fetch_assoc();

// Fetch expenses for the entry
$expense_query = "SELECT id, expense, description FROM expenses WHERE ex_id = $id";
$expense_result = $conn->query($expense_query);

$total_expenses = 0;
$expenses = [];
while ($expense = $expense_result->fetch_assoc()) {
    $expenses[] = $expense;
    $total_expenses += $expense['expense'];
}

$remaining = $entry['total_amount'] - $total_expenses;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expenses for <?= $entry['name'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Include jsPDF library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<!-- Include html2pdf library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>


    <style>
        @font-face {
            font-family: 'Sansation-bold';
            src: url('./fonts/Sansation_Bold.ttf')format('truetype');
        }
        @font-face {
            font-family: 'Sansation-regular';
            src: url('./fonts/Sansation_Regular.ttf')format('truetype');
        }
        @font-face {
            font-family: 'Sansation-light';
            src: url('./fonts/Sansation_Light.ttf')format('truetype');
        }
        body {
            background-color: #f8f9fa;
            
        }
        .table{
            font-family: Sansation-bold;
        }
        .container {
            margin-top: 20px;
        }

        .card-header {
            background-color: #007bff;
            color: white;
        }

        .editable:hover {
            background-color: #fff3cd;
            cursor: pointer;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        <style>.summary-table {
            width: 100%;
            margin-top: 20px;
            position: relative;
            left: 0;
        }

        .summary-table th,
        .summary-table td {
            vertical-align: middle;
            font-size: 1rem;
        }

        #light{
            font-family: Sansation-light;
        }
        #regular{
            font-family: Sansation-regular;
        }
        .card-header{
            background-color: #282828;
        }
        #addExpenseinput::placeholder{
            font-family: Sansation-light;
        }
        #addExpenseinput{
            font-family: Sansation-regular;
        }
        .responsive-image {
            width: 100%; /* Ensures the image spans the entire container width */
            height: auto; /* Maintains the image's aspect ratio */
            display: block; /* Removes any unwanted gaps below the image */
            object-fit: contain; /* Ensures the image is fully visible without cropping */
        }
        @media print {
    .branding {
        display: none; /* Hide the branding image in print view */
    }
}

    </style>
</head>

<body>
<div class="w-100 container branding">
        <img src="uploads/asorg_branding.png" alt="Branding Image" class="responsive-image">
    </div>

    <div class="container">
        <!-- Entry Details -->
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="mb-0">Expenses for <strong><?= $entry['name'] ?></strong></h4>


            </div>
            <div class="card-body">
                <!-- Table for Total Budget and Remaining Amount -->
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Total Budget (₹)</th>
                            <th>Remaining Amount (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td id="regular"><?= $entry['total_amount'] ?></td>
                            <td id="regular" id="remainingAmount"><?= $remaining ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>


        <!-- Expense Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Expense Details</h5>
                <button class="btn btn-success btn-sm" id="addExpenseBtn">+ Add Expense</button>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover" id="expenseTable">
                    <thead class="table-light">
                        <tr>
                            <th>Expense (₹)</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($expenses as $expense) { ?>
                            <tr>
                                <td id="regular" ondblclick="makeEditable(this)" data-id="<?= $expense['id'] ?>" data-column="expense"
                                    class="editable">
                                    <?= $expense['expense'] ?>
                                </td>
                                <td  id="regular" ondblclick="makeEditable(this)" data-id="<?= $expense['id'] ?>"
                                    data-column="description" class="editable">
                                    <?= $expense['description'] ?>
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm"
                                        onclick="confirmDelete(<?= $expense['id'] ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <!-- Summary Table -->
                <div class="summary-card float-end pt-4" style="flex: 1; max-width: 300px;">
                    <h5>Summary</h5>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>Total Expense:</th>
                                <td><span id="totalAmount">₹0</span></td>
                            </tr>
                            <tr>
                                <th>Remaining Amount:</th>
                                <td><span id="remainingAmount">₹<?= $remaining ?></span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Bootstrap Modal for Delete Confirmation -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Delete Expense</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this expense?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toast for Success/Failure -->
        <div class="toast-container position-fixed top-0 end-0 p-3">
            <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert"
                aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body"></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>

    </div>

    <script>
        let addExpenseBtn = document.getElementById('addExpenseBtn');
        let expenseTable = document.getElementById('expenseTable').querySelector('tbody');
        let deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        let confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        let deleteExpenseId = null;
        let totalAmountElem = document.getElementById('totalAmount');
        let remainingAmountElem = document.getElementById('remainingAmount');

        // Bootstrap Toast Function
        function showToast(message, isSuccess = true) {
            const toastElement = document.getElementById('successToast');
            toastElement.querySelector('.toast-body').textContent = message;
            toastElement.classList.remove('bg-success', 'bg-danger');
            toastElement.classList.add(isSuccess ? 'bg-success' : 'bg-danger');

            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        }

        // Confirm Delete
        function confirmDelete(id) {
            deleteExpenseId = id;
            deleteModal.show();
        }

        confirmDeleteBtn.addEventListener('click', function () {
            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: deleteExpenseId, action: 'delete' })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Expense deleted successfully!');
                        removeExpenseRow(deleteExpenseId);
                        updateTotals();
                    } else {
                        showToast('Failed to delete expense.', false);
                    }
                    deleteModal.hide();
                });
        });

        // Remove Expense Row
        function removeExpenseRow(id) {
            const row = document.querySelector(`tr td[data-id='${id}']`).parentNode;
            row.remove();
        }

        // Inline Editing Functionality
        function makeEditable(element) {
            let oldValue = element.textContent.trim();
            let column = element.getAttribute('data-column');
            let id = element.getAttribute('data-id');

            let input = document.createElement('input');
            input.type = 'text';
            input.value = oldValue;
            input.className = 'form-control';

            element.innerHTML = '';
            element.appendChild(input);
            input.focus();

            input.addEventListener('blur', () => {
                let newValue = input.value.trim();
                element.textContent = newValue;

                if (newValue !== oldValue) {
                    fetch('', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: id, column: column, value: newValue, action: 'update' })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showToast('Expense updated successfully!');
                                updateTotals();  // Update totals after editing
                            } else {
                                element.textContent = oldValue;
                                showToast('Failed to update expense.', false);
                            }
                        });
                }
            });
        }

        // Add Expense
        addExpenseBtn.addEventListener('click', () => {
            let newRow = document.createElement('tr');

            newRow.innerHTML = `
                <td><input id="addExpenseinput" type="number" class="form-control" placeholder="Expense" /></td>
                <td><input id="addExpenseinput" type="text" class="form-control" placeholder="Description" /></td>
                <td>
                    <button class="btn btn-success btn-sm" onclick="saveExpense(this)">Save</button>
                    <button class="btn btn-secondary btn-sm" onclick="cancelAddExpense(this)">Cancel</button>
                </td>
            `;

            expenseTable.appendChild(newRow);
        });

        // Save Expense
function saveExpense(button) {
    let row = button.closest('tr');
    let expense = parseFloat(row.querySelector('input[type="number"]').value);
    let description = row.querySelector('input[type="text"]').value;

    // Ensure the expense is a valid number and description is not empty
    if (!expense || !description) {
        showToast('Please fill in both fields.', false);
        return;
    }

    // Get the remaining amount from the page (already calculated)
    let remainingAmount = parseFloat(document.getElementById('remainingAmount').textContent.replace('₹', ''));

    // Check if the entered expense exceeds the remaining amount
    if (expense > remainingAmount) {
        showToast('Insufficient funds to add this expense.', false);
        return;
    }

    // Proceed with adding the expense if valid
    fetch('', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'add',
            ex_id: <?= $id ?>,
            expense: expense,
            description: description
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Expense added successfully!');
            row.remove(); // Remove input row

            let newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td id="regular">${expense}</td>
                <td id="regular">${description}</td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="confirmDelete(${data.expense_id})">Delete</button>
                </td>
            `;
            expenseTable.appendChild(newRow);

            // Update totals and remaining amounts immediately without page reload
            updateTotals();
        } else {
            showToast('Failed to add expense.', false);
        }
    });
}


        // Confirm Delete
        confirmDeleteBtn.addEventListener('click', function () {
            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: deleteExpenseId, action: 'delete' })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Expense deleted successfully!');
                        removeExpenseRow(deleteExpenseId);
                        updateTotals(); // Update totals after delete
                    } else {
                        showToast('Failed to delete expense.', false);
                    }
                    deleteModal.hide();
                });
        });

        // Update Total and Remaining Amount
        function updateTotals() {
            let totalAmount = 0;
            // Calculate total expense
            document.querySelectorAll('#expenseTable tbody tr').forEach(row => {
                totalAmount += parseFloat(row.querySelector('td:first-child').textContent) || 0;
            });

            // Get the total budget amount (use PHP to pass the value from server-side)
            let budgetAmount = <?= $entry['total_amount'] ?>;

            // Calculate the remaining amount
            let remainingAmount = budgetAmount - totalAmount;

            // Update the total and remaining amounts on the page
            totalAmountElem.textContent = `₹${totalAmount}`;
            remainingAmountElem.textContent = `₹${remainingAmount}`;
        }

        // Update totals when page loads
        updateTotals();
        // Cancel Add Expense (Fix for this function)
function cancelAddExpense(button) {
    let row = button.closest('tr');
    row.remove(); // Remove the row where the "Cancel" button was clicked
}
    </script>
</body>

</html>