<?php require_once('header.php'); 
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error_message = '';
$success_message = '';

function generateRandomPassword($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomPassword = '';
    for ($i = 0; $i < $length; $i++) {
        $randomPassword .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomPassword;
}

if(isset($_POST['form1'])) {
    $cust_email = strip_tags($_POST['cust_email']);
    
    // Check if the email exists in the database
    $statement = $pdo->prepare("SELECT * FROM tbl_customer WHERE cust_email=?");
    $statement->execute(array($cust_email));
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    $tot = $statement->rowCount();

    if($tot == 0) {
        $error_message .= 'Email not found.<br>';
    } else {
        $new_password = generateRandomPassword();
        $new_password_hashed = md5($new_password);
        
        // Update the new password in the database
        $statement = $pdo->prepare("UPDATE tbl_customer SET cust_password=? WHERE cust_email=?");
        $statement->execute(array($new_password_hashed, $cust_email));

        // Send email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'jerseyhouse1234@gmail.com'; // Replace with your actual SMTP username
            $mail->Password   = 'usce wrhm wqde hkvx'; // Replace with your actual SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            //Recipients
            $mail->setFrom('jerseyhouse1234@gmail.com', 'Jersey House');
            $mail->addAddress($cust_email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your New Password';
            $mail->Body    = 'Your new password is: ' . $new_password;

            $mail->send();
            $success_message = 'A new password has been sent to your email address.';
        } catch (Exception $e) {
            $error_message = 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo;
        }
    }
}
?>

<div class="page-banner" style="background-color:#444;background-image: url(assets/uploads/<?php echo $banner_reset_password; ?>);">
    <div class="inner">
        <h1>Forgot Password</h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="user-content">
                    <?php
                    if($error_message != '') {
                        echo "<div class='error' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>".$error_message."</div>";
                    }
                    if($success_message != '') {
                        echo "<div class='success' style='padding: 10px;background:#f1f1f1;margin-bottom:20px;'>".$success_message."</div>";
                    }
                    ?>
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="cust_email">Email *</label>
                            <input type="email" class="form-control" name="cust_email" required>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" value="Send New Password" name="form1">
                        </div>
                    </form>
                </div>                
            </div>
        </div>
    </div>
</div>

<?php require_once('footer.php'); ?>
