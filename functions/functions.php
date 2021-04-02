<?php

    /*  show sections + C.P. headlines in the sections-box*/
   
   function showSections($connection) {
       

       if(!$connection) {
           echo "No db connection possible";
       
       } else {

       
       $query = "SELECT * FROM sections WHERE `user_id` = ".mysqli_real_escape_string($connection,$_SESSION['id'])."  ORDER BY id DESC";
       $result = mysqli_query($connection,$query) or die ('Unable to execute query. '.mysqli_error($connection));
       
       while($row = mysqli_fetch_assoc($result)) {
           
            echo "<div class='accordion d-flex' id='section".$row['id']."'><span class='mr-auto'>".$row['label']."</span>";
        echo "<button class='btn btn-success btn-sm mr-1 plus'>+</button><button class='btn btn-danger btn-sm mr-1 cross'>&times;</button></div>";
      
            $cp_headline_query = "SELECT * FROM cpicks WHERE (section_id =".$row['id']." AND ".mysqli_real_escape_string($connection,$_SESSION['id'])." ) ORDER BY cp_id DESC";
            $cp_result = mysqli_query($connection,$cp_headline_query) or die ('Unable to execute query. '.mysqli_error($connection));
            
                 echo "<div class='panel'><ul>";
            
          while($cp_row = mysqli_fetch_assoc($cp_result)) {
              
                
               echo "<li><a class='cp_header' href='javascript:void(0);' data-textId='area".$cp_row['cp_id']."'>".$cp_row['label']."</a></li>";
                             
              
          }
                echo '</ul></div>';
       }
       
   }    

   }

   /* shows c.p. texareas + lables  */
   
   function showTextareas($connection) {
       
       $query = "SELECT `cp_id`,`text`,`new_datetime`,`update_time` from cpicks WHERE ".mysqli_real_escape_string($connection,$_SESSION['id'])."";
       
       $result = mysqli_query($connection,$query) or die ('Unable to execute query. '.mysqli_error($connection));
   
         while($row = mysqli_fetch_assoc($result)) {
       
           echo "<label class='labelTextareas' for='area".$row['cp_id']."'>Text</label>
    <textarea class='form-control myTextareas' id='area".$row['cp_id']."' rows='16'>".$row['text']."</textarea>";
            
    echo "<input type='hidden' id='newdate".$row['cp_id']."' value='".toTime($row['new_datetime'])."'>";
    echo "<input type='hidden' id='update".$row['cp_id']."' value='".toTime($row['update_time'])."'>";

         }
   }

   //create new section

   function newSection($connection) {

         $query = "INSERT INTO `sections` (`user_id`,`label`,`new_datetime`) 
                   VALUES (".mysqli_real_escape_string($connection,$_SESSION['id'])."'".mysqli_real_escape_string($connection,$_POST['sectionText'])."', NOW())"; 
        
         // $_POST['sectionText'];

         mysqli_query($connection,$query) or die ('Unable to execute query. '.mysqli_error($connection)); 

        
   }


   function toTime($db_timestamp) {
     $timestamp = strtotime($db_timestamp);
      return date("d-m-Y",$timestamp);

   }
    //
   
    function createJSON($connection,$variation) {

        $result = [];

    
    $sectionStmt = $connection->prepare('
        SELECT id, label
        FROM sections ORDER BY id DESC
    ');

    $sectionStmt->execute();
    $sectionStmt->store_result();
    $sectionStmt->bind_result($sectionId, $sectionLabel);

    if($variation == 'lite') {

    $itemStmt = $connection->prepare('
        SELECT label, text
        FROM cpicks
        WHERE section_id = ?
    ');

    } elseif($variation == 'full') {
        $itemStmt = $connection->prepare('
        SELECT label, text, new_datetime, update_time
        FROM cpicks
        WHERE section_id = ?
    ');


    }

    $itemStmt->bind_param('i', $sectionId);

    while ($sectionStmt->fetch()) {

        $section = [
            'label' => $sectionLabel,
            'items' => []
        ];
        
        $itemStmt->execute();

        if($variation == 'lite') {

        $itemStmt->bind_result($itemLabel, $itemText);
        
        while ($itemStmt->fetch()) {
            $section['items'][] = [
                'label' => $itemLabel,
                'text' => $itemText
            ];
        } //end while $itemStmt->fetch
        
       
        
        }  elseif($variation == 'full') {

            $itemStmt->bind_result($itemLabel, $itemText,$datetime,$update_time);
        
        while ($itemStmt->fetch()) {
            $section['items'][] = [
                'label' => $itemLabel,
                'text' => $itemText,
                'date' => $datetime,
                'update' => $update_time
            ];
        } //end while $itemStmt->fetch

        }


         $result[] = $section;

    }  //end while $sectionStmt->fetch

        $JSON = json_encode($result);
       
        return $JSON;
    }


    function createFile($info) {
   
    $date = date("d-m-h-i-s");
    $dir = "./JSON/";

    $file = $dir.'CP'.$date.'.json';


    file_put_contents($file, $info, FILE_APPEND);

    return $file;
    }
    

    function downloadFile($file) {

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

// UPLOAD FILE

function uploadJsonFile() {

 $errors= array();    

      $file_name = $_FILES['uploadJSON']['name'];
      $file_size = $_FILES['uploadJSON']['size'];
      $file_tmp = $_FILES['uploadJSON']['tmp_name'];
      $file_type = $_FILES['uploadJSON']['type'];
      $file_ext = strtolower(end(explode('.',$_FILES['uploadJSON']['name'])));
      
      $extensions= array("json");
      
      if(in_array($file_ext,$extensions)=== false){
         $errors[] = "Extension not allowed, please choose a JSON file.";
      }

      if($_FILES['uploadJSON']['size'] == 0) {
          $errors[] = "The file is empty";
      }

       $uploaddir = "./JSON_UPLOAD/";
       $uploadfile = $uploaddir . basename($file_name);
      
      if(empty($errors) == true){
         move_uploaded_file($file_tmp,$uploadfile);
         echo "Success";

        $_SESSION['upFileName'] = $file_name;
        $_SESSION['upFileSize'] = $file_size;

      }else{

       // print_r($errors);
      }


  header("Location:index.php");   
  return;

}

// SHOW UPLOADED FILE

function showUploadedFile() {

   if(isset($_SESSION['upFileName'])) {

        //JSON will set session variables, text will be displayed
        //if $_SESSION['upFileName'] is set
    
     echo '<div class="upload-info">Uploaded File: <span class="file-red">'.$_SESSION['upFileName'].'</span>,&nbsp;Filesize:&nbsp;'.$_SESSION['upFileSize'].'&nbsp;byte<br/><br/>
     <span>Would you like to import the file into the database?</span></div><span style="color:red;">(note: File import will DELETE the existing data in the database!!!</span><br/><span span style="color:red;">Please secure data by file export before.)</span><br/><br/><button id="importJsonData" class="btn btn-success" type="submit">Import Data</button><button id="abortImport" class="btn btn-danger ml-1" type="submit">Abort Import</button><input type="hidden" id="hiddenJSON" name="hiddenJSON" value='.$_SESSION['upFileName'].'>';

       }

}

//FETCH FILE DATA

function fetch_file_data($pdo,$type) {

    if($type == "name")  {

        $stmt = $pdo->prepare('SELECT `file_name` FROM  `file` LIMIT 1');

    }  elseif($type = "timestamp") {

        $stmt = $pdo->prepare('SELECT `upload_timestamp` FROM  `file` LIMIT 1');
    }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!empty($row)) {

            if($type == "name")  return $row['file_name'];

            if($type == "timestamp")  {

               return time_elapsed_string($row['upload_timestamp']); 

            }
        }
}

function time_elapsed_string($raw_date, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($raw_date);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
       
    );
    
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'within less than 24 hours.';
}

// ABORT IMPORT, DELETE FILE

function deleteUploadedJSON($filename) {

    if(!empty($filename)) {

    $uploaddir = "./JSON_UPLOAD/";
    $uploadedfile = $uploaddir . basename($filename);
    unlink($uploadedfile);


    return "File has been deleted";

    } else {

        return "File does exist";
    }  

}

//IMPORT FILE INTO DB

function importDbJsonData($filename,$pdo) {

$uploaddir = "./JSON_UPLOAD/";
$filepath = $uploaddir.$filename;

if (filesize($filepath) == 0){
    echo "The file is empty";
} else {
$data = (file_get_contents($filepath));

}

$obj = json_decode($data); 

//DB PDO stmt

$del_stmt1 = $pdo->prepare('DELETE FROM `sections`');
$del_stmt2 = $pdo->prepare('DELETE FROM `cpicks`');
$del_stmt3 = $pdo->prepare('DELETE FROM `file`');

$del_stmt1->execute();
$del_stmt2->execute();
$del_stmt3->execute();

$stmt_filename = $pdo->prepare('INSERT INTO `file` (`file_name`,`upload_timestamp`) VALUES (?,NOW())');

$stmt_filename->execute([$filename]);


$stmt = $pdo->prepare('INSERT INTO sections (`label`)
    VALUES (?)');

$stmt2 = $pdo->prepare('INSERT INTO cpicks (`section_id`,`label`,`text`,`new_datetime`,`update_time`)
    VALUES (?,?,?,?,?)');


foreach($obj as $key => $value) {

    foreach($value as $k => $v) {

        if($k == 'label') {
        $stmt->execute([$v]);

        $sql = 'SELECT id FROM sections ORDER BY id DESC LIMIT 1';
        $fetch_stmt = $pdo->query($sql); 
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
                        case "date":
                            $cp_date = $y;
                        case "update":
                            $cp_update = $y;
                        }
          
            }
            $stmt2->execute([$id,$cp_label,$cp_text,$cp_date,$cp_update]);
        }
        }
    }
}

}


// UPLOAD USER

function upload_user($pdo) {

    //check if user already exists

    $stmt1 = $pdo->prepare("SELECT id FROM `user` WHERE username  = ? LIMIT 1");

    $stmt1->execute([$_POST['username']]);

    $result_email = $stmt1->fetch(PDO::FETCH_ASSOC);

    if(!empty($result_email)) {

        echo "User ".$_POST['username']." already exists in the db";
        
    }

    else {

        //insert uname + pwd into the DB

        $stmt2 = $pdo->prepare("INSERT INTO `user` (username,pwd) VALUES (?,?)");
        
        $stmt2->execute([$_POST['username'],$_POST['password']]);

        //update pwd, use lastInsertId as salt for encryption

        $stmt3 = $pdo->prepare("UPDATE `user` SET pwd = ? WHERE id = ".$pdo->lastInsertId()."");

        $hashed_salted_pwd = md5(md5($pdo->lastInsertId()).$_POST['password']);

            $stmt3->execute([$hashed_salted_pwd]);


        /* NEXT STEPS: create users
        *   write login validation for login.php   
        *   db query then create SESSION-id
        *   check functions that have to changed for session
        *   create welcome text
        */
    }
    }

function check_user_pwd($pdo) {

     //check if user already exists

    $stmt1 = $pdo->prepare("SELECT id,pwd FROM `user` WHERE username  = ? LIMIT 1");

    $stmt1->execute([$_POST['username']]);

    $result = $stmt1->fetch(PDO::FETCH_ASSOC);

    if($result === FALSE) {

        return "User does not exist";
    }

    else  {

        $hashedPwd = md5(md5($result['id']).$_POST['pwd']);

        if($hashedPwd == $result['pwd']) {

            session_start();
            $_SESSION['id'] = $result['id'];

            return(1);
            }
            
    else {

            return "Password is not correct";
            }

        }

}

function welcome_message($pdo) {

    if(isset($_SESSION['id'])) {

        $stmt = $pdo->prepare("SELECT `username` FROM user WHERE id = ? LIMIT 1");

        $stmt->execute([$_SESSION['id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['username'];

    }
}







   
?>