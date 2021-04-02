<?php
//error_reporting(1);

require_once 'core/init.php';

$user = new User();

if($user->isLoggedIn()) {
    Redirect::to('index.php');
} 

if(Input::exists()) {
    if(Token::check(Input::get('token'))) {

        $validate = new Validate();
        $validation = $validate->check($_POST,array(
            'username' => array('required' => true),
            'password' => array('required' => true)
        ));

        if($validate->passed()) {
            
           $user = new User(); 
           
           //remember User
            $remember = (Input::get('remember') === 'on' ? true : false);

           $login = $user->login(Input::get('username'),Input::get('password'),$remember);

           if($login) {
               Redirect::to('index.php');
           }

           
        }  
        }
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <link rel="stylesheet" type="text/css" href="css/multipage.css?<?php echo date('l jS \of F Y h:i:s A'); ?>"/>


</head>
<body>

<div class="login-container">
  <div class="login-box">
    <h3 class="login-title">C.P. Editor and Collaboration tool</h3>
    <h4 class="login-subtitle">Login:</h4>

    <form action="" method="post">
        <div class="field">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" autocomplete="off"/>
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" autocomplete="off"/>
        </div>
        
        <div class="field remember">
            <label for="remember">
            
                <input type="checkbox" name="remember" id="remember"/> Remember me
            </label>
        
        </div>
       
        <input type="hidden" name="token" value="<?php echo Token::generate();?>"/>
       
          <div class="field">
              <input type="submit" value="Log in" class="login_submit">

          </div>
          <?php
                    if(Input::exists()) {
                        if(isset($validate)) {
                            if(!$validate->passed()) {

                            echo '<div class="login-error">';

                             foreach($validate->errors() as $error) {
                echo $error,'<br/>';
                            }

                            echo '</div>';
                        } else {
                            if(isset($login)) {
                                if(!$login) {
                                     echo '<div class="login-error">';
                                     echo 'Username and/or Password wrong';

                                     echo '</div>';

                                }

                            }

                        }

                    }
                }
                ?>


            <div class="field">
            

              <div class="not_registered">Not registered yet?&nbsp; Please register <a href="./register.php">here</a></div>
          </div>
    
        </form>
    </div>
  </div>

</body>