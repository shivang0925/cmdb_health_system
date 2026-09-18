<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (($_SESSION['role'] ?? '') !== 'Admin') {
    die("Access denied: only an Admin can manage users.");
}

include("includes/db_connect.php");

$errors = [];
$success = "";

// --- Add a new user ---
if (isset($_POST['add_user'])) {
    $new_username  = trim($_POST['username']);
    $new_full_name = trim($_POST['full_name']);
    $new_password  = $_POST['password'];
    $new_role      = $_POST['role'];

    if ($new_username === '') {
        $errors[] = "Username is required.";
    }
    if (strlen($new_password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (empty($errors)) {
        $check = mysqli_query(
            $conn,
            "SELECT id FROM users WHERE username='" . mysqli_real_escape_string($conn, $new_username) . "'"
        );

        if (mysqli_num_rows($check) > 0) {
            $errors[] = "That username already exists.";
        } else {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);

            mysqli_query(
                $conn,
                "INSERT INTO users (username, password, full_name, role) VALUES (
                    '" . mysqli_real_escape_string($conn, $new_username) . "',
                    '" . mysqli_real_escape_string($conn, $hashed) . "',
                    '" . mysqli_real_escape_string($conn, $new_full_name) . "',
                    '" . mysqli_real_escape_string($conn, $new_role) . "'
                )"
            );

            $success = "User '$new_username' created successfully.";
        }
    }
}

// --- Reset an existing user's password ---
if (isset($_POST['reset_password'])) {
    $target_id     = (int) $_POST['user_id'];
    $reset_password = $_POST['new_password'];

    if (strlen($reset_password) < 6) {
        $errors[] = "New password must be at least 6 characters.";
    } else {
        $hashed = password_hash($reset_password, PASSWORD_DEFAULT);

        mysqli_query(
            $conn,
            "UPDATE users SET password='" . mysqli_real_escape_string($conn, $hashed) . "' WHERE id=$target_id"
        );

        $success = "Password reset successfully.";
    }
}

$users_result = mysqli_query($conn, "SELECT id, username, full_name, role FROM users ORDER BY id");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <?php include("includes/sidebar.php"); ?>

    <div style="margin-left:270px;padding:30px;">

        <div class="container">

            <h3 class="mb-4">👤 Manage Users</h3>

            <?php if (!empty($errors)) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error) { ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php } ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>

            <?php if ($success !== "") { ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>

            <div class="row">

                <div class="col-md-5">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">Add New User</div>
                        <div class="card-body">
                            <form method="post">
                                <div class="mb-3">
                                    <label>Username</label>
                                    <input type="text" name="username" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Full Name</label>
                                    <input type="text" name="full_name" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>Password</label>
                                    <input type="password" name="password" class="form-control" minlength="6" required>
                                </div>
                                <div class="mb-3">
                                    <label>Role</label>
                                    <select name="role" class="form-select">
                                        <option value="Admin">Admin (full access)</option>
                                        <option value="Viewer">Viewer (read-only)</option>
                                    </select>
                                </div>
                                <button type="submit" name="add_user" class="btn btn-success w-100">Create User</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">Existing Users</div>
                        <div class="card-body">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Full Name</th>
                                        <th>Role</th>
                                        <th width="220">Reset Password</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($u = mysqli_fetch_assoc($users_result)) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($u['username']); ?></td>
                                            <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                                            <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($u['role']); ?></span></td>
                                            <td>
                                                <form method="post" class="d-flex gap-2">
                                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                                    <input type="password" name="new_password" class="form-control form-control-sm" placeholder="New password" minlength="6" required>
                                                    <button type="submit" name="reset_password" class="btn btn-warning btn-sm">Reset</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

</body>

</html>