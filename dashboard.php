<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<?php

include("includes/db_connect.php");

$view = (isset($_GET['view']) && $_GET['view'] === 'dashboard') ? 'dashboard' : 'home';
$show_recent_table = ($view === 'home');
$show_charts = ($view === 'dashboard');

$total_ci_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM configuration_items");
$total_ci = mysqli_fetch_assoc($total_ci_query);

// healthy ci
$healthy_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) as healthy
 FROM configuration_items
 WHERE health_score >= 80"
);

$healthy = mysqli_fetch_assoc($healthy_query);

//Warning ci's
$warning_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS warning
     FROM configuration_items
     WHERE health_score >= 30
     AND health_score < 80"
);

$warning1 = mysqli_fetch_assoc($warning_query);

//critical ci's
$critical_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS critical
     FROM configuration_items
     WHERE health_score < 30"
);

$critical1 = mysqli_fetch_assoc($critical_query);

// $issues = $warning1['warning'] + $critical1['critical'];

$avg_query = mysqli_query(
    $conn,
    "SELECT AVG(health_score) as avg_score
 FROM configuration_items"
);

$avg = mysqli_fetch_assoc($avg_query);
$average_score = $avg['avg_score'];

$healthy1 = $healthy['healthy'];
$warning  = $warning1['warning'];
$critical = $critical1['critical'];

$server = mysqli_num_rows(mysqli_query(
    $conn,
    "SELECT * FROM configuration_items WHERE ci_type='Server'"
));

$database = mysqli_num_rows(mysqli_query(
    $conn,
    "SELECT * FROM configuration_items WHERE ci_type='Database'"
));

$application = mysqli_num_rows(mysqli_query(
    $conn,
    "SELECT * FROM configuration_items WHERE ci_type='Application'"
));

$network = mysqli_num_rows(mysqli_query(
    $conn,
    "SELECT * FROM configuration_items WHERE ci_type='Network Device'"
));
?>



<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>CMDB Health Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
        }

        .header {
            background: #0d6efd;
            color: white;
            padding: 15px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .metric {
            font-size: 30px;
            font-weight: bold;
        }

        .ci-card {
            transition: all 0.3s ease;
        }

        .ci-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php include("includes/sidebar.php"); ?>
    <?php include("includes/header.php"); ?>
    
    <div style="margin-left:270px;padding:30px;">


        <div class="container mt-4">

            <div class="row">

                <div class="col-md-3">
                    <div class="card p-3 ci-card" data-filter="total" style="cursor:pointer;">

                        <h5>
                            <i class="bi bi-server"></i>
                            Total CIs
                        </h5>

                        <div class="metric">
                            <?php echo $total_ci['total']; ?>
                        </div>

                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3 ci-card" data-filter="healthy" style="cursor:pointer;">
                        <h5 class="text-success">
                            <i class="bi bi-check-circle-fill"></i>
                            Healthy CIs
                        </h5>
                        <div class="metric text-success">
                            <?php echo $healthy['healthy']; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3 ci-card" data-filter="warning" style="cursor:pointer;">

                        <h5 class="text-warning">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            Warning CIs
                        </h5>

                        <div class="metric text-warning">
                            <?php echo $warning1['warning']; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card p-3 ci-card" data-filter="critical" style="cursor:pointer;">
                        <h5 class="text-danger">
                            <i class="bi bi-x-octagon-fill"></i>
                            Critical CIs
                        </h5>

                        <div class="metric text-danger">
                            <?php echo $critical1['critical']; ?>
                        </div>
                    </div>
                </div>

            </div>
            <?php if ($show_recent_table) { ?>
            <div class="row mt-4">

                <div class="col-md-12">

                    <div class="card shadow-sm border-0">

                        <div class="card-body d-flex justify-content-between align-items-center">

                            <div>

                                <h5 class="mb-1">
                                    📊 Average Health Score
                                </h5>

                                <small class="text-muted">
                                    Overall CMDB Health
                                </small>

                            </div>

                            <h2 class="text-primary fw-bold">
                                <?php echo round($average_score); ?>%
                            </h2>

                        </div>

                    </div>

                </div>

            </div>

            <?php } ?>

            <?php if ($show_recent_table) { ?>
            <div class="container-fluid mt-5">
                <h3 class="mb-4">
                    📋 Recent Configuration Items
                </h3>
                <div class="card shadow-sm">

                    <div class="card-body">

                        <table class="table table-hover">

                            <thead class="table-primary">

                                <tr>

                                    <th>CI Name</th>
                                    <th>Type</th>
                                    <th>Owner</th>
                                    <th>Status</th>
                                    <th>Health Score</th>

                                </tr>

                            </thead>
                            <?php

                            $query = mysqli_query(
                                $conn,
                                "SELECT * FROM configuration_items
ORDER BY ci_id DESC
LIMIT 5"
                            );

                            while ($row = mysqli_fetch_assoc($query)) {

                                ?>
                                <tbody>
                                    <tr>

                                        <td>
                                            <?php echo $row['ci_name']; ?>
                                        </td>

                                        <td>
                                            <?php echo $row['ci_type']; ?>
                                        </td>

                                        <td>
                                            <?php echo $row['owner']; ?>
                                        </td>

                                        <td>

                                            <?php

                                            if ($row['status'] == "Active") {
                                                echo "<span class='badge bg-success'>Active</span>";
                                            } else {
                                                echo "<span class='badge bg-secondary'>Inactive</span>";
                                            }

                                            ?>

                                        </td>

                                        <td>

                                            <?php

                                            if ($row['health_score'] >= 80) {
                                                echo "<span class='badge bg-success'>Healthy (" . $row['health_score'] . "%)</span>";
                                            } elseif ($row['health_score'] >= 30) {
                                                echo "<span class='badge bg-warning text-dark'>Warning (" . $row['health_score'] . "%)</span>";
                                            } else {
                                                echo "<span class='badge bg-danger'>Critical (" . $row['health_score'] . "%)</span>";
                                            }

                                            ?>

                                        </td>

                                    </tr>
                                    <?php
                            }
                            ?>
                            </tbody>

                        </table>
                    </div>

                </div>
            </div>
            <?php } ?>

            <?php if ($show_charts) { ?>
            <div class="container-fluid mt-5">
                <h3 class="mb-4">
                    📊 Dashboard Overview
                </h3>
                <div class="row">

                    <div class="col-lg-6">

                        <div class="card shadow">

                            <div class="card-header bg-primary text-white">
                                Health Distribution
                            </div>

                            <div class="card-body">

                                <canvas id="healthChart"></canvas>

                            </div>

                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="card shadow">
                            <div class="card-header bg-success text-white">
                                CI Type Distribution
                            </div>

                            <div class="card-body">
                                <canvas id="typeChart"></canvas>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>

        const ctx = document.getElementById('healthChart');

        if (ctx) {
        new Chart(ctx, {

            type: 'pie',

            data: {

                labels: ['Healthy', 'Warning', 'Critical'],

                datasets: [{

                    data: [

                        <?php echo $healthy1; ?>,

                        <?php echo $warning; ?>,

                        <?php echo $critical; ?>

                    ],

                    backgroundColor: [

                        '#28a745',

                        '#ffc107',

                        '#dc3545'

                    ]

                }]

            }

        });
        }

    </script>
    <script>

        const typeCtx = document.getElementById('typeChart');

        if (typeCtx) {
            new Chart(typeCtx, {

                type: 'bar',

                data: {

                    labels: ['Server', 'Database', 'Application', 'Network Device'],

                    datasets: [{

                        label: 'Configuration Items',

                        data: [

                            <?php echo $server; ?>,

                            <?php echo $database; ?>,

                            <?php echo $application; ?>,

                            <?php echo $network; ?>

                        ]

                    }]

                },

                options: {

                    responsive: true,

                    plugins: {

                        legend: {

                            display: false

                        }

                    },

                    scales: {

                        y: {

                            beginAtZero: true

                        }

                    }

                }

            });
        }

    </script>
    <!-- CI Modal -->
    <div class="modal fade" id="ciModal" tabindex="-1">

        <div class="modal-dialog modal-fullscreen">

            <div class="modal-content">

                <div class="modal-header bg-primary text-white">

                    <h4 class="modal-title" id="modalTitle">
                        Configuration Items
                    </h4>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body p-0" id="modalBody">

                    <div class="text-center p-5">

                        <div class="spinner-border text-primary"></div>

                        <p class="mt-3">
                            Loading Configuration Items...
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>

        document.querySelectorAll(".ci-card").forEach(card => {

            card.addEventListener("click", function () {

                const filter = this.dataset.filter;

                document.getElementById("modalTitle").innerHTML =
                    filter.charAt(0).toUpperCase() +
                    filter.slice(1) +
                    " Configuration Items";

                document.getElementById("modalBody").innerHTML = `
            <div class="text-center p-5">
                <div class="spinner-border text-primary"></div>
                <p class="mt-3">Loading...</p>
            </div>
        `;

                const modal = new bootstrap.Modal(document.getElementById("ciModal"));
                modal.show();

                fetch("view_ci.php?popup=1&filter=" + filter)
                    .then(response => response.text())
                    .then(data => {
                        document.getElementById("modalBody").innerHTML = data;
                    });

            });

        });

    </script>
</body>

</html>