<?php
require_once 'core/init.php';


$user = new User();

 if($user->isLoggedIn()) {
 

        $request = new HTTP(Input::get('action'),$_POST);
        $request->http_exec();
       
       }
    



?>