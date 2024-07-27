<?php
if(!defined("LANG_VALUE_14")){
define("LANG_VALUE_14",'Logout');
}
if(!defined("LANG_VALUE_24")){
define("LANG_VALUE_24",'	
Orders');	
 

    }if(!defined("LANG_VALUE_88")){
        define("LANG_VALUE_88",'	
        Update Billing and Shipping Info');
        }
        if(!defined("LANG_VALUE_117")){
        define("LANG_VALUE_117",'	
        Update Profile');}	
        if(!defined("LANG_VALUE_89")){
            define("LANG_VALUE_89",'	
            Dashboard');}
        
        ?>
<div class="user-sidebar">
    <ul>
    <a href="dashboard.php"><button class="btn btn-danger"><?php echo LANG_VALUE_89; ?></button></a>
    <a href="customer-profile-update.php"><button class="btn btn-danger"><?php echo LANG_VALUE_117; ?></button></a>
        <a href="customer-billing-shipping-update.php"><button class="btn btn-danger"><?php echo LANG_VALUE_88; ?></button></a>
        <a href="customer-password-update.php"><button class="btn btn-danger"><?php echo LANG_VALUE_99; ?></button></a>
        <a href="customer-order.php"><button class="btn btn-danger"><?php echo LANG_VALUE_24; ?></button></a>
        <a href="logout.php"><button class="btn btn-danger"><?php echo LANG_VALUE_14; ?></button></a>
    </ul>
</div>