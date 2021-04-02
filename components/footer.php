<!-- JQuery -->


     <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script> 
     <script
      src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
      integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
      crossorigin="anonymous"
    ></script>
    <script
      src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
      integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
      crossorigin="anonymous"
    ></script> 

     <!-- <script src="../cpicks/jquery/jquery-3.5.1.min.js"></script>
    <script src="../cpicks/bootstrap/js/bootstrap.min.js"></script>  -->

    <script>
     
      (function () { 


         var sectionId,id,cpId;

      /*
      *   date function
      */

      function myDate() {
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = today.getFullYear();

          return dd + '-' + mm + '-' + yyyy;


      }


     
      /*
      *   open / closes accordion panels 
      */  
       

      $(".accordion").on("click", function() {
        var $this = $(this).toggleClass("active");
        var panel = $this.next().toggleClass("show");

        
      });

      /*
      *   manage CP infotext
      */


      /*
      *  manage headline + texareas
      */  

          $(".panel").on("click", "li a.cp_header", function () {
       id = $(this).attr("data-textId");
       cpId = id.replace("area", "");

      $(".updateCP").show();
       $(".cherry_img").hide();      

      $(".sectionInput,.newCP,.info-area,.file-area").hide();

      $("#cp_headline").val($(this).html());

      $(".labelTextareas")
        .hide()
        .filter('[for="' + id + '"]')
        .show();

      $(".myTextareas")
        .hide()
        .filter('[id="' + id + '"]')
        .show();

      $("#created span").text($("#newdate" + cpId + "").val());
      $("#updated span").text($("#update" + cpId + "").val());
    });


    // intiate new CP

      $(".plus").on("click", function(e) {
      $(".newCP").show();
      $(".sectionInput,.updateCP,.cherry_img").hide();
        
      // $('.panel').hide(1000);

      e.stopPropagation();

      $(".labelTextareas").show();
      $(".myTextareas").show();

      //sectionId is the id of the section field (accordion-field), parent of +
      sectionId = $(this).parent(this).attr("id");

      str =
        "&nbsp;for&nbsp;<b>" +
        $(this).siblings("span").text() +
        "</b>,&nbsp;Headline";

      $("label.headlineNewCp").html(str);
    });

    //new CP

    $("#newCP").on("click", function() {

         $(".cherry_img").hide();

     const newCPHeadline = $("#newCpHeadline").val();
     const textarea =  $("#area").val()
     const randomId = Math.random().toString(36).substring(7);

     const now = new Date();
     const formattedDate = myDate();
     

      $("#" + sectionId)
        .next()
        .find("ul")
        .prepend(
          '<li><a class="cp_header" href="javascript:void(0);" data-textId="area' +
            randomId +
            '">' +
            newCPHeadline +
            "</a></li>"
        );

       $(
        '<label class="labelTextareas" for="area' +
          randomId +
          '">Text</label><textarea class="form-control myTextareas" id="area' +
          randomId +
          '" rows="16">' +
          textarea +
          '</textarea><input type="hidden" id="newdate' + 
          randomId + 
          '" value="' + 
          formattedDate + 
          '"> <input type="hidden" id="update' + 
          randomId + 
          '" value="NO DATA">'
          
      ).insertAfter("#cp_headline");

      //empty headline + textarea fields



      $("#newCpHeadline").val("");
      $("#area").val("");


  $.ajax({

    type: "POST",
    url: "./http.php?action=newCP",
    data:
      "section_id=" + sectionId +
      "&newCpHeadline=" + newCPHeadline +
      "&newCpArea=" + textarea,

    success: function (result,status) {
     
      console.log(result);

      // create new fields (headlines) for sections 
    },
    error: function(xhr,status,error) {

        var errorMessage = xhr.status + ': ' + xhr.statusText
         alert('Error - ' + errorMessage);
    },
  });
});

//update CPs

$("#updateCP").on("click", function () {
    const areaval = $("#" + id + "").val();
    const headline = $("#cp_headline").val(); 
    const now = new Date();
     const formattedDate = myDate();

  //updates headline text within .panel li a
      $("a[data-textId='" + id + "']").html(headline);

  //show update time
      $("#updated span").text(formattedDate);

  $.ajax({
    type: "POST",
    url: "./http.php?action=updateCP",
    data:
      "cp_headline=" +  headline +
      "&sectionText=" + areaval +
      "&cp_id=" + cpId,

    success: function (result) {

      console.log(result);
      alert("C.P. content has been updated");
     
    },
     error: function(xhr,status,error) {

        var errorMessage = xhr.status + ': ' + xhr.statusText
         alert('Error - ' + errorMessage);
    },
  });
});

// delete CP

$(".delCP").on("click", function () {
 
       var headline = $("#cp_headline").val();

  if (
    confirm(
      "Are you sure you want to delete the C.P. (" + headline
         + 
        ") ?" )) 
        
    {   if (id) {

       $("a[data-textId='" + id + "']").hide("slow");

      $.ajax({
        type: "POST",
        url: "./http.php?action=delCP",
        data: "cp_id=" + cpId,
        success: function (result) {

          console.log(result);
          alert("Cherry Pick has been deleted");
      
        },
         error: function(xhr,status,error) {

        var errorMessage = xhr.status + ': ' + xhr.statusText
         alert('Error - ' + errorMessage);
        },
      }); 
       $(".myTextareas, .labelTextareas, .updateCP").hide("slow"); 

    }  else {  alert('Error, no id found!'); }}else {
    return false;
  }   

  
});

// validate section input + enter section 
$('#submitSectionText').on('click', function(e) {

      e.preventDefault();
       if( !$('#sectionText').val() ) {
         $('.sectionInputWarning').text('Please enter a section topic'); 
    }  else {

          const newSectionText = $("#sectionText").val();
          const now = new Date();

              $.ajax({

        type: "POST",
        url: "./http.php?action=newSection",
        data: "sectionText=" + newSectionText +
              "&datetime=" + now, 
        success: function (result) { 
                  alert("Section has been successfully generated.");
                  },
                   error: function(xhr,status,error) {

        var errorMessage = xhr.status + ': ' + xhr.statusText
         alert('Error - ' + errorMessage);
        },
       
      });


         window.location.href = "index.php";

    }

        });

  //abort JSON file upload process

    $('button.abort').on('click', function() {


          $.ajax({

        type: "POST",
        url: "./http.php?action=abortUpload",
       
        success: function (result) { 
                 
                  },
                   error: function(xhr,status,error) {

        var errorMessage = xhr.status + ': ' + xhr.statusText
         alert('Error - ' + errorMessage);
        },
       
      });
    }) 

  //import JSON file data

    $('button.import-data').on('click',function(event) {

        event.stopPropagation();
        

       $.ajax({
        type: "POST",
        url: "./http.php?action=importData",       
      });
    });

   $('button.cross').on('click',function(event) {

         

       event.stopPropagation();
       sectionID = ($(this).attr("data-sectionid"));

       if(confirm("Deleting a section will also delete all CPs of this section. Are you sure you want to proceed?")) {

        self = this;

       $.ajax({

        type:'POST',
        url:'./http.php?action=delSection',
        data: "sectionId=" + sectionID, 

         success: function (result) { 

              window.location.href = "index.php";

                  },
                   error: function(xhr,status,error) {

        var errorMessage = xhr.status + ': ' + xhr.statusText
         alert('Error - ' + errorMessage);
        },
           
         });

         }      

   });  

    //MAIL 2 USER 

      $(".btnUsersList").on("click", function() {
        $(".users-message-box-title span b").text(
          $(this).parent("div").siblings("div").text()
        );
        $(".CP-view-request-submit").attr("data-userMail", $(this).attr("id"))
      });

      $(".CP-view-request-submit").on('click',function(){

        userid = ($(this).attr("data-usermail"));
        uid = userid.substring(5,7);
        messageText = ($("#additionalText").val());
        type = "request";

          $.ajax({

          type:'POST',
           url:'./http.php?action=sendMessage',
            data: "user2=" + uid +
                  "&messageTextSender=" + messageText +
                  "&type=" + type,

            success: function(result) {

              alert(result);
            }      
          });

        $("#additionalText").val("");

      });

      $("button#confirmCP").on("click", function() {
            uid = ($(this).attr("data-sender"));
            type = "response";
            status = "confirmed";

            $.ajax({

              type:'POST',
              url:'./http.php?action=sendMessage',
            data: "user1=" + uid +
                  "&type=" + type +
                  "&status=" + status,

            success: function(result) {
              alert(result);

            }       
            });

      });

      $("button#declineCP").on("click", function() {
            
            uid = ($(this).attr("data-sender"));
            type = "response";
            status = "declined";

            $.ajax({

              type:'POST',
              url:'./http.php?action=sendMessage',
            data: "user1=" + uid +
                  "&type=" + type +
                  "&status=" + status,

            success: function(result) {
              alert(result);

            }       
            });

      });

      $("#requestPermitted").on("click",function() {

          id = $(this).attr("data-viewpermitted");

          $.ajax({
            type:'POST',
            url:'./http.php?action=requestPermitted',
            data:'foreignId=' + id,

            success: function(result) {

              var win = window.open();
              win.document.write(result);
            }


          })
          
          
      })


      

      })();  

    </script>
    
  </body>
</html>