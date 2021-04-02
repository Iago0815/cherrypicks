<?php


class UserList {

    private $_db;
    private $_userListLength;
    private $_users = [];
    private $_currentUserId;

    public function __construct($id) {
        
          $this->_db = DB::getInstance();
          $this->_currentUserId = $id;

    }

    private function getUserArray($currentUser = false) {
       //$this->_users
       if(!$currentUser) {

       $data = $this->_db->get('users',array('id','!=',$this->_currentUserId));
       
       } else {

        $data = $this->_db->get('users',array('id','=',$this->_currentUserId));
       }
        
        $this->_userListLength = $data->count();
        $this->_users = $data->results();
    } 

    
    public function displayUsers() {

        $this->getUserArray();

        $display = new Display();

        for($i = 0; $i < $this->_userListLength; $i++) {

            $display->showUser($this->_users[$i]->username,$this->_users[$i]->id,$this->_users[$i]->lang);

            
       }
    }

    public function displayFlag() {
        
            $this->getUserArray(true);

             return $this->_users[0]->lang;


    }


}


?>


