<!DOCTYPE html>
<html>
<head>
    <style>
        /* Style for the modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1;
            justify-content: center;
            align-items: center;
        }

        /* Style for the modal content */
        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>
<body>
    <div class="dropdown">
        <label>
            Incident Case Type
        </label>
        <select id="incidentType">
            <option>A</option>
            <option>B</option>
            <option value="Other">Other</option>
        </select>
    </div>

    <!-- The modal popup -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <!-- Add your content for the modal here -->
            <p>Enter other incident case type:</p>
            <input type="text" id="otherIncidentType">
            <button id="saveButton">Save</button>
        </div>
    </div>

    <script>
        // Get the modal and the select element
        var modal = document.getElementById("myModal");
        var select = document.getElementById("incidentType");

        // When the "Other" option is selected, show the modal
        select.addEventListener("change", function () {
            if (select.value === "Other") {
                modal.style.display = "block";
            } else {
                modal.style.display = "none";
            }
        });

        // Close the modal when the Save button is clicked
        var saveButton = document.getElementById("saveButton");
        saveButton.addEventListener("click", function () {
            modal.style.display = "none";
        });

        // Close the modal if the user clicks outside of it
        window.addEventListener("click", function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        });
    </script>
</body>
</html>
