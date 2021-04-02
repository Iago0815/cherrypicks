<?php
require_once 'core/init.php';

if(Session::exists('home')) {
    echo '<p>'.Session::flash('home').'</p>';
   
}
$user = new User();
$userlist = new UserList(Session::get(Config::get('session/session_name')));  


 if($user->isLoggedIn()) {

  if(isset($_GET['action'])) {
    if($_GET['action'] == 'newSection') {

      $request = new HTTP(Input::get('action'),$_POST);       
    }
   }

   if(isset($_POST['downloadJson'])) {


        if(null !== $user->data()->id) {
        $download = new Download($user->data()->id);   
        $download->downloadJson();
        }

  } 
   

  
//session file upload 
 

 
// HEADER
include("./components/header.php");

//BODY
include("./components/body.php");

//FOOTER
include("./components/footer.php"); 

} else {
      Redirect::to('./logout.php');
    }

    ?>