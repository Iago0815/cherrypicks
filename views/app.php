
     <div class="row">
        <div class="col-sm-4 sections-box">
          <?php  $display = new Display();
            $display->showAccordion();
          
          ?>
        </div>

        <div class="col-sm-8 box-right">
         
          <div class="cherry_img text-center">
          
          
          <img src="./img/cherry.jpg" width="300" alt="">
         
         </div>  
          <div class="form-group updateCP">
            <label class="headline" for="headline"
              >Headline, (<b>Update</b>)</label
            >
            <input type="headline" class="form-control" id="cp_headline" />

            <?php $display->showInputPanel();  ?>

            <div class="d-flex">
              <button
                id="updateCP"
                class="btn btn-primary"
                type="submit"
              >
                Update CP
              </button>
                  <a class="btn btn-danger delCP" type="submit" href="javascript:void(0);" style="color:#fff;">
               Del CP 
                </a>  

              <div id="created" class="ml-auto">CREATED ON: <span></span></div>
              <div id="updated">LAST UPDATE: <span></span></div>
            </div>
          </div>

          <div class="form-group newCP">
            <label class="headlineNewCp" for="headline">Headline</label>
            <input type="headline" class="form-control" id="newCpHeadline" />

            <label class="labelTextareas" for="area">Text</label>

            <textarea class="form-control myTextareas" id="area" rows="16"></textarea>

            <button id="newCP" class="btn btn-primary" type="submit">
              Submit new item
            </button>
          </div>
         

        </div>
      </div>


    