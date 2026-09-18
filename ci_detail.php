<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include("includes/db_connect.php");

$id = (int) $_GET['id'];

$result = mysqli_query($conn, "SELECT * FROM configuration_items WHERE ci_id=$id");
$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("Configuration Item not found.");
}

function badge_for_health($score)
{
    if ($score >= 80) return "<span class='badge bg-success'>Healthy ($score%)</span>";
    if ($score >= 30) return "<span class='badge bg-warning text-dark'>Warning ($score%)</span>";
    return "<span class='badge bg-danger'>Critical ($score%)</span>";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>CI Details - <?php echo htmlspecialchars($row['ci_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg-light">

    <?php include("includes/sidebar.php"); ?>
    <?php include("includes/header.php"); ?>

    <div style="margin-left:270px;padding:30px;">

        <div class="container">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">
                    <i class="bi bi-server me-2"></i>
                    <?php echo htmlspecialchars($row['ci_name']); ?>
                </h3>
                <div>
                    <?php if (($_SESSION['role'] ?? '') === 'Admin') { ?>
                        <a href="edit_ci.php?id=<?php echo $row['ci_id']; ?>" class="btn btn-warning">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                    <?php } ?>
                    <a href="view_ci.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm text-center p-3">
                        <small class="text-muted">Health Score</small>
                        <div class="fs-4 mt-1"><?php echo badge_for_health($row['health_score']); ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm text-center p-3">
                        <small class="text-muted">Status</small>
                        <div class="fs-5 mt-1 fw-bold"><?php echo htmlspecialchars($row['status']); ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm text-center p-3">
                        <small class="text-muted">Last Updated</small>
                        <div class="fs-5 mt-1 fw-bold">
                            <?php echo !empty($row['last_updated']) ? date("d M Y", strtotime($row['last_updated'])) : "-"; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">Basic Information</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">CI Type</small>
                            <?php echo htmlspecialchars($row['ci_type']); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Owner</small>
                            <?php echo htmlspecialchars($row['owner']); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Environment</small>
                            <?php echo htmlspecialchars($row['environment']); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Priority</small>
                            <?php echo htmlspecialchars($row['priority']); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Criticality</small>
                            <?php echo htmlspecialchars($row['criticality']); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Support Group</small>
                            <?php echo htmlspecialchars($row['support_group']); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">Infrastructure Details</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Hostname</small>
                            <?php echo htmlspecialchars($row['hostname']); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">IP Address</small>
                            <?php echo htmlspecialchars($row['ip_address']); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Operating System</small>
                            <?php echo htmlspecialchars($row['operating_system']); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Version</small>
                            <?php echo htmlspecialchars($row['version']); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Location</small>
                            <?php echo htmlspecialchars($row['location']); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning">Additional Information</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Created By</small>
                            <?php echo htmlspecialchars($row['created_by'] ?? '-'); ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Last Updated By</small>
                            <?php echo htmlspecialchars($row['updated_by'] ?? '-'); ?>
                        </div>
                        <div class="col-12">
                            <small class="text-muted d-block">Remarks</small>
                            <?php echo nl2br(htmlspecialchars($row['remarks'])); ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</body>

</html>