<?php

class Messenger {


    private $db;
    private $user1;
    private $user2;
    private $sessionName;

    public function __construct($user2) {

        $this->sessionName = Config::get('session/session_name');
        $this->db =  $this->_db = DB::getInstance();
        $this->user2 = $user2;

           if(Session::exists($this->sessionName)) {

            $this->user1 = Session::get($this->sessionName);
           
        }  else {

            // error, session does not exist
        }

    }

    public function request($message) {

       if($this->user2 && $message) {

        $result = $this->db->getMessageIdByUsers($this->user1,$this->user2);
        $state = 'pending';

        if(count($result) >= 1) {

            echo "Request has already been sent";
        }
        else {

        // echo "User2: ".$this->user2." Text: ".$message;
        $this->db->insert('messages',array(
            'user1'=>$this->user1,
            'user2'=>$this->user2,
            'info_text'=>$message,
            'state'=>$state)
        );
        
            echo "Message has been sent.";
       } 
      }
    }
    
        //$this->user2 == user1
        //$this->user1 == user2 

    public function response($status) {

        if($status == "confirmed") {

            try {
            $this->db->responseUpdate($this->user2,$this->user1,$status);
            }
             catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
            }

            echo "Request has been confirmed. Please reload the page.";

        }

        else if(($status == "declined")) {

                 try {


             $this->db->responseUpdate($this->user2,$this->user1,$status);
                 }
             catch (Exception $e) {
                echo 'Caught exception: ',  $e->getMessage(), "\n";
    }    
      
                 echo "Request has been declined. Please reload the page.";
    }

    }

}


?>