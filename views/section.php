

<div class="row">
    <div class="col-sm-4 sections-box">
       <?php  $display = new Display();
            $display->showAccordion(true);
          
          ?>
       
    </div>

    <div class="col-sm-8 box-right">
            <form id="section-form"
            class="form-group sectionInput"
            method="POST"
            action="index.php?action=newSection"
          >
            <label class="headlineSection" for="sectionText"
              >Section topic</label
            >

            <input
              type="text"
              class="form-control"
              id="sectionText"
              name="sectionText"
            />

            <button
              id="submitSectionText"
              class="btn btn-primary"
              type="submit"
            >
              New Section
            </button> <br/><br/>
            <span class="sectionInputWarning"></span>
          </form>
      </div>
</div>     