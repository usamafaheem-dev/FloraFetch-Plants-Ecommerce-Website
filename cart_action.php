<?php
session_start();
include("includes/db_connect.php");

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $plant_id = isset($_POST['plant_id']) ? (int)$_POST['plant_id'] : 0;
    
    if ($action == 'add' && $plant_id > 0) {
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        
        // Check if plant already in cart
        if (isset($_SESSION['cart'][$plant_id])) {
            $_SESSION['cart'][$plant_id] += $quantity;
        } else {
            $_SESSION['cart'][$plant_id] = $quantity;
        }
        
        if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
            $total_count = array_sum($_SESSION['cart']);
            echo json_encode(['success' => true, 'cart_count' => $total_count]);
            exit;
        }
        
        // Redirect back
        $referer = isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'shop.php';
        header("Location: " . $referer);
        exit;
    }
    
    if ($action == 'update' && $plant_id > 0) {
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        if ($quantity > 0) {
            $_SESSION['cart'][$plant_id] = $quantity;
        } else {
            unset($_SESSION['cart'][$plant_id]);
        }
        header("Location: cart.php");
        exit;
    }
    
    if ($action == 'remove' && $plant_id > 0) {
        if (isset($_SESSION['cart'][$plant_id])) {
            unset($_SESSION['cart'][$plant_id]);
        }
        header("Location: cart.php");
        exit;
    }
}

// Fallback redirect
header("Location: shop.php");
exit;
?>
