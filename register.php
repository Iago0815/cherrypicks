<?php

require_once 'core/init.php';

$user = new User();

if($user->isLoggedIn()) {
    Redirect::to('index.php');
} 


if(Input::exists()) {

    if(Token::check(Input::get('token')))  {

   
   
    $validate = new Validate();
    $validate->check($_POST,array(

        'username' => array(
            'required' => true,
            'min' => 2,
            'max' => 20,
            'unique' => 'users'
        ),
        'password' => array(
            'required' => true,
            'min' => 6

        ),
        'password_again' => array(
            'required' => true,
            'matches' => 'password'

        ),
        'name' => array(
            'required' => true,
            'min' => 2,
            'max' => 50
        )
   ));

   if($validate->passed()) {

        $user = new User();

        $salt = Hash::salt(2);
    

        try {

            $user->create(array(
                'username' => Input::get('username'),
                'password' => Hash::make(Input::get('password'),$salt),
                'non_encr_pwd' => Input::get('password'),
                'salt' => $salt,
                'name' => Input::get('name'),
                'joined' => date('Y-m-d H:i:s'),
                'group' => 1,
                'lang' => Input::get('lang')

            ));

        } catch (Exception $e) {
            die($e->getMessage());
        }

        Session::flash('home','You registered successfully!');
        Redirect::to('index.php');
   
    } 
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>register</title>

       <link rel="stylesheet" type="text/css" href="css/multipage.css?<?php echo date('l jS \of F Y h:i:s A'); ?>"/>

       <style>

           .login-box form {
                margin-left: 3em;
                width: 20em;
                display: flex;
                flex-direction: column;
                }
            
            .login-box form .field {
                display: flex;
                width: 100%;
                justify-content: space-between;
                margin-top:0.3em;
                }    


           .login-box .field.register{
              margin-top:0.3em;
           }

           .login-error: {
                width: 100%;

           }


       </style>

</head>
<body>

<div class="login-container">
  <div class="login-box">
    <h3 class="login-title">C.P. Editor and Collaboration tool</h3>
    <h4 class="login-subtitle">Register:</h4>

    <form action="" method="POST">
        <div class="field">
            <label for="username">username</label>
            <input type="text" name="username" value="<?php echo escape(Input::get("username")); ?>" id="username"/>

        </div>
        <div class="field">
            <label for="password">password</label>
            <input type="password" name="password" id="password"/>
        </div>
        <div class="field">
            <label for="password_again">password again</label>
            <input type="password" name="password_again" id="password_again"/>
        </div>
        <div class="field">
            <label for="name">name</label>
            <input type="text" name="name" value="<?php echo escape(Input::get("name")); ?>" id="name"/>
        </div>
        <div class="field">
           <label for="lang-codes">Language</label>
           <select name="lang" id="lang-codes">
                <option value="us">English (AE)</option>
                <option value="gb">English (BE)</option>
                 <option value="de">German</option>
                <option value="fr">French</option>
                <option value="es">Spanish</option>
                <option value="it">Italien</option>
                <option value="ru">Russian</option>
                <option value="nl">Dutch</option>
           </select>
        </div>

         <div class="field register">
            <input type="hidden" name="token" value="<?php echo Token::generate(); ?>"/>
            <input type="submit" value="Register" class="login_submit"/>
        </div>

           <?php
                if(Input::exists()) {
                     if(isset($validate)) {
                            if(!$validate->passed()) {

                                 echo '<div class="login-error">';            
                                 foreach($validate->errors() as $error) {
         
                   echo $error. '<br/>';

                   }
                   echo '</div>';
                
            }
        }
        }
                ?>

          <div class="field">
              <div class="not_registered">Already registered?&nbsp; Please login <a href="./login.php">here</a></div>
          </div>
    </form>

     </div>
  </div>

</body>    
</html>