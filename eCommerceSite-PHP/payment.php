<?php
require_once('header.php');

if (!isset($_SESSION['customer'])) {
    header('location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];
    $customer_id = $_SESSION['customer']['cust_id'];
    
    // Here you would integrate with your payment gateway
    // This is a placeholder for payment integration
    $payment_status = 'Success'; // This should be dynamically set based on actual payment status from the gateway
    
    if ($payment_status == 'Success') {
        // Insert order into the database
        $statement = $pdo->prepare("INSERT INTO tbl_order (
                customer_id,
                amount,
                payment_status,
                order_status,
                payment_date
            ) VALUES (?, ?, ?, ?, NOW())");
        $statement->execute(array(
            $customer_id,
            $amount,
            $payment_status,
            'Pending' // Or whatever your default order status should be
        ));
        
        $order_id = $pdo->lastInsertId();

        foreach ($_SESSION['cart_p_id'] as $key => $value) {
            $p_id = $_SESSION['cart_p_id'][$key];
            $p_name = $_SESSION['cart_p_name'][$key];
            $p_featured_photo = $_SESSION['cart_p_featured_photo'][$key];
            $p_size = $_SESSION['cart_size_name'][$key];
            $p_color = $_SESSION['cart_color_name'][$key];
            $p_qty = $_SESSION['cart_p_qty'][$key];
            $p_price = $_SESSION['cart_p_current_price'][$key];
            
            $statement = $pdo->prepare("INSERT INTO tbl_order_item (
                    order_id,
                    product_id,
                    product_name,
                    product_featured_photo,
                    product_size,
                    product_color,
                    product_qty,
                    product_price
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $statement->execute(array(
                $order_id,
                $p_id,
                $p_name,
                $p_featured_photo,
                $p_size,
                $p_color,
                $p_qty,
                $p_price
            ));
        }
        
        // Clear the cart
        unset($_SESSION['cart_p_id']);
        unset($_SESSION['cart_size_id']);
        unset($_SESSION['cart_size_name']);
        unset($_SESSION['cart_color_id']);
        unset($_SESSION['cart_color_name']);
        unset($_SESSION['cart_p_qty']);
        unset($_SESSION['cart_p_current_price']);
        unset($_SESSION['cart_p_name']);
        unset($_SESSION['cart_p_featured_photo']);
        
        header('location: payment_success.php');
        exit;
    } else {
        header('location: payment_failure.php');
        exit;
    }
} else {
    header('location: checkout.php');
    exit;
}

require_once('footer.php');
