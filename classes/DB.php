<?php
require_once 'core/init.php';

class DB {

    private static $_instance = null;
    private $_pdo, 
            $_query,             //last query that has been executed
            $_error = false,     
            $_results,           //store result set
            $_count = 0;         

    private function __construct() {

        try {

            $this->_pdo = new PDO('mysql:host='.Config::get('mysql/host').';dbname='.Config::get('mysql/db'),Config::get('mysql/username'),Config::get('mysql/password'));

           

        }  catch(PDOException $e) {
                die($e->getMessage());

        }
    }

    public static function getInstance() {
            if(!isset(self::$_instance)) {
                self::$_instance = new DB();

            } 
            return self::$_instance;
    }

    public function query($sql, $params = array()) {
            $this->_error = false;
            if($this->_query = $this->_pdo->prepare($sql)) {

                $x = 1;
                if(count($params)) {
                           foreach($params as $param) {
                                
                                $this->_query->bindValue($x,$param);
                                $x++;
                        }
                   }

                if($this->_query->execute()) {

                        $this->_results = $this->_query->fetchAll(PDO::FETCH_OBJ);
                         $this->_count = $this->_query->rowCount();
             
                } else {
                        $this->_error = true;
                }                   
            }
        
        return $this;
        }
     

        public function action($action,$table,$where = array()) {
                if(count($where) === 3) {

                        $operators = array('=','>','<','>=','<=','!=');
                        
                        $field      = $where[0];
                        $operator   = $where[1];
                        $value      = $where[2];

                        if(in_array($operator,$operators))  {

                         $sql = "{$action} FROM {$table} WHERE {$field}{$operator}?";
                              
                              if(!$this->query($sql,array($value))->error()) {
                                
                                return $this;
                              }
                        }            
                }
                return false;
        }

        public function get($table,$where) {
                return $this->action('SELECT *',$table,$where);

        }

        public function getUserName($where) {
                return $this->action('SELECT `username`','`users`',$where);

        }

        public function getMessageIdByUsers($user1,$user2) {
               
                $result = $this->_pdo->prepare('SELECT `id` FROM 
                `messages` WHERE user1 = ? AND user2 = ?');

                $result->execute([$user1,$user2]);
                return $result->fetchAll();

        }

        public function delete($table,$where) {
                return $this->action('DELETE',$table,$where);

        }

        public function insert($table, $fields = []) {
          

                        $keys = array_keys($fields);
                        $values = '';
                        $x = 1;

                        foreach($fields as $field) {
                                $values .= "?";
                                if($x < count($fields)) {
                                        $values .= ', ';

                                 }        
                                $x++;                              

                        }

                        $sql_string = implode("`,`", $keys);

                       $sql = "INSERT INTO {$table} (`".$sql_string."`) VALUES ({$values})";

                        if(!$this->query($sql,$fields)->error()) {
                                return true;

                        
                }

                return false;
        }

        public function update($table,$id,$fields = []) {
                $set = '';
                $x = 1;

                foreach($fields as $name => $value) {
                        $set .= "{$name} = ?";
                         if($x < count($fields)) {
                                $set .= ", ";

                         }
                         $x++;
                }

               echo $sql = "UPDATE {$table} SET {$set} WHERE id = {$id}";
                
                  if(!$this->query($sql,$fields)->error()) {
                                return true;
                    
                }

                return false;
        }
      

         public function cpUpdate($table,$id,$fields = []) {
                $set = '';
                $x = 1;

                foreach($fields as $name => $value) {
                        $set .= "{$name} = ?";
                         if($x < count($fields)) {
                                $set .= ", ";

                         }
                         $x++;
                }

                $sql = "UPDATE {$table} SET {$set} WHERE cp_id = {$id}";
                
                  if(!$this->query($sql,$fields)->error()) {
                                return true;
                    
                }

                return false;
        }

        public function responseUpdate($user1,$user2,$status) {

        //UPDATE messages SET state = 'confirmed' WHERE user1 = 32 AND user2 = 33 

              $sql = "UPDATE `messages` SET `state` = ? WHERE `user1` = ? AND `user2` = ?";

              $result = $this->_pdo->prepare($sql);
               $result->execute([$status,$user1,$user2]);
                
        }

        public function fileDataImport($data,$user) {
              

           $stmt = $this->_pdo->prepare('INSERT INTO sections (`label`,`user_id`) VALUES (?,?)');
           $stmt2 = $this->_pdo->prepare('INSERT INTO cpicks (`user_id`,`section_id`,`label`,`text`,`new_datetime`,`update_time`) VALUES (?,?,?,?,?,?)');     

           foreach($data as $key => $value) {

           foreach($value as $k => $v) {

                        if($k == 'label') {
                        $stmt->execute([$v,$user]);

                        $sql = 'SELECT id FROM sections ORDER BY id DESC LIMIT 1';
                        $fetch_stmt = $this->_pdo->query($sql); 
                        $row = $fetch_stmt->fetch(PDO::FETCH_ASSOC);

                    
                        
                        $id = $row['id'];
                        }

                        if($k == 'items'){

                        foreach($v as $a => $b) {

                                foreach($b as $x => $y) {

                                switch($x) {
                                        
                                        case "label":
                                        $cp_label = $y;
                                        case "text":
                                        $cp_text = $y;
                                        case "new_datetime":
                                        $cp_date = $y;
                                        case "update_time":
                                        $cp_update = $y;
                                        }
                        
                        }
                        $stmt2->execute([$user,$id,$cp_label,$cp_text,$cp_date,$cp_update]);
                        }
                }
           }    
          }

        }

        public function fetchJsonData($user) {

              $result = [];
                try {
                $sectionStmt = $this->_pdo->prepare('
                        SELECT `id`, `label`
                        FROM sections
                        WHERE user_id = ?
                        ORDER BY id DESC
                ');
                $sectionStmt->execute(array($user));
                }
                catch(PDOException $e) {
                        echo $e->getMessage();
                }
                if ($result = $sectionStmt->fetchAll()) {
                        try{

                        $itemStmt = $this->_pdo->prepare('
                                SELECT `label`, `text`, `new_datetime`, `update_time`
                                FROM cpicks
                                WHERE section_id = ?
                ');
                
                        foreach ($result as &$section) {
                        $itemStmt->execute([$section['id']]);
                        
                        unset($section['id']);
                                $section['items'] = $itemStmt->fetchAll();
                }
                }

                catch(PDOException $e) {
                        echo $e->getMessage();
                }
                }

                        $JSON = json_encode($result);      
                        return $JSON;
                
                }

        public function fetchDataR($user,$state) {

                   $sender = $this->_pdo->prepare("SELECT * FROM messages 
                        WHERE `user2` = ? AND `state` = ?");

                         $sender->execute([$user,$state]);
                        return $sender->fetchAll(PDO::FETCH_ASSOC);

        }

        public function fetchDataS($user,$state) {

                   $sender = $this->_pdo->prepare("SELECT * FROM messages 
                        WHERE `user1` = ? AND `state` = ?");

                         $sender->execute([$user,$state]);
                        return $sender->fetchAll(PDO::FETCH_ASSOC);

        } 
        
        



        public function results() {
                return $this->_results;
        }

        public function first() {

                return $this->_results[0];
        }

        public function error() {
                return $this->_error;
        }

        public function count() {

                return $this->_count;
        }

}

 ?>
