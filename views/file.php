  
<div class="row">
        <div class="col-sm-4 sections-box">
            <?php  $display = new Display();
            $display->showAccordion(true);         
          ?>
          
        </div>
  
  
  <div class="col-sm-8 box-right">
  
  <div class="file-area">
    <div class="file_download">

      <form class="body" action="index.php?page=files" method="POST">
          <h4>Filedownload</h4><br/>
                <button class="btn" name="downloadJson">JSON</button><br/>
                <button class="btn" name="xml">XML</button>
          
              </form>
              </div> 
              
              
      <div class="file_upload">

                <?php 
             $upload = new Upload();   
   
        if($upload->checkFileUpload()) { ?>

            <form class="body" action="index.php?page=file" method="POST"  enctype="multipart/form-data">
            
            <h4>Fileupload (JSON only)</h4>  <span> <?php

               $upload->returnFilename();     
                
                      ?>
                           
           </span> <br/><br/>
                <div class="import-abort">
                   <button class="btn import-data" name="import">Import Data</button>
                   <button class="btn abort" name="abort">Abort</button>
                 </div> 
                 
                 <div class="info-import">
                  
                    WARNING! If the data is imported into the database, the existing
                        data will be deleted. Please create a backup of your
                        current data.                    
                 </div>
                    </form>
               
               <?php  }  
               
               else {
                 

                 ?>     <form class="body" action="index.php?page=file" method="POST"  enctype="multipart/form-data">
                 <h4>Fileupload (JSON only)</h4>  <span> <?php

                  if(isset($_POST['upload'])) {

                   $upload = new Upload($_FILES);
                 $upload->uploadFile();

                   }  else {?>
                           
                      </span>
                 
               <input type="file" name="file" id="uploadJSON" required/>
                <label for="uploadJSON">
                    <i class="fa fa-file-text-o fa-3x"></i>
                    <p>
                        <span>Browse</span> JSON file to begin upload

                    </p>

                </label>
                  <button class="btn" name="upload">Upload</button> 
           
                       <?php 
            
            } 
          } ?>
              </form>        

            </div>
              
              <div class="file_download_info">
                  <div class="file_download_info_content">
                   

                  </div>
              
              </div>
              <div class="file_upload_info">
                  <div class="file_upload_info_content">
                       <b>Last Upload:</b><br/>
                      <span><?php $timeStamp = $upload->showLastUpdate();
                      $timeStamp = date( "d-m-Y", strtotime($timeStamp));
                      echo $timeStamp;

                      ?></span>
                  </div>


              </div>
          
          
          </div>
</div>      
      