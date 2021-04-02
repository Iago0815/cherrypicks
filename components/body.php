


<body>

<div class="container border border-secondary">
     <div class="row header-bar">
        <div class="col-sm-4 header-bar-left">
          <h4>C.P. Editor 2.0</h4>

      <span>Hello, <?php echo escape($user->data()->username); ?>&nbsp;
    
        <img src='img/png100px/<?php echo $userlist->displayFlag(); ?>.png' width='23' height='14'/>
    
    </span>
         
     </div> 
        <div class="col-sm-8 header-bar-right d-flex">
         
              <a class="btn btn-success addSection" type="submit" href="index.php?page=section" style="color:#fff;">
               + Section 
                </a>          

              <a class="btn btn-primary mainapp ml-auto" href="index.php?page=mainapp"        style="color:#fff;">
             <b>CP</b>
              </a>      
            
            <a class="btn btn-primary user" href="index.php?page=user" style="color:#fff;">
              <i class="fa fa-user" aria-hidden="true">
              </i>
              </a>  

            <a class="btn btn-primary file" href="index.php?page=file" style="color:#fff;"> 
              <i class="fa fa-file" aria-hidden="true">
              </i>
              </a>  
         
        
            <a class="btn btn-primary info" href="index.php?page=info"   style="color:#fff;">
              <i class="fa fa-info" aria-hidden="true">
              </i>
              </a>  
         
            <a class="btn btn-danger logout" href="logout.php" style="color:#fff;"> 
              <i class="fa fa-sign-out" aria-hidden="true">
              </i>
            </a>
       
        </div>
      </div> 
      <?php

      if(Input::get('page') == NULL || (Input::get('page') == 'mainappli'))  {

         include("./views/app.php");

      }  else {

            switch (Input::get('page')) {
             case "section":
           include("./views/section.php");
             break;
             case "info":
           include("./views/info.php");
            break;
                 case "file":
           include("./views/file.php");
            break;


             case "user":
           include("./views/user.php");
            break; 
              case "mainapp" || NULL:
           include("./views/app.php");
          
        break;
        default:
          Redirect::to(404);
        break;
  }}

       
    
    ?>

</div>
  </body>