<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("includes/db_connect.php");

// If already logged in, redirect
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/index.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

$login_error = "";
$register_error = "";
$register_success = "";

// Check if form was submitted
$is_signup_mode = false; // Default to login mode

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'register') {
        $is_signup_mode = true; // Keep it on signup mode if there's an error
        
        $name = sanitize_input($conn, $_POST['name']);
        $email = sanitize_input($conn, $_POST['email']);
        $phone = sanitize_input($conn, $_POST['phone']);
        $password = $_POST['password'];

        if (empty($name) || empty($email) || empty($phone) || empty($password)) {
            $register_error = "Name, Email, Phone, and Password are required.";
        } elseif (strlen($password) < 6) {
            $register_error = "Password must be at least 6 characters long.";
        } else {
            if ($conn) {
                // Check if email or phone already exists
                $check_query = "SELECT id FROM users WHERE email = '$email' OR phone = '$phone'";
                $check_result = mysqli_query($conn, $check_query);

                if ($check_result && mysqli_num_rows($check_result) > 0) {
                    $register_error = "Email address or Phone number is already registered.";
                } else {
                    $profile_image_path = "";
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
                    $profile_img_val = !empty($profile_image_path) ? "'$profile_image_path'" : "NULL";

                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $insert_query = "INSERT INTO users (name, email, phone, password, role, profile_image) 
                                     VALUES ('$name', '$email', '$phone', '$hashed_password', 'customer', $profile_img_val)";
                    
                    if (mysqli_query($conn, $insert_query)) {
                        $register_success = "Registration successful! You can now login.";
                        $is_signup_mode = false; // Switch back to login to let them login
                    } else {
                        $register_error = "Registration failed. Try again.";
                    }
                }
            } else {
                $register_error = "Database connection failed.";
            }
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'login') {
        $login_identifier = sanitize_input($conn, $_POST['login_identifier']);
        $password = $_POST['password'];

        if (empty($login_identifier) || empty($password)) {
            $login_error = "Email/Phone and Password are required.";
        } else {
            if ($conn) {
                $query = "SELECT * FROM users WHERE email = '$login_identifier' OR phone = '$login_identifier'";
                $result = mysqli_query($conn, $query);

                if ($result && mysqli_num_rows($result) == 1) {
                    $user = mysqli_fetch_assoc($result);
                    
                    // Verify password (supports both password_hash and legacy md5)
                    $is_valid = false;
                    if (password_verify($password, $user['password'])) {
                        $is_valid = true;
                    } elseif ($user['password'] === md5($password)) {
                        $is_valid = true;
                        // Seamlessly upgrade MD5 to modern password_hash
                        $new_hash = password_hash($password, PASSWORD_DEFAULT);
                        mysqli_query($conn, "UPDATE users SET password = '$new_hash' WHERE id = " . $user['id']);
                    }
                    
                    if ($is_valid) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_name'] = $user['name'];
                        $_SESSION['role'] = $user['role'];
                        $_SESSION['profile_image'] = $user['profile_image'];

                        if ($user['role'] == 'admin') {
                            header("Location: admin/index.php");
                        } else {
                            header("Location: index.php");
                        }
                        exit;
                    } else {
                        $login_error = "Invalid Password.";
                    }
                } else {
                    $login_error = "No user found with this Email or Phone.";
                }
            } else {
                $login_error = "Database connection failed.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication - FloraFetch</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Custom Styles for Auth -->
    <style>
        :root {
            --bg-dark: #0a0f12;
            --primary: #2ecc71;
            --primary-dark: #27ae60;
            --text-main: #f5f6fa;
            --text-muted: #a4b0be;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-dark);
            color: var(--text-main);
            overflow: hidden; /* Hide scrollbars for the sliding effect */
        }
        
        .back-btn {
            position: absolute;
            top: 30px;
            left: 30px;
            z-index: 100;
            color: var(--text-main);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.1);
            padding: 10px 20px;
            border-radius: 30px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }
        .back-btn:hover {
            background: var(--primary);
            color: #fff;
        }

        .auth-wrapper {
            position: relative;
            width: 100vw;
            height: 100vh;
            display: flex;
        }

        /* --- SIDES --- */
        .image-side {
            position: absolute;
            top: 0;
            left: 0;
            width: 50%;
            height: 100%;
            background-image: url('assets/images/login-bg.jpg');
            background-size: cover;
            background-position: center;
            transition: all 0.8s cubic-bezier(0.77, 0, 0.175, 1);
            z-index: 10;
        }
        .image-side::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(to right, rgba(10,15,18,0.1) 0%, rgba(10,15,18,0.5) 50%, rgba(10,15,18,1) 100%);
        }

        .form-side {
            position: absolute;
            top: 0;
            left: 50%;
            width: 50%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.8s cubic-bezier(0.77, 0, 0.175, 1);
            z-index: 5;
            background: var(--bg-dark);
        }

        /* --- SIGN UP MODE (Toggled) --- */
        .auth-wrapper.sign-up-mode .image-side {
            left: 50%;
            background-image: url('assets/images/signup-bg.jpg');
        }
        .auth-wrapper.sign-up-mode .image-side::after {
            background: linear-gradient(to left, rgba(10,15,18,0.1) 0%, rgba(10,15,18,0.5) 50%, rgba(10,15,18,1) 100%);
        }
        .auth-wrapper.sign-up-mode .form-side {
            left: 0;
        }

        /* --- FORMS --- */
        .form-container {
            position: absolute;
            width: 100%;
            max-width: 450px;
            padding: 40px;
            transition: all 0.6s ease-in-out;
        }
        
        /* Initial States */
        .login-form-container {
            opacity: 1;
            transform: translateY(0);
            pointer-events: all;
        }
        .signup-form-container {
            opacity: 0;
            transform: translateY(30px);
            pointer-events: none;
        }

        /* Toggled States */
        .auth-wrapper.sign-up-mode .login-form-container {
            opacity: 0;
            transform: translateY(-30px);
            pointer-events: none;
        }
        .auth-wrapper.sign-up-mode .signup-form-container {
            opacity: 1;
            transform: translateY(0);
            pointer-events: all;
        }

        h2 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #fff;
        }
        p.subtitle {
            color: var(--text-muted);
            margin-bottom: 30px;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }
        .input-group input {
            width: 100%;
            padding: 15px 15px 15px 45px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            color: #fff;
            font-family: 'Outfit', sans-serif;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s, background 0.3s;
        }
        .input-group input:focus {
            border-color: var(--primary);
            background: rgba(255,255,255,0.08);
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            font-family: 'Outfit', sans-serif;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 10px;
        }
        .btn-submit:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(46, 204, 113, 0.2);
        }

        .toggle-text {
            text-align: center;
            margin-top: 25px;
            color: var(--text-muted);
        }
        .toggle-text span {
            color: var(--primary);
            font-weight: 600;
            cursor: pointer;
            margin-left: 5px;
        }
        .toggle-text span:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-error {
            background: rgba(231, 76, 60, 0.1);
            color: #ff6b6b;
            border: 1px solid rgba(231, 76, 60, 0.2);
        }
        .alert-success {
            background: rgba(46, 204, 113, 0.1);
            color: #2ecc71;
            border: 1px solid rgba(46, 204, 113, 0.2);
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            body { overflow-y: auto; overflow-x: hidden; }
            .auth-wrapper { flex-direction: column; height: auto; min-height: 100vh; overflow: visible; position: relative; }
            
            /* Make image side the full fixed background */
            .image-side { position: fixed !important; top: 0; left: 0 !important; width: 100%; height: 100vh; z-index: 1; }
            .image-side > div { display: none !important; }
            
            /* Keep back button accessible */
            .back-btn { top: 15px; left: 15px; padding: 8px 15px; font-size: 0.85rem; z-index: 100; position: fixed; }
            
            /* Form container acts as overlay */
            .form-side { position: relative !important; width: 100%; height: auto; min-height: 100vh; left: 0 !important; padding: 80px 20px 40px 20px; z-index: 10; display: flex; align-items: center; justify-content: center; background: transparent; }
            
            /* Glassmorphism card */
            .form-container { 
                position: relative; 
                transform: none !important; 
                width: 100%; 
                max-width: 400px;
                background: rgba(10, 25, 15, 0.7) !important;
                backdrop-filter: blur(15px);
                -webkit-backdrop-filter: blur(15px);
                border: 1px solid rgba(255, 255, 255, 0.15);
                border-radius: 24px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
                padding: 30px 20px;
                margin: auto;
            }
            
            /* Hide the other form on mobile */
            .signup-form-container { display: none; }
            .auth-wrapper.sign-up-mode .login-form-container { display: none; }
            .auth-wrapper.sign-up-mode .signup-form-container { display: block; opacity: 1; }
            
            /* Adjust heading size */
            .form-container h2 { font-size: 1.8rem; margin-bottom: 8px; }
            .form-container p.subtitle { font-size: 0.9rem; margin-bottom: 20px; }
        }
    </style>
</head>
<body>

    <a href="index.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Back to Home</a>

    <!-- Add 'sign-up-mode' class to wrapper if signup failed to keep it open -->
    <div class="auth-wrapper <?php echo $is_signup_mode ? 'sign-up-mode' : ''; ?>" id="authWrapper">
        
        <div class="image-side">
            <!-- Content over image if needed -->
            <div style="position: relative; z-index: 20; height: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; padding: 50px; color: #fff;">
                <h1 style="font-family: 'Playfair Display', serif; font-size: 3rem; margin-bottom: 15px;">
                    <i class="fa-solid fa-leaf" style="color: var(--primary);"></i> FloraFetch
                </h1>
                <p style="font-size: 1.2rem; opacity: 0.8; max-width: 400px; line-height: 1.6;">
                    Discover the best plants for your space. Join our community of plant lovers today.
                </p>
            </div>
        </div>

        <div class="form-side">
            
            <!-- Login Form -->
            <div class="form-container login-form-container">
                <h2>Welcome Back!</h2>
                <p class="subtitle">Login to access your FloraFetch account.</p>

                <?php if(!empty($login_error)): ?>
                    <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $login_error; ?></div>
                <?php endif; ?>
                <?php if(!empty($register_success)): ?>
                    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?php echo $register_success; ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="action" value="login">
                    <div class="input-group">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="text" name="login_identifier" placeholder="Email Address / Phone Number" required>
                    </div>
                    <div class="input-group">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn-submit">Login</button>
                </form>
                
                <p class="toggle-text">Don't have an account? <span onclick="toggleAuth()">Sign up</span></p>
            </div>

            <!-- Sign Up Form -->
            <div class="form-container signup-form-container">
                <h2>Create Account</h2>
                <p class="subtitle">Start your plant parenting journey.</p>

                <?php if(!empty($register_error)): ?>
                    <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $register_error; ?></div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="register">
                    <div class="input-group">
                        <i class="fa-solid fa-user"></i>
                        <input type="text" name="name" placeholder="Full Name" required>
                    </div>
                    <div class="input-group">
                        <i class="fa-solid fa-envelope"></i>
                        <input type="email" name="email" placeholder="Email Address" required>
                    </div>
                    <div class="input-group">
                        <i class="fa-solid fa-phone"></i>
                        <input type="text" name="phone" placeholder="Phone Number" required>
                    </div>
                    <div class="input-group">
                        <i class="fa-solid fa-image"></i>
                        <input type="file" name="profile_image" accept="image/*" style="padding-top: 10px; color: var(--text-muted);" title="Choose Profile Image">
                    </div>
                    <div class="input-group">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" placeholder="Password (Min 6 chars)" required>
                    </div>
                    <button type="submit" class="btn-submit">Sign Up</button>
                </form>
                
                <p class="toggle-text">Already have an account? <span onclick="toggleAuth()">Login</span></p>
            </div>

        </div>
    </div>

    <script>
        function toggleAuth() {
            const wrapper = document.getElementById('authWrapper');
            wrapper.classList.toggle('sign-up-mode');
        }
    </script>
</body>
</html>
