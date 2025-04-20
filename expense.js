<
script type = "text" >
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

// Add Expense Function
function addExpense(entryId) {
    // Locate the target table body using the entryId
    const tbody = document.getElementById(expense - body - $ { entryId });

    if (!tbody) {
        console.error(Table body with ID 'expense-body-${entryId}'
            not found.);
        return; // Exit if tbody is not found
    }

    // Create a new table row
    const newRow = document.createElement('tr');

    // Set the row's inner HTML with editable cells and a save button
    newRow.innerHTML = `
        <td ondblclick="makeEditable(this)" class="editable" data-column="expense">Double-click to edit</td>
        <td ondblclick="makeEditable(this)" class="editable" data-column="description">Double-click to edit</td>
        <td class="actions-btn">
            <button class="btn btn-success btn-sm" onclick="saveExpense(${entryId}, this)">Save</button>
        </td>
    `;

    // Append the new row to the table body
    tbody.appendChild(newRow);

    console.log(New expense row added
        for entry ID: $ { entryId });

    // Optional: Attach 'makeEditable' functionality to newly added cells
    newRow.querySelectorAll('.editable').forEach(cell => {
        cell.ondblclick = () => makeEditable(cell);
    });
}

// Example function to make a cell editable (if not defined already)
function makeEditable(cell) {
    if (cell.isContentEditable) return;

    const originalText = cell.textContent.trim();
    cell.setAttribute('contenteditable', 'true');
    cell.focus();

    // Add blur event to save content when editing is done
    cell.onblur = function() {
        cell.setAttribute('contenteditable', 'false');
        console.log(Updated $ { cell.getAttribute('data-column') }: , cell.textContent.trim());
    };

    // Optional: Pressing Enter also finalizes editing
    cell.onkeydown = function(e) {
        if (e.key === 'Enter') {
            cell.blur();
            e.preventDefault(); // Prevent new line
        }
    };

    console.log(Editing enabled
        for cell: $ { cell.getAttribute('data-column') });
}

// Example saveExpense function to demonstrate its role
function saveExpense(entryId, button) {
    console.log(Save clicked
        for entry ID: $ { entryId });
    const row = button.closest('tr');
    const expense = row.querySelector('[data-column="expense"]').textContent.trim();
    const description = row.querySelector('[data-column="description"]').textContent.trim();

    if (!expense || !description) {
        alert('Expense or description cannot be empty.');
        return;
    }

    // AJAX/Fetch request to save data (pseudo-code example)
    console.log(Saving: Expense = $ { expense }, Description = $ { description });
    alert(Data saved: Expense = $ { expense }, Description = $ { description });
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

function makeEditable(cell) {
    const originalValue = cell.textContent.trim();
    cell.contentEditable = true;
    cell.focus();

    // Save on blur
    cell.onblur = function() {
        cell.contentEditable = false;
        const newValue = cell.textContent.trim();
        const expenseId = cell.dataset.id;
        const column = cell.dataset.column;

        if (expenseId && originalValue !== newValue) {
            updateExpense(expenseId, column, newValue);
        }
    };

    // Save on Enter key
    cell.onkeydown = function(event) {
        if (event.key === "Enter") {
            event.preventDefault(); // Prevent newline in contentEditable
            cell.blur(); // Trigger the blur event to save
        }
    };
}

let deleteEntryId = null;

// Show Delete Entry Modal
function showDeleteEntryModal(entryId) {
    deleteEntryId = entryId;
    new bootstrap.Modal(document.getElementById('deleteEntryModal')).show();
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

<
/script>