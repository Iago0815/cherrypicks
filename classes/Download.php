<?php

class Download  {

    private $content;
    private $_user;
    private $_db;

    public function __construct($user) {
        	$this->_db = DB::getInstance();
            $this->_user = $user;

        }

    public function downloadJson() {

        $content = $this->_db->fetchJsonData($this->_user);     
       $this->downloadFile($this->createFile($content));        
    }


    private function createFile($content) {

        $jsonDir = "./downloads/";
        $date = date("d-m-h-i-s");

        $file = $jsonDir.'CP'.$date.'.json';

             file_put_contents($file, $content, FILE_APPEND);

        return $file;
    } 

    public function downloadFile($file) {

        

                if (file_exists($file)) {
            
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($file).'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit();


    
            }   else {
                    echo "Sorry, file does not exist";
    } 
        }
}

?>