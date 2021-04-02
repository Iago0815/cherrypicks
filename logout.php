<?php 
require_once('core/init.php');

$user = new User();

if($user->isLoggedIn()) {
$user->logout();

}



?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>

     <link rel="stylesheet" type="text/css" href="css/multipage.css?<?php echo date('l jS \of F Y h:i:s A'); ?>"/>

     <style>
         .login-box .field {
            color:white;
            margin-left: 3em;
            margin-bottom:2em;
         }

          .login-box .field a{
            color:white;

         }

     </style>

</head>
<body>

<div class="login-container">
  <div class="login-box">
    <h3 class="login-title">C.P. Editor and Collaboration tool</h3>
    <h4 class="login-subtitle">You have been successfully logged out.</h4>


         <div class="field">
    Would you like to <a href="./login.php">login</a> again? 
    </div>

</div>
</div>
</body>
</html>