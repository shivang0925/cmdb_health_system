<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<?php
include("includes/db_connect.php");
$popup = isset($_GET['popup']);
?>

<?php if (!$popup) { ?>

    <!DOCTYPE html>
    <html>

    <head>

        <title>View Configuration Items</title>

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    </head>

    <body class="bg-light">

        <div class="container mt-5">

            <div class="card shadow">

                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

                    <h3 class="mb-0">Configuration Items</h3>

                    <button type="button" class="btn-close btn-close-white" onclick="window.history.back();" aria-label="Close"></button>

                </div>

                <div class="card-body">

                <?php } ?>

                <div class="card mb-3 shadow-sm">

                    <div class="card-body">

                        <div class="row align-items-end">

                            <!-- Search Box -->
                            <div class="col-md-4">

                                <label class="form-label fw-bold">
                                    Search Configuration Item
                                </label>

                                <input type="text" id="searchInput" class="form-control"
                                    placeholder="Search by CI Name, Owner, Hostname...">

                            </div>

                            <!-- CI Type -->
                            <div class="col-md-2">

                                <label class="form-label fw-bold">
                                    CI Type
                                </label>

                                <select id="typeFilter" class="form-select">

                                    <option value="">All</option>

                                    <option value="Server">Server</option>

                                    <option value="Database">Database</option>

                                    <option value="Application">Application</option>

                                    <option value="Network Device">Network Device</option>

                                    <option value="Storage">Storage</option> 

                                </select>

                            </div>

                            <!-- Environment -->
                            <div class="col-md-2">

                                <label class="form-label fw-bold">
                                    Environment
                                </label>

                                <select id="environmentFilter" class="form-select">

                                    <option value="">All</option>

                                    <option value="Production">Production</option>

                                    <option value="Development">Development</option>

                                    <option value="Testing">Testing</option>

                                    <option value="UAT">UAT</option>

                                </select>

                            </div>

                            <!-- Priority -->
                            <div class="col-md-2">

                                <label class="form-label fw-bold">
                                    Priority
                                </label>

                                <select id="priorityFilter" class="form-select">

                                    <option value="">All</option>

                                    <option value="Low">Low</option>

                                    <option value="Medium">Medium</option>

                                    <option value="High">High</option>

                                    <option value="Critical">Critical</option>

                                </select>

                            </div>

                            <!-- Export Button -->
                            <div class="col-md-2 text-end">

                                <a href="export_ci.php" class="btn btn-success w-100">

                                    <i class="bi bi-file-earmark-excel"></i>

                                    Export CSV

                                </a>

                            </div>

                        </div>

                    </div>

                </div>
                <?php
                include("includes/ci_table.php");

                ?>

                <?php if (!$popup) { ?>

                </div>

            </div>

        </div>

        <script>

            function filterTable() {

                let searchValue = document.getElementById("searchInput").value.toLowerCase();
                let typeValue = document.getElementById("typeFilter").value.toLowerCase();
                let environmentValue = document.getElementById("environmentFilter").value.toLowerCase();
                let priorityValue = document.getElementById("priorityFilter").value.toLowerCase();

                let table = document.getElementById("ciTable");
                let rows = table.getElementsByTagName("tr");

                for (let i = 1; i < rows.length; i++) {

                    let row = rows[i];
                    let cells = row.getElementsByTagName("td");

                    if (cells.length == 0)
                        continue;

                    let rowText = row.textContent.toLowerCase();
                    let ciType = cells[1].textContent.toLowerCase();
                    let environment = cells[3].textContent.toLowerCase();
                    let priority = cells[4].textContent.toLowerCase();

                    let searchMatch = rowText.includes(searchValue);
                    let typeMatch = (typeValue === "" || ciType === typeValue);
                    let environmentMatch = (environmentValue === "" || environment === environmentValue);
                    let priorityMatch = (priorityValue === "" || priority === priorityValue);
                    if (searchMatch && typeMatch && environmentMatch && priorityMatch) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }

                }

            }

            document.getElementById("searchInput").addEventListener("keyup", filterTable);

            document.getElementById("typeFilter").addEventListener("change", filterTable);
            document.getElementById("environmentFilter").addEventListener("change", filterTable);

            document.getElementById("priorityFilter").addEventListener("change", filterTable);

        </script>
    </body>

    </html>

<?php } ?>