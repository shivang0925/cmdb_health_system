<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (($_SESSION['role'] ?? '') !== 'Admin') {
    die("Access denied: only an Admin can add Configuration Items.");
}
?>
<?php
include("includes/db_connect.php");
include("includes/health_score.php");

if (isset($_POST['save'])) {
    $ci_name = trim($_POST['ci_name']);
    $ci_type = $_POST['ci_type'];
    $owner = trim($_POST['owner']);
    $environment = $_POST['environment'];
    $status = $_POST['status'];
    $priority = $_POST['priority'];
    $criticality = $_POST['criticality'];
    $hostname = trim($_POST['hostname']);
    $ip_address = trim($_POST['ip_address']);
    $operating_system = $_POST['operating_system'];
    $version = $_POST['version'];
    $location = $_POST['location'];
    $support_group = $_POST['support_group'];
    $remarks = $_POST['remarks'];

    $created_by = $_SESSION['username'];

    // --- Server-side validation ---
    $errors = [];

    if ($ci_name === '') {
        $errors[] = "CI Name is required.";
    }
    if ($owner === '') {
        $errors[] = "Owner is required.";
    }
    if ($ip_address === '') {
        $errors[] = "IP Address is required.";
    } elseif (!filter_var($ip_address, FILTER_VALIDATE_IP)) {
        $errors[] = "IP Address is not a valid IPv4/IPv6 address.";
    }

    if (empty($errors)) {

        $health_score = calculateHealthScore($_POST);

        $sql = "INSERT INTO configuration_items
(
ci_name,
ci_type,
owner,
environment,
status,
priority,
criticality,
hostname,
ip_address,
operating_system,
version,
location,
support_group,
last_updated,
health_score,
monitoring_status,
remarks,
created_by
)

VALUES
(
'$ci_name',
'$ci_type',
'$owner',
'$environment',
'$status',
'$priority',
'$criticality',
'$hostname',
'$ip_address',
'$operating_system',
'$version',
'$location',
'$support_group',
CURDATE(),
'$health_score',
'Running',
'$remarks',
'$created_by'
)";
        mysqli_query($conn, $sql);

        echo '
<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
    <strong>Success!</strong> Configuration Item has been added successfully.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>';

        // Clear the submitted values so the form resets after a successful save
        $_POST = [];

    } else {
        echo '<div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
    <strong>Please fix the following:</strong>
    <ul class="mb-0">';
        foreach ($errors as $error) {
            echo '<li>' . htmlspecialchars($error) . '</li>';
        }
        echo '</ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>';
    }
}

// Helper to redisplay a submitted value if validation failed, otherwise blank
function old($field)
{
    return htmlspecialchars($_POST[$field] ?? '');
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add CI</title>

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

        <div class="card shadow-lg border-0 rounded-4">
            <!-- <div class="card-header bg-primary text-white py-3 rounded-top-4">
                <h3 class="mb-0">
                    <i class="bi bi-server me-2"></i>
                    Add Configuration Item
                </h3>
            </div> -->

            <div class="card-header bg-primary text-white py-3 rounded-top-4 d-flex justify-content-between align-items-center">

                <h3 class="mb-0">
                    <i class="bi bi-server me-2"></i>
                    Add Configuration Item
                </h3>

                <button type="button" class="btn-close btn-close-white" onclick="window.history.back();" aria-label="Close"></button>

            </div>

            <div class="card-body">

                <form method="post">
                    <h5 class="text-primary mb-3">
                        Basic Information
                    </h5>
                    <hr>
                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label>CI Name <span class="text-danger">*</span></label>

                            <input type="text" name="ci_name" class="form-control" value="<?php echo old('ci_name'); ?>" required>

                        </div>

                        <div class="col-md-6 mb-3">

                            <label>CI Type</label>

                            <select name="ci_type" class="form-select">
                                <option>Server</option>
                                <option>Application</option>
                                <option>Database</option>
                                <option>Network Device</option>
                                <!-- <option>Network Device</option> -->
                            </select>

                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Owner <span class="text-danger">*</span></label>
                            <input type="text" name="owner" class="form-control" value="<?php echo old('owner'); ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label>Environment</label>
                            <select name="environment" class="form-select">
                                <option>Production</option>
                                <option>UAT</option>
                                <option>Development</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Status</label>
                            <select name="status" class="form-select">
                                <option>Active</option>
                                <option>Maintenance</option>
                                <option>Retired</option>
                                <option>Failed</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Priority</label>

                            <select name="priority" class="form-select">

                                <option>Low</option>

                                <option>Medium</option>

                                <option>High</option>

                                <option>Critical</option>

                            </select>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Criticality</label>

                            <select name="criticality" class="form-select">

                                <option>Low</option>

                                <option>Medium</option>

                                <option>High</option>

                            </select>

                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Support Group</label>

                            <input type="text" name="support_group" class="form-control">

                        </div>
                    </div>
                    <h5 class="text-success mt-4 mb-3">
                        Infrastructure Details
                    </h5>
                    <hr>

                    <div class="row">
                        <div class="col-md-6 mb-3">

                            <label>Hostname</label>

                            <input type="text" name="hostname" class="form-control" value="<?php echo old('hostname'); ?>">

                        </div>
                        <div class="col-md-6 mb-3">
                            <label>IP Address <span class="text-danger">*</span></label>

                            <input type="text" name="ip_address" class="form-control" value="<?php echo old('ip_address'); ?>" required>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Operating System</label>

                            <input type="text" name="operating_system" class="form-control">

                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Version</label>

                            <input type="text" name="version" class="form-control">

                        </div>
                    </div>

                    <div class="mb-3">
                        <label>Location</label>

                        <input type="text" name="location" class="form-control">

                    </div>

                    <div class="mb-3">
                        <h5 class="text-warning mt-4 mb-3">
                            Additional Information
                        </h5>
                        <hr>
                        <label>Remarks</label>

                        <textarea name="remarks" rows="4" class="form-control"></textarea>

                    </div>

                    <div class="d-flex justify-content-end mt-4">

                        <button type="submit" name="save" class="btn btn-success btn-lg px-4">

                            <i class="bi bi-check-circle-fill me-2"></i>

                            Save Configuration Item

                        </button>

                    </div>

                </form>

            </div>
        </div>

    </div>

</body>

</html>