<?php
session_start();

if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['plant_id'])) {
    $plant_id = (int)$_POST['plant_id'];
    $action = $_POST['action'];

    if ($action === 'toggle') {
        if (in_array($plant_id, $_SESSION['wishlist'])) {
            // Remove
            $_SESSION['wishlist'] = array_diff($_SESSION['wishlist'], [$plant_id]);
            echo json_encode(['status' => 'success', 'state' => 'removed', 'count' => count($_SESSION['wishlist'])]);
        } else {
            // Add
            $_SESSION['wishlist'][] = $plant_id;
            echo json_encode(['status' => 'success', 'state' => 'added', 'count' => count($_SESSION['wishlist'])]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>
