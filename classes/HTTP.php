<?php
class HTTP {

    private $_httpAction;
    private $_postParams = [];
    private $_trimmedSectionId;
    private $_user;
    private $_timestamp;
    private $_uploadDir;
    private $_filename;

    public function __construct($action,&$post) {
        $this->_db = DB::getInstance();
        $this->_httpAction = $action;
        $this->_postParams = $post;
        $this->_sessionName = Config::get('session/session_name');
        $this->_timestamp = date('Y-m-d H:i:s');
        $this->_uploadDir = "./uploads/";


         if(Session::exists($this->_sessionName)) {

            $this->_user = Session::get($this->_sessionName);
           
        }  else {

            // error, session does not exist
        }

        if(isset($this->_postParams['section_id'])) {
            $this->_trimmedSectionId = ltrim($this->_postParams['section_id'],'section');

        }
    }


    public function http_exec() {

        switch($this->_httpAction) {

           case 'newCP' : $this->newCP();
           break;
           case 'updateCP' : $this->updateCP();
           break;
           case 'delCP' : $this->delCP();
           break;
           case 'newSection' : $this->newSection();
           break;
           case 'abortUpload' : $this->abortUpload();
           break;
           case 'importData' : $this->importData();
           break;
           case 'delSection' : $this->delSection();
           break;
           case 'downloadJson' : $this->downloadJson();
           break;
           case 'sendMessage' : $this->sendMessage();
           break;
           case 'requestPermitted': $this->showRequestedCPs();
           break;

        }
    }

    private function newSection()  {
            

        if(!empty($this->_postParams['sectionText'])) {

          $this->_db->insert('sections',array(
            'user_id' => $this->_user, 
            'label' => $this->_postParams['sectionText'],
            'new_datetime' => $this->_postParams['datetime']
          ));

        }   else {
            // Session flash

        }
            
        header("Location:index.php");

    }

    private function newCP() {

         $this->_db->insert('cpicks',array(
            'section_id' => $this->_trimmedSectionId,
            'user_id' => $this->_user,
            'label' => $this->_postParams['newCpHeadline'],
            'text' => $this->_postParams['newCpArea'],
            'new_datetime' => $this->_timestamp
        ));
    }

    private function updateCP() {

         $this->_db->cpUpdate('cpicks',$this->_postParams['cp_id'],array(
                'label' => $this->_postParams['cp_headline'],
                'text' => $this->_postParams['sectionText'],
                'update_time' => $this->_timestamp,
           ));

    }

    private function delCP() {

        $this->_db->delete('cpicks',array('cp_id','=', $this->_postParams['cp_id']
    ));
    }

    private function abortUpload() {
            //get file from db

            $this->_filename = $this->returnFilename();

            $location = $this->_uploadDir.$this->_filename;
            if(unlink($location)) {

                try {

                   $this->_db->delete('file',array('user_id','=',$this->_user));

                }
                catch (Exception $e) {
                     echo 'Caught exception: ',  $e->getMessage(), "\n";
                    }

            }  else {

                echo "File not found";
            }
            echo $location;

    }

    private function importData() {   
            $filename = $this->returnFilename();
            $filepath = $this->_uploadDir.$filename;

        if (filesize($filepath) == 0){
                echo "The file is empty";
            } else {
            $data = (file_get_contents($filepath));

            $obj = json_decode($data);    
               
                try {
                
                 $this->_db->delete('sections', array('user_id','=',$this->_user));  
                 $this->_db->delete('cpicks', array('user_id','=',$this->_user));      
                 $this->_db->delete('file', array('user_id','=',$this->_user));   

                    unlink($filepath);

                    $this->_db->update('users',$this->_user,array(
                'file_upload' => $this->_timestamp,
           ));

                 $this->_db->fileDataImport($obj,$this->_user);

                   
                }
                
                 catch (Exception $e) {
                     echo 'Caught exception: ',  $e->getMessage(), "\n";
                    }   
            }                
    }

    private function delSection() {

                if($this->_postParams['sectionId']) {

                    $this->_db->delete('cpicks',array('section_id','=',$this->_postParams['sectionId']));
                    $this->_db->delete('sections',array('id','=',$this->_postParams['sectionId']));

                }                      
    }


    private function returnFilename() {
        $uploadedFile = $this->_db->get('file',array('user_id','=',$this->_user));
          return $uploadedFile->first()->file_name;
    }


    public function sendMessage() {

        if($this->_postParams['type'] == 'request') {

            if(isset($this->_postParams['user2']) && isset($this->_postParams['messageTextSender'])) {

            $message = new Messenger($this->_postParams['user2']);
            $message->request($this->_postParams['messageTextSender']);

            } else {

                
            }
        }

        elseif($this->_postParams['type'] == 'response') {

            if(isset($this->_postParams['user1'])) {
              $message = new Messenger($this->_postParams['user1']);               
              $message->response($this->_postParams['status']);

                }
       }
    }

    public function showRequestedCPs() {

        $display = new Display($this->_postParams['foreignId']);


        //HEADER
        include("./components/header.php");

        $user = new User($this->_postParams['foreignId']);
        $userlist = new UserList($this->_postParams['foreignId']);

        
            
        echo "<body>";
        echo "<div class='container border border-secondary'>";
          echo "<div class='row header-bar'>";
          echo "<div class='col-sm-4 header-bar-left'>";
          echo "<h4>C.P. Editor 1.0</h4>";
        
           ?> <span>Cherry Picks from <b><?php echo escape($user->data()->username); 
           
           
           ?></b>&nbsp;
           
             <img src='img/png100px/<?php echo $userlist->displayFlag(); ?>.png' width='23' height='14'/> (READ only)
           <?php
          echo "</div></div>";
        echo "<div class='row'>";
        echo "<div class='col-sm-4 sections-box'>";
        

            $display->showAccordion();

           echo "</div>";
           echo "  <div class='col-sm-8 box-right'>
         
           <div class='cherry_img text-center'>
          
          
             <img src='./img/cherry.jpg' width='300' alt=''>
         
           </div>  
          <div class='form-group updateCP'>
            <label class='headline' for='headline'
              >Headline, (<b>Update</b>)</label
            >
            <input type='headline' readonly class='form-control' id='cp_headline' />";


        $display->showInputpanel('readonly');

        echo "</div></div></div></div>";

        echo "<script src='https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js'></script>"; 
        
        ?>     <script>
            
                    $(".accordion").on("click", function() {
                var $this = $(this).toggleClass("active");
                var panel = $this.next().toggleClass("show");
            
                
            });

                  $(".panel").on("click", "li a.cp_header", function () {
                    id = $(this).attr("data-textId");
                    cpId = id.replace("area", "");

                    $(".updateCP").show();
                    $(".cherry_img").hide();   

                     $("#cp_headline").val($(this).html());

                    $(".labelTextareas")
                        .hide()
                        .filter('[for="' + id + '"]')
                        .show();

                    $(".myTextareas")
                        .hide()
                        .filter('[id="' + id + '"]')
                        .show();

                     });

            </script>
           
        </body></html>

            <?php    

    }

   }



?>