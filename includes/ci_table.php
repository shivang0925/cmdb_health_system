<?php

$filter = $_GET['filter'] ?? 'total';

switch ($filter) {

    case 'healthy':
        $sql = "SELECT * FROM configuration_items
                WHERE health_score >= 80";
        break;

    case 'warning':
        $sql = "SELECT * FROM configuration_items
                WHERE health_score >= 30
                AND health_score < 80";
        break;

    case 'critical':
        $sql = "SELECT * FROM configuration_items
                WHERE health_score < 30";
        break;

    default:
        $sql = "SELECT * FROM configuration_items";
}

$result = mysqli_query($conn, $sql);

?>

<div class="table-responsive">

    <table id="ciTable" class="table table-hover table-bordered align-middle">
        <thead class="table-dark">

            <tr>

                <th>CI Name</th>

                <th>CI Type</th>

                <th>Owner</th>

                <th>Environment</th>

                <th>Priority</th>

                <th>Hostname</th>

                <th>Support Group</th>

                <th>Health Score</th>

                <th>Last Updated</th>

                <th width="170">Actions</th>

            </tr>

        </thead>

        <tbody id="ciTableBody">

            <?php

            while ($row = mysqli_fetch_assoc($result)) {

                ?>

                <tr>

                    <td>
                        <a href="ci_detail.php?id=<?php echo $row['ci_id']; ?>" class="text-decoration-none fw-bold">
                            <?php echo $row['ci_name']; ?>
                        </a>
                    </td>

                    <td><?php echo $row['ci_type']; ?></td>

                    <td><?php echo $row['owner']; ?></td>

                    <td><?php echo $row['environment']; ?></td>

                    <td><?php echo $row['priority']; ?></td>

                    <td><?php echo $row['hostname']; ?></td>

                    <td><?php echo $row['support_group']; ?></td>

                    <td><?php echo $row['health_score']; ?>%</td>

                    <td>
                        <?php
                        echo !empty($row['last_updated'])
                            ? date("d M Y", strtotime($row['last_updated']))
                            : "-";
                        ?>
                    </td>

                    <td>

                        <?php if (($_SESSION['role'] ?? '') === 'Admin') { ?>

                        <a href="edit_ci.php?id=<?php echo $row['ci_id']; ?>" class="btn btn-warning btn-sm">

                            <i class="bi bi-pencil-square"></i>

                            Edit

                        </a>

                        <a href="delete_ci.php?id=<?php echo $row['ci_id']; ?>" class="btn btn-danger btn-sm"
                            onclick="return confirm('Are you sure?')">

                            <i class="bi bi-trash"></i>

                            Delete

                        </a>

                        <?php } else { ?>

                        <span class="text-muted small">View only</span>

                        <?php } ?>

                    </td>

                </tr>

                <?php
            }
            ?>

        </tbody>

    </table>
</div>