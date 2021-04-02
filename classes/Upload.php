<?php

class Upload {

    private $_user;
    private $_file_name;
    private $_file_temp;
    private $_file_size;
    private $_errors = array();
    private $_file_extension;
    private $_db;
    private $_location = "./uploads/";
    private $_upload_dir;
    private $_isNotValid = false;
    
    public function __construct(&$files = 0) {

       
    $this->_db = DB::getInstance();   

    $this->_sessionName = Config::get('session/session_name'); 

        if($files) { 

    $this->_file_name = $files['file']['name'];
    $this->_file_temp = $files['file']['tmp_name'];
    $this->_file_size = $files['file']['size'];

        }

    $temp_file_extension = explode('.',$this->_file_name);

    $this->_file_extension = strtolower(end($temp_file_extension));
   
    $this->_upload_dir = $this->_location;
   
    if(Session::exists($this->_sessionName)) {

            $this->_user = Session::get($this->_sessionName);
           
        }  else {

            // error, session does not exist
        }

}
    public function uploadFile() {

        if($this->_user) { 
       
        $this->validate();
        
        if(!empty($this->_errors)) {

            foreach($this->_errors as $err) {
                echo $err."<br/>";

            }

            echo "  <input type='file' name='file' id='uploadJSON' required/>
                <label for='uploadJSON'>
                    <i class='fa fa-file-text-o fa-3x'></i>
                    <p>
                        <span>Browse</span> JSON file to begin upload

                    </p>

                </label>
                  <button class='btn' name='upload'>Upload</button>";

          
        } else {

            move_uploaded_file($this->_file_temp,$this->_upload_dir.$this->_file_name);

            try {

            $this->_db->delete('file',array(
            'user_id','=',$this->_user));    

            $this->_db->insert('file',array(    
            'user_id' => $this->_user, 'file_name' => $this->_file_name));

            } 

            catch(Exception $e) {
                echo $e->getMessage();

            }
            
             echo "<b class='success'>".$this->_file_name."<br/>has been uploaded!</b><br/><br/>";

             echo " <div class='import-abort'>
                   <button class='btn import-data' name='import'>Import Data</button>
                   <button class='btn abort' name='abort'>Abort</button>
                 </div> 
                 
                 <div class='info-import'>
                  
                    WARNING! If the data is imported into the database, the existing
                        data will be deleted. Please create a backup of your
                        current data.                    
                 </div>";
             
        }

    }
    } 
    
    private function validate() {

    if(($this->_file_extension != "json")) {
            $this->addError("<b class='error'>Please select a JSON file!</b>");
        }

    else if($this->_file_size == 0) {
         $this->addError("<b class='error'>File is empty!</b>");
    }

    }

    private function addError($error) {
        $this->_errors[] = $error;

    }

    public function checkFileUpload() {

        $result = $this->_db->get('file',array('user_id','=',$this->_user));   
        if(!$this->_db->count()) {

            return false;
        
        } else {

            $this->_file_name = $result->first()->file_name;
            return true;

        }
     
    }

    public function returnFilename() {
         echo "<b class='success'>".$this->_file_name."<br/>has been uploaded!</b>";
    }

    public function isNotValid() {

        return $this->_isNotValid;
    }


    public function showLastUpdate() {

        try {

        $user = $this->_db->get('users',array('id','=',$this->_user));
        return $user->first()->file_upload;
          }

        catch (Exception $e) {
                     echo 'Caught exception: ',  $e->getMessage(), "\n";
                    }   

                }



   

}



?>