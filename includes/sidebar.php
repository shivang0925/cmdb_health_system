<div class="bg-primary text-white p-3" style="width:250px;height:100vh;position:fixed;left:0;top:0;">

    <h3 class="text-center mb-4">CMDB Health</h3>

    <hr>

    <a href="dashboard.php?view=home" class="btn btn-primary w-100 text-start mb-2">
        🏠 Home
    </a>

    <a href="dashboard.php?view=dashboard" class="btn btn-primary w-100 text-start mb-2">
        📊 Dashboard
    </a>

    <?php if (($_SESSION['role'] ?? '') === 'Admin') { ?>

    <a href="add_ci.php" class="btn btn-primary w-100 text-start mb-2">
        ➕ Add CI
    </a>

    <?php } ?>

    <a href="view_ci.php" class="btn btn-primary w-100 text-start mb-2">
        📋 View CIs
    </a>

    <?php if (($_SESSION['role'] ?? '') === 'Admin') { ?>

    <a href="manage_users.php" class="btn btn-primary w-100 text-start mb-2">
        👤 Manage Users
    </a>

    <?php } ?>

    <hr>

    <a href="logout.php" class="btn btn-danger w-100">
        Logout
    </a>

</div>