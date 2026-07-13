<?php
include("includes/head.php");
include("includes/header.php");
include("includes/db_connect.php");

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$success_msg = "";
$error_msg = "";

// Fetch user data
$user = null;
if ($conn) {
    $q = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
    if ($q && mysqli_num_rows($q) > 0) {
        $user = mysqli_fetch_assoc($q);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    $profile_image_path = $user['profile_image'];
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['profile_image']['tmp_name'];
        $name_file = time() . '_' . basename($_FILES['profile_image']['name']);
        $upload_dir = 'assets/images/profiles/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        if (move_uploaded_file($tmp_name, $upload_dir . $name_file)) {
            $profile_image_path = 'assets/images/profiles/' . $name_file;
        }
    }

    $update = "UPDATE users SET name = '$name', phone = '$phone', address = '$address', profile_image = '$profile_image_path' WHERE id = $user_id";
    if (mysqli_query($conn, $update)) {
        $_SESSION['user_name'] = $name;
        $_SESSION['profile_image'] = $profile_image_path;
        $success_msg = "Profile updated successfully!";
        // Refresh user data
        $q = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
        $user = mysqli_fetch_assoc($q);
    } else {
        $error_msg = "Error updating profile.";
    }
}

// Handle Password Change
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_pw = $_POST['current_password'];
    $new_pw = $_POST['new_password'];
    $confirm_pw = $_POST['confirm_password'];

    // Support both md5 legacy and modern hashes
    $is_valid = false;
    if (password_verify($current_pw, $user['password'])) {
        $is_valid = true;
    } elseif ($user['password'] === md5($current_pw)) {
        $is_valid = true;
    }

    if (!$is_valid) {
        $error_msg = "Current password is incorrect.";
    } elseif (strlen($new_pw) < 6) {
        $error_msg = "New password must be at least 6 characters.";
    } elseif ($new_pw !== $confirm_pw) {
        $error_msg = "New passwords do not match.";
    } else {
        $hashed = password_hash($new_pw, PASSWORD_DEFAULT);
        if (mysqli_query($conn, "UPDATE users SET password = '$hashed' WHERE id = $user_id")) {
            $success_msg = "Password changed successfully!";
            // Refresh user
            $q = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
            $user = mysqli_fetch_assoc($q);
        } else {
            $error_msg = "Error changing password.";
        }
    }
}

// Handle Address Add
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_address'])) {
    $label = mysqli_real_escape_string($conn, $_POST['addr_label']);
    $fname = mysqli_real_escape_string($conn, $_POST['addr_fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['addr_lname']);
    $email = mysqli_real_escape_string($conn, $_POST['addr_email']);
    $phone = mysqli_real_escape_string($conn, $_POST['addr_phone']);
    $addr = mysqli_real_escape_string($conn, $_POST['addr_address']);
    $city = mysqli_real_escape_string($conn, $_POST['addr_city']);
    $zip = mysqli_real_escape_string($conn, $_POST['addr_zip']);

    $ins = "INSERT INTO user_addresses (user_id, label, first_name, last_name, email, phone, address, city, zip) 
            VALUES ($user_id, '$label', '$fname', '$lname', '$email', '$phone', '$addr', '$city', '$zip')";
    if (mysqli_query($conn, $ins)) {
        $success_msg = "Address added successfully!";
    } else {
        $error_msg = "Error adding address.";
    }
}

// Handle Address Delete
if (isset($_GET['delete_addr'])) {
    $addr_id = (int)$_GET['delete_addr'];
    mysqli_query($conn, "DELETE FROM user_addresses WHERE id = $addr_id AND user_id = $user_id");
    header("Location: profile.php");
    exit;
}

// Handle Address Edit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_address'])) {
    $addr_id = (int)$_POST['addr_id'];
    $label = mysqli_real_escape_string($conn, $_POST['addr_label']);
    $fname = mysqli_real_escape_string($conn, $_POST['addr_fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['addr_lname']);
    $email = mysqli_real_escape_string($conn, $_POST['addr_email']);
    $phone = mysqli_real_escape_string($conn, $_POST['addr_phone']);
    $addr = mysqli_real_escape_string($conn, $_POST['addr_address']);
    $city = mysqli_real_escape_string($conn, $_POST['addr_city']);
    $zip = mysqli_real_escape_string($conn, $_POST['addr_zip']);

    $upd = "UPDATE user_addresses SET label='$label', first_name='$fname', last_name='$lname', email='$email', phone='$phone', address='$addr', city='$city', zip='$zip' WHERE id=$addr_id AND user_id=$user_id";
    if (mysqli_query($conn, $upd)) {
        $success_msg = "Address updated successfully!";
    } else {
        $error_msg = "Error updating address.";
    }
}

// Fetch saved addresses
$addresses = [];
if ($conn) {
    $addr_q = mysqli_query($conn, "SELECT * FROM user_addresses WHERE user_id = $user_id ORDER BY created_at DESC");
    if ($addr_q) {
        while ($a = mysqli_fetch_assoc($addr_q)) {
            $addresses[] = $a;
        }
    }
}

// Fetch order history (Plant History)
$orders = [];
if ($conn) {
    $ord_q = mysqli_query($conn, "SELECT o.*, GROUP_CONCAT(p.name SEPARATOR ', ') as plant_names 
                                   FROM orders o 
                                   JOIN order_items oi ON o.id = oi.order_id 
                                   JOIN plants p ON oi.plant_id = p.id 
                                   WHERE o.user_id = $user_id 
                                   GROUP BY o.id 
                                   ORDER BY o.order_date DESC LIMIT 10");
    if ($ord_q) {
        while ($o = mysqli_fetch_assoc($ord_q)) {
            $orders[] = $o;
        }
    }
}
?>

<div class="page-header">
    <img src="assets/images/plants/hero_image_transparent_2.png" alt="Decoration" class="hero-plant-left">
    <img src="assets/images/plants/hero_image_transparent_2.png" alt="Decoration" class="hero-plant-right" style="transform: translateY(-50%) scaleX(-1);">
    
    <!-- Animated 3D Butterfly -->
    <div class="butterfly-container" style="top: 25%; right: 15%;">
        <div class="butterfly">
            <div class="wing left"></div>
            <div class="wing right"></div>
            <div class="butterfly-body"></div>
        </div>
    </div>
    
    <!-- Crawling Ladybug -->
    <div class="ladybug" style="top: 75%; left: 10%; z-index: 5;"></div>
    
    <div class="container" style="position: relative; z-index: 2;">
        <h1>My Profile</h1>
        <p>Manage your account details, addresses, and plant history.</p>
    </div>
</div>

<main style="padding: 60px 0 80px; min-height: 50vh;">
    <div class="container">

        <?php if ($success_msg): ?>
            <div class="alert alert-success border-0" style="background: #dcfce7; color: #166534;">
                <i class="fa-solid fa-circle-check me-2"></i><?php echo $success_msg; ?>
            </div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert-danger border-0" style="background: #fee2e2; color: #991b1b;">
                <i class="fa-solid fa-triangle-exclamation me-2"></i><?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- LEFT COLUMN: Profile Info -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4 position-relative">
                            <?php if (!empty($user['profile_image'])): ?>
                                <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Image" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid var(--primary);">
                            <?php else: ?>
                                <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--primary-glow); display: flex; align-items: center; justify-content: center; font-size: 2rem; color: var(--primary); font-weight: 700;">
                                    <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                                </div>
                            <?php endif; ?>
                            <div class="ms-3">
                                <h4 class="mb-0" style="font-family: var(--font-heading); font-weight: 700;"><?php echo htmlspecialchars($user['name']); ?></h4>
                                <span class="badge bg-light text-dark mt-1" style="font-size: 0.85rem; border: 1px solid var(--border);"><i class="fa-solid fa-envelope me-1 text-muted"></i><?php echo $user['email']; ?></span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-dark position-absolute top-0 end-0" data-bs-toggle="modal" data-bs-target="#editProfileModal" style="border-radius: 8px;">
                                <i class="fa-solid fa-pen-to-square me-1"></i>Edit
                            </button>
                        </div>

                        <div class="profile-details mt-4">
                            <div class="mb-3 border-bottom pb-2">
                                <div class="text-muted" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Phone Number</div>
                                <div class="fw-semibold text-dark mt-1"><?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?></div>
                            </div>
                            <div class="mb-2">
                                <div class="text-muted" style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px;">Default Delivery Address</div>
                                <div class="fw-semibold text-dark mt-1"><?php echo htmlspecialchars($user['address'] ?? 'Not provided'); ?></div>
                            </div>
                        </div>

                        <!-- Edit Profile Modal -->
                        <div class="modal fade" id="editProfileModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" style="font-family: var(--font-heading); font-weight: 700;">Edit Profile</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="" enctype="multipart/form-data">
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Full Name</label>
                                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required style="border-radius: 10px; padding: 12px 15px;">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Profile Photo <small class="text-muted">(Optional)</small></label>
                                                <input type="file" name="profile_image" class="form-control" accept="image/*" style="border-radius: 10px; padding: 8px 12px;">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Email <small class="text-muted">(cannot change)</small></label>
                                                <input type="email" class="form-control" value="<?php echo $user['email']; ?>" disabled style="border-radius: 10px; padding: 12px 15px; background: #f8fafc;">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Phone</label>
                                                <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="03001234567" style="border-radius: 10px; padding: 12px 15px;">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">Default Address</label>
                                                <textarea name="address" class="form-control" rows="2" style="border-radius: 10px; padding: 12px 15px;"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" name="update_profile" class="btn btn-flora w-100 py-2" style="border-radius: 10px;">
                                                <i class="fa-solid fa-floppy-disk me-2"></i>Save Changes
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="card border-0 shadow-sm mt-4" style="border-radius: 16px; overflow: hidden;">
                    <div class="card-body p-4">
                        <h5 class="mb-3" style="font-family: var(--font-heading); font-weight: 700;">
                            <i class="fa-solid fa-lock me-2 text-secondary"></i>Change Password
                        </h5>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Current Password</label>
                                <input type="password" name="current_password" class="form-control" required style="border-radius: 10px; padding: 12px 15px;">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">New Password</label>
                                <input type="password" name="new_password" class="form-control" required minlength="6" style="border-radius: 10px; padding: 12px 15px;">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Confirm New Password</label>
                                <input type="password" name="confirm_password" class="form-control" required style="border-radius: 10px; padding: 12px 15px;">
                            </div>
                            <button type="submit" name="change_password" class="btn btn-outline-dark w-100 py-2" style="border-radius: 10px;">
                                <i class="fa-solid fa-key me-2"></i>Update Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: Addresses & Plant History -->
            <div class="col-lg-6">
                <!-- Saved Addresses -->
                <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0" style="font-family: var(--font-heading); font-weight: 700;">
                                <i class="fa-solid fa-location-dot me-2 text-primary"></i>Saved Addresses
                            </h5>
                            <button class="btn btn-sm btn-flora" data-bs-toggle="collapse" data-bs-target="#addAddressForm" style="border-radius: 8px;">
                                <i class="fa-solid fa-plus me-1"></i>Add
                            </button>
                        </div>

                        <!-- Add Address Form (Collapsed) -->
                        <div class="collapse mb-3" id="addAddressForm">
                            <div class="p-3" style="background: #f8fafc; border-radius: 12px; border: 1px solid var(--border);">
                                <form method="POST" action="">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <select name="addr_label" class="form-select form-select-sm" style="border-radius: 8px;">
                                                <option value="Home">🏠 Home</option>
                                                <option value="Office">🏢 Office</option>
                                                <option value="Other">📍 Other</option>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <input type="text" name="addr_fname" class="form-control form-control-sm" placeholder="First Name" required style="border-radius: 8px;">
                                        </div>
                                        <div class="col-6">
                                            <input type="text" name="addr_lname" class="form-control form-control-sm" placeholder="Last Name" style="border-radius: 8px;">
                                        </div>
                                        <div class="col-6">
                                            <input type="email" name="addr_email" class="form-control form-control-sm" placeholder="Email" style="border-radius: 8px;">
                                        </div>
                                        <div class="col-6">
                                            <input type="text" name="addr_phone" class="form-control form-control-sm" placeholder="Phone" style="border-radius: 8px;">
                                        </div>
                                        <div class="col-12">
                                            <textarea name="addr_address" class="form-control form-control-sm" rows="2" placeholder="Full Address" required style="border-radius: 8px;"></textarea>
                                        </div>
                                        <div class="col-6">
                                            <input type="text" name="addr_city" class="form-control form-control-sm" placeholder="City" style="border-radius: 8px;">
                                        </div>
                                        <div class="col-6">
                                            <input type="text" name="addr_zip" class="form-control form-control-sm" placeholder="ZIP Code" style="border-radius: 8px;">
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" name="add_address" class="btn btn-flora btn-sm w-100" style="border-radius: 8px;">Save Address</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <?php if (count($addresses) > 0): ?>
                            <?php foreach ($addresses as $addr): ?>
                                <div class="d-flex justify-content-between align-items-start p-3 mb-2" style="background: #f8fafc; border-radius: 12px; border: 1px solid var(--border);">
                                    <div>
                                        <span class="badge bg-light text-dark mb-1" style="font-size: 0.75rem;"><?php echo $addr['label']; ?></span>
                                        <div class="fw-semibold text-dark"><?php echo htmlspecialchars($addr['first_name'] . ' ' . $addr['last_name']); ?></div>
                                        <div class="text-secondary" style="font-size: 0.88rem;"><?php echo htmlspecialchars($addr['address']); ?></div>
                                        <div class="text-muted" style="font-size: 0.82rem;"><?php echo htmlspecialchars($addr['city'] . ($addr['zip'] ? ', ' . $addr['zip'] : '')); ?></div>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-light text-primary border me-1" data-bs-toggle="modal" data-bs-target="#editAddr<?php echo $addr['id']; ?>" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <a href="?delete_addr=<?php echo $addr['id']; ?>" class="btn btn-sm btn-light text-danger border" onclick="return confirm('Delete this address?');" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </div>

                                <!-- Edit Modal -->
                                <div class="modal fade" id="editAddr<?php echo $addr['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" style="font-family: var(--font-heading); font-weight: 700;">Edit Address</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="">
                                                <div class="modal-body">
                                                    <input type="hidden" name="addr_id" value="<?php echo $addr['id']; ?>">
                                                    <div class="row g-2">
                                                        <div class="col-12">
                                                            <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Label</label>
                                                            <select name="addr_label" class="form-select form-select-sm" style="border-radius: 8px;">
                                                                <option value="Home" <?php echo ($addr['label']=='Home')?'selected':''; ?>>🏠 Home</option>
                                                                <option value="Office" <?php echo ($addr['label']=='Office')?'selected':''; ?>>🏢 Office</option>
                                                                <option value="Other" <?php echo ($addr['label']=='Other')?'selected':''; ?>>📍 Other</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">First Name</label>
                                                            <input type="text" name="addr_fname" class="form-control form-control-sm" value="<?php echo htmlspecialchars($addr['first_name'] ?? ''); ?>" required style="border-radius: 8px;">
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Last Name</label>
                                                            <input type="text" name="addr_lname" class="form-control form-control-sm" value="<?php echo htmlspecialchars($addr['last_name'] ?? ''); ?>" style="border-radius: 8px;">
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Email</label>
                                                            <input type="email" name="addr_email" class="form-control form-control-sm" value="<?php echo htmlspecialchars($addr['email'] ?? ''); ?>" style="border-radius: 8px;">
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Phone</label>
                                                            <input type="text" name="addr_phone" class="form-control form-control-sm" value="<?php echo htmlspecialchars($addr['phone'] ?? ''); ?>" style="border-radius: 8px;">
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">Full Address</label>
                                                            <textarea name="addr_address" class="form-control form-control-sm" rows="2" required style="border-radius: 8px;"><?php echo htmlspecialchars($addr['address'] ?? ''); ?></textarea>
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">City</label>
                                                            <input type="text" name="addr_city" class="form-control form-control-sm" value="<?php echo htmlspecialchars($addr['city'] ?? ''); ?>" style="border-radius: 8px;">
                                                        </div>
                                                        <div class="col-6">
                                                            <label class="form-label" style="font-size: 0.85rem; font-weight: 600;">ZIP Code</label>
                                                            <input type="text" name="addr_zip" class="form-control form-control-sm" value="<?php echo htmlspecialchars($addr['zip'] ?? ''); ?>" style="border-radius: 8px;">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" name="edit_address" class="btn btn-flora btn-sm w-100" style="border-radius: 8px;">Update Address</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-4 text-secondary">
                                <i class="fa-solid fa-map-location-dot fa-2x mb-2" style="color: var(--border-hover);"></i>
                                <p class="mb-0">No saved addresses yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Plant History -->
                <div class="card border-0 shadow-sm mt-4" style="border-radius: 16px; overflow: hidden;">
                    <div class="card-body p-4">
                        <h5 class="mb-3" style="font-family: var(--font-heading); font-weight: 700;">
                            <i class="fa-solid fa-clock-rotate-left me-2 text-success"></i>Plant History
                        </h5>
                        <?php if (count($orders) > 0): ?>
                            <?php foreach ($orders as $ord): ?>
                                <div class="d-flex justify-content-between align-items-center p-3 mb-2" style="background: #f8fafc; border-radius: 12px; border: 1px solid var(--border);">
                                    <div>
                                        <div class="fw-semibold text-dark" style="font-size: 0.9rem;"><?php echo htmlspecialchars($ord['plant_names']); ?></div>
                                        <div class="text-muted" style="font-size: 0.8rem;">
                                            <?php echo date('d M Y', strtotime($ord['order_date'])); ?> — Rs <?php echo number_format($ord['total_amount']); ?>
                                        </div>
                                    </div>
                                    <?php
                                        $st = $ord['status'];
                                        $bc = 'bg-secondary';
                                        if($st == 'Pending') $bc = 'bg-warning text-dark';
                                        elseif($st == 'Quality Check') $bc = 'bg-info text-dark';
                                        elseif($st == 'In Transit') $bc = 'bg-primary';
                                        elseif($st == 'Delivered') $bc = 'bg-success';
                                        elseif($st == 'Cancelled') $bc = 'bg-danger';
                                    ?>
                                    <span class="badge <?php echo $bc; ?>" style="font-size: 0.75rem;"><?php echo $st; ?></span>
                                </div>
                            <?php endforeach; ?>
                            <a href="my_orders.php" class="btn btn-light w-100 mt-2 border" style="border-radius: 10px; font-weight: 500;">View All Orders →</a>
                        <?php else: ?>
                            <div class="text-center py-4 text-secondary">
                                <i class="fa-solid fa-seedling fa-2x mb-2" style="color: var(--border-hover);"></i>
                                <p class="mb-0">No purchase history yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include("includes/footer.php"); ?>
