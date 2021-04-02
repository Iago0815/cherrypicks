<?php

class User {

    private $_db,
            $_data, 
            $_sessionName,
            $_isLoggedIn,  
            $_cookieName;      


    public function __construct($user=null) {

        $this->_db = DB::getInstance();

        $this->_sessionName = Config::get('session/session_name');
        $this->_cookieName = Config::get('remember/cookie_name');

        //check if the user actually exists or not


        // if $user == null
        if(!$user) {

            if(Session::exists($this->_sessionName)) {
                $user = Session::get($this->_sessionName);

                if($this->find($user)) {

                    $this->_isLoggedIn = true;
                }  else {
                    //process logout

                }

            } 
                     
            }   //if $user has been defined
            else {

               $this->find($user);
                
        }
    }

    public function update($fields = array(),$id = null) {

        if(!$id && $this->isLoggedIn()) {
            $id = $this->data()->id;

        }

        if(!$this->_db->update('users',$id,$fields)) {
            throw new Exception('There was a problem updating');
        }

    }

    public function create($fields = array()) {
        if(!$this->_db->insert('users',$fields)) {
            throw new Exception("There was a problem creating an account.");

        }
    }

    /*find user by username OR id (would fail for user that have only
    numbers as usernames) 
    $field is either id or username
    store user data in $_data
    
    */

    public function find($user = null) {
        if($user) {

            $field = (is_numeric($user)) ? 'id' : 'username';
            $data = $this->_db->get('users',array($field,'=',$user));

            if($data->count()) {

                $this->_data = $data->first();
              

                return true;
            }
        }
        return false;

    }


    public function login($username = null,$password = null,$remember = false) {

        if(!$username && !$password && $this->exists()){
                
               Session::put($this->_sessionName,$this->data()->id);


        } else {

             $user = $this->find($username);

        if($user) {

           /* echo "<b>Data from the database.</b><br/>";
            print_r($this->data());
            echo "<br/><br/>";

            echo "<b>PWD(Hash) from the DB:</b><br/>".$this->data()->password."<br/><br/><b>PWD check Hash:</b><br/>".Hash::make($password,$this->data()->salt)."<br/><br/><b>Salt from the db (for the check!!):</b><br/>".$this->data()->salt."<br/><br/><b>PWD typed in by user (for the check!!)</b><br/>".$password."<br/><br/>";*/
            


           if($this->data()->password === Hash::make($password,$this->data()->salt)) {

                Session::put($this->_sessionName, $this->data()->id);

                //session management user

                if($remember) {
                    $hash = Hash::unique();


                    //does userId exist in the db table user_session already??
                    $hashCheck = $this->_db->get('user_session',array('user_id','=',$this->data()->id));

                    /*we do not want to have more than one hash assigend to  a user id: !$hashCheck->count()
                    only one record per user */

                    

                    if(!$hashCheck->count()) {
                        $this->_db->insert('user_session',array(
                            'user_id' => $this->data()->id,
                            'hash' => $hash

                        ));

                    } else {
                        $hash = $hashCheck->first()->hash;

                    }

                    Cookie::put($this->_cookieName,$hash,Config::get('remember/cookie_expiry'));

                }


              return true;         
        
          }

        }

    }
         return false;
    
    }

    public function exists() {
        return (!empty($this->_data)) ? true : false;
    }

    public function logout() {

        $this->_db->delete('user_session',array('user_id','=',$this->data()->id));

        Session::delete($this->_sessionName);
        Cookie::delete($this->_cookieName);
    }


    public function data() {

        return $this->_data;
    }

    public function isLoggedIn() {

        return $this->_isLoggedIn;
    }
}

?>