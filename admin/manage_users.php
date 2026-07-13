<?php
include("../includes/db_connect.php");
include("includes/header.php");

$message = "";

// Handle Delete User
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($conn) {
        // Optional: you can prevent deleting other admins
        $del_query = "DELETE FROM users WHERE id = $id AND role != 'admin'";
        if (mysqli_query($conn, $del_query)) {
            $message = "<div class='alert alert-success border-0 bg-success text-white'><i class='fa-solid fa-circle-check me-2'></i>Customer deleted successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger border-0 bg-danger text-white'><i class='fa-solid fa-triangle-exclamation me-2'></i>Error deleting customer. Maybe they are an admin or have related orders.</div>";
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="admin-page-title mb-0">Manage Customers</h2>
</div>

<?php echo $message; ?>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined On</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($conn) {
                    $query = "SELECT * FROM users ORDER BY created_at DESC";
                    $result = mysqli_query($conn, $query);
                    
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $role_badge = $row['role'] == 'admin' ? '<span class="badge bg-primary px-2 py-1">Admin</span>' : '<span class="badge bg-secondary px-2 py-1">Customer</span>';
                            
                            echo "<tr>";
                            echo "<td style='font-weight: 600;'>#{$row['id']}</td>";
                            echo "<td style='font-weight: 600; color: var(--text-primary);'>{$row['name']}</td>";
                            echo "<td>{$row['email']}</td>";
                            echo "<td>{$role_badge}</td>";
                            echo "<td>" . date('d M Y', strtotime($row['created_at'])) . "</td>";
                            echo "<td class='text-end'>";
                            if ($row['role'] !== 'admin') {
                                echo "<a href='?delete={$row['id']}' class='btn btn-sm btn-light text-danger border' onclick=\"return confirm('Are you sure you want to delete this customer?');\"><i class='fa-solid fa-trash'></i></a>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center py-4 text-secondary'>No users found.</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center py-4 text-warning'>Database not connected.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("includes/footer.php"); ?>
