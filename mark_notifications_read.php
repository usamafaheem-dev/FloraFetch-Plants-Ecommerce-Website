<?php
session_start();
include("includes/db_connect.php");

header('Content-Type: application/json');

if (isset($_SESSION['user_id']) && isset($_POST['action']) && $_POST['action'] == 'mark_read') {
    $uid = (int)$_SESSION['user_id'];
    if ($conn) {
        $update_query = "UPDATE notifications SET is_read = 1 WHERE user_id = $uid AND is_read = 0";
        if (mysqli_query($conn, $update_query)) {
            echo json_encode(['success' => true]);
            exit;
        }
    }
}
echo json_encode(['success' => false]);
?>
