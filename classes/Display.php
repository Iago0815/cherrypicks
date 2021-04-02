<?php

class Display {

    private $db;
    private $user;
    private $sessionName;

    public function __construct($user=null) {

        $this->db = DB::getInstance();
        $this->sessionName = Config::get('session/session_name');

        if(!$user) {
            if(Session::exists($this->sessionName)) {

            $this->user = Session::get($this->sessionName);
           
          } 
        }   else {

            if($this->userExists($user)) {
            $this->user = $user;
          }
   
        }
        }    

         private function userExists($user) {

            if($user && (is_numeric($user))) {
                  $data = $this->db->get('users',array('id','=',$user));

            if($data->count()) {
                  return true;               
            } else {

            return false;
        }
        }
    }

    public function showAccordion($blocked = null) {

        $resSections = [];
        $resCPs = [];

         $result = $this->db->action('SELECT `id`,`label`','sections',array('user_id','=',$this->user));

         $resSections = $result->results();

        

        for($i = 0; $i < count($resSections); $i++) {

            echo "<div class='accordion d-flex' id='section".$resSections[$i]->id."'><span class='mr-auto'>".$resSections[$i]->label."</span>";


             if($blocked) { echo "<button class='btn btn-secondary btn-sm mr-1'>+</button><button class='btn btn-secondary btn-sm mr-1'>&times;</button></div>"; }  else {

              echo "<button class='btn btn-success btn-sm mr-1 plus' >+</button><button class='btn btn-danger btn-sm mr-1 cross' data-sectionid='".$resSections[$i]->id."'>&times;</button></div>";
             }
            

            if(!$blocked) {
            $result2 = $this->db->action('SELECT `label`,`cp_id`','cpicks',array('section_id','=',$resSections[$i]->id));  

            $resCPs = $result2->results();

                  echo "<div class='panel'><ul>";
            
            for($j = 0; $j < count($resCPs); $j++) {

                 echo "<li><a class='cp_header' href='javascript:void(0);' data-textId='area".$resCPs[$j]->cp_id."'>".$resCPs[$j]->label."</a></li>";
            }
            echo '</ul></div>';
            }
        } 
    }
    
    public function showInputPanel($readonly = false) {

            $resCPs = [];

            $result = $this->db->action('SELECT `cp_id`,`text`,`new_datetime`,`update_time`','cpicks',array('user_id','=',$this->user));       


            $resCPs = $result->results();

            for($i = 0; $i < $result->count(); $i++) {

            echo "<label class='labelTextareas' for='area".$resCPs[$i]->cp_id."'>Text</label>
            <textarea class='form-control myTextareas' id='area".$resCPs[$i]->cp_id."' rows='16' ".$readonly.">".$resCPs[$i]->text."</textarea>";
                    
            echo "<input type='hidden' id='newdate".$resCPs[$i]->cp_id."' value='".$this->toTime($resCPs[$i]->new_datetime)."'>";
            echo "<input type='hidden' id='update".$resCPs[$i]->cp_id."' value='".$this->toTime($resCPs[$i]->update_time)."'>";

            }
    }

    private function toTime($db_timestamp) {

            if($db_timestamp == null) {
                return "NO DATA";
            }
            else {

             $timestamp = strtotime($db_timestamp);
           return date("d-m-Y",$timestamp);
            }
    }

    public function showUser($username,$id,$lang) {

           echo"<li><div class='userList-left'><i class='fa fa-user-circle'></i>&nbsp;".$username."</div>";

            echo "<div class='userList-right'><img src='img/png100px/".$lang.".png' width='23' height='14'/>";

            echo "<span class='btnUsersList' id='uList".$id."'><i class='fa fa-envelope-o'></i></span></div></li>";

    }

    public function showRequestSR() {     


            $sender = $this->db->fetchDataS($this->user,'pending');
            $counter = count($sender);
            
            if($sender !== null) {
               
             for($i = 0; $i <$counter; $i++) {

                    $result = $this->db->getUserName(array('id','=',$sender[$i]['user2'])); 
                    $name = $result->first()->username;

                echo" <li>
                  <div style='width:650px !important;'><i class='fa fa-envelope' aria-hidden='true'></i> From <b>admin <i>(info):</i></b><br/>
                  Request has been successfully sent to <b>{$name}</b>. Please await the response.
                  
                  
                  </div></li>
                ";
             }
            }

            $receiver = $this->db->fetchDataR($this->user,'pending');
            $counterR = count($receiver);

            if($receiver !== null) {

              for($i = 0; $i <$counterR; $i++ ) {

                   $result = $this->db->getUserName(array('id','=',$receiver[$i]['user1'])); 
                    $name = $result->first()->username;

                   echo " <li>
                  <div class='inbox-messages-left'>
                   <i class='fa fa-envelope' aria-hidden='true'></i> 
                    From <b>{$name} <i>(C.P. view request)</i></b><br/> 
                    <b>Message:</b> {$receiver[$i]['info_text']}
             
                  </div>
                  <div class='inbox-messages-right'>
                    <button id='confirmCP' class='btn btn-success btn-sm' data-sender='{$receiver[$i]['user1']}' >Confirm</button>
                    <button id='declineCP' class='btn btn-danger btn-sm' data-sender='{$receiver[$i]['user1']}'>Decline</button>
                  </div>
                </li>";

              }
            }            
             
         }  

         public function showConfirmedS() {

            $sender = $this->db->fetchDataS($this->user,'confirmed');
            $counter = count($sender);
            
            if($sender !== null) {
               
             for($i = 0; $i <$counter; $i++) {

                    $result = $this->db->getUserName(array('id','=',$sender[$i]['user2'])); 
                    $name = $result->first()->username;

                echo"
                
                 <li>
                  <div class='inbox-messages-left'><i class='fa fa-envelope' aria-hidden='true'></i> From <b>{$name}</b><br/> 
                  access to Cherry Picks permitted.
                   
                  </div>
                  <div class='inbox-messages-right'>
                     <button class='btn btn-primary btn-sm' id='requestPermitted' data-viewpermitted='{$sender[$i]['user2']}'>View CPs</button>
                  </div>
                </li>
                ";
             }
            }

         }

          public function showDeclinedS() {

            $sender = $this->db->fetchDataS($this->user,'declined');
            $counter = count($sender);
            
            if($sender !== null) {
               
             for($i = 0; $i <$counter; $i++) {

                    $result = $this->db->getUserName(array('id','=',$sender[$i]['user2'])); 
                    $name = $result->first()->username;

                echo" 

                 <li>
                  <div class='inbox-messages-left'><i class='fa fa-envelope' aria-hidden='true'></i> From <b>{$name}</b><br/> Access to Cherry Picks denied
                  
                  </div>
               
                </li>   
                ";
             }
            }

         }

    
}

?>