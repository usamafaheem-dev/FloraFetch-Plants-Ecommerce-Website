<?php
include("../includes/db_connect.php");
include("includes/header.php");

$message = "";

// Handle Delete Review
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($conn) {
        $del_query = "DELETE FROM reviews WHERE id = $id";
        if (mysqli_query($conn, $del_query)) {
            $message = "<div class='alert alert-success border-0 bg-success text-white'><i class='fa-solid fa-circle-check me-2'></i>Review deleted successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger border-0 bg-danger text-white'><i class='fa-solid fa-triangle-exclamation me-2'></i>Error deleting review.</div>";
        }
    }
}

// Handle Approve Review
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    if ($conn) {
        // Get user_id and plant name for notification
        $user_id = 0;
        $plant_name = "";
        $u_q = mysqli_query($conn, "SELECT r.user_id, p.name FROM reviews r JOIN plants p ON r.plant_id = p.id WHERE r.id = $id");
        if ($u_q && mysqli_num_rows($u_q) > 0) {
            $u_row = mysqli_fetch_assoc($u_q);
            $user_id = $u_row['user_id'];
            $plant_name = $u_row['name'];
        }

        $upd_query = "UPDATE reviews SET is_approved = 1 WHERE id = $id";
        if (mysqli_query($conn, $upd_query)) {
            $message = "<div class='alert alert-success border-0 bg-success text-white'><i class='fa-solid fa-circle-check me-2'></i>Review approved successfully!</div>";
            
            // Insert Notification
            if ($user_id > 0) {
                $notif_title = "Review Approved";
                $notif_msg = "Your review for " . mysqli_real_escape_string($conn, $plant_name) . " has been approved and is now live!";
                $n_q = "INSERT INTO notifications (user_id, title, message) VALUES ($user_id, '$notif_title', '$notif_msg')";
                mysqli_query($conn, $n_q);
            }
        } else {
            $message = "<div class='alert alert-danger border-0 bg-danger text-white'><i class='fa-solid fa-triangle-exclamation me-2'></i>Error approving review.</div>";
        }
    }
}

// Handle Disapprove Review
if (isset($_GET['disapprove'])) {
    $id = (int)$_GET['disapprove'];
    if ($conn) {
        $upd_query = "UPDATE reviews SET is_approved = 0 WHERE id = $id";
        if (mysqli_query($conn, $upd_query)) {
            $message = "<div class='alert alert-success border-0 bg-success text-white'><i class='fa-solid fa-circle-check me-2'></i>Review disapproved successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger border-0 bg-danger text-white'><i class='fa-solid fa-triangle-exclamation me-2'></i>Error disapproving review.</div>";
        }
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="admin-page-title mb-0">Manage Reviews</h2>
</div>

<?php echo $message; ?>

<div class="admin-card">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Plant</th>
                    <th>User</th>
                    <th>Rating</th>
                    <th>Comment</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($conn) {
                    $query = "SELECT r.*, p.name as plant_name, u.name as user_name 
                              FROM reviews r 
                              JOIN plants p ON r.plant_id = p.id 
                              JOIN users u ON r.user_id = u.id 
                              ORDER BY r.created_at DESC";
                    $result = mysqli_query($conn, $query);
                    
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $status_badge = $row['is_approved'] ? "<span class='badge bg-success'>Approved</span>" : "<span class='badge bg-warning text-dark'>Pending</span>";
                            $rating_stars = str_repeat("<i class='fa-solid fa-star text-warning'></i>", $row['rating']) . str_repeat("<i class='fa-regular fa-star text-warning'></i>", 5 - $row['rating']);
                            $img_html = "";
                            if (!empty($row['review_image'])) {
                                $img_html = "<div class='mt-1'><a href='../" . htmlspecialchars($row['review_image']) . "' target='_blank'><img src='../" . htmlspecialchars($row['review_image']) . "' alt='review photo' style='max-width: 80px; border-radius: 6px;'></a></div>";
                            }
                            echo "<tr>
                                    <td><strong>" . htmlspecialchars($row['plant_name']) . "</strong></td>
                                    <td>" . htmlspecialchars($row['user_name']) . "</td>
                                    <td>$rating_stars</td>
                                    <td>
                                        <div>" . htmlspecialchars($row['comment']) . "</div>
                                        $img_html
                                    </td>
                                    <td>$status_badge</td>
                                    <td class='text-end'>";
                                    if ($row['is_approved']) {
                                        echo "<a href='?disapprove={$row['id']}' class='btn btn-sm btn-light border text-warning me-1' title='Disapprove'><i class='fa-solid fa-ban'></i></a>";
                                    } else {
                                        echo "<a href='?approve={$row['id']}' class='btn btn-sm btn-light border text-success me-1' title='Approve'><i class='fa-solid fa-check'></i></a>";
                                    }
                            echo "      <a href='?delete={$row['id']}' class='btn btn-sm btn-light text-danger border' onclick=\"return confirm('Are you sure you want to delete this review?');\"><i class='fa-solid fa-trash'></i></a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center py-4 text-secondary'>No reviews found.</td></tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include("includes/footer.php"); ?>
