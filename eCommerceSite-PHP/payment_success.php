<?php
require_once('header.php');

// Function to generate a unique UUID
function generate_uuid() {
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

// Check if the required session data is available
if (!isset($_SESSION['cart_p_id']) || !isset($_SESSION['customer'])) {
    header('Location: cart.php');
    exit;
}

// Fetch the customer details
$customer = $_SESSION['customer'];

// Calculate the total amount from the session
$total_amount = 0;
foreach ($_SESSION['cart_p_current_price'] as $key => $value) {
    $total_amount += $value;
}

// Insert the order into the database
try {
    $pdo->beginTransaction();

    $payment_id = generate_uuid();

    // Insert order details
    $statement = $pdo->prepare("INSERT INTO tbl_order (product_id, product_name, size, color, quantity, unit_price, payment_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($_SESSION['cart_p_id'] as $key => $product_id) {
        $size_id = $_SESSION['cart_size_id'][$key];
        $color_id = $_SESSION['cart_color_id'][$key];
        $quantity = $_SESSION['cart_p_qty'][$key];
        $price = $_SESSION['cart_p_current_price'][$key];
        
        // Insert each item
        $statement->execute([$product_id, $_SESSION['cart_p_name'][$key], $size_id, $color_id, $quantity, $price, $payment_id]);
    }

    // Fetch the order ID (if needed, though not used further here)
    $order_id = $pdo->lastInsertId();

    // Insert payment details
    $payment_date = date('Y-m-d H:i:s');
    $statement = $pdo->prepare("INSERT INTO tbl_payment (
        customer_id,
        customer_name,
        customer_email,
        payment_date,
        txnid,
        paid_amount,
        card_number,
        card_cvv,
        card_month,
        card_year,
        bank_transaction_info,
        payment_method,
        payment_status,
        shipping_status,
        payment_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $statement->execute([
        $customer['cust_id'],
        $customer['cust_name'] ?? 'esewa',
        $customer['cust_email'] ?? 'esewa',
        $payment_date,
        'esewa',
        $total_amount,
        'esewa',
        'esewa',
        'esewa',
        'esewa',
        'esewa',
        'eSewa',
        'Completed',
        'Pending',
        $payment_id
    ]);

    $pdo->commit();

    // Clear the cart session
    unset($_SESSION['cart_p_id']);
    unset($_SESSION['cart_size_id']);
    unset($_SESSION['cart_color_id']);
    unset($_SESSION['cart_p_qty']);
    unset($_SESSION['cart_p_current_price']);
    unset($_SESSION['cart_p_name']);
    unset($_SESSION['cart_p_featured_photo']);

} catch (Exception $e) {
    $pdo->rollBack();
    echo "Failed to process the order: " . $e->getMessage();
    exit;
}
?>

<div class="page-banner" style="background-image: url(assets/uploads/banner_success.jpg)">
    <div class="overlay"></div>
    <div class="page-banner-inner">
        <h1>Order Successful</h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3 class="special">Thank You for Your Order!</h3>
                <p>Your order has been successfully placed. </p>
                <p><a href="index.php" class="btn btn-primary">Return to Home</a></p>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>
<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/66826635eaf3bd8d4d16bbb1/1i1mlugii';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();
</script>
<!--End of Tawk.to Script-->
</body>
</html>