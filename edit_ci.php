<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (($_SESSION['role'] ?? '') !== 'Admin') {
    die("Access denied: only an Admin can edit Configuration Items.");
}
?>
<?php

include("includes/db_connect.php");
include("includes/health_score.php");

$id = $_GET['id'];

$result = mysqli_query(
    $conn,
    "SELECT * FROM configuration_items WHERE ci_id=$id"
);

$row = mysqli_fetch_assoc($result);

if (isset($_POST['update'])) {
    $ci_name = trim($_POST['ci_name']);
    $ci_type = $_POST['ci_type'];
    $owner = $row['owner']; // locked field - not editable, always keep original value
    $environment = $_POST['environment'];
    $status = $_POST['status'];

    $priority = $_POST['priority'];
    $criticality = $_POST['criticality'];
    $hostname = trim($_POST['hostname']);
    $ip_address = $row['ip_address']; // locked field - not editable, always keep original value
    $operating_system = $_POST['operating_system'];
    $version = $_POST['version'];
    $location = $_POST['location'];
    $support_group = $_POST['support_group'];
    $remarks = $_POST['remarks'];

    $updated_by = $_SESSION['username'];

    // --- Server-side validation ---
    $errors = [];

    if ($ci_name === '') {
        $errors[] = "CI Name is required.";
    }

    if (empty($errors)) {

        $health_score = calculateHealthScore($_POST);

        mysqli_query(
            $conn,
            "UPDATE configuration_items
SET
    ci_name='$ci_name',
    ci_type='$ci_type',
    owner='$owner',
    environment='$environment',
    status='$status',
    priority='$priority',
    criticality='$criticality',
    hostname='$hostname',
    ip_address='$ip_address',
    operating_system='$operating_system',
    version='$version',
    location='$location',
    support_group='$support_group',
    remarks='$remarks',
    health_score='$health_score',
    updated_by='$updated_by',
last_updated=CURDATE()
WHERE ci_id=$id"
        );

        echo "<script>
    alert('CI Updated Successfully');
    window.location='view_ci.php';
    </script>";

    } else {
        // Re-fetch original row into $row so the form still shows current DB values,
        // but keep the invalid submission visible via $formError
        $formErrors = $errors;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit CI</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        select.form-select {
            display: block;
            width: 100%;
            padding: 0.375rem 2.5rem 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            background-color: #fff;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }
    </style>
</head>

<body class="bg-light">

    <div class="container mt-5">

        <div class="card shadow">

            <div class="card-header bg-warning d-flex justify-content-between align-items-center">

                <h3 class="mb-0">
                    <i class="bi bi-pencil-square me-2"></i>
                    Edit Configuration Item
                </h3>

                <button type="button" class="btn-close" onclick="window.history.back();">
                </button>

            </div>

            <div class="card-body">

                <?php if (!empty($formErrors)) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Please fix the following:</strong>
                    <ul class="mb-0">
                        <?php foreach ($formErrors as $error) { ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php } ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php } ?>

                <form method="post">

                    <div class="mb-3">
                        <label>CI Name</label>
                        <input type="text" name="ci_name" class="form-control" value="<?php echo $row['ci_name']; ?>">
                    </div>
                    <div class="mb-3">
                        <label>CI Type</label>

                        <select name="ci_type" class="form-select">

                            <option value="Server" <?php if ($row['ci_type'] == "Server")
                                echo "selected"; ?>>
                                Server
                            </option>

                            <option value="Application" <?php if ($row['ci_type'] == "Application")
                                echo "selected"; ?>>
                                Application
                            </option>

                            <option value="Database" <?php if ($row['ci_type'] == "Database")
                                echo "selected"; ?>>
                                Database
                            </option>

                            <option value="Network Device" <?php if ($row['ci_type'] == "Network Device")
                                echo "selected"; ?>>
                                Network Device
                            </option>

                            <option value="Storage" <?php if ($row['ci_type'] == "Storage")
                                echo "selected"; ?>>
                                Storage
                            </option>

                        </select>

                    </div>

                    <div class="mb-3">
                        <label>Owner</label>
                        <input type="text" name="owner" class="form-control bg-light" value="<?php echo $row['owner']; ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Environment</label>
                        <select name="environment" class="form-select">

                            <option value="Production" <?php if ($row['environment'] == "Production")
                                echo "selected"; ?>>
                                Production</option>

                            <option value="Development" <?php if ($row['environment'] == "Development")
                                echo "selected"; ?>>Development</option>

                            <option value="Testing" <?php if ($row['environment'] == "Testing")
                                echo "selected"; ?>>Testing
                            </option>

                            <option value="UAT" <?php if ($row['environment'] == "UAT")
                                echo "selected"; ?>>UAT</option>

                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Priority</label>

                        <select name="priority" class="form-select">

                            <option value="Low" <?php if ($row['priority'] == "Low")
                                echo "selected"; ?>>
                                Low
                            </option>

                            <option value="Medium" <?php if ($row['priority'] == "Medium")
                                echo "selected"; ?>>
                                Medium
                            </option>

                            <option value="High" <?php if ($row['priority'] == "High")
                                echo "selected"; ?>>
                                High
                            </option>

                            <option value="Critical" <?php if ($row['priority'] == "Critical")
                                echo "selected"; ?>>
                                Critical
                            </option>

                        </select>

                    </div>
                    <div class="mb-3">
                        <label>Criticality</label>

                        <select name="criticality" class="form-select">

                            <option value="Low" <?php if ($row['criticality'] == "Low")
                                echo "selected"; ?>>
                                Low
                            </option>

                            <option value="Medium" <?php if ($row['criticality'] == "Medium")
                                echo "selected"; ?>>
                                Medium
                            </option>

                            <option value="High" <?php if ($row['criticality'] == "High")
                                echo "selected"; ?>>
                                High
                            </option>

                        </select>

                    </div>

                    <div class="mb-3">
                        <label>Status</label>

                        <select name="status" class="form-select">

                            <option value="Active" <?php if ($row['status'] == "Active")
                                echo "selected"; ?>>
                                Active
                            </option>

                            <option value="Maintenance" <?php if ($row['status'] == "Maintenance")
                                echo "selected"; ?>>
                                Maintenance
                            </option>

                            <option value="Retired" <?php if ($row['status'] == "Retired")
                                echo "selected"; ?>>
                                Retired
                            </option>

                            <option value="Failed" <?php if ($row['status'] == "Failed")
                                echo "selected"; ?>>
                                Failed
                            </option>

                        </select>

                    </div>

                    <div class="mb-3">
                        <label>Hostname</label>
                        <input type="text" name="hostname" class="form-control" value="<?php echo $row['hostname']; ?>">
                    </div>

                    <div class="mb-3">
                        <label>IP Address</label>
                        <input type="text" name="ip_address" class="form-control bg-light" value="<?php echo $row['ip_address']; ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label>Operating System</label>
                        <input type="text" name="operating_system" class="form-control" value="<?php echo $row['operating_system']; ?>">
                    </div>

                    <div class="mb-3">
                        <label>Version</label>
                        <input type="text" name="version" class="form-control" value="<?php echo $row['version']; ?>">
                    </div>

                    <div class="mb-3">
                        <label>Location</label>
                        <input type="text" name="location" class="form-control" value="<?php echo $row['location']; ?>">
                    </div>

                    <div class="mb-3">
                        <label>Support Group</label>
                        <input type="text" name="support_group" class="form-control" value="<?php echo $row['support_group']; ?>">
                    </div>

                    <div class="mb-3">
                        <label>Remarks</label>
                        <textarea name="remarks" class="form-control"><?php echo $row['remarks']; ?></textarea>
                    </div>

                    <div class="d-flex justify-content-end mt-4">

                        <button type="submit" name="update" class="btn btn-primary btn-lg px-4">

                            <i class="bi bi-floppy-fill me-2"></i>

                            Update

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</body>

</html>