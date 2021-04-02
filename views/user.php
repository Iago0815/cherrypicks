   <div class="row">
        <div class="col-sm-4 sections-box">
           <?php  $display = new Display();
            $display->showAccordion(true);          
          ?>
          
        </div>
  
  
  <div class="col-sm-8 box-right">
    <div class="user-panel">

        <div class="user-panel-wrapper">  
          <div class="inbox-messages">

            <h4 class="inbox-messages-title">

             Inbox:
            </h4>
            <div class="inbox-messages-messages">
              <ul>

                <?php $display->showRequestSR();
                $display->showConfirmedS();
                $display->showDeclinedS();
              ?>
              

              
              </ul>
            </div>
          </div>

          <div class="users-box">

            <div class="users-list">
              <h4 class="users-list-title">
                  Users:                 
              </h4>
                
              <div class="users-list-list">
                <ul>
               <?php
                                
                 $userlist->displayUsers(); 
                  
                  ?>
                </ul>
              </div>
                
            </div>

            <div class="users-message-box">

              <h4 class="users-message-box-title">
                   Send request to:&nbsp;<span><b>no user selected</b></span>

              </h4>
              <div class="cp-request-title">

                 Write additional text:
              </div>

              <textarea id="additionalText" rows="4"></textarea> 

              <button class="CP-view-request-submit" data-userMail="" type="submit" >Submit</button>

              </div>
            </div>

          </div> 
          </div>
  </div>

        
    