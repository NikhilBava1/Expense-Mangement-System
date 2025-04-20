<?php
session_start();
include('db_conn.php');
// Check if the user is logged in
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Manager</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

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
            padding: 10px;
            
        }

        .editable {
            background-color: #f5f5f5;
            border: 1px solid #ccc;
            padding: 5px;
            cursor: pointer;
        }

        .editable:focus {
            outline: none;
            background-color: #fff;
        }

        .actions-btn {
            display: flex;
            gap: 10px;
        }

        .actions-btn button {
            padding: 5px 10px;
        }

        .table-container {
            padding: 10px;
        }

        .expense-table th,
        .expense-table td {
            text-align: center;
        }

        .expense-table td {
            vertical-align: middle;
        }

        .expense-entry-table {
            margin-top: 30px;
        }

        .total-expense {
            font-weight: bold;
            margin-top: 10px;
            font-size: 1.2rem;
        }
        .title{
            color:#282828;
            font-family: Sansation-bold;
            font-size: 44px;
        }
        .search-box{
            font-family: Sansation-light;
        }
        #addEntryModal{
            font-family: Sansation-bold;
        }
        #addEntryModal input::placeholder{
            font-family: Sansation-light;
        }
        #addEntryModal input{
            font-family: Sansation-regular;
        }
        .responsive-image {
        width: 100%; /* Ensures the image spans the entire container width */
            height: auto; /* Maintains the image's aspect ratio */
        display: block; /* Removes any unwanted gaps below the image */
        object-fit: contain; /* Ensures the image is fully visible without cropping */
    }
    </style>
</head>

<body>
<div class="w-100 container">
    <img src="uploads/asorg_branding.png" alt="Branding Image" class="responsive-image">
</div>
    <div class="container my-5">
        <h1 class="text-center title">Expense Manager</h1>

        <div class="text-end my-3">
            <a href="#" class="btn btn-success float-start" data-bs-toggle="modal" data-bs-target="#addEntryModal"><i class="fa-regular fa-plus"></i> New Entry</a>
        </div><div class="container my-3">
    <div class="row justify-content-end">
        <div class="col-md-4">
            <div class="input-group search-box">
                <input type="text" id="search-input" class="form-control border border-dark shadow-sm"
                    placeholder="Search entries..." onkeyup="filterEntries()"
                    style="height: 45px; font-size: 16px; background-color: white; color: black;">
                <button id="clear-search" class="btn btn-secondary" onclick="clearSearch()" 
                    style="height: 45px; width: 40px;">
                    <i class="bi bi-x-lg" style="color: white;"></i>
                </button>
            </div>
        </div>
    </div>
</div>





        <div id="entries-container">
            <!-- Tables for entries and expenses will render here -->
            <?php include 'fetch_entries.php'; ?>
        </div>

        <!-- Add new entry form modal -->
        <div class="modal fade" id="addEntryModal" tabindex="-1" aria-labelledby="addEntryModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addEntryModalLabel">Add New Entry</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="save_entry.php" method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter name"
                                    required>
                            </div>
                            <div class="mb-3">
                                <label for="amount_description" class="form-label">Description</label>
                                <input type="text" class="form-control" id="amount_description"
                                    name="amount_description" placeholder="Description" required>
                            </div>
                            <div class="mb-3">
                                <label for="total_amount" class="form-label">Total Amount</label>
                                <input type="number" class="form-control" id="total_amount" name="total_amount"
                                    placeholder="Enter total amount" required>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Save Entry</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this expense?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Entry Confirmation Modal -->
    <div class="modal fade" id="deleteEntryModal" tabindex="-1" aria-labelledby="deleteEntryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteEntryModalLabel">Confirm Entry Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this entry and all associated expenses?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmDeleteEntryBtn" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Expense added successfully!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


    <script>



        let deleteExpenseId = null;
        let deleteEntryId = null;

        // Show Delete Confirmation Modal
        function showDeleteModal(expenseId, entryId) {
            deleteExpenseId = expenseId;
            deleteEntryId = entryId;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }

        // Confirm Delete
        document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
            fetch(`delete_expense.php?id=${deleteExpenseId}&entryId=${deleteEntryId}`, {
                method: 'GET',
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        // Remove deleted row dynamically
                        const row = document.querySelector(`[data-expense-id="${deleteExpenseId}"]`);
                        if (row) row.remove();

                        // Update Remaining Amount
                        const remainingCell = document.getElementById(`remaining-${deleteEntryId}`);
                        remainingCell.textContent = parseInt(remainingCell.textContent) + parseInt(data.deletedAmount);

                        // Close modal
                        const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                        deleteModal.hide();
                    } else {
                        alert("Error occurred while deleting!");
                    }
                });
        });

        //Add expense
        function addExpense(entryId) {
            // Locate the target table body using the entryId
            const tbody = document.getElementById(`expense-body-${entryId}`);

            if (!tbody) {
                console.error(`Table body with ID 'expense-body-${entryId}' not found.`);
                return; // Exit if tbody is not found
            }

            // Create a new table row
            const newRow = document.createElement('tr');

            // Set the row's inner HTML with editable cells and a save button
            newRow.innerHTML = `
        <td ondblclick="makeEditable(this)" class="editable" data-column="expense"></td>
        <td ondblclick="makeEditable(this)" class="editable" data-column="description"></td>
        <td class="actions-btn">
            <button class="btn btn-success btn-sm" onclick="saveExpense(${entryId}, this)">Save</button>
        </td>
    `;

            // Append the new row to the table body
            tbody.appendChild(newRow);

            console.log(`New expense row added for entry ID: ${entryId}`);

            // Optional: Attach 'makeEditable' functionality to newly added cells
            newRow.querySelectorAll('.editable').forEach(cell => {
                cell.ondblclick = () => makeEditable(cell);

            });
            let currentlyEditingCell = null; // To track the currently selected cell

            function makeEditable(cell) {
                // Remove the black border and make the previously selected cell non-editable
                if (currentlyEditingCell && currentlyEditingCell !== cell) {
                    currentlyEditingCell.style.border = ""; // Reset border of the previous cell
                    currentlyEditingCell.contentEditable = "false"; // Disable editing for the previous cell
                }

                // Set the current cell as the one being edited
                currentlyEditingCell = cell;

                // Apply black border to the current cell and make it editable
                cell.style.border = "2px solid black";
                cell.contentEditable = "true";

                // Focus on the cell to enable immediate editing
                cell.focus();

                // Remove the border when editing ends (on blur)
                cell.onblur = () => {
                    cell.style.border = ""; // Remove the black border
                    cell.contentEditable = "false"; // Disable editing
                    currentlyEditingCell = null; // Clear the currently editing cell
                };
            }

            // Add the event listener to all editable cells
            document.querySelectorAll('.editable').forEach(cell => {
                cell.ondblclick = () => makeEditable(cell);
            });

        }

        // Example saveExpense function to demonstrate its role
        function saveExpense(entryId, button) {
            console.log(`Save clicked for entry ID: ${entryId}`);
            const row = button.closest('tr');
            const expense = row.querySelector('[data-column="expense"]').textContent.trim();
            const description = row.querySelector('[data-column="description"]').textContent.trim();

            if (!expense || !description) {
                alert('Expense or description cannot be empty.');
                return;
            }

            // AJAX/Fetch request to save data (pseudo-code example)
            console.log(`Saving: Expense = ${expense}, Description = ${description}`);
            alert(`Data saved: Expense = ${expense}, Description = ${description}`);
        }



        // Save Expense to Database
        function saveExpense(entryId, btn) {
            let row = btn.parentElement.parentElement;
            let expense = row.cells[0].textContent.trim();
            let description = row.cells[1].textContent.trim();

            if (!expense || !description) {
                alert("Please fill in all fields!");
                return;
            }

            fetch('add_expense.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    entryId,
                    expense,
                    description,
                }),
            })
                .then((res) => res.json())
                .then((data) => {
                    if (data.success) {
                        // Trigger the success modal after saving the expense
                        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                        successModal.show();

                        setTimeout(() => {
                            window.location.reload(); // Reload the page to reflect changes
                        }, 2000); // Close the modal and reload after 2 seconds
                    } else {
                        alert("Error adding expense!");
                    }
                });
        }

        // Confirm Delete Entry
        document.getElementById('confirmDeleteEntryBtn').addEventListener('click', () => {
            fetch(`delete_entry.php?id=${deleteEntryId}`, {
                method: 'GET',
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        // Remove deleted entry dynamically
                        const entryTable = document.querySelector(`.expense-entry-table[data-entry-id="${deleteEntryId}"]`);
                        if (entryTable) entryTable.remove();

                        // Close modal
                        const deleteEntryModal = bootstrap.Modal.getInstance(document.getElementById('deleteEntryModal'));
                        deleteEntryModal.hide();
                    } else {
                        alert("Error occurred while deleting the entry!");
                    }
                });
        });

        // Make cell editable on double-click
        function makeEditable(cell) {
            const currentValue = cell.textContent;
            const column = cell.getAttribute('data-column');
            const expenseId = cell.getAttribute('data-id');

            const inputField = document.createElement('input');
            inputField.value = currentValue;
            inputField.classList.add('editable-input');
            cell.innerHTML = '';
            cell.appendChild(inputField);

            const saveBtn = cell.closest('tr').querySelector('.save-btn');
            saveBtn.style.display = 'inline-block'; // Show save button

            // When input field loses focus, save changes
            inputField.addEventListener('blur', function () {
                const newValue = inputField.value;
                saveEdit(saveBtn, expenseId, column, newValue);
            });
        }

        // Save edited value to database and hide the save button
        function saveEdit(button, expenseId, column, value) {
            // Send updated data to the server via AJAX
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_expense.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send(`id=${expenseId}&column=${column}&value=${encodeURIComponent(value)}`);

            xhr.onload = function () {
                if (xhr.status === 200) {
                    console.log('Update successful');
                    button.style.display = 'none'; // Hide save button after save
                    // Update total and remaining expenses
                    updateTotalAndRemaining();
                } else {
                    console.log('Error updating');
                }
            };

            // Update the displayed value
            const cell = button.closest('tr').querySelector(`[data-id="${expenseId}"][data-column="${column}"]`);
            cell.textContent = value;
        }

        // Update total and remaining expenses
        function updateTotalAndRemaining() {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_totals.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send();

            xhr.onload = function () {
                if (xhr.status === 200) {
                    // Update the total and remaining expense on the page
                    const totalExpenses = document.querySelector('#total-expenses');
                    const remainingExpenses = document.querySelector('#remaining-expenses');
                    const data = JSON.parse(xhr.responseText);
                    totalExpenses.textContent = data.total_expenses;
                    remainingExpenses.textContent = data.remaining_expenses;
                } else {
                    console.log('Error updating totals');
                }
            };
        }

        function filterEntries() {
            const query = document.getElementById('search-input').value;

            // AJAX request to PHP to fetch filtered entries
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `fetch_entries.php?search=${query}`, true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    document.getElementById('entries-container').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }

        function clearSearch() {
            document.getElementById('search-input').value = '';
            filterEntries(); // Reload all entries
        }


    </script>
</body>

</html>