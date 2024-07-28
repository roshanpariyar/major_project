<?php
require_once('header.php');
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Fetch banner image for registration page
$statement = $pdo->prepare("SELECT * FROM tbl_settings WHERE id = 1");
$statement->execute();
$result = $statement->fetch(PDO::FETCH_ASSOC);
$banner_registration = $result['banner_registration'];

// Initialize variables for error and success messages
$error_message = '';
$success_message = '';

// Process form submission
if (isset($_POST['form1'])) {
    // Validate form fields
    $valid = true;
    $required_fields = ['cust_name', 'cust_email', 'cust_phone', 'cust_address', 'cust_country', 'cust_city', 'cust_state', 'cust_zip', 'cust_password', 'cust_re_password'];

    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $valid = false;
            $error_message .= "Field $field is required.<br>";
        }
    }

    if (!filter_var($_POST['cust_email'], FILTER_VALIDATE_EMAIL)) {
        $valid = false;
        $error_message .= "Invalid email format.<br>";
    }

    // Check if email already exists
    $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_email = ?");
    $statement->execute([$_POST['cust_email']]);
    if ($statement->rowCount() > 0) {
        $valid = false;
        $error_message .= "Email already exists.<br>";
    }

    // Ensure passwords match
    if ($_POST['cust_password'] != $_POST['cust_re_password']) {
        $valid = false;
        $error_message .= "Passwords do not match.<br>";
    }

    if ($valid) {
        // Generate token and timestamp
        $token = md5(time());
        $cust_datetime = date('Y-m-d H:i:s');
        $cust_timestamp = time();

        // Insert customer data into database
        $statement = $pdo->prepare("INSERT INTO tbl_customer (
                                        cust_name, cust_cname, cust_email, cust_phone, cust_country, 
                                        cust_address, cust_city, cust_state, cust_zip, 
                                        cust_password, cust_token, cust_datetime, cust_timestamp, cust_status
                                    ) VALUES (
                                        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                                    )");

        $params = [
            strip_tags($_POST['cust_name']),
            strip_tags($_POST['cust_cname']),
            $_POST['cust_email'],
            $_POST['cust_phone'],
            strip_tags($_POST['cust_country']),
            strip_tags($_POST['cust_address']),
            strip_tags($_POST['cust_city']),
            strip_tags($_POST['cust_state']),
            strip_tags($_POST['cust_zip']),
            md5($_POST['cust_password']),
            $token,
            $cust_datetime,
            $cust_timestamp,
            0
        ];

        $statement->execute($params);

        // Send email for account verification using PHPMailer
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'jerseyhouse1234@gmail.com'; // Replace with your actual SMTP username
            $mail->Password   = 'usce wrhm wqde hkvx';//Replace with your actual SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom('jerseyhouse1234@gmail.com', 'Jersey House');
                                                                            
            $mail->addAddress($_POST['cust_email']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Account Verification';
            $base_url = 'http://localhost:8000/';
            $verify_link = $base_url . 'verify.php?email=' . urlencode($_POST['cust_email']) . '&token=' . $token;
            $mail->Body    = 'Click the following link to verify your account:<br><br><a href="' . $verify_link . '">' . $verify_link . '</a>';

            $mail->send();
            $success_message = 'Registration successful! Please check your email for verification link.';
            $_POST = []; // Clear form data after successful registration
        } catch (Exception $e) {
           $error_message = 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
        }
    }
}

// Display registration form and messages
?>
<?php require_once('header.php'); ?>

<div class="page-banner" style="background-color:#444;background-image: url(assets/uploads/<?php echo $banner_registration; ?>);">
    <div class="inner">
        <h1>Register</h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="user-content">
                    <form action="" method="post">
                        <?php $csrf->echoInputField(); ?>
                        <div class="row">
                            <div class="col-md-2"></div>
                            <div class="col-md-8">
                                <?php if ($error_message != '') : ?>
                                    <div class='error' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'><?php echo $error_message; ?></div>
                                <?php endif; ?>
                                <?php if ($success_message != '') : ?>
                                    <div class='success' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'><?php echo $success_message; ?></div>
                                <?php endif; ?>

                                <div class="col-md-6 form-group">
                                    <label for="">Name *</label>
                                    <input type="text" class="form-control" name="cust_name" value="<?php echo isset($_POST['cust_name']) ? $_POST['cust_name'] : ''; ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="">Company Name</label>
                                    <input type="text" class="form-control" name="cust_cname" value="<?php echo isset($_POST['cust_cname']) ? $_POST['cust_cname'] : ''; ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="">Email *</label>
                                    <input type="email" class="form-control" name="cust_email" value="<?php echo isset($_POST['cust_email']) ? $_POST['cust_email'] : ''; ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="">Phone *</label>
                                    <input type="text" class="form-control" name="cust_phone" value="<?php echo isset($_POST['cust_phone']) ? $_POST['cust_phone'] : ''; ?>">
                                </div>
                                <div class="col-md-12 form-group">
                                    <label for="">Address *</label>
                                    <textarea name="cust_address" class="form-control" cols="30" rows="10" style="height:70px;"><?php echo isset($_POST['cust_address']) ? $_POST['cust_address'] : ''; ?></textarea>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="">District *</label>
                                    <select name="cust_country" class="form-control select2">
                                        <option value="">Select district</option>
                                        <?php
                                        $statement = $pdo->prepare("SELECT * FROM tbl_state ORDER BY state_name ASC");
                                        $statement->execute();
                                        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($result as $row) {
                                            echo "<option value='" . $row['state_id'] . "'>" . $row['state_name'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="">City *</label>
                                    <input type="text" class="form-control" name="cust_city" value="<?php echo isset($_POST['cust_city']) ? $_POST['cust_city'] : ''; ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="">State *</label>
                                    <input type="text" class="form-control" name="cust_state" value="<?php echo isset($_POST['cust_state']) ? $_POST['cust_state'] : ''; ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="">Zip Code *</label>
                                    <input type="text" class="form-control" name="cust_zip" value="<?php echo isset($_POST['cust_zip']) ? $_POST['cust_zip'] : ''; ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="">Password *</label>
                                    <input type="password" class="form-control" name="cust_password">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="">Confirm Password *</label>
                                    <input type="password" class="form-control" name="cust_re_password">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for=""></label>
                                    <input type="submit" class="btn btn-danger" value="Register" name="form1">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>
